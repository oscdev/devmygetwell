<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class SaveOpening extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $openingValue = $this->getRequest()->getParam('opening_value');
        $result = [];

        try
        {
            $storeId = $this->_registry->getCurrentStoreId();
            $date = date('Y-m-d');

            $opening = $this->_openingFactory->create()->loadByStoreDate($storeId, $date);
            $opening->setpo_amount($openingValue)->save();

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
