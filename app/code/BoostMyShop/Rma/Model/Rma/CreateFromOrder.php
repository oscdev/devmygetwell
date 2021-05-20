<?php

namespace BoostMyShop\Rma\Model\Rma;


class CreateFromOrder
{
    protected $_rmaFactory;
    protected $_addressRenderer;
    protected $_backendAuthSession;
    protected $_orderItemFactory;

    public function __construct(
        \BoostMyShop\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
    )
    {
        $this->_rmaFactory = $rmaFactory;
        $this->_addressRenderer = $addressRenderer;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_orderItemFactory = $orderItemFactory;
    }


    public function create($order, $additional = [], $items = null)
    {
        $rma = $this->_rmaFactory->create();

        $rma->setrma_order_id($order->getId());
        $rma->setrma_customer_id($order->getCustomerId());
        $rma->setrma_store_id($order->getStoreId());
        $rma->setrma_customer_name($order->getcustomer_firstname().' '.$order->getcustomer_lastname());
        $rma->setrma_customer_email($order->getcustomer_email());
        $rma->setrma_currency_code($order->getorder_currency_code());

        $address = ($order->getShippingAddress() ? $order->getShippingAddress() : $order->getBillingAddress());
        $address = $this->_addressRenderer->format($address, 'txt');

        $rma->setrma_shipping_address($address);

        $userId = null;
        if ($this->_backendAuthSession && $this->_backendAuthSession->isLoggedIn())
            $userId =  $this->_backendAuthSession->getUser()->getId();
        $rma->setrma_manager($userId);

        foreach($additional as $k => $v)
            $rma->setData($k, $v);

        $rma->save();

        if (!$items)
        {
            foreach($order->getAllItems() as $orderItem) {
                if (($orderItem->getproduct_type() != 'bundle') && ($orderItem->getproduct_type() != 'configurable'))
                    $this->addOrderItem($rma, $orderItem);
            }
        }
        else
        {
            foreach($order->getAllItems() as $orderItem)
            {
                if (($orderItem->getproduct_type() != 'bundle') && ($orderItem->getproduct_type() != 'configurable'))
                {
                    if (isset($items[$orderItem->getId()]) && ($items[$orderItem->getId()]['ri_qty'] > 0))
                        $this->addOrderItem($rma, $orderItem, $items[$orderItem->getId()]);
                }
            }
        }

        return $rma;
    }

    public function addOrderItem($rma, $orderItem, $data = [])
    {
        $orderItemId = $orderItem->getId();

        if($orderItem->getParentItemId()) {
            $parentItem = $this->_orderItemFactory->create()->load($orderItem->getparent_item_id());
            if($parentItem->getproduct_type() == 'configurable')
                $orderItemId = $parentItem->getId();
        }

        $item = $rma->addItem($orderItem->getqty_ordered(), $orderItem->getproduct_id(), ['ri_order_item_id' => $orderItemId]);
        foreach($data as $k => $v)
            $item->setData($k, $v);
        $item->save();

        return $item;
    }

}