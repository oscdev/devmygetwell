<?php
namespace Born\Pos\Plugin;

class PosAttributeQuoteToOrderItem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return mixed
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {

        $orderItem = $proceed($item, $additional);
        $orderItem->setPosAliseName($item->getPosAliseName());
        return $orderItem;
    }
}