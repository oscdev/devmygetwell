<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer;

use Magento\Framework\DataObject;

class Eta extends AbstractRenderer
{

    public function render(DataObject $row)
    {
        $value = $row->getpop_eta();
        $html = '<input size="9" type="textbox" name="products['.$row->getId().'][eta]" id="products['.$row->getId().'][eta]" value="'.$value.'">';

        return $html;
    }
}