<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\Region;

use Magento\Framework\Exception\LocalizedException;
use Oscprofessionals\OscShop\Controller\Adminhtml\BaseIndex;
use Magento\Framework\DataObject;

/**
 * Class Save.
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Save extends BaseIndex
{
    /**
     * Magento_Directory Region Model.
     *
     * @var \Magento\Directory\Model\Region
     */
    private $regionModel;

    /**
     * Magento_Directory Region Collection.
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    private $regionCollection;

    /**
     * Save Region Execute action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if ($data) {
            $regionId = $this->getRequest()->getParam('region_id');
            $name = $this->getRequest()->getParam('name');
            $defaultName = $this->getRequest()->getParam('default_name');

            try {
                $this->regionModel = $this->regionFactory->create();
                $this->regionCollection = $this->regionModel->getCollection();

                $update = false;

                $regionIds = $this->checkIfRegionIsAvailable($data)->getData();

                if (empty($regionIds)) {
                    if ($regionId) {
                        $this->regionModel->load($regionId);
                        if ($this->regionModel->getData('name')) {
                            $update = true;
                        }
                    }

                    $this->regionModel->setData($data);
                    $this->regionModel->save();

                    $bind = [
                        'locale' => 'en_US',
                        'name' => $name ? $name : $defaultName,
                        'region_id' => $regionId ? $regionId : $this->regionModel->getId(),
                    ];

                    $this->updateDirectoryCountryRegionNameTable($update, $bind);

                    $this->messageManager->addSuccess(
                        __('The Region Has been Saved Successfully.')
                    );

                    /* @noinspection PhpUndefinedMethodInspection */
                    $this->_session->setFormData(false);
                } else {
                    $this->messageManager->addError(
                        __('The Country & Region combination must be unique')
                    );
                }

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect(
                        '*/*/edit',
                        [
                            'region_id' => $this->regionModel->getId(),
                            '_current' => true,
                        ]
                    );
                }

                return $this->_redirect(
                    '*/*/',
                    [
                        'country_id' => $data['country_id'],
                        '_current' => true,
                    ]
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the region.')
                );
            }

            /* @noinspection PhpUndefinedMethodInspection */
            $this->_session()->setFormData($data);

            return $this->_redirect(
                '*/*/edit',
                ['region_id' => $this->getRequest()->getParam('region_id')]
            );
        }

        return $this->_redirect('*/*/');
    }

    /**
     * To check duplicate Entries of given region + country combination.
     *
     * @param array $data
     *
     * @return DataObject
     */
    private function checkIfRegionIsAvailable($data)
    {
        /** @var DataObject $regionIdObject */
        $regionIdObject = $this->objectFactory->create();

        $regionIds = [];
        if (!array_key_exists('region_id', $data)) {
            $regionIds =
                $regionCollection = $this->regionCollection
                    ->addRegionCodeFilter($data['code'])
                    ->addFieldToFilter('country_id', $data['country_id'])
                    ->load()->getAllIds();
        }

        $regionIdObject->setData($regionIds);

        return $regionIdObject;
    }

    /**
     * Updates the entries in the directory_country_region_name Table.
     *
     * @param bool  $update
     * @param array $bind
     *
     * @return bool
     */
    private function updateDirectoryCountryRegionNameTable($update, $bind)
    {
        $connection = $this->regionFactory->create()->getResource()
            ->getConnection();

        if ($update) {
            $connection->update(
                'directory_country_region_name',
                $bind,
                ['region_id = ?' => $bind['region_id']]
            );
        } else {
            $connection->insert('directory_country_region_name', $bind);
        }

        return true;
    }
}
