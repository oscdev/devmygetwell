<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Opening;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    protected function _construct()
    {
        $this->_init('BoostMyShop\PointOfSales\Model\Opening', 'BoostMyShop\PointOfSales\Model\ResourceModel\Opening');
    }



}
