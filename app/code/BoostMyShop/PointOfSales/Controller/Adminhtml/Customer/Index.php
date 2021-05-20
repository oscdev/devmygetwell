<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Customer;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Customer
{

    /**
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_customer_index');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
