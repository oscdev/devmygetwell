<?php
namespace Oscprofessionals\OscShop\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $_curl;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_request;
    protected $_resultFactory;
    protected $_result = array();
    protected $_httpcode = '';

    /**
     * @var \Oscprofessionals\OscShop\Helper\Data
     */
    private $dataHelper;

    /**
     * @var  Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    public $paymentInformationManagement;


    /**
     * @var \Magento\Quote\Api\Data\PaymentInterface
     */
    private $paymentInterface;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface
     */
    private $addressInterface;

    private $host;

    private $cartRepositoryInterface;

    private $cartManagementInterface;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http $httpRequest
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Oscprofessionals\OscShop\Helper\Data $helperData
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentInterface
     * @param \Magento\Quote\Api\Data\AddressInterface $addressInterface
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     */

    private $quoteManagement;

    private $quoteFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http $httpRequest,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Oscprofessionals\OscShop\Helper\Data $helperData,
        PaymentInformationManagementInterface $paymentInformationManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\Data\PaymentInterface $paymentInterface,
        \Magento\Quote\Api\Data\AddressInterface $addressInterface,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->_curl = $curl;
        $this->_storeManager = $storeManager;
        $this->_request = $httpRequest;
        $this->_resultFactory = $resultRawFactory;
        $this->dataHelper = $helperData;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->paymentInterface = $paymentInterface;
        $this->paymentMethodManagement =  $paymentMethodManagement;
        $this->addressInterface = $addressInterface;
        $this->host = "https://api.sandbox.paypal.com/v1";
        $payment_mode = $this->dataHelper->getSandboxMode();
        if ($payment_mode==1) {
            $this->host = "https://api.paypal.com/v1";
        }

        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->quoteManagement = $quoteManagement;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context);
    }


    /**
     * @return mixed
     */
    public function execute()
    {
        if ($this->dataHelper->isEnabledApiTimeLog()) {
            $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/track-api-time.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Request Time=='.print_r(microtime(true), true)); // Array Log
        }

        $RequestUri = $this->_request->getServer('REQUEST_URI');

        if ($this->dataHelper->isEnabledApiTimeLog()) {
            $logger->info('Request Url==' . print_r($RequestUri, true)); // Array Log
        }

        $requestScheme = $this->_request->getServer('REQUEST_SCHEME');
        if ($requestScheme == '') {
            $requestScheme = 'http';
        }
        $serverName = $this->_request->getServer('SERVER_NAME');
        $fullUrl = $requestScheme.'://'.$serverName.$RequestUri;

        $endPointString = explode('/::', $RequestUri);
        $endPointString = $endPointString[1];

        $oscpAccessToken = $this->_request->getServer('HTTP_X_OSCP_ACCESS_TOKEN');

        if ($endPointString !== 'checkconnection') {
            /*Salt Key */
            $saltKeyNumber = substr($oscpAccessToken, -1);
            $oscpToken = substr($oscpAccessToken, 0, -1);
            $saltKey = substr($fullUrl, -($saltKeyNumber));
            $encryptedString = md5($saltKey.$fullUrl);
            /*Salt Key */
            /*if ($oscpToken != $encryptedString) {
                $message = array('message' => 'Oops , Something went wrong regarding Security , Please contact your service provider.');
                $response = $this->_resultFactory->create();
                $response->setContents(json_encode($message, true));
                $response->setStatusHeader(401, '1.1', 'Unauthorized');
                return $response;
            }*/
        }

        $data = file_get_contents("php://input");

        switch ($endPointString) {
            case 'oscpgetpaymentlist':
                $paymentList['payment_methods'] = $this->dataHelper->getPaymentMethodsList();
                echo json_encode($paymentList, true);
                break;

            case 'verifywithpaypal':
                $paypalVerification = $this->verifyWithPayPal($data);
                echo json_encode($paypalVerification, true);
                break;

            case 'checkconnection':
                $this->checkConnection();
                break;
			
			case 'isguestenabled':
				echo $this->dataHelper->getGuestCheckoutEnabled();
                break;

            case 'shippingrules':
                $getFreeShippingData = $this->dataHelper->getFreeShippingMinAmount();
                echo json_encode($getFreeShippingData, true);
                break;

            default:
                $curlRequests = $this->curlRequests();
                if ($this->dataHelper->isEnabledApiTimeLog()) {
                    $logger->info('Response Time=='.print_r(microtime(true), true)); // Array Log
                    $logger->info('Response Data=='.print_r($curlRequests, true)); // Array Log
                }
                echo json_encode($curlRequests, true);
                break;
        }
    }

    /**
     *
     */
    public function curlRequests()
    {
        $header = [];
        $token = $this->dataHelper->getTokenValueByTokenId();
        $RequestUri = $this->_request->getServer('REQUEST_URI');
        $params = $this->getRequest()->getParams();
        $storeId = $params['storeid'];
        $endPointString = explode('/::', $RequestUri);
        $endPointString = $endPointString[1];
        $authorization = $this->_request->getServer('HTTP_AUTHORIZATION');

        $server_software = $_SERVER['SERVER_SOFTWARE'];
        $server_name = explode('/', $server_software);
        if (!empty($server_name) && $server_name[0]=='Apache') {
            $header[] = ($authorization !== '') ? "Authorization: " . $authorization : "Authorization: Bearer " . $token;
        } else {
            $header[] = (isset($authorization)) ? "Authorization: " . $authorization : "Authorization: Bearer " . $token;
        }
        
        $header[] = "Accept: application/json";
        $header[] = "Content-Type: application/json";
        $method = $this->_request->getServer('REQUEST_METHOD');

        if ($method == 'DELETE' || $method == 'PUT') {
            $header[] = "X-HTTP-Method-Override: " . $method;
        }

        $payload = file_get_contents("php://input");
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $url = $baseUrl . 'rest/' . $storeId . '/V1/' . $endPointString;

        $curl = curl_init($url);
        $curlOptions = $this->getCurlOptions($method, $header, $payload);

        foreach ($curlOptions as $key => $value) {
            curl_setopt($curl, constant($key), $value);
        }

        $output = curl_exec($curl);

		if ($this->dataHelper->isEnabledApiTimeLog()) {
            $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/track-api.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('url=='.$url); // Array Log
            $logger->info('payload=='.print_r($payload, true)); // Array Log
            $logger->info('header=='.print_r($header, true)); // Array Log
            $logger->info('curlOptions=='.print_r($curlOptions, true)); // Array Log
            $logger->info('output=='.print_r($output, true)); // Array Log
        }

        $this->_httpcode = curl_getinfo($curl);
        $response = json_decode($output, true);

        return $response;
    }

    /**
     * @param $method
     * @param $header
     * @param $payload
     * @return array
     */
    public function getCurlOptions($method, $header, $payload)
    {
        $options = array(
            "CURLOPT_HEADER" => "TRUE",
            "CURLOPT_RETURNTRANSFER" => "TRUE",
            "CURLOPT_FOLLOWLOCATION" => "TRUE",
            "CURLOPT_MAXREDIRS" => 3,
            "CURLOPT_FRESH_CONNECT" => "FALSE",
            "CURLOPT_SSL_VERIFYPEER" => "FALSE",
            "CURLOPT_CONNECTTIMEOUT" => 30,
            "CURLOPT_TIMEOUT" => 30,
            "CURLOPT_CUSTOMREQUEST" => $method,
            "CURLOPT_HTTPHEADER" => $header,
            "CURLOPT_POSTFIELDS" => $payload
        );
        return $options;
    }

    public function verifyWithPayPal($data)
    {
        $postData = json_decode($data);

        
        //$setToken = $this->getRequest()->getParam('token');

        /*****************org code*****************************/
        $postData = json_decode($data);
        $quoteId = $postData->method->params->where->quote_id;

        $payment = $this->getPaymentDetails($postData);


        $response = array();

        if ($payment['state'] == 'approved') {
			//ob_start();
            $this->setBillingAddress($postData);
            try {
                $savePaymentInformationAndPlaceOrder = $this->paymentInformationManagement->savePaymentInformationAndPlaceOrder($quoteId, $this->paymentInterface, $this->addressInterface);
                $response['order_id'] = $savePaymentInformationAndPlaceOrder;
				$response['status'] = 'done';
            } catch (\Exception $e) {
                return $response['exceptionn'] = $e->getMessage();
            }
        } else {
            $response["error"] = true;
            $response["message"] = "Payment has not been verified. Status is " . $payment['state'];
        }



        return $response;
    }

    public function setBillingAddress($postData)
    {
        $this->paymentInterface->setMethod($postData->method->params->where->payment_method);
        $this->addressInterface->setCountryId($postData->method->params->where->billingAddress->countryId);
        $this->addressInterface->setRegionId($postData->method->params->where->billingAddress->regionId);
        $this->addressInterface->setRegionCode($postData->method->params->where->billingAddress->regionCode);
        $this->addressInterface->setStreet($postData->method->params->where->billingAddress->street);
        $this->addressInterface->setTelephone($postData->method->params->where->billingAddress->telephone);
        $this->addressInterface->setPostcode($postData->method->params->where->billingAddress->postcode);
        $this->addressInterface->setCity($postData->method->params->where->billingAddress->city);
        $this->addressInterface->setFirstname($postData->method->params->where->billingAddress->firstname);
        $this->addressInterface->setLastname($postData->method->params->where->billingAddress->lastname);
        $this->addressInterface->setSaveInAddressBook(null);
    }


    /* PayPal: Get Payment Detals From PayPal */
    /**
     * @param $data
     * @return mixed
     */
    public function getPaymentDetails($data)
    {
        $token = $this->getPaypalAccessToken();

        $paymentId = $data->method->params->where->proof2->response->id;

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $url = $this->host . "/payments/payment/" . $paymentId;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ));
        $response = curl_exec($curl);

        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            /*if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                die();
            }*/
        }

        $jsonResponse = json_decode($response, true);


        return $jsonResponse;
    }


    /* PayPal: Get Access Token to make PayPal Payment */
    public function getPaypalAccessToken()
    {
        $url = $this->host."/oauth2/token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->dataHelper->getApiClientId() . ":" . $this->dataHelper->getApiSecretKey());
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);

        $response = curl_exec($curl);

        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if ($info['http_code'] != 200 && $info['http_code'] != 201 ) {
                die();
            }
        }
        // Convert the result from JSON format to a PHP array
        $jsonResponse = json_decode($response);
        return $jsonResponse->access_token;
    }



    public function checkConnection()
    {
        $header = [];
        $token = $this->dataHelper->getTokenValueByTokenId();

        $RequestUri = $this->_storeManager->getStore()->getBaseUrl()."oscshop/index/index/storeid/default/::categories";
        $params = $this->getRequest()->getParams();
        $storeId = $params['storeid'];
        $endPointString = explode('/::', $RequestUri);
        $endPointString = $endPointString[1];

        $authorization = $this->_request->getServer('HTTP_AUTHORIZATION');
        $server_software = $_SERVER['SERVER_SOFTWARE'];
        $server_name = explode('/', $server_software);
        if (!empty($server_name) && $server_name[0]=='Apache') {
            $header[] = ($authorization !== '') ? "Authorization: " . $authorization : "Authorization: Bearer " . $token;
        } else {
            $header[] = (isset($authorization)) ? "Authorization: " . $authorization : "Authorization: Bearer " . $token;
        }

        $header[] = "Accept: application/json";
        $header[] = "Content-Type: application/json";
        $method = $this->_request->getServer('REQUEST_METHOD');

        /* if ($method == 'DELETE' || $method == 'PUT') {
             $header[] = "X-HTTP-Method-Override: " . $method;
         }*/

        $payload = file_get_contents("php://input");

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $url = $baseUrl . 'rest/' . $storeId . '/V1/' . $endPointString;

        $curl = curl_init($url);
        $curlOptions = $this->getCurlOptions($method, $header, $payload);

        foreach ($curlOptions as $key => $value) {
            curl_setopt($curl, constant($key), $value);
        }

        $output = curl_exec($curl);
        $info = curl_getinfo($curl);
        $this->_httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        #$test = json_decode(rtrim($output, '[]'));
        $response = json_decode($output, true);
        $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/api-connection.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($info);

        $status = '';
        if ($this->_httpcode == 200) {
            $status = "success";
        } else {
            $status = "failed";
        }

        echo "<script> window.location.href = document.referrer+'connection/".$status."'; </script>";
    }
}
