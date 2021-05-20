<?php
/**
 * Callback
 * 
 * @author Slava Yurthev
 */
namespace Oscprofessionals\FranchiseEnquiry\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Oscprofessionals\FranchiseEnquiry\Model\FranchiseEnquiryFactory;
class Save extends \Magento\Framework\App\Action\Action {

    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';

	protected $_resultPageFactory;
	protected $_modelFranchiseEnquiryFactory;
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
    protected $_regionColl;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

	public function __construct(
	    Context $context,
        PageFactory $resultPageFactory,
        FranchiseEnquiryFactory $modelFranchiseEnquiryFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Directory\Model\RegionFactory $regionFactory
    )
    {
		$this->_resultPageFactory = $resultPageFactory;
        $this->_modelFranchiseEnquiryFactory = $modelFranchiseEnquiryFactory;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->_regionFactory = $regionFactory;
		parent::__construct($context);
	}
	public function execute(){
		$resultRedirect = $this->resultRedirectFactory->create();
		$FranchiseEnquiryModel = $this->_modelFranchiseEnquiryFactory->create();
		//$params = $this->getRequest()->getPostValue();
        $data = $this->getRequest()->getPost();
        $region = $this->_regionFactory->create();
        $region->load($data['region']);
        $regionName = $region->getData('name');
        try {
            $FranchiseEnquiryModel->setData('name', $data['name']);
            $FranchiseEnquiryModel->setData('mobile', $data['mobile']);
            $FranchiseEnquiryModel->setData('email', $data['email']);
            $FranchiseEnquiryModel->setData('region', $data['region']);
            $FranchiseEnquiryModel->setData('city', $data['city']);
            $FranchiseEnquiryModel->setData('comment', $data['comment']);
            $FranchiseEnquiryModel->setData('captcha', $data['captcha']['franchise_enquiry_form']);

            $FranchiseEnquiryModel->save();
            /* Send Mail to customer start */
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $sender = [
                'name' => 'MyGetWellStore',
                'email' => 'franchise@mygetwellstore.com'
            ];

            $sentToEmail = $data['email'];

            $sentToName = $data['name'];


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier('franchise_enquiry_email_template')
                ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'name'  => $data['name'],
                    'mobile'  => $data['mobile'],
                    'email'  => $data['email'],
                    'region'  => $regionName,
                    'city'  => $data['city'],
                    'comment'  => $data['comment']
                ])
                ->setFrom($sender)
                ->addTo($sentToEmail,$sentToName)
                //->addTo('owner@example.com','owner')
                ->getTransport();

            $transport->sendMessage();

            $this->_inlineTranslation->resume();
            /* Send Mail to Customer End */
            /* Send Mail to admin start */
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $sender = [
                'name' => 'MyGetWellStore',
                'email' => 'franchise@mygetwellstore.com'
            ];

            $sentToEmail = $this->_scopeConfig->getValue(
                'trans_email/ident_general/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $sentToName = $this->_scopeConfig->getValue(
                'trans_email/ident_general/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            /*$sentToEmail = $data['email'];
            $sentToEmail = ['pranjali@oscprofessionals.com', 'pranjali@oscprofessionals.com'];

            $sentToName = $data['name'];*/

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier('franchise_enquiry_admin_email_template')
                ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'name'  => $data['name'],
                    'mobile'  => $data['mobile'],
                    'email'  => $data['email'],
                    'region'  => $regionName,
                    'city'  => $data['city'],
                    'comment'  => $data['comment']
                ])
                ->setFrom($sender)
                ->addTo($sentToEmail,$sentToName)
                ->addCc('mygetwellstore@gmail.com')
                //->addTo('owner@example.com','owner')
                ->getTransport();

            $transport->sendMessage();

            $this->_inlineTranslation->resume();
            /* Send Mail to admin End */

            $this->_redirect('franchiseenquiry/index');
            $this->messageManager->addSuccess(__('The Franchise Enquiry has been Submitted'));
		} catch (\Exception $e) {
			$this->messageManager->addException($e, __('Something went wrong.'));
		}
		return $resultRedirect->setPath($this->_redirect->getRefererUrl());
	}
}