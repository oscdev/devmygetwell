<?php

namespace BoostMyShop\PointOfSales\Model\Payment;


class MultiplePayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_MULTIPLE_CODE = 'multiple_payment';

    protected $_code = self::PAYMENT_METHOD_MULTIPLE_CODE;

    protected $_formBlockType = 'BoostMyShop\PointOfSales\Block\Payment\MultiplePayment\Form';

    protected $_infoBlockType = 'BoostMyShop\PointOfSales\Block\Payment\MultiplePayment\Info';

    protected $_isOffline = true;

}
