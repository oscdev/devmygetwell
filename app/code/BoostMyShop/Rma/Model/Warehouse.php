<?php

namespace BoostMyShop\Rma\Model;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManagerFactory;

class Warehouse
{
    protected $_moduleManager;

    protected $objectManagerFactory;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        ObjectManagerFactory $objectManagerFactory
    ) {
        $this->_moduleManager = $moduleManager;
        $this->objectManagerFactory = $objectManagerFactory;
    }

    public function getWarehouses()
    {
        $warehouses = [];

        if ($this->_moduleManager->isEnabled('BoostMyShop_AdvancedStock'))
        {
            $collection = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\ResourceModel\Warehouse\Collection');
            foreach($collection as $item)
            {
                $warehouses[$item->getId()] = $item->getw_name();
            }
        }
        else
        {
            $warehouses['0'] = __('Default');
        }

        return $warehouses;
    }

    public function getWarehouseName($warehouseId)
    {
        foreach($this->getWarehouses() as $k => $v)
        {
            if ($k == $warehouseId)
                return $v;
        }
        return '[unknown]';
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