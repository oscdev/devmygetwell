<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\AppForm;

use Magento\Backend\App\Action;
use Magento\Framework\HTTP\Client\Curl;

/**
 * Class SendData.
 */
class SendData extends Action
{
    /**
     * Url link to the oscprofessionals server.
     *
     * @const OSC_URL
     */
    const OSC_URL = 'http://49.248.5.148/development/osc-shop/index.php';

    /**
     * Curl object.
     *
     * @var Curl
     */
    private $curl;

    /**
     * Main Constructor.
     *
     * @param Action\Context $context
     * @param Curl           $curl
     */
    public function __construct(
        Action\Context $context,
        Curl $curl
    ) {
    
        $this->curl = $curl;

        parent::__construct($context);
    }

    /**
     * Send Data using Curl action.
     *
     * @return void.
     */
    public function execute()
    {
        $params = $this->_session->getData();

        if (!$params) {
            $this->_redirect('*/*/');
            return;
        }

        try {
            $this->curl->post(self::OSC_URL, $params);

            /** @noinspection PhpUnusedLocalVariableInspection */
            $responseStatus = $this->curl->getStatus();

            if ($responseStatus = 200) {
                $this->messageManager->addSuccess(
                    __('Data is Submitted Successfully.')
                );
                /** @noinspection PhpUndefinedMethodInspection */
                $this->_session->setData(false);
            } else {
                $this->messageManager->addError(
                    __('Please check the apk information provided.')
                );

                /** @noinspection PhpUndefinedMethodInspection */
                $this->_session->setData($params);

                $this->_redirect('*/*/');
                return;
            }

            $this->_redirect($this->getUrl('*/*/aftersend'));
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );

            $this->_redirect('*/*/');
            return;
        }
    }
}
