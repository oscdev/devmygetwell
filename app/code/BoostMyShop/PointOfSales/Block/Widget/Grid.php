<?php

namespace BoostMyShop\PointOfSales\Block\Widget;

use Magento\Backend\Block\Widget\Grid\Column;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_template = 'BoostMyShop_PointOfSales::Widget/Grid.phtml';

    protected function _prepareCollection()
    {
        if ($globalSearch = $this->getGlobalFilterValue()) {
            if (strlen($globalSearch))
                $this->getCollection()->addGlobalFilter($globalSearch);
        }

        return parent::_prepareCollection();
    }

    public function getGlobalFilterValue()
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);
        if ($filter)
        {
            $filter = $this->_backendHelper->prepareFilterString($filter);
            if (is_array($filter) && isset($filter['global_filter']))
                return $filter['global_filter'];
        }
    }

}
