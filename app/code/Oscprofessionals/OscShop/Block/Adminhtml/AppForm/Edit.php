<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\AppForm;

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
        $this->_blockGroup = 'Oscprofessionals_OscShop';
        $this->_controller = 'adminhtml_appForm';
        parent::_construct();
    }


    public function getFormHtml()
    {
        // get the current form as html content.
        //$html = parent::getFormHtml();
        //Append the phtml file after the form content.
        $html = $this->setTemplate('Oscprofessionals_OscShop::downloadapk/customdata.phtml')->toHtml();
        return $html;
    }

    /**
     * Prepare layout.
     *
     * @return $this
     */
    protected function _preparelayout()
    {
        $this->buttonList->update('save', 'label', __('Download Application'));
        //$this->removeButton('save');
        $this->removeButton('reset');
        $this->buttonList->update(
            'back',
            'onclick',
            'history.back()'
        );

        return parent::_prepareLayout();
    }
}
