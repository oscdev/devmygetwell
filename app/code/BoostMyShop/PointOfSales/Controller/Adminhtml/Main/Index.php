<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Main;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Main
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Point of Sales'));
        $this->_view->renderLayout();
    }
}
