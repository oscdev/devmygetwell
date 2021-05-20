<?php

namespace BoostMyShop\AvailabilityStatus\Block\Adminhtml\System\Config;

class LeadTimeRanges extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BoostMyShop\AvailabilityStatus\Model\Config $config,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_config = $config;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/leadTimeRanges.phtml');
        }
        return $this;
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        return $this->_toHtml();
    }

    public function getFieldValue($type, $index)
    {
        $scopeId = 0;
        return $this->_config->getSetting('backorder/'.$type.'_'.$index, $scopeId);
    }

}
