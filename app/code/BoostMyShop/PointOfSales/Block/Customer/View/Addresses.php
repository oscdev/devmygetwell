<?php
namespace BoostMyShop\PointOfSales\Block\Customer\View;

class Addresses  extends AbstractBlock
{

    public function getBillingAddress()
    {
        $address = $this->_accountManagement->getDefaultBillingAddress($this->getCustomer()->getId());
        return $this->_addressHelper->getFormatTypeRenderer(
            'html'
        )->renderArray(
            $this->_addressMapper->toFlatArray($address)
        );
    }

    public function getShippingAddress()
    {
        $address = $this->_accountManagement->getDefaultShippingAddress($this->getCustomer()->getId());
        return $this->_addressHelper->getFormatTypeRenderer(
            'html'
        )->renderArray(
            $this->_addressMapper->toFlatArray($address)
        );
    }

}
