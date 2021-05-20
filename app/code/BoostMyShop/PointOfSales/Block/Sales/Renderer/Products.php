<?php

namespace BoostMyShop\PointOfSales\Block\Sales\Renderer;

use Magento\Framework\DataObject;

class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        $html = [];

        foreach($row->getAllItems() as $orderItem)
        {
            $html[] = ((int)$orderItem->getQtyOrdered()).'x '.$orderItem->getName();
        }

        return implode('<br>', $html);

    }
}