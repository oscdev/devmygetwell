<?php

namespace BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer;

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
    protected $_imageHelperFactory;
    protected $_registry;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,
                                \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
                                StoreManagerInterface $storemanager,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
        $this->_imageHelperFactory = $imageHelperFactory;
        $this->_registry = $registry;
    }

    public function getRma()
    {
        return $this->_registry->registry('current_rma');
    }

    public function render(DataObject $row)
    {
        $imageUrl = $this->_imageHelperFactory->create()->init($row->getProduct(), 'product_listing_thumbnail')->getUrl();

        if ($imageUrl)
            return '<img src="'.$imageUrl.'">';
        else
            return '';
    }

}