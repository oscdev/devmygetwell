<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Order;

use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    public function joinManager()
    {
        $this->getSelect()->joinLeft(
            array('order_manager' => $this->getTable('bms_pointofsales_order_manager')),
            'main_table.entity_id = order_manager.order_id'
        );
        $this->getSelect()->joinLeft(
            array('user' => $this->getTable('admin_user')),
            'order_manager.user_id = user.user_id',
            ['username']
        );
        //die($this->getSelect());
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->AddFieldToFilter('customer_id', $customerId);
        return $this;
    }

    public function addGlobalFilter($condition)
    {
        $fields = [];
        $fields['increment_id'] = 'increment_id';
        $fields['customer_email'] = 'customer_email';
        $fields['customer_firstname'] = 'customer_firstname';
        $fields['customer_lastname'] = 'customer_lastname';

        $conditions = [];
        $conditions['increment_id'] = ['like' => '%'.$condition.'%'];
        $conditions['customer_email'] = ['like' => '%'.$condition.'%'];
        $conditions['customer_firstname'] = ['like' => '%'.$condition.'%'];
        $conditions['customer_lastname'] = ['like' => '%'.$condition.'%'];

        $this->addFieldToFilter($fields, $conditions);

        return $this;
    }

}