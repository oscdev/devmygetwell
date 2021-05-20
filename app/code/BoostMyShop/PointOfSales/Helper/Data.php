<?php

namespace BoostMyShop\PointOfSales\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

   // const CUSTOMER_COUNTRY = 'pointofsales/guest/country_id';


    protected $scopeConfig;
    protected $storeManager;
    protected $_posRegistry = null;

    public function __construct
    (
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_posRegistry = $posRegistry;
        $this->storeManager = $storeManager;


    }
    /*** Get Recipient Name* @return object*/


    public function getCurrentStoreId()
    {
        return $this->_posRegistry->getCurrentStoreId();
    }

    public function getGuestCustCountryId()
    {
        $currentStoreId = $this->getCurrentStoreId();
        $currentCode = $this->_posRegistry->getCurrentStoreCode();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue("pointofsales/guest/country_id", $storeScope, $currentCode);
    }

    public function getGuestCustRegionId()
    {
        $currentStoreId = $this->getCurrentStoreId();
        $currentCode = $this->_posRegistry->getCurrentStoreCode();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue("pointofsales/guest/region_id", $storeScope, $currentCode);
    }
}
