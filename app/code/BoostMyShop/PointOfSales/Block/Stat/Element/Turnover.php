<?php
namespace BoostMyShop\PointOfSales\Block\Stat\Element;

class Turnover extends AbstractElement
{
    protected $_template = 'Stat/Element/Turnover.phtml';

    protected $_rsrcTurnover;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Stat\TurnOver $rsrcTurnover,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = [])
    {
        parent::__construct($context, $registry, $posRegistry, $priceHelper, $data);

        $this->_rsrcTurnover = $rsrcTurnover;
    }

    public function getValues()
    {
        $values = $this->_rsrcTurnover->getTurnOver($this->_posRegistry->getCurrentStoreId(), $this->_posRegistry->getStatFromDate(), $this->_posRegistry->getStatToDate());
        return $values;
    }


}