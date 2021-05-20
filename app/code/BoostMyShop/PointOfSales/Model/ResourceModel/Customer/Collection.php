<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Customer;

use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{

    public function addGlobalFilter($condition)
    {
        $this->addAttributeToFilter(
            [
                ['attribute' => 'firstname',  'condition' => ['like' => '%'.$condition.'%']],
                ['attribute' => 'lastname',  'condition' => ['like' => '%'.$condition.'%']],
                ['attribute' => 'email',  'condition' => ['like' => '%'.$condition.'%']]
            ]
        );
        return $this;
    }

}