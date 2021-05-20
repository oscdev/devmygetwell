<?php

namespace BoostMyShop\BarcodeInventory\Model;

use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManagerFactory;

class StockUpdater
{
    protected $_stockRegistryProvider;
    protected $_stockConfiguration;
    protected $_config;
    protected $_backendAuthSession;

    protected $_objectManagerFactory;
    protected $_objectManager;

    public function __construct(
        StockRegistryProviderInterface $stockRegistryProvider,
        \BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory $config,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        ObjectManagerFactory $objectManagerFactory,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->_stockRegistryProvider = $stockRegistryProvider;
        $this->_stockConfiguration = $stockConfiguration;
        $this->_config = $config;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_objectManagerFactory = $objectManagerFactory;
    }

    public function updateStock($productId, $warehouseId, $qty)
    {
        if ($this->_config->isAdvancedStockIsInstalled())
        {
            $userId = null;
            if ($this->_backendAuthSession->getUser())
                $userId = $this->_backendAuthSession->getUser()->getId();

            $stockItem = $this->getObjectManager()->create('\BoostMyShop\AdvancedStock\Model\Warehouse\Item')->loadByProductWarehouse($productId, $warehouseId);
            $originalQty = $stockItem->getwi_physical_quantity();

            $this->getObjectManager()->create('\BoostMyShop\AdvancedStock\Model\StockMovementFactory')->create()->updateProductQuantity($productId,
                                                $warehouseId,
                                                $originalQty,
                                                $qty,
                                                'From barcode inventory',
                                                $userId);
        }
        else
        {
            $stockItem = $this->_stockRegistryProvider->getStockItem($productId, $this->_stockConfiguration->getDefaultScopeId());

            $stockItem->setQty($qty);
            if ($this->_stockConfiguration->getCanBackInStock($stockItem->getStoreId()) && ($stockItem->getQty() > $stockItem->getMinQty()))
            {
                $stockItem->setIsInStock(true);
                $stockItem->setStockStatusChangedAutomaticallyFlag(true);
            }

            $stockItem->save();
        }

    }

    protected function getObjectManager()
    {
        if (null == $this->_objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $this->_objectManager = $this->_objectManagerFactory->create($_SERVER);
            $appState = $this->_objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->_objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->_objectManager->configure($configLoader->load($area));
        }
        return $this->_objectManager;
    }

}
