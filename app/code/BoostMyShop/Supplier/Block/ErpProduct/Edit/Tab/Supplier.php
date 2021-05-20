<?php

namespace BoostMyShop\Supplier\Block\ErpProduct\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class Supplier extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_template = 'ErpProduct/Edit/Tab/Supplier.phtml';

    public function getGridBlock()
    {
        $block = $this->getLayout()->createBlock('BoostMyShop\Supplier\Block\ErpProduct\Edit\Tab\Supplier\Grid');
        return $block;
    }

    public function getFormBlock()
    {
        $block = $this->getLayout()->createBlock('BoostMyShop\Supplier\Block\ErpProduct\Edit\Tab\Supplier\Form');
        return $block;
    }

    public function getTabLabel()
    {
        return __('Suppliers');
    }

    public function getTabTitle()
    {
        return __('Suppliers');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }


}