<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class Shortcuts extends AbstractCheckout
{
    protected $_template = 'Checkout/Shortcuts.phtml';

    protected $_productCollectionFactory;
    protected $_priceHelper;
    protected $_posRegistry;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                \BoostMyShop\PointOfSales\Model\ProductInformation $productInformation,
                                \Magento\Framework\Pricing\Helper\Data $priceHelper,
                                \BoostMyShop\PointOfSales\Model\Config $config,
                                array $data = [])
    {
        parent::__construct($context, $registry, $productInformation, $config, $data);

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_priceHelper = $priceHelper;
        $this->_posRegistry = $posRegistry;
    }


    public function getShortcuts()
    {
        $collection = $this->_productCollectionFactory->create();

        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('pos_shortcut', 1);
        $collection->addFieldToFilter('type_id', ['in' => ['simple']]);

        $this->hydrate($collection, $this->_posRegistry->getCurrentWebsiteId());

        return $collection;
    }

    public function hydrate(&$collection, $websiteId = 0)
    {
        foreach($collection as &$product)
        {
            $url = $this->_productInformation->getImage($product->getId());
            $product->setImageUrl($url);

            $qty = $this->_productInformation->getQty($product, $websiteId);
            $product->setQty("".$qty);

            $sellable = $this->_productInformation->getSellable($product, $websiteId);
            $product->setSellable($sellable);
        }
    }

    public function currencyFormat($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }
}