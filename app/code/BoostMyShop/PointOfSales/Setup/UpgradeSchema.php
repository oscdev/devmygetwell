<?php

namespace BoostMyShop\PointOfSales\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        //0.0.9
        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('bms_pointofsales_order_payment'))
                ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Supplier product id')
                ->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Product id')
                ->addColumn('method', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 100, [], 'Payment method')
                ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Date')
                ->addColumn('amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,2', [], 'Amount')
                ->addColumn('comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Comments')
                ->addIndex(
                    $setup->getIdxName('bms_pointofsales_order_payment', ['order_id']),
                    ['order_id']
                )
                ->setComment('POS multiple payment');
            $setup->getConnection()->createTable($table);
        }

        //0.0.10
        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('bms_pointofsales_order_payment'),
                'user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'User id',
                    'default' => 0
                ]
            );
        }

        //0.0.11
        if (version_compare($context->getVersion(), '0.0.11', '<')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('bms_pointofsales_opening'))
                ->addColumn('po_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Primary key')
                ->addColumn('po_date', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, null, [], 'Date')
                ->addColumn('po_amount', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,2', [], 'Amount')
                ->addColumn('po_store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Store')
                ->setComment('POS Opening');
            $setup->getConnection()->createTable($table);
        }

        //0.0.12
        if (version_compare($context->getVersion(), '0.0.12', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'pos_ship_later',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Pos ship later',
                    'default' => 0
                ]
            );
        }

        $setup->endSetup();
    }

}
