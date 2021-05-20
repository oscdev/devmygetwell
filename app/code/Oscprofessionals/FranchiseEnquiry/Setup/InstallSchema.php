<?php
/**
 * Franchise Enquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class InstallSchema implements InstallSchemaInterface{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();
		$tableName = $installer->getTable('franchise_enquiry');
		if ($installer->getConnection()->isTableExists($tableName) != true) {
			$table = $installer->getConnection()
				->newTable($tableName)
				->addColumn('id', Table::TYPE_INTEGER, null, [
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true
					], 'ID')
				->addColumn('name', Table::TYPE_TEXT, null, [
						'length' => 255,
						'nullable' => true
					], 'Name')
				->addColumn('mobile', Table::TYPE_TEXT, null, [
						'length' => 255,
						'nullable' => true
					], 'Telephone')
                ->addColumn('email', Table::TYPE_TEXT, null, [
                    'length' => 255,
                    'nullable' => true
                ], 'Email')
                ->addColumn('region', Table::TYPE_INTEGER, null, [
                    'length' => 255,
                    'nullable' => true
                ], 'Region')
                ->addColumn('city', Table::TYPE_TEXT, null, [
                    'length' => 255,
                    'nullable' => true
                ], 'City')
				->addColumn('comment', Table::TYPE_TEXT, null, [
						'nullable' => true
					], 'Comment')
                ->addColumn('captcha', Table::TYPE_TEXT, null, [
                    'length' => 255,
                    'nullable' => true
                ], 'Captcha');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}