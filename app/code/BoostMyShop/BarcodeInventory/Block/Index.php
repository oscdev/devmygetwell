<?php
namespace BoostMyShop\BarcodeInventory\Block;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManagerFactory;

class Index extends \Magento\Backend\Block\Template
{
    protected $_template = 'index.phtml';

    protected $_config;
    protected $_barcodeInventoryRegistry;

    protected $objectManagerFactory;
    protected $objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = [],
        ObjectManagerFactory $objectManagerFactory,
        \BoostMyShop\BarcodeInventory\Model\Registry $barcodeInventoryRegistry,
        \BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory $config
    )
    {
        $this->_config = $config;
        $this->objectManagerFactory = $objectManagerFactory;
        $this->_barcodeInventoryRegistry = $barcodeInventoryRegistry;

        parent::__construct($context, $data);
    }

    public function getChangeWarehouseUrl()
    {
        return $this->getUrl('*/*/ChangeWarehouse', array('warehouse_id' => '[warehouse_id]'));
    }

    public function isMultipleWarehouse()
    {
        return count($this->getWarehouses()) > 1;
    }

    public function getWarehouses()
    {
        if (!$this->_config->isAdvancedStockIsInstalled())
            return [['value' => 1, 'label' => 'Default']];
        else
        {
            return $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\Config\Source\Warehouse')->toOptionArray();
        }
    }

    public function getCurrentWarehouseId()
    {
        return $this->_barcodeInventoryRegistry->getCurrentWarehouseId();
    }

    public function getModes()
    {
        $obj = new \BoostMyShop\BarcodeInventory\Model\Config\Source\Modes();
        return $obj->toOptionArray(false);
    }

    public function isImmediateMode()
    {
        return false;
    }

    public function getProductInformationUrl()
    {
        return $this->getUrl('*/*/ProductInformation', ['barcode' => '[barcode]']);
    }

    public function CommitProductStockUrl()
    {

    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/Save');
    }

    public function getDefaultMode()
    {
        $value = $this->_config->getSetting('general/default_mode');

        return $value;
    }

    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }

}