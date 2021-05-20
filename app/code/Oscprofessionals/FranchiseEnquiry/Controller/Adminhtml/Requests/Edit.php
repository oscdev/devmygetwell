<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Controller\Adminhtml\Requests;
use Magento\Backend\App\Action;
class Edit extends \Magento\Backend\App\Action{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
    protected function _isAllowed(){
        return $this->_authorization->isAllowed('Oscprofessionals_FranchiseEnquiry::requests');
    }
    protected function _initAction(){
        $resultPage = $this->resultPageFactory->create();
        /*$resultPage->setActiveMenu('Oscprofessionals_FranchiseEnquiry::header')
            ->addBreadcrumb(__('Requests'), __('Requests'))
            ->addBreadcrumb(__('Edit'), __('Edit'));*/

        return $resultPage;
    }
    public function execute(){
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Oscprofessionals\FranchiseEnquiry\Model\FranchiseEnquiry');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('franchise_enquiry_request', $model);
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Franchise Enquiry Report'));
        $resultPage->getConfig()->getTitle()
            ->prepend(__('Edit'));


        return $resultPage;
    }
}