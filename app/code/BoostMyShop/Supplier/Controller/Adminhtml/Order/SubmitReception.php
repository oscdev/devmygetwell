<?php

namespace BoostMyShop\Supplier\Controller\Adminhtml\Order;

class SubmitReception extends \BoostMyShop\Supplier\Controller\Adminhtml\Order
{
    /**
     * @return void
     */
    public function execute()
    {

        $poId = $this->getRequest()->getParam('po_id');
        $model = $this->_orderFactory->create();
        $model->load($poId);

        $products = $this->getRequest()->getPost('products');

        $userName = '?';
        if ($this->_backendAuthSession->isLoggedIn())
            $userName =  $this->_backendAuthSession->getUser()->getUsername();

        $model->processReception($userName, $products);

        $this->messageManager->addSuccess(__('Reception saved.'));
        $this->_redirect('supplier/order/edit', ['po_id' => $poId]);
    }
}
