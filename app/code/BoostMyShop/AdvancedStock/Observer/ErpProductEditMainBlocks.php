<?php

namespace BoostMyShop\AdvancedStock\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ErpProductEditMainBlocks implements ObserverInterface
{
    protected $_eventManager;

    public function execute(EventObserver $observer)
    {
        $block = $observer->getEvent()->getblock();
        $product = $observer->getEvent()->getProduct();
        $layout = $observer->getEvent()->getLayout();

        $blockContent = $layout->createBlock('BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview\StockSettingsPerWebsite');
        $blockContent->setProduct($product);
        $block->addBlock('Stock settings per website', $blockContent->toHtml());

        $blockContent = $layout->createBlock('BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview\Warehouses');
        $blockContent->setProduct($product);
        $block->addBlock('Warehouses', $blockContent->toHtml());

        $blockContent = $layout->createBlock('BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview\NewStockMovement');
        $blockContent->setProduct($product);
        $block->addBlock('New Stock Movement', $blockContent->toHtml());

        $blockContent = $layout->createBlock('BoostMyShop\AdvancedStock\Block\ErpProduct\Edit\Overview\SalesHistory');
        $blockContent->setProduct($product);
        $block->addBlock('Sales History', $blockContent->toHtml());

        return $this;
    }
}
