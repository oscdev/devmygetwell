<?php
namespace Born\Pos\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetup
     */
    private $salesSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

    $installer->startSetup();

    $eavTable = $installer->getTable('quote_item');

    $columns = [
        'pos_alise_name' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'pos_alise_name',
        ],

    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($eavTable, $name, $definition);
    }
	
	 $eavTable = $installer->getTable('sales_order_item');

    $columns = [
        'pos_alise_name' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'pos_alise_name',
        ],

    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($eavTable, $name, $definition);
    }

    $installer->endSetup();

       

       
    }
}