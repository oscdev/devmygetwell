<?php

namespace BoostMyShop\BarcodeInventory\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class ProductInformation
{
    protected $_productRepository;
    protected $_barcodeInventoryRegistry;
    protected $_objectManager;
    protected $_stockState;
    protected $_config;
    protected $_logger;
    protected $_collectionFactory;

    public function __construct(ProductRepositoryInterface $productRepository,
                                ObjectManagerInterface $om,
                                \Magento\CatalogInventory\Api\StockStateInterface $stockState,
                                \Magento\Catalog\Model\ResourceModel\Product\Collection $collectionFactory,
                                \BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory $config,
                                \BoostMyShop\BarcodeInventory\Helper\Logger $logger,
                                \BoostMyShop\BarcodeInventory\Model\Registry $barcodeInventoryRegistry
                                ) {
        $this->_objectManager = $om;
        $this->_productRepository = $productRepository;
        $this->_barcodeInventoryRegistry = $barcodeInventoryRegistry;
        $this->_stockState = $stockState;
        $this->_collectionFactory = $collectionFactory;
        $this->_config = $config;
        $this->_logger = $logger;

    }


    public function getJsonDataForBarcode($barcode)
    {
        $productId = $this->getIdFromBarcode($barcode);
        if (!$productId)
            throw new \Exception('No product found with barcode '.$barcode);

        $product = $this->_productRepository->getById($productId);

        $data['id'] = $product->getId();
        $data['name'] = $product->getName();
        $data['sku'] = $product->getSku();
        $data['image_url'] = $this->getImage($product);
        $data['barcode'] = $barcode;

        $warehouseId = $this->_barcodeInventoryRegistry->getCurrentWarehouseId();
        $data['qty'] = $this->getStock($product->getId(), $warehouseId);

        return $data;
    }

    protected function getIdFromBarcode($barcode)
    {
        $collection = $this->_collectionFactory;
        $collection->addAttributeToFilter($this->getBarcodeAttribute(), $barcode);

        foreach($collection as $item)
            return $item->getId();

        return false;
    }

    protected function getImage($product)
    {
        $helper = $this->_objectManager->get('\Magento\Catalog\Helper\Product');
        return $helper->getImageUrl($product);
    }

    protected function getBarcodeAttribute()
    {
        $config = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory');
        return $config->getSetting('general/barcode_attribute');
    }

    /**
     * Return stock level
     *
     * @param $productId
     * @return mixed
     */
    protected function getStock($productId, $warehouseId)
    {
        $value = 0;
        if (!$this->_config->isAdvancedStockIsInstalled())
            $value = $this->_stockState->getStockQty($productId);
        else
        {
            $stockItem = $this->_objectManager->create('\BoostMyShop\AdvancedStock\Model\Warehouse\Item')->loadByProductWarehouse($productId, $warehouseId);
            $currentQty = $stockItem->getwi_physical_quantity();
            if($currentQty && $currentQty > 0)
                $value = $currentQty;
        }
        $this->_logger->log('getStock for product #'.$productId.' and warehouse #'.$warehouseId.' is '.$value.' (advanced stock is '.($this->_config->isAdvancedStockIsInstalled() ? '' : 'NOT').' installed)');
        return $value;
    }

}
