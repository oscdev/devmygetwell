<?php

namespace BoostMyShop\AdvancedStock\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class SalesOrderShipmentSaveAfter implements ObserverInterface
{

    protected $_stockMovementFactory;
    protected $_backendAuthSession;
    protected $_extendedSalesFlatOrderItemFactory;
    protected $_logger;
    protected $_orderItemFactory;
    protected $_stockRegistry;

    /**
     * @param StockIndexInterface $stockIndex
     * @param StockConfigurationInterface $stockConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param StockItemRepositoryInterface $stockItemRepository
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\AdvancedStock\Model\StockMovementFactory $stockMovementFactory,
        \BoostMyShop\AdvancedStock\Model\ExtendedSalesFlatOrderItemFactory $extendedSalesFlatOrderItemFactory,
        \BoostMyShop\AdvancedStock\Helper\Logger $logger,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
    ) {
        $this->_stockMovementFactory = $stockMovementFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_extendedSalesFlatOrderItemFactory = $extendedSalesFlatOrderItemFactory;
        $this->_logger = $logger;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_stockRegistry = $stockRegistry;
    }

    /**
     * Create stock movements for shipment
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        if ($shipment->getOrigData('entity_id'))
            return $this;
        if ($shipment->getStockMovementCreated())
            return $this;
        if (!$shipment)
            return $this;

        $this->_logger->log('Process shipment for order #'.$shipment->getOrderId(), \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);

        $userId = null;
        if ($this->_backendAuthSession->getUser())
            $userId = $this->_backendAuthSession->getUser()->getId();

        foreach($shipment->getAllItems() as $shipmentItem)
        {
            $productId = $shipmentItem->getProductId();
            $orderItem = $shipmentItem->getOrderItem();

            $qty = $shipmentItem->getQty();
            $this->_logger->log('Process item with product #'.$productId." for qty ".$qty, \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);
            $extendedOrderItem = $this->_extendedSalesFlatOrderItemFactory->create()->loadByItemId($orderItem->getId());
            if (!$extendedOrderItem->getesfoi_warehouse_id())
                $extendedOrderItem->setesfoi_warehouse_id(1);

            $this->processChild($shipmentItem, $extendedOrderItem->getesfoi_warehouse_id(), $qty, $shipment, $userId);

            if (!$this->createStockMovementForProductType($orderItem->getProductType())) {
                $this->_logger->log('Dont create stock movement for product type '.$orderItem->getProductType(), \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);
                continue;
            }
            if (!$this->createStockMovementForOrderItem($orderItem)) {
                $this->_logger->log('Dont create stock movement for this order item (product #'.$productId.')', \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);
                continue;
            }

            $this->createStockMovement($productId, $extendedOrderItem->getesfoi_warehouse_id(), $qty, $shipment, $userId);

        }

        $shipment->setStockMovementCreated(true);

        return $this;
    }

    public function createStockMovement($productId, $warehouseId, $qty, $shipment, $userId)
    {
        $this->_logger->log('Create stock movement for shipment item (qty='.$qty.',product='.$productId.',warehouse='.$warehouseId.')', \BoostMyShop\AdvancedStock\Helper\Logger::kLogInventory);

        $this->_stockMovementFactory->create()->create($productId,
            $warehouseId,
            0,
            $qty,
            \BoostMyShop\AdvancedStock\Model\StockMovement\Category::shipment,
            'Shipment #'.$shipment->getincrement_id(),
            $userId
        );

    }

    public function processChild($shipmentItem, $warehouseId, $qty, $shipment, $userId)
    {
        $parentItem = $shipmentItem->getOrderItem();
        switch($parentItem->getProductType())
        {
            case 'configurable':
                $this->_logger->log('Process child (configurable) for product #'.$parentItem->getProductId(), \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);
                $childItem = $this->_orderItemFactory->create()->load($parentItem->getId(), 'parent_item_id');
                $extendedOrderItem = $this->_extendedSalesFlatOrderItemFactory->create()->loadByItemId($childItem->getId());
                if (!$extendedOrderItem->getesfoi_warehouse_id())
                    $extendedOrderItem->setesfoi_warehouse_id(1);
                $warehouseId = $extendedOrderItem->getesfoi_warehouse_id();
                $this->createStockMovement($childItem->getproduct_id(), $warehouseId, $qty, $shipment, $userId);
                break;
            default:
                $this->_logger->log('No child to process for product #'.$parentItem->getProductId(), \BoostMyShop\AdvancedStock\Helper\Logger::kLogShipment);
                break;
        }
    }

    public function createStockMovementForProductType($productType)
    {
        switch($productType)
        {
            case 'configurable':
            case 'bundle':
            case 'configurator':
            case 'grouped':
                return false;
        }

        return true;
    }

    public function createStockMovementForOrderItem($orderItem)
    {
        //check if product manages stock
        $stockitem = $this->_stockRegistry->getStockItem($orderItem->getproduct_id());
        return $stockitem->getManageStock();

    }

}
