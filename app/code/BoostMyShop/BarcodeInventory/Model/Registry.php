<?php

namespace BoostMyShop\BarcodeInventory\Model;

class Registry
{
    protected $_adminSession;
    protected $_storeManager;
    protected $_dateTime;
    protected $_userFactory;
    protected $_storeFactory;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_adminSession = $adminSession;
        $this->_storeManager = $storeManager;
        $this->_dateTime = $dateTime;
        $this->_userFactory = $userFactory;
        $this->_storeFactory = $storeFactory;
    }

    public function getCurrentWarehouseId()
    {
        return $this->getRegistry('barcode_inventory_warehouse_id');
    }

    public function setCurrentWarehouseId($id)
    {
        return $this->setRegistry('barcode_inventory_warehouse_id', $id);
    }

    protected function getRegistry($key)
    {
        $extra = $this->getUserExtra();
        $value = '';
        if (isset($extra['barcodeinventory'][$key]))
            $value = $extra['barcodeinventory'][$key];
        else
        {
            switch($key)
            {
                case 'barcode_inventory_warehouse_id':
                    return 1;
                    break;
                case 'current_store_id':
                    $value = $this->_storeManager->getStore()->getId();
                    break;
                case 'pos_stat_start_date':
                    $value = $this->_dateTime->gmtDate('Y-m-d');
                    break;
                case 'pos_stat_to_date':
                    $value = $this->_dateTime->gmtDate('Y-m-d');
                    break;
            }
        }

        return $value;
    }

    protected function setRegistry($key, $value)
    {
        $extra = $this->getUserExtra();
        $extra['barcodeinventory'][$key] = $value;
        $this->_adminSession->getUser()->setExtra(serialize($extra));
        $this->_adminSession->getUser()->saveExtra($extra);

        return $this;
    }

    protected function getUserExtra()
    {
        if (!$this->_adminSession->getUser())
            return [];
        $extra = $this->_adminSession->getUser()->getExtra();
        if (is_string($extra))
            $extra = unserialize($extra);
        if (!is_array($extra))
            $extra = [];
        if (!isset($extra['barcodeinventory']))
            $extra['barcodeinventory'] = [];
        return $extra;
    }

}