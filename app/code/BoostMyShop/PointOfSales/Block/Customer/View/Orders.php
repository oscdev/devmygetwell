<?php
namespace BoostMyShop\PointOfSales\Block\Customer\View;

use Magento\Backend\Block\Widget\Grid\Column;

class Orders extends \BoostMyShop\PointOfSales\Block\Widget\Grid
{

    protected $_orderCollectionFactory;
    protected $_coreRegistry = null;

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
        \BoostMyShop\PointOfSales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    public function getCustomer()
    {
        return $this->_coreRegistry->registry('pos_current_customer');
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Orders'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setPagerVisibility(false);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_orderCollectionFactory->create()->addAddressFields()->joinManager();
        $collection->addCustomerFilter($this->getCustomer()->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', ['header' => __('Date'), 'index' => 'created_at']);
        $this->addColumn('increment_id', ['header' => __('#'), 'index' => 'increment_id']);
        $this->addColumn('customer_name', ['header' => __('Customer'), 'renderer' => 'BoostMyShop\PointOfSales\Block\Sales\Renderer\Customer']);
        $this->addColumn('products', ['header' => __('Products'), 'index' => 'entity_id', 'renderer' => 'BoostMyShop\PointOfSales\Block\Sales\Renderer\Products']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status']);
        $this->addColumn('store_id', ['header' => __('Store'), 'index' => 'store_id', 'renderer' => '\Magento\Backend\Block\Widget\Grid\Column\Renderer\Store']);
        $this->addColumn('username', ['header' => __('Manager'), 'index' => 'username']);

        return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales/index');
    }

    public function getRowUrl($item)
    {
        $title = "Order #".$item->getincrement_id();
        if ($item->getusername())
            $title .= ' (placed by '.$item->getusername().')';
        return "objPosUi.showPopup('".$title."', '".$this->getUrl('pointofsales/sales/view', ['id' => $item->getId()])."')";
    }
}
