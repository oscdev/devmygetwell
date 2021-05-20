<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class Edit.
 *
 * @method setData(array $value)
 * @method getDefaultName()
 */
class Edit extends BaseIndex
{
    /**
     * Region Edit Action.
     *
     * @return void
     */
    public function execute()
    {
        // 1. Get ID and create model
        $regionId = $this->getRequest()->getParam('region_id');

        // 2. Initial checking
        $region = '';

        if ($regionId) {
            $region = $this->regionFactory->create()->load($regionId);

            /* @noinspection PhpUndefinedMethodInspection */
            $this->_session->setFormData(false);

            if (!$region->getId()) {
                $this->messageManager
                    ->addError(__('This region no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        // 3. Set entered data if was error when we do save

        /* @noinspection PhpUndefinedMethodInspection */
        $data = $this->_session->getFormData();

        if (!empty($data)) {
            /** @noinspection PhpExpressionResultUnusedInspection */
            $region ? $region->setData($data): '';
        }

        $this->registry->register('current_oscshop_region_instance', $region);

        $this->_view->loadLayout();

        /* @noinspection PhpUndefinedMethodInspection */
        $this->_view->getLayout()->initMessages();

        if ($regionId) {
            $headerText = __('Edit '.$region->getDefaultName().' Region');
        } else {
            $headerText = __('Add New Region');
        }

        $this->_view->getPage()->getConfig()->getTitle()->prepend($headerText);
        $this->_view->renderLayout();
    }
}
