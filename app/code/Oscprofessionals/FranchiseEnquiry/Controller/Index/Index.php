<?php
/*
* @author      Oscprofessionals Team (support@oscprofessionals.com)
* @copyright   Copyright (c) 2017 Oscprofessionals (http://www.oscprofessionals.com)
* @category    Oscprofessionals
* @package     Oscprofessionals_FranchiseEnquiry
*/
namespace Oscprofessionals\FranchiseEnquiry\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $resultForwardFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        //$resultPage->getConfig()->getTitle()->set(__('Franchise Enquiry Form'));
        return $resultPage;
    }
}
