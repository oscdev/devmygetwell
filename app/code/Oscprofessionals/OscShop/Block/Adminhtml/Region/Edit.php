<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\Region;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

/**
 * Class Edit.
 *
 * CMS block edit form container.
 */
class Edit extends FormContainer
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry = null;

    /**
     * Main Constructor.
     *
     * @param Context  $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
    
        $this->coreRegistry = $registry;
        parent::__construct($context, $data = []);
    }

    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'region_id';
        $this->_blockGroup = 'Oscprofessionals_OscShop';
        $this->_controller = 'adminhtml_region';
        parent::_construct();
    }

    /**
     * Prepare layout.
     *
     * @return $this
     */
    protected function _preparelayout()
    {
        $this->buttonList->update('save', 'label', __('Save Region'));
        $this->buttonList->update('delete', 'label', __('Delete Region'));
        $this->buttonList->update(
            'back',
            'onclick',
            'history.back()'
        );

        return parent::_prepareLayout();
    }

    /**
     * Getter Region Instance.
     *
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function getRegionInstance()
    {
        return $this->coreRegistry
            ->registry('current_oscshop_region_instance');
    }
}
