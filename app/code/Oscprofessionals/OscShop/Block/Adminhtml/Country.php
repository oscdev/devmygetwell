<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

/**
 * Class Country.
 */
class Country extends GridContainer
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_country';
        $this->_blockGroup = 'Oscprofessionals_OscShop';
        $this->_headerText = __('Country');

        parent::_construct();
        $this->buttonList->remove('add');
    }
}
