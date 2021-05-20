<?php
namespace BoostMyShop\PointOfSales\Block\Stat\Element;

class Sellers extends AbstractElement
{
    protected $_template = 'Stat/Element/Sellers.phtml';

    protected $_rsrcSellers;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Stat\Sellers $rsrcSellers,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = [])
    {
        parent::__construct($context, $registry, $posRegistry, $priceHelper, $data);

        $this->_rsrcSellers = $rsrcSellers;
    }

    public function getItems()
    {
        return $this->_rsrcSellers->getItems($this->_posRegistry->getCurrentStoreId(), $this->_posRegistry->getStatFromDate(), $this->_posRegistry->getStatToDate());
    }


}