<?php

namespace Oscprofessionals\PaymentPaypal\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;;

class Select extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Oscprofessionals_PaymentPaypal::system/config/select.phtml';

    /**
     * @var null
     */
    protected $_values = null;
    /**
     * @var \Oscprofessionals\OscShop\Helper\Data
     */
    private $dataHelper;

    /**
     * Checkbox constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Oscprofessionals\OscShop\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }
    /**
     * Retrieve element HTML markup.
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml();
    }

    /**
     * @return array
     */
   public function getDefaultPaypalValues()
    {
        $values = $this->dataHelper->getPaypalConfigurationDetails();
        return $values;
    }
}
