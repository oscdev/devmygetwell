<?php
namespace BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab;

class Current extends \Magento\Backend\Block\Template
{
    protected $_template = 'Checkout/Customer/Tab/Current.phtml';


    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
    }

    public function getInformationForm()
    {
        return $this->getLayout()->createBlock('BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Current\Information')->toHtml();
    }

    public function getAddressForm()
    {
        return $this->getLayout()->createBlock('BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Current\Address')->toHtml();
    }
}