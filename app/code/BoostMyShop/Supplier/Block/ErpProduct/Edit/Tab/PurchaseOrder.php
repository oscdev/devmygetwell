<?php

namespace BoostMyShop\Supplier\Block\ErpProduct\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class PurchaseOrder extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_orderProductCollectionFactory;
    protected $_coreRegistry;
    protected $_supplierCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \BoostMyShop\Supplier\Model\ResourceModel\Order\Product\CollectionFactory $orderProductCollectionFactory,
        \BoostMyShop\Supplier\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_orderProductCollectionFactory = $orderProductCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_supplierCollectionFactory = $supplierCollectionFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('purchaseorder');
        $this->setDefaultSort('po_eta');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_orderProductCollectionFactory->create()->getOrdersHistory($this->getProduct()->getId())->addRealEta();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('po_created_at', ['header' => __('Date'), 'index' => 'po_created_at', 'type' => 'date']);
        $this->addColumn('po_sup_id', ['header' => __('Supplier'), 'index' => 'po_sup_id', 'type' => 'options', 'options' => $this->getSupplierOptions()]);
        $this->addColumn('real_eta', ['header' => __('ETA'), 'index' => 'real_eta', 'type' => 'date']);
        $this->addColumn('po_reference', ['header' => __('PO #'), 'index' => 'po_reference', 'renderer' => 'BoostMyShop\Supplier\Block\ErpProduct\Edit\Tab\Renderer\Po']);
        $this->addColumn('po_status', ['header' => __('Status'), 'index' => 'po_status']);
        $this->addColumn('pop_qty', ['header' => __('Qty ordered'), 'index' => 'pop_qty', 'type' => 'number']);
        $this->addColumn('pop_qty_received', ['header' => __('Qty received'), 'index' => 'pop_qty_received', 'type' => 'number']);
        $this->addColumn('pop_price_base', ['header' => __('Buying price'), 'index' => 'pop_price_base', 'type' => 'number']);
        $this->addColumn('pop_price_with_cost_base', ['header' => __('Buying price with costs'), 'index' => 'pop_price_base', 'type' => 'number']);

        return $this;
    }


    public function getSupplierOptions()
    {
        $options = [];
        foreach($this->_supplierCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getsup_name();
        }
        return $options;
    }

    public function getGridUrl()
    {
        return $this->getUrl('supplier/erpproduct_purchaseorder/grid', ['product_id' => $this->getProduct()->getId()]);
    }


    public function getTabLabel()
    {
        return __('Purchase orders');
    }

    public function getTabTitle()
    {
        return __('Purchase orders');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
