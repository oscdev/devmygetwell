<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Order;


class Manager extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_pointofsales_order_manager', 'id');
    }

}
