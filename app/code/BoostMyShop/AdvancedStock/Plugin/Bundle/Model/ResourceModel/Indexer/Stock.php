<?php

namespace BoostMyShop\AdvancedStock\Plugin\Bundle\Model\ResourceModel\Indexer;

use Magento\Catalog\Api\Data\ProductInterface;


class Stock extends \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock
{
    private $queryProcessorComposite;

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        $this->_updateIndex($entityIds);

        return $this;
    }

    /**
     * Retrieve table name for temporary bundle option stock index
     *
     * @return string
     */
    protected function _getBundleOptionTable()
    {
        return $this->getTable('catalog_product_bundle_stock_index');
    }

    /**
     * Prepare stock status per Bundle options, website and stock
     *
     * @param int|array $entityIds
     * @param bool $usePrimaryTable use primary or temporary index table
     * @return $this
     */
    protected function _prepareBundleOptionStockData($entityIds = null, $usePrimaryTable = false)
    {
        $this->_cleanBundleOptionStockData();
        $linkField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();
        $idxTable = $usePrimaryTable ? $this->getMainTable() : $this->getIdxTable();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['product' => $this->getTable('catalog_product_entity')],
            ['entity_id']
        );
        $select->join(
            ['bo' => $this->getTable('catalog_product_bundle_option')],
            "bo.parent_id = product.$linkField",
            []
        );
        $status = new \Zend_Db_Expr(
            'MAX(' . $connection->getCheckSql('e.required_options = 0', 'i.stock_status', '0') . ')'
        );
        $select->join(
            ['cis' => $this->getTable('cataloginventory_stock')],
            '',
            ['website_id', 'stock_id']
        )->joinLeft(
            ['bs' => $this->getTable('catalog_product_bundle_selection')],
            'bs.option_id = bo.option_id',
            []
        )->joinLeft(
            ['i' => $idxTable],
            'i.product_id = bs.product_id AND i.website_id = cis.website_id AND i.stock_id = cis.stock_id',
            []
        )->joinLeft(
            ['e' => $this->getTable('catalog_product_entity')],
            'e.entity_id = bs.product_id',
            []
        )->group(
            ['product.entity_id', 'cis.website_id', 'cis.stock_id', 'bo.option_id']
        )->columns(
            ['option_id' => 'bo.option_id', 'status' => $status]
        );

        if ($entityIds !== null) {
            $select->where('product.entity_id IN(?)', $entityIds);
        }

        // clone select for bundle product without required bundle options
        $selectNonRequired = clone $select;

        $select->where('bo.required = ?', 1);
        $selectNonRequired->where('bo.required = ?', 0)->having($status . ' = 1');
        $query = $select->insertFromSelect($this->_getBundleOptionTable());
        $connection->query($query);

        $query = $selectNonRequired->insertFromSelect($this->_getBundleOptionTable());
        $connection->query($query);

        return $this;
    }

    /**
     * Get the select object for get stock status by product ids
     *
     * @param int|array $entityIds
     * @param bool $usePrimaryTable use primary or temporary index table
     * @return \Magento\Framework\DB\Select
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $this->_prepareBundleOptionStockData($entityIds, $usePrimaryTable);
        $connection = $this->getConnection();
        $select = parent::_getStockStatusSelect($entityIds, $usePrimaryTable);

        //BMS: invert columns website_id & stock_id to match to columns order in table  cataloginventory_stock_status_idx
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        )->columns(
            ['e.entity_id', 'cis.website_id', 'cis.stock_id']
        )->joinLeft(
            ['o' => $this->_getBundleOptionTable()],
            'o.entity_id = e.entity_id AND o.website_id = cis.website_id AND o.stock_id = cis.stock_id',
            []
        )->columns(
            ['qty' => new \Zend_Db_Expr('0')]
        );

        $statusExpr = $this->getStatusExpression($connection);
        $select->columns(
            [
                'status' => $connection->getLeastSql(
                    [
                        new \Zend_Db_Expr(
                            'MIN(' . $connection->getCheckSql('o.stock_status IS NOT NULL', 'o.stock_status', '0') . ')'
                        ),
                        new \Zend_Db_Expr('MIN(' . $statusExpr . ')'),
                    ]
                ),
            ]
        );

        //BMS customization to remove restriction on getDefaultScopeId
        $select->reset(\Magento\Framework\DB\Select::WHERE);
        $select->where("(e.type_id = 'bundle')");


        if ($entityIds !== null) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        return $select;
    }

    /**
     * Prepare stock status data in temporary index table
     *
     * @param int|array $entityIds  the product limitation
     * @return $this
     */
    protected function _prepareIndexTable($entityIds = null)
    {
        parent::_prepareIndexTable($entityIds);
        $this->_cleanBundleOptionStockData();

        return $this;
    }

    /**
     * Update Stock status index by product ids
     *
     * @param array|int $entityIds
     * @return $this
     */
    protected function _updateIndex($entityIds)
    {
        $connection = $this->getConnection();
        $select = $this->_getStockStatusSelect($entityIds, true);
        $select = $this->getQueryProcessorComposite()->processQuery($select, $entityIds, true);
        $query = $connection->query($select);

        $i = 0;
        $data = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $i++;
            $data[] = [
                'product_id' => (int)$row['entity_id'],
                'website_id' => (int)$row['website_id'],
                'stock_id' => (int)$row['stock_id'],    //BMS Changes
                'qty' => (double)$row['qty'],
                'stock_status' => (int)$row['status'],
            ];

            if ($i % 1000 == 0) {
                $this->_updateIndexTable($data);
                $data = [];
            }
        }
        $this->_updateIndexTable($data);

        $this->_cleanBundleOptionStockData();

        return $this;
    }

    /**
     * Clean temporary bundle options stock data
     *
     * @return $this
     */
    protected function _cleanBundleOptionStockData()
    {
        $this->getConnection()->delete($this->_getBundleOptionTable());
        return $this;
    }

    private function getQueryProcessorComposite()
    {
        if (null === $this->queryProcessorComposite) {
            $this->queryProcessorComposite = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\QueryProcessorComposite');
        }
        return $this->queryProcessorComposite;
    }

}
