<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\Region\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory
    as CountryCollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\DataObject;

/**
 * Class RegionInformation.
 *
 * @method getData()
 */
class RegionInformation extends Generic implements TabInterface
{
    /**
     * Data Object Factory.
     *
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * Magento Directory Country Collection.
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    private $countryCollection;

    /**
     * Constructor.
     *
     * @param Context                  $context
     * @param Registry                 $registry
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param FormFactory              $formFactory
     * @param DataObjectFactory        $objectFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CountryCollectionFactory $countryCollectionFactory,
        FormFactory $formFactory,
        DataObjectFactory $objectFactory
    ) {
    
        $this->countryCollection = $countryCollectionFactory->create();
        $this->objectFactory = $objectFactory;

        parent::__construct($context, $registry, $formFactory, $data = []);
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Region Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Region Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry
            ->registry('current_oscshop_region_instance');

        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Region Information')]
        );

        if ($model ? $model->getRegionId() : '') {
            $fieldset->addField(
                'region_id',
                'hidden',
                ['name' => 'region_id']
            );
            $fieldset->addField(
                'country_id',
                'select',
                [
                    'name' => 'country_id',
                    'label' => __('Country'),
                    'title' => __('Country'),
                    'options' => $this->getCountryNameOptions(
                        $model->getCountryId()
                    )->getData('countries'),
                    'required' => true,
                    'readonly' => true,
                ]
            );
        } else {
            $options = $this->getCountryNameOptions('')->getData('countries');

            $fieldset->addField(
                'country_id',
                'select',
                [
                    'name' => 'country_id',
                    'label' => __('Country'),
                    'title' => __('Country'),
                    'options' => $options,
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            'default_name',
            'text',
            [
                'name' => 'default_name',
                'label' => __('Region Name'),
                'title' => __('Region Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('Region Code'),
                'title' => __('Region Code'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Locale en_US Region Name'),
                'title' => __('Locale en_US Region Name'),
                'required' => true,
            ]
        );

        $form->setValues($model ? $model->getData() : '');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Collects country Names using country id from locale.
     *
     * @param $countryId
     *
     * @return DataObject
     */
    public function getCountryNameOptions($countryId)
    {
        $countryOptionsObject = $this->objectFactory->create();

        $options = $this->countryCollection
            ->setForegroundCountries('')
            ->load()
            ->toOptionArray(__('-- Please Select Country --'));

        $prepared = [];

        foreach ($options as $option) {
            $prepared[$option['value']] = $option['label'];
        }

        $countryNameOptions = $prepared;

        if ($countryId) {
            $subOption[$countryId] = $prepared[$countryId];
            $countryNameOptions = $subOption;
        }

        $countryOptionsObject->setData('countries', $countryNameOptions);

        return $countryOptionsObject;
    }

    /**
     * Check permission for passed action.
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
