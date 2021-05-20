<?php

namespace BoostMyShop\PointOfSales\Model;


class Opening extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('BoostMyShop\PointOfSales\Model\ResourceModel\Opening');
    }

    public function loadByStoreDate($storeId, $date)
    {
        $this->_getResource()->loadByStoreDate($this, $storeId, $date);
        $this->setOrigData();
        if (!$this->getId())
        {
            $this->setpo_store_id($storeId);
            $this->setpo_date($date);
            $this->setpo_amount(0);
        }
        else
            $this->_hasDataChanges = false;
        return $this;
    }

}