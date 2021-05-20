<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_checkout_index');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
