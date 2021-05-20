<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Country;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class MassDelete.
 */
class MassDelete extends BaseIndex
{
    /**
     * Country Mass Delete Action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $countryIds = $this->getRequest()->getParam('country_id');
        if (!is_array($countryIds) || empty($countryIds)) {
            $this->messageManager->addError(
                __('Please select Country / Countries.')
            );
        } else {
            try {
                foreach ($countryIds as $countryId) {
                    $this->countryFactory->create()->load($countryId)->delete();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
                        count($countryIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->_redirect('*/*/');
    }
}
