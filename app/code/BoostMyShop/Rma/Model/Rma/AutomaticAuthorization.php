<?php

namespace BoostMyShop\Rma\Model\Rma;

use Magento\Customer\Api\Data\GroupInterface;

class AutomaticAuthorization 
{
	protected $_config;
	protected $_storeManager;

	public function __construct(\BoostMyShop\Rma\Model\Config $config,
		\Magento\Store\Model\StoreManagerInterface $storeManager){
        $this->_config = $config;
        $this->_storeManager = $storeManager; 
    }

	public function isAllowed($order){
		$customerGroupids 	= $this->_config->getAutomaticreturnCustomerGroup($this->getStoreId());
		$countries 			= $this->_config->getAutomaticreturnCountries($this->getStoreId());
		$maxOrderAmount     = $this->_config->getAutomaticreturnMaximumorder($this->getStoreId());
		$enabled 			= $this->_config->getAutomaticreturnEnabled($this->getStoreId());
		$address 			= $order->getShippingAddress();
		$country 			= $address->getCountryId();
		$customerGroupCheck = false;
		if(in_array(GroupInterface::CUST_GROUP_ALL,$customerGroupids)){
			$customerGroupCheck = true;
		}elseif(in_array($order->getCustomerGroupId(),$customerGroupids)){
			$customerGroupCheck = true;
		}else{
			$customerGroupCheck = false;
		}

		if(!$enabled){
			return false;
		}elseif(!$customerGroupCheck){
			return false;
		}elseif(($maxOrderAmount > 0) && ($order->getGrandTotal()>$maxOrderAmount)){
			return false;
		}elseif(!in_array($country, $countries)){
			return false;
		}else{
			return true;
		}
	}

	/**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }


}