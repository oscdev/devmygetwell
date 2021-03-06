<?php

namespace BoostMyShop\PointOfSales\Block\Checkout\Customer\Tab\Current;

class Address extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_LocaleLists;
    protected $_countryLists;
    protected $_regionCollectionFactory;
    protected $_helperData;
    protected $_countryFactory;

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
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \BoostMyShop\PointOfSales\Helper\Data $helperData,
        \Magento\Directory\Model\CountryFactory $countryFactory,

        array $data = []
    ) {
        $this->_LocaleLists = $localeLists;
        $this->_countryLists = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_helperData = $helperData;
        $this->_countryFactory = $countryFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */

    public function getRegionOptionsSel()
    {
        $storeCountryId =  $this->_helperData->getGuestCustCountryId();
        $storeRegionId =  $this->_helperData->getGuestCustRegionId();
        //$collection = $this->_regionCollectionFactory->create()
        //   ->addFieldToFilter('main_table.country_id' ,$storeCountryId)
        //    ->addFieldToFilter('main_table.region_id' ,$storeRegionId)->load();

        $collection = $this->_regionCollectionFactory->create()->addCountryFilter($storeCountryId)->load();

        $regionCollection=$collection->getData();
        foreach ($regionCollection as $row) {
            $id = $row['region_id'];
            $name= $row['name'];
            $regionarray[$id]=$name;
        }
        return $regionarray;
    }

    protected function _prepareForm()
    {
        $storeCountryId =  $this->_helperData->getGuestCustCountryId();

        $storeRegionId =  $this->_helperData->getGuestCustRegionId();

        $form = $this->_formFactory->create();

        $baseFieldset = $form->addFieldset('fieldset_customer_address', ['legend' => __('Address'), 'class' => 'admin__fieldset']);

        $baseFieldset->addField(
            'customer_address_firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('Firstname'),
                'id' => 'firstname',
                'title' => __('Firstname')
            ]
        );

        $baseFieldset->addField(
            'customer_address_lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Lastname'),
                'id' => 'lastname',
                'title' => __('Lastname')
            ]
        );

        $baseFieldset->addField(
            'customer_address_company',
            'text',
            [
                'name' => 'company',
                'label' => __('Company'),
                'id' => 'company',
                'title' => __('Company')
            ]
        );

        $baseFieldset->addField(
            'customer_address_street0',
            'text',
            [
                'name' => 'street0',
                'label' => __('Street'),
                'id' => 'street0',
                'title' => __('Street')
            ]
        );

        $baseFieldset->addField(
            'customer_address_street1',
            'text',
            [
                'name' => 'street1',
                'label' => __('Street'),
                'id' => 'street1',
                'title' => __('Street')
            ]
        );

        $baseFieldset->addField(
            'customer_address_city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'id' => 'city',
                'title' => __('City')
            ]
        );

//        $baseFieldset->addField(
//            'customer_address_region',
//            'text',
//            [
//                'name' => 'region',
//                'label' => __('region'),
//                'id' => 'region',
//                'title' => __('Region'),
//                'value' => 'test'
//            ]
//        );

        $baseFieldset->addField(
            'customer_address_region',
            'select',
            [
                'name'  => 'region',
                'label' => __('Region'),
                'title' => __('Region'),
                'id' => 'region',
                'options' => $this->getRegionOptionsSel(),
                'value' => $storeRegionId,
                //'readonly' => true,
                'disabled' => true,
            ]
        );

        $baseFieldset->addField(
            'customer_address_postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'id' => 'postcode',
                'title' => __('Postcode')
            ]
        );

        $baseFieldset->addField(
            'customer_address_country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->_countryLists->create()->toOptionArray(),
                'class' => 'select',
                'value' => $storeCountryId,
               // 'readonly' => true,
                'disabled' => true,
            ]
        );

        $baseFieldset->addField(
            'customer_address_telephone',
            'text',
            [
                'name' => 'telephone',
                'label' => __('Telephone'),
                'id' => 'telephone',
                'title' => __('Telephone')
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }


}
