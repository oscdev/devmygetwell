<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Stat;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

class ZReport extends \Magento\Backend\App\AbstractAction
{

    protected $_coreRegistry;
    protected $_resultLayoutFactory;
    protected $_posRegistry;
    protected $_zReport;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\Pdf\Zreport $zReport,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory

    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_posRegistry = $posRegistry;
        $this->_zReport = $zReport;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $settings = [];
        $settings['register'] = $this->_posRegistry->getCurrentStore()->getName().' / '.$this->_posRegistry->getCurrentUser()->getName();
        $settings['store_id'] = $this->_posRegistry->getCurrentStoreId();
        $settings['user_id'] = $this->_posRegistry->getCurrentUserId();
        $settings['from'] = $this->_posRegistry->getStatFromDate();
        $settings['to'] = $this->_posRegistry->getStatToDate();

        $pdf = $this->_zReport->getPdf($settings);
        return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
            'zreport_' . date('Y-m-d'). '.pdf',
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );

    }
}
