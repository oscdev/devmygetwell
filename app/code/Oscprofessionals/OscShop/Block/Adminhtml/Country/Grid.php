<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\Country;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory
    as CountryCollectionFactory;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\DataObject;

/**
 * Class Grid.
 *
 * @method setId(string $value)
 * @method setUseAjax(bool $value)
 * @method setFormFieldName(string $value)
 * @method addItem(string $value1, array $value2)
 */
class Grid extends Extended
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
     * Main Grid Constructor.
     *
     * @param Context                  $context
     * @param BackendHelper            $backendHelper
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param DataObjectFactory        $objectFactory
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CountryCollectionFactory $countryCollectionFactory,
        DataObjectFactory $objectFactory
    ) {
    
        $this->countryCollection = $countryCollectionFactory->create();
        $this->objectFactory = $objectFactory;

        parent::__construct($context, $backendHelper, $data = []);
    }

    /**
     * Get Grid Url.
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'oscshop/country/index',
            ['_current' => true]
        );
    }

    /**
     * Get Grid Row Url.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'oscshop/region/index',
            [
                'store' => $this->getRequest()->getParam('store'),
                'country_id' => $row->getData('country_id'),
            ]
        );
    }

    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('oscShopCountryGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(false);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get Store.
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Prepare Collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
            $collection = $this->countryCollection->loadByStore();

            $this->setCollection($collection);

            parent::_prepareCollection();

            return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'country_id',
            [
                'header' => __('Country ID'),
                'type' => 'text',
                'index' => 'country_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'country_name',
            [
                'header' => __('Country Name'),
                'index' => 'country_id',
                'class' => 'country_id',
                'type' => 'options',
                'options' => $this->getCountryNameOptions()->getData(),
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Collects country Names using country id from locale.
     *
     * @return DataObject
     */
    public function getCountryNameOptions()
    {
        /** @var DataObject $preparedOptionObject */
        $preparedOptionObject = $this->objectFactory->create();

        $countryNameOptions = $this->countryCollection
            ->setForegroundCountries('')
            ->load()
            ->toOptionArray(__('-- Please Select Country to Filter --'));
        $prepared = [];
        foreach ($countryNameOptions as $option) {
            $prepared[$option['value']] = $option['label'];
        }

        $preparedOptionObject->setData($prepared);

        return $preparedOptionObject;
    }

    /**
     * Prepare delete Mass Action.
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('country_id');

        $this->getMassactionBlock()->setFormFieldName('country_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('oscshop/country/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }
}
