<?php
namespace BoostMyShop\PointOfSales\Block\Stat\Element;


class AbstractElement extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry;
    protected $_posRegistry;
    protected $_priceHelper;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_posRegistry = $posRegistry;
        $this->_priceHelper = $priceHelper;
    }

    public function formatCurrency($value)
    {
        return $this->_priceHelper->currency($value, true, false);
    }

}