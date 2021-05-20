<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Stat;

class Settings extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Stat
{

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue('stat');
        $result = [];

        try
        {

            $this->_posRegistry->changeStatFromDate($data['from']);
            $this->_posRegistry->changeStatToDate($data['to']);

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
