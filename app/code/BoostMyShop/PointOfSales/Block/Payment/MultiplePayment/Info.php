<?php

namespace BoostMyShop\PointOfSales\Block\Payment\MultiplePayment;

class Info extends \Magento\Payment\Block\Info
{
    protected $_template = 'BoostMyShop_PointOfSales::Payment/MultiplePayment/Info.phtml';
    protected $_paymentCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Payment\CollectionFactory $paymentCollectionFactory,
        array $data = []
    )
    {
        $this->_paymentCollectionFactory = $paymentCollectionFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('BoostMyShop_PointOfSales::Payment/MultiplePayment/Pdf/Info.phtml');
        return $this->toHtml();
    }

    public function getPayments()
    {
        $orderId = $this->getInfo()->getData('parent_id');
        return $this->_paymentCollectionFactory->create()->addOrderFilter($orderId);
    }

}
