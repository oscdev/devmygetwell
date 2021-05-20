<?php

namespace BoostMyShop\PointOfSales\Model\Source;

class MultiplePaymentMethods implements \Magento\Framework\Option\ArrayInterface
{

    protected $_config;


    public function __construct(\BoostMyShop\PointOfSales\Model\Config $config)
    {
        $this->_config = $config;
    }

    public function toOptionArray()
    {
        $options = array();

        $options[] = array('value' => '', 'label' => '');
        foreach($this->_config->getMultiplePaymentMethods() as $method)
        {
            $options[] = array('value' => $method, 'label' => ucfirst($method));
        }

        return $options;
    }

}
