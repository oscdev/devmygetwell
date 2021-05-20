<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Customer;

class View extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Customer
{

    /**
     * @return void
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        $customer = $this->_customerFactory->create()->load($customerId);
        $this->_coreRegistry->register('pos_current_customer', $customer);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_customer_view');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
