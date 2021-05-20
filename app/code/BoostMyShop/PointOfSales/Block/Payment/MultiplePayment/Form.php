<?php

namespace BoostMyShop\PointOfSales\Block\Payment\MultiplePayment;

class Form extends \Magento\Payment\Block\Form
{
    protected $_template = 'BoostMyShop_PointOfSales::Payment/MultiplePayment/Form.phtml';

    protected $_config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BoostMyShop\PointOfSales\Model\Config $config,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_config = $config;
    }


    public function getMethods()
    {
        return $this->_config->getMultiplePaymentMethods();
    }

    public function convertMethodToCode($method)
    {
        $method = strtolower($method);
        $method = str_replace(' ', '_', $method);
        return $method;
    }

    public function getInfoData($field)
    {
        return 0;
    }

}
