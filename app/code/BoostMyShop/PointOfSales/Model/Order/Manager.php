<?php

namespace BoostMyShop\PointOfSales\Model\Order;


class Manager extends \Magento\Framework\Model\AbstractModel
{
    protected $_user;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\User\Model\UserFactory $userFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_userFactory = $userFactory;
    }


    protected function _construct()
    {
        $this->_init('BoostMyShop\PointOfSales\Model\ResourceModel\Order\Manager');
    }

    public function assignManagerToOrder($orderId, $userId)
    {
        $this->setorder_id($orderId);
        $this->setuser_id($userId);
        $this->save();
        return $this;
    }

    public function loadByOrderId($orderId)
    {
        return $this->load($orderId, 'order_id');
    }

    public function getUser()
    {
        if (!$this->_user)
            $this->_user = $this->_userFactory->create()->load($this->getuser_id());
            return $this->_user;
    }

}