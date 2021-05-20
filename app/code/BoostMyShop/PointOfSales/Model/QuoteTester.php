<?php

namespace BoostMyShop\PointOfSales\Model;

class QuoteTester
{
    protected $_quote;

    public function __construct(
        \BoostMyShop\PointOfSales\Model\Quote $quote,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Quote\Model\Cart\Currency $currencyFactory
    )
    {
        $this->_quote = $quote;
        $this->_storeFactory = $storeFactory;
        $this->_currencyFactory = $currencyFactory;
    }

    public function test()
    {
        //$currency = $this->_currencyFactory->create()->load('EUR');
        $store = $this->_storeFactory->create()->load(1);
        $this->_quote->initQuote($store, '');

        $this->_quote->addProduct(30, 2);
        $this->_quote->addProduct(321, 1);

        $result = $this->_quote->getResult();

        var_dump($result);

        die ("\nfin test\n");

    }

}