<?php
namespace BoostMyShop\PointOfSales\Block\Stat\Element;

class Payments extends AbstractElement
{
    protected $_template = 'Stat/Element/Payments.phtml';

    protected $_rsrcPayments;
    protected $_openingFactory;
    protected $_config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\Config $config,
        \BoostMyShop\PointOfSales\Model\OpeningFactory $openingFactory,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Stat\Payments $rsrcPayments,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = [])
    {
        parent::__construct($context, $registry, $posRegistry, $priceHelper, $data);

        $this->_rsrcPayments = $rsrcPayments;
        $this->_openingFactory = $openingFactory;
        $this->_config = $config;
    }

    public function getItems()
    {
        $items = $this->_rsrcPayments->getItems($this->_posRegistry->getCurrentStoreId(), $this->_posRegistry->getStatFromDate(), $this->_posRegistry->getStatToDate());

        return $items;
    }

    public function getOpeningValue()
    {
        $from = $this->_posRegistry->getStatFromDate();
        $to = $this->_posRegistry->getStatToDate();

        list($from, $fake) = explode(" ", $from);
        list($to, $fake) = explode(" ", $to);

        if ($from == $to)
        {
            $storeId = $this->_posRegistry->getCurrentStoreId();
            $opening = $this->_openingFactory->create()->loadByStoreDate($storeId, $from);
            return $opening->getpo_amount();
        }
        else
            return 0;

    }

}