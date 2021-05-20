<?php
namespace BoostMyShop\BarcodeInventory\Controller\Adminhtml\Main;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
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

        $stockUpdater = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\StockUpdater');

        $warehouseId = $this->_barcodeInventoryRegistry->getCurrentWarehouseId();

        $changes = explode(';', $this->getRequest()->getPost('changes'));
        foreach($changes as $change)
        {
            if (!$change)
                continue;

            list($productId, $qty) = explode('=', $change);

            $stockUpdater->updateStock($productId, $warehouseId, $qty);
        }

        $this->messageManager->addSuccess(__('Inventory updated'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/Index');

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostmyshop_Barcodeinventory::Main');
    }

}