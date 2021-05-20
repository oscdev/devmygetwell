<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Helper;

use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;

/**
 * Class Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Module Enable config path.
     *
     * @const string XML_PATH_MODULE_ENABLED
     */
    const XML_PATH_MODULE_ENABLED = 'oscshop/oscshopsettings/is_enabled';
    const XML_PATH_BUSINESS_ACCOUNT = 'paypal/general/business_account';
    const XML_PATH_API_USERNAME = 'paypal/wpp/api_username';
    const XML_PATH_API_PASSWORD = 'paypal/wpp/api_password';
    const XML_PATH_API_SIGNATURE = 'paypal/wpp/api_signature';
    const XML_PATH_SANDBOX_MODE = 'paypal/wpp/sandbox_flag';
    const XML_PATH_INTEGRATION_API_TOKEN = 'oscshop/token_setting/api_token';
    const XML_PATH_REST_API_CLIENT_ID = 'payment/oscppayment/rest_api_client_id';
    const XML_PATH_REST_API_SECRET_KEY = 'payment/oscppayment/rest_api_secret_key';
    const XML_PATH_API_PAYMENT_MODE = 'payment/oscppayment/mode';
    const XML_PATH_API_PAYMENT_TITLE = 'payment/oscppayment/title';
    const XML_PATH_API_PAYMENT_METHOD_CODE = 'oscppayment';
    const XML_PATH_GUEST_CHECKOUT_ENABLED = 'checkout/options/guest_checkout';
    const XML_PATH_ENABLE_APITIMELOG = 'oscshop/oscshopsettings/enable_apitimelog';
    const XML_PATH_FREE_SHIPPING_AMOUNT = 'carriers/freeshipping/free_shipping_subtotal';
    const XML_PATH_FREE_SHIPPING_ENABLED = 'carriers/freeshipping/active';
    const XML_PATH_PAYMENT_MODULE_ENABLED = 'payment/oscppayment/active';

    /**
     * @var \Magento\Integration\Api\IntegrationServiceInterface
     */
    protected $_integrationService;
    /**
     * @var \Magento\Framework\Module\ModuleList
     */
    private $list;
    /**
     * @var
     */
    private $productRepository;
    /**
     * @var Config
     */
    protected $_paymentModelConfig;
    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;

    /**
     * Data constructor.
     * @param \Magento\Framework\Module\ModuleList $list
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param TokenCollectionFactory $tokenCollectionFactory
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     * @param Config $paymentModelConfig
     * @param \Magento\Integration\Api\IntegrationServiceInterface $integrationService
     */
    public function __construct(
        \Magento\Framework\Module\ModuleList $list,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        TokenCollectionFactory $tokenCollectionFactory,
        ScopeConfigInterface $appConfigScopeConfigInterface,
        Config $paymentModelConfig,
        \Magento\Integration\Api\IntegrationServiceInterface $integrationService
    ) {
        $this->moduleList = $list;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->collection = $tokenCollectionFactory->create();
        $this->_integrationService = $integrationService;
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
    }


    /**
     * @return mixed
     */
    public function getAutoIncrementId()
    {
        $entityId = [];
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection();
        foreach ($orderDatamodel as $orderData) {
            $entityId[] = $orderData->getEntityId();
        }
        $id = end($entityId);
        $incrementId = $id + 1;
        return $incrementId;
    }

    /**
     * Checks whether the module is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_MODULE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * @return mixed
     */
    public function getBusinessAccount()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUSINESS_ACCOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getApiUsername()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_USERNAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getApiPassword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_PASSWORD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getApiSignature()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_SIGNATURE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSandboxMode()
    {
        //XML_PATH_API_PAYMENT_MODE
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_PAYMENT_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPaymentTitle()
    {
        //XML_PATH_API_PAYMENT_TITLE
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_PAYMENT_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodCode()
    {
        //XML_PATH_API_PAYMENT_METHOD_CODE
        return self::XML_PATH_API_PAYMENT_METHOD_CODE;
    }

    /**
     * Generate log file
     *
     * @return bool
     */
    public function isEnabledApiTimeLog()
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_APITIMELOG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getPaymentMethodsList()
    {
        $enabledModules = $this->moduleList->getNames();
		$oscpModules = array();
        foreach ($enabledModules as $key => $value) {
            if (substr($value, 0, 24) == 'Oscprofessionals_Payment') {
                $oscpModules[] = $value;
            }
        }
        /*        if (($key = array_search('Oscprofessionals_OscShop', $oscpModules)) !== false) {
                    unset($oscpModules[$key]);
                }*/
        $paymentMethods = $this->getAllPaymentMethods();
        if ($oscpModules) {
            $is_paypal_payment_active = $this->scopeConfig->getValue(
                self::XML_PATH_PAYMENT_MODULE_ENABLED,
                ScopeInterface::SCOPE_STORE
            );
            // only if paypal payment method is active
            if ($is_paypal_payment_active) {
                $paypalConfigDetails = $this->getMcartPayPalPaymentsDetails();
                $paymentData = array_merge($paypalConfigDetails, $paymentMethods);
            } else {
                $paymentData = $paymentMethods;
            }

        } else {
            $paymentData = $paymentMethods;
        }
        return $paymentData;
    }

    /**
     * @return array
     */
    public function getAllPaymentMethods()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();

        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_appConfigScopeConfigInterface
                ->getValue('payment/'.$paymentCode.'/title');
            if (($paymentCode=="phoenix_cashondelivery") || ($paymentCode=="checkmo") || ($paymentCode=="banktransfer") || ($paymentCode=="cashondelivery")) {
                $methods[] = array(
                    'ccTypes'=> "",
                    "instruction"=> "",
                    'title' => $paymentTitle,
                    'code' => $paymentCode,
                    'min_order_total' => $paymentModel->getConfigData('min_order_total'),
                    'max_order_total' => $paymentModel->getConfigData('max_order_total')
                );
            }
        }
        return $methods;
    }

    /**
     * @return array
     */
    public function getMcartPayPalPaymentsDetails()
    {
        $paypalPaymentDetails = array();
        $clientId = $this->getApiClientId();
        $mode = $this->getSandboxMode();
        $title = $this->getPaymentTitle();
        $code = $this->getPaymentMethodCode();

        $paypalPaymentDetails[] = array(
            'ccTypes'=> "",
            'clientId'=>$clientId,
            'code'=> $code,
            'environment'=> $mode,
            'instruction'=> "You will be redirected to the PayPal when you place an order.",
            'privacy_policy_uri'=> "",
            'title'=> $title,
            'user_agreement_uri'=> "",
        );
        return $paypalPaymentDetails;
    }
    /**
     * @return array
     */
    public function getPaypalConfigurationDetails()
    {
        $details = array();
        $businessMail = $this->getBusinessAccount();
        $apiUsername = $this->getApiUsername();
        $apiPassword = $this->getApiPassword();
        $apiSignature = $this->getApiSignature();
        $sandboxMode = $this->getSandboxMode();
        $details[] = array(
            'business_mail'=>$businessMail,
            'api_username'=>$apiUsername,
            'api_password'=>$apiPassword,
            'api_signature'=>$apiSignature,
            'sandbox_mode'=>$sandboxMode
        );
        return $details;
    }
    public function getProductDetails($sku)
    {
        $collection = $this->productRepository->get($sku);
        $customAttributes = $collection->getCustomAttributes();
        $custom = [];
        foreach ($customAttributes as $key => $value) {
            $custom[] = array(
                'attribute_code' => $value->getAttributeCode(),
                'value'=> $value->getValue()
            );
        }
        $output = [];

        $output['id'] = $collection->getEntityId();
        $output['sku'] = $collection->getSku();
        $output['name'] = $collection->getName();
        $output['attribute_set_id'] = $collection->getAttributeSetId();
        $output['price'] = $collection->getPrice();
        $output['status'] = $collection->getStatus();
        $output['visibility'] = $collection->getVisibility();
        $output['type_id'] = $collection->getTypeId();
        $output['created_at'] = $collection->getCreatedAt();
        $output['updated_at'] = $collection->getUpdatedAt();
        $output['extension_attributes'] = $collection->getExtensionAttributes();
        $output['product_links'] = $collection->getProductLinks();
        $output['options'] = $collection->getOptions();
        $output['tier_prices'] = $collection->getTierPrices();
        $output['custom_attributes'] = $custom;

        return $output;
    }

    /**
     * @return array
     */
    public function getTokenDetails()
    {
        $options = [];
        foreach ($this->collection as $key => $value) {
            if ($value->getConsumerId() != '') {
                $options[$key] = [
                    'label' => $this->_integrationService->get($value->getConsumerId())->getName(),
                    'value' => $value->getConsumerId()
                ];
            }
        }
        return $options;
    }

    /**
     * @return mixed
     */
    public function getTokenValueByTokenId()
    {
        $token ='';
        $tokenId = $this->scopeConfig->getValue(
            self::XML_PATH_INTEGRATION_API_TOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $tokenData = $this->collection->addFilter('consumer_id', $tokenId);
        foreach ($tokenData as $key => $value) {
            $token = $value->getToken();
        }

        return $token;
    }

    /**
     * @return mixed
     */
    public function getApiClientId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REST_API_CLIENT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getApiSecretKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REST_API_SECRET_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }


	 /**
     * @return mixed
     */
    public function getGuestCheckoutEnabled()
    {
        //XML_PATH_GUEST_CHECKOUT_ENABLED
        return $this->scopeConfig->getValue(
            self::XML_PATH_GUEST_CHECKOUT_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getFreeShippingMinAmount()
    {
        $getFreeShippingMinAmount = $this->scopeConfig->getValue(
            self::XML_PATH_FREE_SHIPPING_AMOUNT,
            ScopeInterface::SCOPE_STORE
        );
        $free_shipping_min_amt = (!empty($getFreeShippingMinAmount)) ? $getFreeShippingMinAmount : 0;

        $free_shipping_active = $this->scopeConfig->getValue(
            self::XML_PATH_FREE_SHIPPING_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
        $return = array('min_amount' => $free_shipping_min_amt, 'status' => $free_shipping_active);

        return $return;
    }
}
