<?php

namespace BoostMyShop\PointOfSales\Model\Order;


class Invoice
{
    protected $_invoiceService;
    protected $_transaction;
    protected $_logger;

    public function __construct(
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \BoostMyShop\PointOfSales\Helper\Logger $logger,
        \Magento\Framework\DB\Transaction $transaction
    ){
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_logger = $logger;
    }

    public function createInvoice($order, $invoiceItems = null)
    {
        $this->_logger->log("Create invoice for order #".$order->getIncrementId(), \BoostMyShop\PointOfSales\Helper\Logger::kLogGeneral);

        if ($invoiceItems == null)
            $invoiceItems = $this->prepareInvoiceItems($order);

        $this->appendParents($order, $invoiceItems);

        $invoice = $this->_invoiceService->prepareInvoice($order, $invoiceItems);
        //$this->_registry->register('current_invoice', $invoice);
        //$invoice->setRequestedCaptureCase($data['capture_case']);

        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);

        $transactionSave = $this->_transaction->addObject($invoice)->addObject($invoice->getOrder());

        $transactionSave->save();

        return $invoice;
    }

    protected function prepareInvoiceItems($order)
    {
        $items = [];

        foreach($order->getAllItems() as $item)
        {
            $items[$item->getId()] = $item->getqty_ordered();
        }

        return $items;
    }

    protected function appendParents($order, &$invoiceItems)
    {
        foreach($order->getAllItems() as $item)
        {
            if (isset($invoiceItems[$item->getitem_id()]) && ($invoiceItems[$item->getitem_id()] > 0))
            {
                if (!isset($invoiceItems[$item->getParentItemId()]))
                    $invoiceItems[$item->getParentItemId()] = $item->getqty_ordered();

            }
        }
    }

}