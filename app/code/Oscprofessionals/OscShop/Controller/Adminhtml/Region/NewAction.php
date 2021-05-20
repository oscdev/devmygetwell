<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class NewAction.
 */
class NewAction extends BaseIndex
{
    /**
     * New Region Create action.
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
