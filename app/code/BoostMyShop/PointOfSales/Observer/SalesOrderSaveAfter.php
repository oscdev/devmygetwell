<?php

namespace BoostMyShop\PointOfSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class SalesOrderSaveAfter implements ObserverInterface
{
    protected $_payment;
    protected static $_processedOrders = [];

    public function __construct(
        \BoostMyShop\PointOfSales\Model\Payment $payment
    ) {
        $this->_payment = $payment;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getOrigData('entity_id'))
            return $this;
        if (in_array($order->getId(), self::$_processedOrders))
            return $this;

        $this->_payment->createForOrder($order);

        self::$_processedOrders[] = $order->getId();

        return $this;
    }

}
