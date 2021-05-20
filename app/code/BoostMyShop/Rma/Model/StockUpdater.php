<?php

namespace BoostMyShop\Rma\Model;

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
    protected $_moduleManager;

    protected $objectManagerFactory;
    protected $objectManager;

    protected $_backendAuthSession;

    public function __construct(
        StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        ObjectManagerFactory $objectManagerFactory,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->_stockRegistryProvider = $stockRegistryProvider;
        $this->_stockConfiguration = $stockConfiguration;
        $this->_moduleManager = $moduleManager;
        $this->_backendAuthSession = $backendAuthSession;
        $this->objectManagerFactory = $objectManagerFactory;
    }

    public function productBackToStock($productId, $qty, $warehouseId, $description)
    {
        if ($this->_moduleManager->isEnabled('BoostMyShop_AdvancedStock'))
        {
            //create stock movement
            $userId = null;
            if ($this->_backendAuthSession->getUser())
                $userId = $this->_backendAuthSession->getUser()->getId();
            $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockMovement')->create(
                                $productId,
                                0,
                                $warehouseId,
                                $qty,
                                \BoostMyShop\AdvancedStock\Model\StockMovement\Category::productReturn,
                                $description,
                                $userId);
        }
        else
        {
            //simple stock increment
            $this->incrementStock($productId, $qty, $description);
        }
    }

    public function incrementStock($productId, $qty, $description = '')
    {
        $stockItem = $this->_stockRegistryProvider->getStockItem($productId, $this->_stockConfiguration->getDefaultScopeId());
        $newQty = $stockItem->getQty() + $qty;
        $this->updateMagentoStock($productId, $newQty, $description);
    }

    protected function updateMagentoStock($productId, $qty)
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
