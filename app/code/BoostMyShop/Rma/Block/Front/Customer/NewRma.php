<?php

namespace BoostMyShop\Rma\Block\Front\Customer;

class NewRma extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Rma/NewRma.phtml';

    protected $_rmaCollectionFactory;
    protected $_customerSession;
    protected $_rmas;
    protected $_config;
    protected $_coreRegistry;
    protected $_automaticAuthorization;
    protected $_rmaFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollectionFactory,
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\Rma\Model\Rma\AutomaticAuthorization $_automaticAuthorization,
        \BoostMyShop\Rma\Model\RmaFactory $rmaFactory,
        array $data = []
    ) {
        $this->_rmaCollectionFactory = $rmaCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_rmaFactory = $rmaFactory;
        $this->_config = $config;
        $this->_coreRegistry = $registry;
        $this->_automaticAuthorization = $_automaticAuthorization;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Returns'));
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('rma_order');
    }

    public function getReasons()
    {
        return $this->_config->getReasons();
    }

    public function getRequests()
    {
        return $this->_config->getRequests();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/submitRequest');
    }

    public function canDisplayItem($orderItem)
    {
        if (($orderItem->getproduct_type() != 'bundle') && ($orderItem->getproduct_type() != 'configurable'))
            return true;
        else
            return false;
    }

    public function enablePrintReturn(){
        return $this->_automaticAuthorization->isAllowed($this->getOrder());
    }
}
