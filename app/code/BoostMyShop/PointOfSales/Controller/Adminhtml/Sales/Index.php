<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Sales;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Sales
{

    /**
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_sales_index');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
