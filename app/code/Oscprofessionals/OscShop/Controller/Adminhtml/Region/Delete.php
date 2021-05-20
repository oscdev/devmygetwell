<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;

/**
 * Class Delete.
 */
class Delete extends BaseIndex
{
    /**
     * Delete Region action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $regionId = $this->getRequest()->getParam('region_id');

        $countryId = '';
        try {
            $region = $this->regionFactory->create()->load($regionId);

            $countryId = $region->getDataByKey('country_id');

            $region->delete();

            $this->messageManager->addSuccess(
                __('Region deleted successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $this->_redirect('*/*/', ['country_id' => $countryId]);
        }

        return $this->_redirect('*/*/', ['country_id' => $countryId]);
    }
}
