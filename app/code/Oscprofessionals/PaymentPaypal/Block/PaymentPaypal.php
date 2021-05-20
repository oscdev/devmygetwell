<?php
namespace Oscprofessionals\PaymentPaypal\Block;

class PaymentPaypal extends \Magento\Framework\View\Element\Template
{
    /**
     * PaymentPaypal constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }


    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }
}
