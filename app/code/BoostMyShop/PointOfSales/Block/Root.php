<?php
namespace BoostMyShop\PointOfSales\Block;

class Root extends \Magento\Backend\Block\Template
{
    protected $_template = 'Root.phtml';

    protected $_coreRegistry = null;
    protected $_logo;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Theme\Block\Html\Header\Logo $logo,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_logo = $logo;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('pointofsales/checkout/index');
    }

    public function getMagentoUrl()
    {
        return $this->getUrl('');
    }

    public function getLogoSrc()
    {
        return $this->getViewFileUrl('images/magento-icon.svg');
    }

}