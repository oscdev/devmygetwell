<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class CreateCustomer extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try
        {

            $customer = $this->_customerFactory->create()->setData($data)->save();

            $result['customer_id'] = $customer->getId();
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

}
