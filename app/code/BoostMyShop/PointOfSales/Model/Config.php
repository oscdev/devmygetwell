<?php

namespace BoostMyShop\PointOfSales\Model;

class Config
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_posRegistry = null;


    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry

    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_posRegistry = $posRegistry;

    }
    /*BOF code to get store ID*/
    public function getCurrentStoreId()
    {
        return $this->_posRegistry->getCurrentStoreId();
    }
    /*BOF code to get store ID*/


//    public function getSetting($path, $storeId = 0)
//    {
//        return $this->_scopeConfig->getValue('pointofsales/'.$path, 'store', $storeId);
//    }

    public function getSetting($path, $storeId = 0)
    {
        $storeId = $this->getCurrentStoreId();
        return $this->_scopeConfig->getValue('pointofsales/'.$path, 'store', $storeId);
    }

//    public function getGlobalSetting($path, $storeId = 0)
//    {
//        return $this->_scopeConfig->getValue($path, 'store', $storeId);
//    }

    public function getGlobalSetting($path, $storeId = 0)
    {
        $storeId = $this->getCurrentStoreId();
        return $this->_scopeConfig->getValue($path, 'store', $storeId);
    }

    public function getBarcodeAttribute()
    {
        return $this->_scopeConfig->getValue('pointofsales/general/barcode_attribute');
    }

    public function getDefaultShippingMethod()
    {
        return $this->_scopeConfig->getValue('pointofsales/checkout/shipping_method');
    }

    public function getDefaultPaymentMethod()
    {
        $value = $this->_scopeConfig->getValue('pointofsales/checkout/payment_method');
        if (!$value)
            $value = "multiple_payment";
        return $value;
    }

    public function getMultiplePaymentMethods()
    {
        return explode(',', $this->_scopeConfig->getValue('payment/multiple_payment/methods'));
    }

    public function getChangeMethod()
    {
        return $this->_scopeConfig->getValue('payment/multiple_payment/change_method');
    }

    public function getGuestField($field)
    {
        return $this->getSetting('guest/'.$field);
    }

    public function getReceiptWidth()
    {
        return $this->getSetting('receipt/width');
    }

    public function getReceiptHeaderText()
    {
        return $this->getSetting('receipt/header');
    }

    public function getReceiptFooterText()
    {
        return $this->getSetting('receipt/footer');
    }

    public function isOpeningEnabled()
    {
        return $this->getSetting('cash_drawer/enable_opening');
    }

    public function getCashDrawerMethod()
    {
        return $this->getSetting('cash_drawer/method');
    }
}