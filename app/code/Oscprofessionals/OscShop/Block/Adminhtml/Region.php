<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

/**
 * Class Region.
 */
class Region extends GridContainer
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_region';
        $this->_blockGroup = 'Oscprofessionals_OscShop';
        $this->_addButtonLabel = __('Add New Region');
        $this->_headerText = __('Manage Regions');

        parent::_construct();
    }
}
