<?php

namespace BoostMyShop\PointOfSales\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class ProductInformation
{
    protected $_productRepository;
    protected $_objectManager;
    protected $_stockState;
    protected $_stockRegistry;
    protected $_collectionFactory;

    public function __construct(ProductRepositoryInterface $productRepository,
                                ObjectManagerInterface $om,
                                \Magento\CatalogInventory\Api\StockStateInterface $stockState,
                                \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                                \Magento\Catalog\Model\ResourceModel\Product\Collection $collectionFactory
                                ) {
        $this->_objectManager = $om;
        $this->_productRepository = $productRepository;
        $this->_stockState = $stockState;
        $this->_stockRegistry = $stockRegistry;
        $this->_collectionFactory = $collectionFactory;

    }


    public function getImage($product)
    {
        if (is_numeric($product))
            $product = $this->_productRepository->getById($product);

        $helper = $this->_objectManager->get('\Magento\Catalog\Helper\Product');
        return $helper->getImageUrl($product);
    }

    public function getQty($product, $websiteId = 0)
    {
        return $this->_stockState->getStockQty($product->getId(), $websiteId);
    }

    public function getSellable($product, $websiteId = 0)
    {
        return $this->_stockRegistry->getProductStockStatus($product->getId(), $websiteId);
    }

}
