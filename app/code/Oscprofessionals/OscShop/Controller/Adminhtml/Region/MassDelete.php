<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class MassDelete.
 */
class MassDelete extends BaseIndex
{
    /**
     * Region Mass Delete action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $regionIds = $this->getRequest()->getParam('region_id');

        if (empty($regionIds)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            try {
                foreach ($regionIds as $regionId) {
                    $this->regionFactory->create()->load($regionId)
                        ->delete();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
                        count($regionIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
