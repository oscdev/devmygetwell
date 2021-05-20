<?php


namespace Bluethink\Ccavenue\Model;

use Magento\Sales\Api\Data\TransactionInterface;

class Ccavenue extends \Magento\Payment\Model\Method\AbstractMethod {

    const PAYMENT_PAYU_CODE = 'ccavenue';
    //const ACC_BIZ = 'payubiz';
    const ACC_MONEY = 'ccavenue';

    protected $_code = self::PAYMENT_PAYU_CODE;

    /**
     *
     * @var \Magento\Framework\UrlInterface 
     */
    protected $_urlBuilder;
    protected $_supportedCurrencyCodes = array(
        'AFN', 'ALL', 'DZD', 'ARS', 'AUD', 'AZN', 'BSD', 'BDT', 'BBD',
        'BZD', 'BMD', 'BOB', 'BWP', 'BRL', 'GBP', 'BND', 'BGN', 'CAD',
        'CLP', 'CNY', 'COP', 'CRC', 'HRK', 'CZK', 'DKK', 'DOP', 'XCD',
        'EGP', 'EUR', 'FJD', 'GTQ', 'HKD', 'HNL', 'HUF', 'INR', 'IDR',
        'ILS', 'JMD', 'JPY', 'KZT', 'KES', 'LAK', 'MMK', 'LBP', 'LRD',
        'MOP', 'MYR', 'MVR', 'MRO', 'MUR', 'MXN', 'MAD', 'NPR', 'TWD',
        'NZD', 'NIO', 'NOK', 'PKR', 'PGK', 'PEN', 'PHP', 'PLN', 'QAR',
        'RON', 'RUB', 'WST', 'SAR', 'SCR', 'SGF', 'SBD', 'ZAR', 'KRW',
        'LKR', 'SEK', 'CHF', 'SYP', 'THB', 'TOP', 'TTD', 'TRY', 'UAH',
        'AED', 'USD', 'VUV', 'VND', 'XOF', 'YER'
    );
    
