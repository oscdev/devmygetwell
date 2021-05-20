<?php

namespace BoostMyShop\AdvancedStock\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ErpProductEditSave implements ObserverInterface
{
    protected $_eventManager;
    protected $_stockMovementFactory;
    protected $_backendAuthSession;
    protected $_warehouseItemFactory;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\AdvancedStock\Model\StockMovementFactory $stockMovementFactory,
        \BoostMyShop\AdvancedStock\Model\Warehouse\ItemFactory $warehouseItemFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_stockMovementFactory = $stockMovementFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_warehouseItemFactory = $warehouseItemFactory;
    }

    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $postData = $observer->getEvent()->getPostData();
        $messageManager = $observer->getEvent()->getmessage_manager();

        $smData = (isset($postData['newStockMovement']) ? $postData['newStockMovement'] : false);
        if ($smData && isset($smData['sm_qty']) && ($smData['sm_qty'] > 0)) {
            try
            {
                $this->createStockMovement($product, $smData);
                $messageManager->addSuccess(__('Stock movement created.'));
            }
            catch(\Exception $ex)
            {
                $messageManager->addError(__('Unable to create stock movement : '.$ex->getMessage()));
            }
        }

        $wData = (isset($postData['warehouses']) ? $postData['warehouses'] : false);
        if ($wData)
        {
            foreach($wData as $warehouseItemId => $warehouseData)
            {
                $this->updateWarehouseItem($warehouseItemId, $warehouseData);
            }
        }

        return $this;
    }

    protected function createStockMovement($product, $smData)
    {
        $userId = null;
        if ($this->_backendAuthSession->getUser())
            $userId = $this->_backendAuthSession->getUser()->getId();

        $this->_stockMovementFactory->create()->create( $product->getId(),
                                                        $smData['sm_from_warehouse_id'],
                                                        $smData['sm_to_warehouse_id'],
                                                        $smData['sm_qty'],
                                                        $smData['sm_category'],
                                                        $smData['sm_comments'],
                                                        $userId
                                                        );

        return $this;
    }

    protected function updateWarehouseItem($warehouseItemId, $warehouseData)
    {
        $warehouseItem = $this->_warehouseItemFactory->create()->load($warehouseItemId);

        $fields = ['wi_shelf_location', 'wi_use_config_warning_stock_level', 'wi_warning_stock_level', 'wi_use_config_ideal_stock_level', 'wi_ideal_stock_level'];
        foreach($fields as $field)
        {
            if (isset($warehouseData[$field]))
                $warehouseItem->setData($field, $warehouseData[$field]);
        }

        $warehouseItem->save();
    }

}
