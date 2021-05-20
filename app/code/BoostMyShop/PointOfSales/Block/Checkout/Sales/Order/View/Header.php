<?php
namespace BoostMyShop\PointOfSales\Block\Checkout\Sales\Order\View;

class Header extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry = null;
    protected $_productInformation = null;
    protected $_invoice;
    protected $_invoiceCollectionFactory;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
    }

    public function getOrder()
    {
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
    }

    public function hasInvoice()
    {
        return ($this->getInvoice()->getId() > 0);
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('sales/order_invoice/print', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    public function getReceiptUrl()
    {
        return $this->getUrl('pointofsales/order/printReceipt', ['order_id' => $this->getOrder()->getId()]);
    }

    public function getInvoice()
    {
        if (!$this->_invoice)
            $this->_invoice = $this->_invoiceCollectionFactory->create()->addFieldToFilter('order_id', $this->getOrder()->getId())->getFirstItem();
        return $this->_invoice;
    }


    public function downloadReceiptRequired()
    {
        return ($this->getRequest()->getParam('download_receipt'));
    }

}