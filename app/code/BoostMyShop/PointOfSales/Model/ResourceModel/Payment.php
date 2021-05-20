<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel;


class Payment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_pointofsales_order_payment', 'id');
    }

}
