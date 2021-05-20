<?php

namespace BoostMyShop\PointOfSales\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $setup->getConnection()
            ->newTable($setup->getTable('bms_pointofsales_order_manager'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id')
                ->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Order')
            ->addColumn('user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => true, ], 'Manager')
            ->addIndex(
                $setup->getIdxName('bms_pointofsales_order_manager', ['order_id']),
                ['order_id']
            )
            ->addIndex(
                $setup->getIdxName('bms_pointofsales_order_manager', ['order_id']),
                ['order_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName('bms_pointofsales_order_manager', ['user_id']),
                ['user_id']
            )
            ->setComment('POS order manager');
        $setup->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
