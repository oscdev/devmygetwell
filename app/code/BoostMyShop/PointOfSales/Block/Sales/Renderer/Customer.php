<?php

namespace BoostMyShop\PointOfSales\Block\Sales\Renderer;

use Magento\Framework\DataObject;

class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        return $row->getFirstname().' '.$row->getLastname();
    }
}