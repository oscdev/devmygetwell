<?php
namespace BoostMyShop\PointOfSales\Block\Checkout\Customer;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected $_coreRegistry;

    protected $_template = 'BoostMyShop_PointOfSales::Checkout/Customer/Tabs.phtml';

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('tab_container');

    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'tab_current',
            [
                'label' => __('Customer details'),
                'title' => __('Customer details'),
                'content' => $this->getLayout()->createBlock('BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Current')->toHtml()
            ]
        );

        $this->addTab(
            'tab_existing',
            [
                'label' => __('Select existing customer'),
                'title' => __('Select existing customer'),
                'content' => $this->getLayout()->createBlock('BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Existing')->toHtml()
            ]
        );

        $this->addTab(
            'tab_new',
            [
                'label' => __('Create new customer'),
                'title' => __('Create new customer'),
                'content' => $this->getLayout()->createBlock('BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\NewCustomer')->toHtml()
            ]
        );

        return parent::_beforeToHtml();
    }
}