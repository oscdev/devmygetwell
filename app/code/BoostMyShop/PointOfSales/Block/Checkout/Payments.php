<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class Payments extends AbstractCheckout
{
    protected $_template = 'Checkout/Payments.phtml';

    public function invoiceByDefault()
    {
        return true;
    }

    public function shipmentByDefault()
    {
        return true;
    }

}