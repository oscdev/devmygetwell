<?php

namespace BoostMyShop\Supplier\Controller\Po;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Framework\App\Action\Action
{

    protected $registry;
    protected $resultPageFactory;
    protected $httpContext;
    protected $_customerSession;
    protected $_adminNotification;
    protected $_orderFactory;


    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \BoostMyShop\Supplier\Model\OrderFactory $orderFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->registry = $registry;
        $this->_orderFactory = $orderFactory;
        $this->httpContext = $httpContext;

    }

    public function execute()
    {
        $poId = $this->getRequest()->getParam('po_id');
        $token = $this->getRequest()->getParam('token');

        $po = $this->_orderFactory->create()->load($poId);
        if (($token) && ($token == $po->getToken()))
        {
            $pdf = $this->_objectManager->create('BoostMyShop\Supplier\Model\Pdf\Order')->getPdf([$po]);
            return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
                'purchase_order_' . $po->getpo_reference(). '.pdf',
                $pdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }

    }

}
