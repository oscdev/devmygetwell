<?php
namespace BoostMyShop\Rma\Block\Rma;

class Process extends \Magento\Backend\Block\Template
{
    protected $_template = 'Rma/Process.phtml';
    protected $_coreRegistry = null;
    protected $_warehouse = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \BoostMyShop\Rma\Model\Warehouse $warehouse,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_warehouse = $warehouse;

        parent::__construct($context, $data);
    }

    public function getRma()
    {
        return $this->_coreRegistry->registry('current_rma');
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/complete');
    }

    public function getProductUrl($item)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $item->getri_product_id()]);
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/edit', ['rma_id' => $this->getRma()->getId()]);
    }

    public function getJsonData()
    {
        $data = [];

        $data['items'] = [];
        $data['shipping'] = $this->getRma()->getOrder()->getshipping_amount();
        foreach($this->getRma()->getAllItems() as $rmaItem)
        {
            if($rmaItem->getri_qty() == 0)
				continue; 
            
            $item = [];
            $item['id'] = $rmaItem->getId();
            $item['price'] = $rmaItem->getOrderItem()->getPrice();
            $data['items'][] = $item;
        }

        return json_encode($data);
    }

    public function getWarehouses()
    {
        return $this->_warehouse->getWarehouses();
    }


}