    private $checkoutSession;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
      public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Bluethink\Ccavenue\Helper\Ccavenue $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Checkout\Model\Session $checkoutSession      
              
    ) {
        $this->helper = $helper;
        $this->orderSender = $orderSender;
        $this->httpClientFactory = $httpClientFactory;
        $this->checkoutSession = $checkoutSession;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

    }

    public function canUseForCurrency($currencyCode) {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function getRedirectUrl() {
        return $this->helper->getUrl($this->getConfigData('redirect_url'));
    }

    public function getReturnUrl() {
        return $this->helper->getUrl($this->getConfigData('return_url'));
    }

    public function getCancelUrl() {
        return $this->helper->getUrl($this->getConfigData('cancel_url'));
    }

    /**
     * Return url according to environment
     * @return string
     */
    public function getCgiUrl() {
        $env = $this->getConfigData('environment');
        if ($env === 'production') {
            return $this->getConfigData('production_url');
        }
        return $this->getConfigData('sandbox_url');
    }

/*    public function buildCheckoutRequest_1() {
        $order = $this->checkoutSession->getLastRealOrder();
        $billing_address = $order->getBillingAddress();

        $params = array();
        $params["key"] = $this->getConfigData("merchant_key");
        if ($this->getConfigData('account_type') == self::ACC_MONEY) {
            $params["service_provider"] = $this->getConfigData("service_provider");
        }
        $params["txnid"] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $params["amount"] = round($order->getBaseGrandTotal(), 2);
        $params["productinfo"] = $this->checkoutSession->getLastRealOrderId();
        $params["firstname"] = $billing_address->getFirstName();
        $params["lastname"] = $billing_address->getLastname();
        $params["city"]                 = $billing_address->getCity();
        $params["state"]                = $billing_address->getRegion();
        $params["zip"]                  = $billing_address->getPostcode();
        $params["country"]              = $billing_address->getCountryId();
        $params["email"] = $order->getCustomerEmail();
        $params["phone"] = $billing_address->getTelephone();
        $params["curl"] = $this->getCancelUrl();
        $params["furl"] = $this->getReturnUrl();
        $params["surl"] = $this->getReturnUrl();

        $params["hash"] = $this->generatePayuHash($params['txnid'],
                $params['amount'], $params['productinfo'], $params['firstname'],
                $params['email']);
 


        return $params;
    }*/

    public function generatePayuHash($txnid, $amount, $productInfo, $name,$email) {
        $SALT = $this->getConfigData('salt');
        $posted = array(
            'key' => $this->getConfigData("merchant_key"),
            'txnid' => $txnid,
            'amount' => $amount,
            'productinfo' => $productInfo,
            'firstname' => $name,
            'email' => $email,
        );

        $hashSequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10';

        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;
        return strtolower(hash('sha512', $hash_string));
    }

    //validate response
    public function validateResponse($returnParams) {
        if ($returnParams['status'] == 'pending' || $returnParams['status'] == 'failure') {
            return false;
        }
        if ($returnParams['key'] != $this->getConfigData("merchant_key")) {
            return false;
        }
        return true;
    }

    public function postProcessing(\Magento\Sales\Model\Order $order,\Magento\Framework\DataObject $payment, $response) {
        
        $payment->setTransactionId($response['txnid']);
        $payment->setTransactionAdditionalInfo('payu_mihpayid',$response['mihpayid']);
        $payment->setAdditionalInformation('payu_order_status', 'approved');
        $payment->addTransaction(TransactionInterface::TYPE_ORDER);
        $payment->setIsTransactionClosed(0);
        $payment->place();
        $order->setStatus('processing');
        $order->save();
    }

    public function buildCheckoutRequest(){
        $order = $this->checkoutSession->getLastRealOrder();      
        $billing_address = $order->getBillingAddress();    

        $ccavenues = array();
        $ccavenues['merchant_id'] = $this->getConfigData("merchant_key");
        if ($this->getConfigData('account_type') == self::ACC_MONEY) {
            $ccavenues["service_provider"] = $this->getConfigData("service_provider");
        }
        
        $ccavenues["txnid"] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $ccavenues["amount"] = round($order->getBaseGrandTotal(), 2);
        $ccavenues["order_id"] = $this->checkoutSession->getLastRealOrderId();
        $ccavenues["billing_name"] = $billing_address->getFirstName()." ".$billing_address->getLastname();
        $ccavenues['tid'] =  time() ;
        $ccavenues['currency'] =  $order->getOrderCurrencyCode() ;
        $ccavenues['language'] =  'EN' ; 
        $ccavenues["billing_city"]                 = $billing_address->getCity();
        $ccavenues["billing_state"]                = $billing_address->getRegion();
        $ccavenues["billing_zip"]                  = $billing_address->getPostcode();
        $ccavenues["billing_country"]              = $billing_address->getCountryId();
        $ccavenues["billing_email"] = $order->getCustomerEmail();
        $ccavenues["phone"] = $billing_address->getTelephone();
        
        $shippingAddress = $order->getShippingAddress();
        if ( $shippingAddress )
        $shippingData = $shippingAddress->getData();
        $ccavenues['delivery_name'] = $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname();
        $ccavenues['delivery_address'] = implode('', $shippingAddress->getStreet());
        $ccavenues['delivery_city'] = $shippingAddress->getCity();
        $ccavenues['delivery_state'] = $shippingAddress->getRegion();
        $ccavenues['delivery_zip'] =   $shippingAddress->getPostcode();
        $ccavenues['delivery_country'] = $shippingAddress->getCountryId();
        $ccavenues['delivery_tel'] = $shippingAddress->getTelephone();

        $ccavenues["cancel_url"] = $this->getCancelUrl();
        $ccavenues["furl_url"] = $this->getReturnUrl();
        $ccavenues["redirect_url"] = $this->getReturnUrl();
         
        $merchant_data = '' ;
        foreach ($ccavenues as $key => $value){
    
          $merchant_data.=$key.'='.urlencode($value).'&';
        }
    
        $workingKey =  $this->getConfigData("working_key");
        $accessCode =  $this->getConfigData("access_code");
        $encrypted_data =$this->encrypt($merchant_data,$workingKey);    
        $ccavenues['encRequest'] = $encrypted_data ;
        $ccavenues['access_code'] =  $accessCode ;

        return $ccavenues;     
   } 


   private function encrypt($plainText,$key)
  {
    $secretKey = $this->hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
      $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
      $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
    $plainPad = $this->pkcs5_pad($plainText, $blockSize);
      if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
    {
          $encryptedText = mcrypt_generic($openMode, $plainPad);
                mcrypt_generic_deinit($openMode);
                
    } 
    return bin2hex($encryptedText);
  }

  public function decrypt($encryptedText,$key)
  {
    $secretKey = $this->hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText=$this->hextobin($encryptedText);
      $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
    mcrypt_generic_init($openMode, $secretKey, $initVector);
    $decryptedText = mdecrypt_generic($openMode, $encryptedText);
    $decryptedText = rtrim($decryptedText, "\0");
    mcrypt_generic_deinit($openMode);
    return $decryptedText;
    
  }
  //*********** Padding Function *********************

   private function pkcs5_pad ($plainText, $blockSize)
  {
      $pad = $blockSize - (strlen($plainText) % $blockSize);
      return $plainText . str_repeat(chr($pad), $pad);
  }

  //********** Hexadecimal to Binary function for php 4.0 version ********

    private function hextobin($hexString){ 
        $length = strlen($hexString); 
        $binString="";   
        $count=0; 
        while($count<$length) 
        {       
            $subString =substr($hexString,$count,2);           
             $packedString = pack("H*",$subString); 
            if ($count==0)
            {
                $binString=$packedString;
            }else 
            {
                $binString.=$packedString;
            } 

        $count+=2; 
        } 
        return $binString; 
    } 
}
