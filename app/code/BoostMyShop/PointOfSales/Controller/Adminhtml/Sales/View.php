<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Sales;

class View extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Sales
{

    /**
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = $this->_orderFactory->create()->load($orderId);
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_sales_view');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
