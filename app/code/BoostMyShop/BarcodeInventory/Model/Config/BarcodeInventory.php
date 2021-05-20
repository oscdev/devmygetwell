<?php

namespace BoostMyShop\BarcodeInventory\Model\Config;

class BarcodeInventory
{
    protected $_scopeConfig;
    protected $_moduleManager;

    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_moduleManager = $moduleManager;
    }

    public function getSetting($path)
    {
        return $this->_scopeConfig->getValue('barcodeinventory/'.$path);
    }

    public function isAdvancedStockIsInstalled()
    {
        return $this->_moduleManager->isEnabled('BoostMyShop_AdvancedStock');
    }

}