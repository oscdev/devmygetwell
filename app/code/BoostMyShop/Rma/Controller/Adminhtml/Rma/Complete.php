<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Complete extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $rmaId = $data['rma_id'];
        $rma = $this->_rmaFactory->create()->load($rmaId);

        try
        {
            $hasRefund = false;
            $refundData = ['items' => [], 'qtys' => []];
            $refundData['shipping_amount'] = ($data['shipping'] ? $rma->getOrder()->getshipping_amount() : 0);
            if ($refundData['shipping_amount'])
                $hasRefund = true;
            $refundData['adjustment_positive'] = $data['adjustment'];
            $refundData['adjustment_negative'] = $data['fee'];
            $refundData['do_offline'] = 1;
            $refundData['send_email'] = 1;
            $refundData['comment_text'] = 'Rma #'.$rma->getrma_reference();
            foreach($data['items'] as $itemId => $itemData)
            {
                $rmaItem = $this->_rmaItemFactory->create()->load($itemId);
                if ($itemData['refund'])
                {
                    $hasRefund = true;
                    $refundData['items'][$rmaItem->getri_order_item_id()] = ['qty' => $itemData['refund']];
                }
                else
                    $refundData['items'][$rmaItem->getri_order_item_id()] = ['qty' => "0"];
                $refundData['qtys'][$rmaItem->getri_order_item_id()] = $itemData['refund'];
            }

            if ($hasRefund)
                $rma->processRefund($refundData);

            //process back to stock
            foreach($data['items'] as $itemId => $itemData)
            {
                if ($itemData['qty_back_to_stock'] > 0)
                    $rma->itemBackToStock($itemId, $itemData['qty_back_to_stock'], $itemData['warehouse']);
            }

            $rma->complete();

            $this->messageManager->addSuccess('Rma successfully completed');
            $this->_redirect('*/*/edit', ['rma_id' => $rmaId]);
        }
        catch(\Exception $ex)
        {
            $this->messageManager->addError($ex->getMessage());
            $this->_redirect('*/*/process', ['rma_id' => $rmaId]);
        }



    }
}
