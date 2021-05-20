<?php
namespace BoostMyShop\PointOfSales\Block\Stat;

class Index extends \Magento\Backend\Block\Template
{
    protected $_template = 'Stat/Index.phtml';

    protected $_coreRegistry = null;
    protected $_posRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_posRegistry = $posRegistry;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getFromDate()
    {
        return str_replace(' 00:00:00', '', $this->_posRegistry->getStatFromDate());
    }

    public function getToDate()
    {
        return str_replace(' 23:59:59', '', $this->_posRegistry->getStatToDate());
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/stat/settings');
    }

    public function getZReportUrl()
    {
        return $this->getUrl('*/stat/zreport');
    }

}