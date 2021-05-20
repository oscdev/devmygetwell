<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class AbstractCheckout extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry = null;
    protected $_productInformation = null;
    protected $_config = null;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\PointOfSales\Model\ProductInformation $productInformation,
                                \BoostMyShop\PointOfSales\Model\Config $config,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_productInformation = $productInformation;
        $this->_config = $config;
    }

}