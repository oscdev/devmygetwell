<?php
namespace Born\Pos\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetItemPosAttribute implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setPosAliseName($product->getPosAliseName());
    }
}