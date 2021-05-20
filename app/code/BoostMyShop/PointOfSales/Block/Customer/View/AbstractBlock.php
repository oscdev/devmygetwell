<?php
namespace BoostMyShop\PointOfSales\Block\Customer\View;

use Magento\Customer\Api\AccountManagementInterface;

class AbstractBlock extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry = null;
    protected $_addressHelper;
    protected $_addressMapper;
    protected $_accountManagement;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Customer\Helper\Address $addressHelper,
                                \Magento\Customer\Model\Address\Mapper $addressMapper,
                                AccountManagementInterface $accountManagement,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_addressHelper = $addressHelper;
        $this->_addressMapper = $addressMapper;
        $this->_accountManagement = $accountManagement;
    }

    public function getCustomer()
    {
        return $this->_coreRegistry->registry('pos_current_customer');
    }
}
