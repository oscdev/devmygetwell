<?php

namespace BoostMyShop\OrderPreparation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;


class SalesOrderShipmentSaveAfter implements ObserverInterface
{
    protected $_inProgressFactory;

    public function __construct(
        \BoostMyShop\OrderPreparation\Model\InProgressFactory $inProgressFactory
    ) {
        $this->_inProgressFactory = $inProgressFactory;

    }

    public function execute(EventObserver $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $orderId = $shipment->getOrderId();

        $inProgress = $this->_inProgressFactory->create()->load($orderId, 'ip_order_id');
        if ($inProgress->getId()) {
            $inProgress->setip_shipment_id($shipment->getId())->save();
            $inProgress->changeStatus(\BoostMyShop\OrderPreparation\Model\InProgress::STATUS_SHIPPED);
        }

        return $this;
    }

}
