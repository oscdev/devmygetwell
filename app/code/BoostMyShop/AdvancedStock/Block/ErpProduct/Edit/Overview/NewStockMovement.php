<?php

namespace BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview;

class NewStockMovement extends \Magento\Backend\Block\Template
{
    protected $_template = 'ErpProduct/Edit/Overview/NewStockMovement.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BoostMyShop\AdvancedStock\Model\ResourceModel\Warehouse\CollectionFactory $warehouseCollectionFactory,
        \BoostMyShop\AdvancedStock\Model\StockMovement\Category $categoryHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->_categoryHelper = $categoryHelper;
    }

    public function getWarehouses()
    {
        return $this->_warehouseCollectionFactory
            ->create()
            ;
    }

    public function getCategories()
    {
        return $this->_categoryHelper->getAll();
    }

}