<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Downloadapk;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class Index.
 */
class Index extends BaseIndex
{
    /**
     * Result Page.
     *
     * @var \Magento\Framework\View\Result\Page
     */
    private $resultPage;

    /**
     * Apk details form controller.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->resultPage = $this->resultPageFactory->create();

        $this->resultPage->setActiveMenu(
            'Oscprofessionals_OscShop::oscshop'
        );

        $this->resultPage->getConfig()->getTitle()
            ->set((__('Download APK')));

        return $this->resultPage;
    }
}
