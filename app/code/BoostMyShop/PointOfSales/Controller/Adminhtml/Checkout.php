<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

abstract class Checkout extends \Magento\Backend\App\AbstractAction
{

    protected $_coreRegistry;
    protected $_resultLayoutFactory;
    protected $_quoteHelper;
    protected $_config;
    protected $_productHelper;
    protected $_backendAuthSession;
    protected $_orderManagerFactory;
    protected $_orderFactory;
    protected $_customerFactory;
    protected $_customerRepository;
    protected $_registry;
    protected $_storeManager;
    protected $_userFactory;
    protected $_invoiceHelper;
    protected $_shipmentHelper;
    protected $_emptyProductLayoutBlock;
    protected $_logger;
    protected $_openingFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        \BoostMyShop\PointOfSales\Model\Quote $quoteHelper,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \BoostMyShop\PointOfSales\Model\Config $config,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\PointOfSales\Model\Order\ManagerFactory $orderManagerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \BoostMyShop\PointOfSales\Model\Registry $registry,
        \BoostMyShop\PointOfSales\Model\OpeningFactory $openingFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\User\Model\UserFactory $userFactory,
        \BoostMyShop\PointOfSales\Model\Order\Invoice $invoiceHelper,
        \BoostMyShop\PointOfSales\Model\Order\Shipment $shipmentHelper,
        \BoostMyShop\PointOfSales\Helper\Logger $logger,
        \BoostMyShop\PointOfSales\Block\Checkout\Products\EmptyLayout $emptyProductLayoutBlock

    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_quoteHelper = $quoteHelper;
        $this->_storeFactory = $storeFactory;
        $this->_config = $config;
        $this->_productHelper = $productHelper;
        $this->_productFactory = $productFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_orderManagerFactory = $orderManagerFactory;
        $this->_orderFactory = $orderFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customerRepository = $customerRepository;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_userFactory = $userFactory;
        $this->_invoiceHelper = $invoiceHelper;
        $this->_shipmentHelper = $shipmentHelper;
        $this->_emptyProductLayoutBlock = $emptyProductLayoutBlock;
        $this->_logger = $logger;
        $this->_openingFactory = $openingFactory;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
