<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Block\Adminhtml\Requests\Edit\Tab;
 
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class General extends Generic implements TabInterface{
    protected $_wysiwygConfig;
    protected $_newsStatus;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm(){
        $model = $this->_coreRegistry->registry('franchise_enquiry_request');
        $form = $this->_formFactory->create();
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );
 
        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'        => 'name',
                'label'    => __('Name'),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'mobile',
            'text',
            [
                'name'        => 'mobile',
                'label'    => __('Mobile'),
                'required'     => true,
                'class'     => 'validate-number',
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'        => 'email',
                'label'    => __('Email'),
                'required'     => true
            ]
        );
        $regionCollection = $this->_regionFactory->create()->getCollection()->addCountryFilter('IN');
        $regions = $regionCollection->toOptionArray();
        $fieldset->addField(
            'region',
            'select',
            [
                'name' => 'region',
                'label' => __('State'),
                'required'     => true,
                'values' => $regions]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'        => 'city',
                'label'    => __('City'),
                'required'     => true
            ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'comment',
            'textarea',
            [
                'name'      => 'comment',
                'label'     => __('Comment'),
                'required'  => true,
                'style'     => 'height: 15em; width: 100%;',
                'config'    => $wysiwygConfig
            ]
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
    public function getTabLabel(){
        return __('Request Info');
    }
    public function getTabTitle(){
        return __('Request Info');
    }
    public function canShowTab(){
        return true;
    }
    public function isHidden(){
        return false;
    }
}