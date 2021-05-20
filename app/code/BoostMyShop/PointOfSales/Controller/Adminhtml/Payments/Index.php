<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Payments;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Payments
{

    /**
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_payments_index');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
