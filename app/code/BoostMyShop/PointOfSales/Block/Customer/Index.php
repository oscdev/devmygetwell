<?php

namespace BoostMyShop\PointOfSales\Block\Customer;

use Magento\Backend\Block\Widget\Grid\Column;

class Index extends \BoostMyShop\PointOfSales\Block\Widget\Grid
{
    protected $_customerCollectionFactory;
    protected $_customerGroupCollectionFactory;
    protected $_websiteCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setTitle(__('Customers'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_customerCollectionFactory->create();
        $collection->addNameToSelect();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', ['header' => __('#'), 'index' => 'entity_id']);
        $this->addColumn('customer_name', ['header' => __('Customer'), 'index' => 'name']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn('created_at', ['header' => __('Created at'), 'index' => 'created_at']);
        $this->addColumn('group_id', ['header' => __('Group'), 'index' => 'group_id', 'type' => 'options', 'options' => $this->getCustomerGroupOptions()]);
        $this->addColumn('website_id', ['header' => __('Website'), 'index' => 'website_id', 'type' => 'options', 'options' => $this->getWebsitesOptions()]);

        return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/customer/index');
    }

    protected function getCustomerGroupOptions()
    {
        $options = [];
        foreach($this->_customerGroupCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getcustomer_group_code();
        }
        return $options;
    }

    protected function getWebsitesOptions()
    {
        $options = [];
        foreach($this->_websiteCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getName();
        }
        return $options;
    }

    public function getRowUrl($item)
    {
        $title = $item->getName();
        return "objPosUi.showPopup('".$title."', '".$this->getUrl('pointofsales/customer/view', ['id' => $item->getId()])."')";
    }

}
