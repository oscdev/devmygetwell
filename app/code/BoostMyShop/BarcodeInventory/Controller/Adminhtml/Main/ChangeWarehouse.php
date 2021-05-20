<?php
namespace BoostMyShop\BarcodeInventory\Controller\Adminhtml\Main;

use Magento\Framework\Controller\ResultFactory;

class ChangeWarehouse extends \Magento\Backend\App\Action
{
    protected $barcodeInventoryRegistry;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \BoostMyShop\BarcodeInventory\Model\Registry $barcodeInventoryRegistry
    ) {
        parent::__construct($context);

        $this->_barcodeInventoryRegistry = $barcodeInventoryRegistry;
    }

    public function execute()
    {

        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $this->_barcodeInventoryRegistry->setCurrentWarehouseId($warehouseId);

        $this->messageManager->addSuccess(__('Warehouse updated'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/Index');

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostmyshop_Barcodeinventory::Main');
    }

}