<?php

namespace BoostMyShop\PointOfSales\Model\Order;


class Shipment
{

    protected $_shipmentFactory;
    protected $_shipmentSender;
    protected $_trackFactory;
    protected $_logger;

    public function __construct(
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \BoostMyShop\PointOfSales\Helper\Logger $logger,
        \Magento\Framework\DB\Transaction $transaction
    ) {
        $this->_shipmentFactory = $shipmentFactory;
        $this->_transaction = $transaction;
        $this->_shipmentSender = $shipmentSender;
        $this->_logger = $logger;
    }

    public function createShipment($order, $productIds = [])
    {
        $this->_logger->log("Create shipment for order #".$order->getIncrementId(), \BoostMyShop\PointOfSales\Helper\Logger::kLogGeneral);

        $shipmentItems = $this->prepareShipmentItems($order, $productIds);
        $this->_logger->log("Add ".count($shipmentItems)." skus in the shipment", \BoostMyShop\PointOfSales\Helper\Logger::kLogGeneral);

        $shipment = $this->_shipmentFactory->create($order, $shipmentItems, []);
        $shipment->register();

        $transactionSave = $this->_transaction->addObject($order);
        $transactionSave->addObject($shipment);
        $transactionSave->save();

        return $shipment;
    }

    /**
     *
     * @param $inProgress
     * @return array
     */
    protected function prepareShipmentItems($order, $productIds)
    {
        $items = [];

        foreach($order->getAllItems() as $item)
        {
            if ((count($productIds) == 0) || (in_array($item->getProductId(), $productIds)))
            {
                $items[$item->getId()] = $item->getqty_ordered();

                if ($item->getParentItemId())
                    $items[$item->getParentItemId()] = $item->getqty_ordered();
            }
        }

        return $items;
    }

}