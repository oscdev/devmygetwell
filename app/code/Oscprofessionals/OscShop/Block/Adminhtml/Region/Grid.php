<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\Region;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory
    as RegionCollectionFactory;

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
     * Magento Directory Region Collection.
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    private $regionCollection;

    /**
     * Main Grid Constructor.
     *
     * @param Context                 $context
     * @param BackendHelper           $backendHelper
     * @param RegionCollectionFactory $regionFactory
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        RegionCollectionFactory $regionFactory
    ) {
    
        $this->regionCollection = $regionFactory->create();

        parent::__construct($context, $backendHelper, $data = []);
    }

    /**
     * Grid Url.
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('oscshop/region/index', ['_current' => true]);
    }

    /**
     * Get Row Url Getter function.
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'oscshop/region/edit',
            [
                'store' => $this->getRequest()->getParam('store'),
                'region_id' => $row->getData('region_id'),
            ]
        );
    }

    /**
     * Grid constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('oscShopRegionGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    /**
     * Get Store Getter.
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Prepare Region Collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $countryId = $this->_request->getParam('country_id');

        $regionCollection = $this->regionCollection
            ->addCountryFilter($countryId)->load();

        $this->setCollection($regionCollection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare columns of the region grid.
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'default_name',
            [
                'header' => __('Default Region Name'),
                'index' => 'default_name',
                'class' => 'default_name',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Locale en_US Region Name'),
                'index' => 'name',
                'class' => 'name',
            ]
        );

        $this->addColumn(
            'code',
            [
                'header' => __('Region Code'),
                'index' => 'code',
                'class' => 'code',
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => [
                                'store' => $this->getRequest()->getParam('store'),
                            ],
                        ],
                        'field' => 'region_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $this->addColumn(
            'delete',
            [
                'header' => __('Delete'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Delete'),
                        'url' => [
                            'base' => '*/region/delete',
                            'params' => [
                                'store' => $this->getRequest()->getParam('store'),
                            ],
                        ],
                        'field' => 'region_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare delete mass action.
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('region_id');
        $this->getMassactionBlock()->setFormFieldName('region_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('oscshop/region/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        return $this;
    }
}
