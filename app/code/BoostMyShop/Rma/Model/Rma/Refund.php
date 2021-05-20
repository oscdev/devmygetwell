<?php

namespace BoostMyShop\Rma\Model\Rma;

use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class Refund
{
    protected $_creditMemoFactory;
    protected $_creditMemoManagement;
    protected $_creditmemoSender;
    protected $_transaction;

    public function __construct(
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditMemoManagement,
        CreditmemoSender $creditmemoSender,
        \Magento\Framework\DB\Transaction $transaction
    ){
        $this->_creditMemoManagement = $creditMemoManagement;
        $this->_transaction = $transaction;
        $this->_creditmemoSender = $creditmemoSender;
        $this->_creditMemoFactory = $creditmemoFactory;
    }

    public function process($rma, $data)
    {
        $order = $rma->getOrder();
        $creditmemo = $this->_creditMemoFactory->createByOrder($order, $data);

        if (!$creditmemo)
            throw new \Exception('Unable to create credit memo.');

        $creditmemo->addComment($data['comment_text'], false, false);

        $this->_creditMemoManagement->refund($creditmemo, (bool)$data['do_offline'], true);

        $this->_creditmemoSender->send($creditmemo);

        return $creditmemo;
    }

}