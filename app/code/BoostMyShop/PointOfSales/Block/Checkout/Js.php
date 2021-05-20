<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class Js extends \Magento\Backend\Block\Template
{
    protected $_template = 'Checkout/Js.phtml';

    protected $_coreRegistry = null;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getRefreshUrl()
    {
        return $this->getUrl('*/checkout/refresh');
    }

    public function getSearchProductUrl()
    {
        return $this->getUrl('*/checkout/productSearch');
    }

    public function getCustomerInformationUrl()
    {
        return $this->getUrl('*/checkout/customerInformation');
    }

    public function getCreateCustomerUrl()
    {
        return $this->getUrl('*/checkout/createCustomer');
    }

    public function getChangeStoreUrl()
    {
        return $this->getUrl('*/checkout/changeStore');
    }

    public function getChangeUserUrl()
    {
        return $this->getUrl('*/checkout/changeUser');
    }

    public function getProductIdFromBarcodeUrl()
    {
        return $this->getUrl('*/checkout/productIdFromBarcode');
    }

    public function getProductEmptyLayoutUrl()
    {
        return $this->getUrl('*/checkout/emptyProductLayout');
    }

    public function getSaveOpeningUrl()
    {
        return $this->getUrl('*/checkout/saveOpening');
    }

}