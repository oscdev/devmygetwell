<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Controller\Adminhtml\Requests;
use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
class Delete extends Action {
	protected $_resultPageFactory;
	protected $_resultPage;
	public function __construct(Context $context, PageFactory $resultPageFactory){
		parent::__construct($context);
		$this->_resultPageFactory = $resultPageFactory;
	}
	public function execute(){
		$id = $this->getRequest()->getParam('id');
		if($id>0){
			$model = $this->_objectManager->create('\Oscprofessionals\FranchiseEnquiry\Model\FranchiseEnquiry');
			$model->load($id);
			try {
				$model->delete();
				$this->messageManager->addSuccess(__('Deleted.'));
			} catch (\Exception $e) {
				$this->messageManager->addSuccess(__('Something went wrong.'));
			}
		}
		$this->_redirect('franchise_enquiry/requests');
	}
	protected function _isAllowed(){
		return $this->_authorization->isAllowed('Oscprofessionals_FranchiseEnquiry::requests');
	}
}