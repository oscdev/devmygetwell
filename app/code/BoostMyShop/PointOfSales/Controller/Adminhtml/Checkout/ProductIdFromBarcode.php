<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class ProductIdFromBarcode extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $barcode = $data['barcode'];

        try
        {
            $barcodeAttribute = $this->_config->getBarcodeAttribute();
            if (!$barcodeAttribute)
                throw new \Exception('No barcode attribute configured');

            $product = $this->_productFactory->create()->loadByAttribute($barcodeAttribute, $barcode);
            if (!$product)
                throw new \Exception('No product matching barcode "'.$barcode.'"');

            $result['product_id'] = $product->getId();
            $result['success'] = true;
        }
        catch(\Exception $ex)
        {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            $result['stack'] = $ex->getTraceAsString();
        }

        die(json_encode($result));
    }

}
