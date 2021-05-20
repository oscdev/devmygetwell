<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class CustomerInformation extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $result = [];

        try
        {
            $customerId = $data['customer_id'];

            switch($customerId)
            {
                case 'guest':
                    $result = $this->getGuestInformation();
                    break;
                default:
                    $result = $this->getCustomerInformation($customerId);
                    break;
            }

            if (!isset($result['address']))
                $result['address'] = $this->getDefaultAddress();

            $result['success'] = true;
        }
        catch(\Exception $ex)
        {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            $result['stack'] = $ex->getTraceAsString();
        }


        die(json_encode($result));
    }

    protected function getCustomerInformation($customerId)
    {
        $customer = $this->_customerFactory->create()->load($customerId);
        $result = [];

        if (!$customerId)
            throw new \Exception('This customer does not exist');

        $result['customer'] = $customer->getData();
        $result['customer']['mode'] = 'customer';

        if ($customer->getDefaultBillingAddress())
            $result['address'] = $customer->getDefaultBillingAddress()->getData();
        else
        {
            if ($customer->getDefaultShippingAddress())
                $result['address'] = $customer->getDefaultShippingAddress()->getData();
        }

        return $result;
    }

    protected function getGuestInformation()
    {
        $result = [];

        $result['customer'] = [];
        $result['customer']['mode'] = 'guest';
        $result['customer']['entity_id'] = '';
        $result['customer']['email'] = '';
        $result['customer']['taxvat'] = '';
        $result['customer']['group_id'] = '';
        $result['customer']['website_id'] = '';

        return $result;
    }

    protected function getDefaultAddress()
    {
        $address = [];

        $address['firstname'] = "";
        $address['lastname'] = "";
        $address['company'] = "";
        $address['region'] = "";
        $address['country_id'] = "";
        $address['city'] = "";
        $address['postcode'] = "";
        $address['telephone'] = "";

        return $address;
    }
}
