<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer;

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class AbstractRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
    protected $_imageHelper;
    protected $_registry;
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,
                                \Magento\Catalog\Helper\Image $imageHelper, StoreManagerInterface $storemanager,
                                \BoostMyShop\Supplier\Model\Config $config,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
        $this->_imageHelper = $imageHelper;
        $this->_registry = $registry;
        $this->_config = $config;
    }

    public function getOrder()
    {
        return $this->_registry->registry('current_purchase_order');
    }

}