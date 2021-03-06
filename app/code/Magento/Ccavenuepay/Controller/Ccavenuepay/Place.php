<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Ccavenuepay\Controller\Ccavenuepay;

use Magento\Ccavenuepay\Controller\Ccavenuepay;
use Magento\Ccavenuepay\Helper\DataFactory;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Payment\Model\IframeConfigProvider;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Class Place
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Place extends Ccavenuepay {

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $onepageCheckout;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataFactory $dataFactory
     * @param CartManagementInterface $cartManagement
     * @param Onepage $onepageCheckout
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
    Context $context,
    // Registry $coreRegistry,
            \Magento\Checkout\Model\Session $coreRegistry,
    //DataFactory $dataFactory,
            \Magento\Sales\Model\OrderFactory $dataFactory,
    //CartManagementInterface $cartManagement,
            \Magento\Ccavenuepay\Model\Ccavenuepay $cartManagement,
    //Onepage $onepageCheckout,
            \Magento\Ccavenuepay\Helper\Checkout $onepageCheckout,
    //JsonHelper $jsonHelper
            \Magento\Framework\View\Result\PageFactory $jsonHelper
    ) {
       /* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->logger->info("Place extends Ccavenuepay=====");*/

        $this->eventManager = $context->getEventManager();
        $this->cartManagement = $cartManagement;
        $this->onepageCheckout = $onepageCheckout;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $coreRegistry, $dataFactory, $cartManagement, $onepageCheckout, $logger, $jsonHelper);
    }

    /**
     * Send request to authorize.net
     *
     * @return string
     */
    public function execute() {
        /*$this->logger->info("Place extends Ccavenuepay=====execute");*/
        $paymentParam = $this->getRequest()->getParam('payment');
        $controller = $this->getRequest()->getParam('controller');
        $response = $this->getResponse();

        if (isset($paymentParam['method'])) {
            $this->_getDirectPostSession()->setQuoteId($this->_getCheckout()->getQuote()->getId());
            /**
             * Current workaround depends on Onepage checkout model defect
             * Method Onepage::getCheckoutMethod performs setCheckoutMethod
             */
            $this->onepageCheckout->getCheckoutMethod();

            if ($controller == IframeConfigProvider::CHECKOUT_IDENTIFIER) {
                return $this->placeCheckoutOrder();
            }

            $params = $this->dataFactory
                    ->create(DataFactory::AREA_FRONTEND)
                    ->getSaveOrderUrlParams($controller);
            $this->_forward(
                    $params['action'], $params['controller'], $params['module'], $this->getRequest()->getParams()
            );
        } else {
            $result = ['error_messages' => __('Please choose a payment method.'), 'goto_section' => 'payment'];
            if ($response instanceof Http) {
                $response->representJson($this->jsonHelper->jsonEncode($result));
            }
        }
    }

    /**
     * Place order for checkout flow
     *
     * @return string
     */
    protected function placeCheckoutOrder() {
        /*$this->logger->info("Place extends Ccavenuepay=====placeCheckoutOrder");*/
        $result = new DataObject();
        $response = $this->getResponse();
        try {
            $this->cartManagement->placeOrder($this->_getCheckout()->getQuote()->getId());
            $result->setData('success', true);
            $this->eventManager->dispatch(
                    'checkout_ccavenuepay_placeOrder', [
                'result' => $result,
                'action' => $this
                    ]
            );
        } catch (\Exception $exception) {
            $result->setData('error', true);
            $result->setData('error_messages', __('Cannot place order.'));
        }
        if ($response instanceof Http) {
            $response->representJson($this->jsonHelper->jsonEncode($result));
        }
    }

}
