<?php

namespace BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview;

class StockSettingsPerWebsite extends \Magento\Backend\Block\Template
{
    protected $_template = 'ErpProduct/Edit/Overview/StockSettingsPerWebsite.phtml';

    protected $_stockWebsiteCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BoostMyShop\AdvancedStock\Model\ResourceModel\StockWebsite\CollectionFactory $stockWebsiteCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_stockWebsiteCollectionFactory = $stockWebsiteCollectionFactory;
    }

    public function getRecords()
    {
        return $this->_stockWebsiteCollectionFactory->create()->addProductFilter($this->getProduct()->getId());
    }

}