<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\Region\Edit;

/**
 * Class Tabs.
 *
 * @method setId(string $value)
 * @method setTitle(string $value)
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('oscshop_region_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Region Information'));
    }
}
