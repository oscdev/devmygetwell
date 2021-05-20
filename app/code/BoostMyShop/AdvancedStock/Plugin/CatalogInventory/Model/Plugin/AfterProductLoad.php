<?php

namespace BoostMyShop\AdvancedStock\Plugin\CatalogInventory\Model\Plugin;

class AfterProductLoad extends \Magento\CatalogInventory\Model\Plugin\AfterProductLoad
{
    protected $_storeFactory;

    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
    ) {
        $this->_storeFactory = $storeFactory;
        parent::__construct($stockRegistry, $productExtensionFactory);
    }

    //Original magento class doesnt consider the product store to pass the website to the getStockItem method
    public function afterLoad(\Magento\Catalog\Model\Product $product)
    {
        $productExtension = $product->getExtensionAttributes();
        if ($productExtension === null) {
            $productExtension = $this->productExtensionFactory->create();
        }

        $websiteId = null;
        if ($product->getStoreId())
        {
            $store = $this->_storeFactory->create()->load($product->getStoreId());
            $websiteId = $store->getwebsite_id();
        }

        // stockItem := \Magento\CatalogInventory\Api\Data\StockItemInterface
        $productExtension->setStockItem($this->stockRegistry->getStockItem($product->getId(), $websiteId));
        $product->setExtensionAttributes($productExtension);
        return $product;
    }

}
