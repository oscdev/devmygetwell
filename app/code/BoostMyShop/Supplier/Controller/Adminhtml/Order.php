<?php

namespace BoostMyShop\Supplier\Controller\Adminhtml;

abstract class Order extends \Magento\Backend\App\AbstractAction
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_orderFactory;

    protected $_resultLayoutFactory;

    protected $_backendAuthSession;

    protected $_config;

    protected $_notification;

    protected $_timezoneInterface;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\Supplier\Model\Config $config,
        \BoostMyShop\Supplier\Model\Order\Notification $notification,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \BoostMyShop\Supplier\Model\OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_orderFactory = $orderFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_config = $config;
        $this->_notification = $notification;
        $this->_timezoneInterface = $timezoneInterface;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        return $this;
    }

    protected function convertToEuropeanDateFormat($dateString)
    {
        $t = explode('/', $dateString);
        if (count($t) == 3)
            return $t[2].'-'.$t[1].'-'.$t[0];
        else
            return $dateString;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
