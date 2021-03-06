<?php

namespace BoostMyShop\PointOfSales\Model;

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

    public function getCurrentStoreId()
    {
        return $this->getRegistry('current_store_id');
    }

    public function getCurrentStore()
    {
        return $this->_storeFactory->create()->load($this->getCurrentStoreId());
    }

    public function getCurrentWebsiteId()
    {
        return $this->getCurrentStore()->getWebsiteId();
    }
    /*****************/
    public function getCurrentStoreCode()
    {
        return $this->getCurrentStore()->getCode();
    }
    /*****************/

    public function getCurrentUserId()
    {
        return $this->getRegistry('current_user_id');
    }

    public function getCurrentUser()
    {
        return $this->_userFactory->create()->load($this->getCurrentUserId());
    }

    public function changeCurrentUserId($userId)
    {
        $this->setRegistry('current_user_id', $userId);
    }

    public function changeCurrentStoreId($storeId)
    {
        $this->setRegistry('current_store_id', $storeId);
    }

    public function getStatFromDate()
    {
        return $this->getRegistry('pos_stat_start_date').' 00:00:00';
    }

    public function getStatToDate()
    {
        return $this->getRegistry('pos_stat_to_date').' 23:59:59';
    }

    public function changeStatFromDate($value)
    {
        $this->setRegistry('pos_stat_start_date', $value);
    }

    public function changeStatToDate($value)
    {
        $this->setRegistry('pos_stat_to_date', $value);
    }

    public function getRegistry($key)
    {
        $extra = $this->getUserExtra();
        $value = '';
        if (isset($extra['pointofsales'][$key]))
            $value = $extra['pointofsales'][$key];
        else
        {
            switch($key)
            {
                case 'current_user_id':
                    if ($this->_adminSession && $this->_adminSession->getUser())
                        $value = $this->_adminSession->getUser()->getId();
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

    public function setRegistry($key, $value)
    {
        $extra = $this->getUserExtra();
        $extra['pointofsales'][$key] = $value;
        $this->_adminSession->getUser()->setExtra(serialize($extra));
        $this->_adminSession->getUser()->saveExtra($extra);

        return $this;
    }

    public function getUserExtra()
    {
        if (!$this->_adminSession->getUser())
            return [];
        $extra = $this->_adminSession->getUser()->getExtra();
        if (is_string($extra))
            $extra = unserialize($extra);
        if (!is_array($extra))
            $extra = [];
        if (!isset($extra['pointofsales']))
            $extra['pointofsales'] = [];
        return $extra;
    }

}