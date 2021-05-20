<?php
namespace BoostMyShop\PointOfSales\Block;

class Header extends \Magento\Backend\Block\Template
{
    protected $_template = 'Header.phtml';

    protected $_coreRegistry = null;
    protected $_posRegistry = null;
    protected $_storeCollectionFactory = null;
    protected $_userCollection;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\User\Model\ResourceModel\User\Collection $userCollection,
                                \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
                                \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_posRegistry = $posRegistry;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_userCollection = $userCollection;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getStores()
    {
        return $this->_storeCollectionFactory->create();
    }

    public function getStoreFullName($store)
    {
        $name = [];
        $name[] = $store->getWebsite()->getName();
        $name[] = $store->getGroup()->getName();
        $name[] = $store->getName();

        return implode(' > ', $name);
    }

    public function getUsers()
    {
        return $this->_userCollection;
    }

    public function getCurrentUserName()
    {
        return ucfirst($this->_posRegistry->getCurrentUser()->getusername());
    }

    public function getCurrentStoreName()
    {
        $value = ucfirst($this->_posRegistry->getCurrentStore()->getName());
        if (strlen($value) > 15)
            $value = substr($value, 0, 15).'...';
        return $value;
    }

}
