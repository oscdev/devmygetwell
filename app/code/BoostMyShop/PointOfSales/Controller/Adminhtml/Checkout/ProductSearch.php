<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class ProductSearch extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $queryString = $this->getRequest()->getParam('search_string');
        $this->_coreRegistry->register('pos_currency_search', $queryString);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_checkout_productsearchresult');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
