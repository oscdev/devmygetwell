<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Stat;

class Index extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Stat
{

    /**
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('pointofsales_stat_index');
        $result = $resultPage->getLayout()->renderElement('content');
        return $this->_resultRawFactory->create()->setContents($result);
    }
}
