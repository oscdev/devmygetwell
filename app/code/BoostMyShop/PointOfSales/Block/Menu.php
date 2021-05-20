<?php
namespace BoostMyShop\PointOfSales\Block;

class Menu extends \Magento\Backend\Block\Template
{
    protected $_template = 'Menu.phtml';

    protected $_coreRegistry = null;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getItems()
    {
        $items = [];

        $items[] = $this->createItem('checkout', 'Checkout', '*/checkout/index');
        $items[] = $this->createItem('sales', 'Sales', '*/sales/index');
        $items[] = $this->createItem('customer', 'Customers', '*/customer/index');
        //$items[] = $this->createItem('catalog', 'Catalog', '*/catalog/index');
        //$items[] = $this->createItem('payments', 'Transactions', '*/payments/index');
        $items[] = $this->createItem('report', 'Reports', '*/stat/index');
        $items[] = $this->createItem('settings', 'Settings', 'adminhtml/system_config/edit', ['section' => 'pointofsales'], false);

        return $items;
    }

    protected function createItem($id, $title, $url, $urlParams = [], $isAjax = true)
    {
        $item = new \Magento\Framework\DataObject();

        $item->setId($id);
        $item->setTitle($title);
        $item->setClass($title);
        $item->setIsAjax($isAjax);
        $item->setUrl($this->getUrl($url, $urlParams));

        return $item;
    }
}