<?php

namespace BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Current;

class Information extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_LocaleLists;
    protected $_countryLists;
    protected $_customerGroupList;
    protected $_websiteList;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Customer\Model\Config\Source\Group $customerGroupList,
        \Magento\Store\Model\ResourceModel\Website\Collection $websiteList,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        $this->_LocaleLists = $localeLists;
        $this->_countryLists = $countryCollectionFactory;
        $this->_customerGroupList = $customerGroupList;
        $this->_websiteList = $websiteList;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {

        $form = $this->_formFactory->create();

        $baseFieldset = $form->addFieldset('fieldset_customer_information', ['legend' => __('Main'), 'class' => 'admin__fieldset']);

        $baseFieldset->addField(
            'customer_information_mode',
            'select',
            [
                'name' => 'mode',
                'label' => __('Mode'),
                'id' => 'mode',
                'title' => __('Mode'),
                'class' => 'input-select',
                'options' => ['guest' => __('Guest'), 'customer' => __('Customer')],
                'onchange' => 'objPosCheckout.applyCustomerMode();'

            ]
        );

        $baseFieldset->addField(
            'customer_information_id',
            'hidden',
            [
                'name' => 'entity_id',
                'label' => __('Id'),
                'id' => 'id',
                'title' => __('Id'),
                'class' => 'input-select'

            ]
        );

        $baseFieldset->addField(
            'customer_information_website_id',
            'select',
            [
                'name' => 'website_id',
                'label' => __('Website'),
                'id' => 'website',
                'title' => __('Website'),
                'class' => 'input-select',
                'disabled' => 'disabled',
                'values' => $this->_websiteList->toOptionArray(),
            ]
        );

        $baseFieldset->addField(
            'customer_information_group_id',
            'select',
            [
                'name' => 'group_id',
                'label' => __('Group'),
                'id' => 'group',
                'title' => __('Group'),
                'class' => 'input-select',
                'disabled' => 'disabled',
                'values' => $this->_customerGroupList->toOptionArray(),
            ]
        );

        $baseFieldset->addField(
            'customer_information_email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'id' => 'email',
                'title' => __('Email')
            ]
        );

        $baseFieldset->addField(
            'customer_information_taxvat',
            'text',
            [
                'name' => 'taxvat',
                'label' => __('Tax number'),
                'id' => 'tax_number',
                'title' => __('Tax number')
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
