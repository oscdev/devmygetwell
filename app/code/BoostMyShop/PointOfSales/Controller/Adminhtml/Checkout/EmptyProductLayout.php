<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class EmptyProductLayout extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $result = $this->_emptyProductLayoutBlock->toHtml();
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
