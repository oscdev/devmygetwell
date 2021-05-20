<?php

namespace BoostMyShop\Rma\Model\Rma;


class Item extends \Magento\Framework\Model\AbstractModel
{
    protected $_product = null;
    protected $_rma = null;
    protected $_orderItem = null;
    protected $_productFactory = null;
    protected $_rmaFactory = null;
    protected $_orderItemFactory = null;
    protected $_stockUpdater = null;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \BoostMyShop\Rma\Model\StockUpdater $stockUpdater,
        \BoostMyShop\Rma\Model\RmaFactory $rmaFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_productFactory = $productFactory;
        $this->_rmaFactory = $rmaFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_stockUpdater = $stockUpdater;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\ResourceModel\Rma\Item');
    }


    public function getProduct()
    {
        if ($this->_product == null)
        {
            $this->_product = $this->_productFactory->create()->load($this->getri_product_id());
        }
        return $this->_product;
    }

    public function getRma()
    {
        if ($this->_rma == null)
        {
            $this->_rma = $this->_rmaFactory->create()->load($this->getri_rma_id());
        }
        return $this->_rma;
    }

    public function getOrderItem()
    {
        if ($this->getri_order_item_id())
        {
            if (!$this->_orderItem)
                $this->_orderItem = $this->_orderItemFactory->create()->load($this->getri_order_item_id());
        }
        return $this->_orderItem;
    }

    public function getRefundableQuantity()
    {
        if ($this->getOrderItem())
        {
            return ($this->getOrderItem()->getqty_invoiced() - $this->getOrderItem()->getqty_refunded());
        }
        else
            return 0;
    }

    public function backToStock($qty, $targetWarehouse)
    {
        $this->_stockUpdater->productBackToStock($this->getri_product_id(), $qty, $targetWarehouse, 'RMA #'.$this->getRma()->getrma_reference());
    }

}