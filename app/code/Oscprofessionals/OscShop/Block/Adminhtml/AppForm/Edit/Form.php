<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Block\Adminhtml\AppForm\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Form.
 */
class Form extends Generic
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('oscshop/appform/sendemail'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
