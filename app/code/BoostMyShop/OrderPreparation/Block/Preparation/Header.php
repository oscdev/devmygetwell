<?php
namespace BoostMyShop\OrderPreparation\Block\Preparation;

class Header extends \Magento\Backend\Block\Template
{
    protected $_template = 'OrderPreparation/Preparation/Header.phtml';

    protected $_coreRegistry = null;
    protected $_preparationRegistry;
    protected $_config = null;
    protected $_userCollection = null;
    protected $_warehouses;
    protected $_request;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\OrderPreparation\Model\Config $config,
                                \Magento\User\Model\ResourceModel\User\Collection $userCollection,
                                \BoostMyShop\OrderPreparation\Model\Registry $preparationRegistry,
                                \BoostMyShop\OrderPreparation\Model\Config\Source\Warehouses $warehouses,
                                array $data = [],
                                \Magento\Framework\App\Request\Http $request
    )
    {
        parent::__construct($context, $data);
        $this->_config = $config;
        $this->_coreRegistry = $registry;
        $this->_userCollection = $userCollection;
        $this->_preparationRegistry = $preparationRegistry;
        $this->_warehouses = $warehouses;
        $this->_request = $request;
    }

    public function getSteps()
    {
        $steps = [];

        $steps[] = ['label' => 'Orders Selection', 'action' => "setLocation('".$this->getUrl('*/preparation/index')."')"];

        if ($this->_config->getSetting('steps/picking'))
            $steps[] = ['label' => 'Picking', 'action' => "setLocation('".$this->getUrl('*/preparation/pickingList')."')"];
        if ($this->_config->getSetting('steps/packing'))
            $steps[] = ['label' => 'Packing', 'action' => "setLocation('".$this->getUrl('*/packing/index')."')"];
        if ($this->_config->getSetting('steps/create'))
            $steps[] = ['label' => 'Mass create shipments & invoices', 'action' => "setLocation('".$this->getUrl('*/preparation/massCreate')."')"];
        if ($this->_config->getSetting('steps/download'))
            $steps[] = ['label' => 'Download PDFs', 'action' => "setLocation('".$this->getUrl('*/preparation/downloadDocuments')."')"];
        if ($this->_config->getSetting('steps/shipping'))
            $steps[] = ['label' => 'Shipping', 'action' => "setLocation('".$this->getUrl('*/shipping/index')."')"];
        $steps[] = ['label' => 'Flush shipped orders', 'action' => "setLocation('".$this->getUrl('*/preparation/flush')."')"];

        return $steps;
    }

    public function getOperators()
    {
        return $this->_userCollection;
    }

    public function getCurrentOperatorId()
    {
        return $this->_preparationRegistry->getCurrentOperatorId();
    }

    public function getWarehouses()
    {
        return $this->_warehouses->toOptionArray();
    }

    public function getCurrentWarehouseId()
    {
        return $this->_preparationRegistry->getCurrentWarehouseId();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/saveRegistry');
    }

    public function showScopeForm()
    {
        return ($this->_request->getControllerName() == 'preparation');
    }

}