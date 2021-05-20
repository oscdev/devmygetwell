<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class ChangeUser extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
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

            $userId = $data['user_id'];
            $this->_registry->changeCurrentUserId($userId);

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
