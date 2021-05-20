<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class ChangeStore extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
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

            $storeId = $data['store_id'];
            $this->_registry->changeCurrentStoreId($storeId);

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
