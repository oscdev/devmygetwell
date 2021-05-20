<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Stat;


class BestSellers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('', '');
    }


    public function getItems($storeId, $from, $to)
    {


        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order_item'), array(
                    'sku',
                    'name',
                    new \Zend_Db_Expr('SUM(qty_ordered) as qty'),
                    new \Zend_Db_Expr('SUM(base_row_total) as total')
                )
            )

            ->where('created_at >= "'.$from.'"')
            ->where('store_id = '.$storeId)
            ->where('created_at <= "'.$to.'"')
            ->group('sku')
            ->order('SUM(base_row_total) desc')
            ->limit(10);
        ;

        $result = $this->getConnection()->fetchAll($select);

        return $result;
    }


}
