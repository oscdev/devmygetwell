<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class CustomerSearch extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
