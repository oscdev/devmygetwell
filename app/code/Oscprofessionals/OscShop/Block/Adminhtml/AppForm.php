<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

/**
 * Class AppForm.
 */
class AppForm extends GridContainer
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_appform';
        $this->_blockGroup = 'Oscprofessionals_OscShop';
        $this->_addButtonLabel = __('add new');
        $this->_headerText = __('App Form');

        parent::_construct();
    }
}
