<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Payment;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    protected function _construct()
    {
        $this->_init('BoostMyShop\PointOfSales\Model\Payment', 'BoostMyShop\PointOfSales\Model\ResourceModel\Payment');
    }

    public function joinOrder()
    {
        return $this;
    }

    public function addGlobalFilter($condition)
    {
        $fields = [];
        $fields['comments'] = 'comments';
        $fields['method'] = 'method';

        $conditions = [];
        $conditions['comments'] = ['like' => '%'.$condition.'%'];
        $conditions['method'] = ['like' => '%'.$condition.'%'];

        $this->addFieldToFilter($fields, $conditions);

        return $this;
    }

    public function addOrderFilter($orderId)
    {
        $this->getSelect()->where("order_id = ".$orderId);

        return $this;

    }

}
