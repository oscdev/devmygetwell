<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\AppForm;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class AfterSend.
 */
class AfterSend extends BaseIndex
{
    /**
     * Result Page.
     *
     * @var \Magento\Framework\View\Result\Page
     */
    private $resultPage;

    /**
     * After sending mail and curl request.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->resultPage = $this->resultPageFactory->create();

        /* @noinspection PhpUndefinedMethodInspection */
        $this->resultPage->setActiveMenu(
            'Oscprofessionals_OscShop::oscshop'
        );

        $this->resultPage->getConfig()->getTitle()
            ->set((__('Thank you !!!')));

        return $this->resultPage;
    }
}
