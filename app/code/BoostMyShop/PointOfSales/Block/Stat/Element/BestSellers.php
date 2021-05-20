<?php
namespace BoostMyShop\PointOfSales\Block\Stat\Element;

class BestSellers extends AbstractElement
{
    protected $_template = 'Stat/Element/BestSellers.phtml';

    protected $_rsrcBestSeller;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Stat\BestSellers $rsrcBestSeller,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = [])
    {
        parent::__construct($context, $registry, $posRegistry, $priceHelper, $data);

        $this->_rsrcBestSeller = $rsrcBestSeller;
    }

    public function getItems()
    {
        return $this->_rsrcBestSeller->getItems($this->_posRegistry->getCurrentStoreId(), $this->_posRegistry->getStatFromDate(), $this->_posRegistry->getStatToDate());
    }
}