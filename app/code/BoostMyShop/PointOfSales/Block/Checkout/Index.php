<?php
namespace BoostMyShop\PointOfSales\Block\Checkout;

class Index extends \Magento\Backend\Block\Template
{
    protected $_template = 'Checkout/Index.phtml';

    protected $_coreRegistry = null;
    protected $_openingFactory;
    protected $_config = null;
    protected $_posRegistry;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\OpeningFactory $openingFactory,
        \BoostMyShop\PointOfSales\Model\Config $config,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_posRegistry = $posRegistry;
        $this->_coreRegistry = $registry;
        $this->_openingFactory = $openingFactory;
        $this->_config = $config;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function showOpeningPopup()
    {
        if ($this->_config->isOpeningEnabled())
        {
            if (!$this->getCurrentOpening()->getId())
                return true;
        }

        return false;
    }

    protected function getCurrentOpening()
    {
        $storeId = $this->_posRegistry->getCurrentStoreId();
        $date = date('Y-m-d');

        return $this->_openingFactory->create()->loadByStoreDate($storeId, $date);
    }



}