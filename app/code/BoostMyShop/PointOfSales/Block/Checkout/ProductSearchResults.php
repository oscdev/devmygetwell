<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class ProductSearchResults extends AbstractCheckout
{
    protected $_template = 'Checkout/ProductSearchResults.phtml';

    protected $_productSearch;
    protected $_priceHelper;
    protected $_posRegistry;

    protected $_results;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\PointOfSales\Model\ProductInformation $productInformation,
                                \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
                                \BoostMyShop\PointOfSales\Model\Config $config,
                                \BoostMyShop\PointOfSales\Model\ResourceModel\ProductSearchFactory $productSearch,
                                \Magento\Framework\Pricing\Helper\Data $priceHelper,
                                array $data = [])
    {
        parent::__construct($context, $registry, $productInformation, $config, $data);

        $this->_productSearch = $productSearch;
        $this->_priceHelper = $priceHelper;
        $this->_posRegistry = $posRegistry;
    }

    public function getResults()
    {
        if (!$this->_results) {
            $this->_results = $this->_productSearch->create()->search($this->getQueryString());
            $this->hydrate($this->_results, $this->_posRegistry->getCurrentWebsiteId());
        }
        return $this->_results;
    }

    public function getQueryString()
    {
        return $this->_coreRegistry->registry('pos_currency_search');
    }

    public function getProductImage($product)
    {
        return $this->_productInformation->getImage($product);
    }

    public function formatPrice($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }

    public function hydrate(&$collection, $websiteId)
    {
        foreach($collection as &$product)
        {
            $qty = $this->_productInformation->getQty($product, $websiteId);
            $product->setQty("".$qty);

            $sellable = $this->_productInformation->getSellable($product, $websiteId);
            $product->setSellable($sellable);
        }
    }

}