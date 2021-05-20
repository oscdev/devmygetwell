<?php
namespace BoostMyShop\PointOfSales\Block;

class Content extends \Magento\Backend\Block\Template
{
    protected $_template = 'Content.phtml';

    protected $_coreRegistry = null;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}