<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Country;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index.
 */
class Index extends BaseIndex
{
    /**
     * Result Page.
     *
     * @var Page
     */
    private $resultPage;

    /**
     * Country Index Page Loader.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->resultPage = $this->resultPageFactory->create();

        $this->resultPage->setActiveMenu(
            'Oscprofessionals_OscShop::oscshop'
        );

        $this->resultPage->getConfig()->getTitle()
            ->set((__('Advance Directory')));

        return $this->resultPage;
    }
}
