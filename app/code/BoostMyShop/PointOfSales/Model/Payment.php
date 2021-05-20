<?php

namespace BoostMyShop\PointOfSales\Model;


class Payment extends \Magento\Framework\Model\AbstractModel
{
    protected $_paymentFactory;
    protected $_dateTime;
    protected $_config;
    protected $_posRegistry;
    protected $_logger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\PaymentFactory $paymentFactory,
        \BoostMyShop\PointOfSales\Model\Config $config,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \BoostMyShop\PointOfSales\Helper\Logger $logger,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_paymentFactory = $paymentFactory;
        $this->_dateTime = $dateTime;
        $this->_config = $config;
        $this->_posRegistry = $posRegistry;
        $this->_logger = $logger;
    }

    protected function _construct()
    {
        $this->_init('BoostMyShop\PointOfSales\Model\ResourceModel\Payment');
    }

    public function createForOrder($order)
    {
        $payment = $order->getPayment();
        $this->_logger->log('Create payment records for order #'.$order->getId().' and method '.$payment->getMethod());
        if ($payment)
        {
            if ($payment->getMethod() == 'multiple_payment')
                $this->processMultiplePayment($order);
            else
                $this->processOtherMethod($order, $payment);
        }
    }

    protected function processMultiplePayment($order)
    {
        $paymentTotal = 0;
        $items = $order->getPayment()->getData('additional_information');
        if (isset($items['multiple_payment']))
        {
            foreach($items['multiple_payment'] as $k => $v)
            {
                if ($v > 0) {
                    $this->addPayment($k, $v, $order->getId(), 'Order #' . $order->getIncrementId());
                    $paymentTotal += $v;
                }
            }
        }

        //check for change
        if ($paymentTotal > $order->getGrandTotal())
        {
            $change = $paymentTotal - $order->getGrandTotal();
            $changeMethod = $this->_config->getChangeMethod();
            if (!$changeMethod)
                $changeMethod = 'Change';
            $this->_logger->log('Create change record for order #'.$order->getId().' '.$changeMethod.' / '.$change);
            $this->addPayment($changeMethod, -$change, $order->getId(), 'Change for order #' . $order->getIncrementId());
        }
    }

    protected function processOtherMethod($order, $payment)
    {
        $data = $payment->getAdditionalData();
        $methodTitle = isset($data['method_title']) ? $data['method_title'] : $payment->getMethod();
        $this->addPayment($methodTitle, $order->getGrandTotal(), $order->getId(), 'Order #' . $order->getIncrementId());
    }

    public function addPayment($method, $total, $orderId, $comments)
    {
        $now = $this->_dateTime->gmtDate();

        $userId = $this->_posRegistry->getCurrentUserId();

        $data = [
            'method' => $method,
            'created_at' => $now,
            'amount' => $total,
            'comments' => $comments,
            'order_id' => $orderId,
            'user_id' => $userId
        ];

        $obj = $this->_paymentFactory->create();
        $obj->setData($data);
        $this->_getResource()->save($obj);

        return $obj;
    }

}