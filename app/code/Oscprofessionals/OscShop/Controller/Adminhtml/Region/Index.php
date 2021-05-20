<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

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
     * Region Index Page Action.
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

        $middleTitle = $this->getRequest()->getParam('country_id');

        $this->resultPage->getConfig()->getTitle()
            ->set((__('Manage '.$middleTitle.' Regions')));

        return $this->resultPage;
    }
}
