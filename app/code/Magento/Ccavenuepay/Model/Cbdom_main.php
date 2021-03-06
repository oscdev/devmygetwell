<?php

/*
  Payment Name      : CCAvenue MCPG
  Description	    : Extends Payment with  CCAvenue MCPG.
  CCAvenue Version  : MCPG-2.0
  Author            : CCAvenues
  Copyright         : � 2014-2019
 */
/**
 * 
 * This code coonect with the opencart databse for inserting the module details into the user server.
 * 
 */

namespace Magento\Ccavenuepay\Model;

use Magento\Framework\App\ObjectManager;

$app_path = BP . '/app';
$replaced_app_path = str_replace("\\", "/", $app_path);
if (!defined("DOM_BZ_PATH_PG_MAIN_201")) {
    define("DOM_BZ_PATH_PG_MAIN_201", $replaced_app_path . '/code/local/Mage/Ccavenuepay/Model/Cbdom_main.php');
}
$base_path = BP;

class Cbdom_main {

    private $logger;
    private $_default_currency = "INR";
    private $_default_language = "EN";
    private $_pg_live_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    private $_pg_test_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    private $_errors = array();
    private $_license_api_table = "apibzcc";
    private $_license_api_table_prefix = "bzccpg_";
    private $_ini_created = false;
    private $_pgmod_ver = "";  /* ==> Module Version */
    private $_pgcat = "";  /* ==>Category */
    private $_pgcat_ver = "";  /* ==>Category Version */
    private $_pgcms = "";  /* ==>CMS */
    private $_pgcms_ver = "";  /* ==>CMS Version */
    private $_pg_lic_key = '';  /* Payment module license key */

    /*public function __construct() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
        $this->logger->info("Cbdom_main __construct ");
    }*/

    /* Encrypt and Decrypt functions */

    public function getAllowedCurrencyList() {
        $allowedCurrenciesCode = array(
            'AUD', 'CAD', 'EUR', 'GBP', 'JPY', 'USD', 'NZD', 'CHF', 'HKD', 'SGD',
            'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK', 'ILS', 'MXN', 'MYR', 'BRL',
            'PHP', 'TWD', 'THB', 'TRY', 'INR'
        );
        return $allowedCurrenciesCode;
    }

    public function getAllowedCurrency($payment_currency = '') {
        $allowedCurrencies = $this->getAllowedCurrencyList();
        if (in_array($payment_currency, $allowedCurrencies)) {
            return $payment_currency;
        }
        if ($payment_currency == '') {
            return $this->_default_currency;
        }
        return false;
    }

    public function getAllowedLanguage($req_lang = 'EN') {
        $allowedLanguages = array('EN');
        if (in_array($req_lang, $allowedLanguages)) {
            return $req_lang;
        }
        return $this->_default_language;
    }

    public function getPaymentGatewayUrl($live_server = true) {
        $pg_gateway_url = '';
        if ($live_server) {
            $pg_gateway_url = $this->_pg_live_url;
        } else {
            $pg_gateway_url = $this->_pg_test_url;
        }
        return $pg_gateway_url;
    }
    
    function encrypt($plainText, $key) {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

//*****************
    function decrypt($encryptedText, $key) {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    //*********** Padding Function *********************
    public function pkcs5_pad($plainText, $blockSize) {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    public function getErrors() {
        return $this->_errors;
    }


    //********** Hexadecimal to Binary function for php 4.0 version ********
    public function hextobin($hexString) {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }
            $count += 2;
        }
        return $binString;
    }

    public function getFormatCallbackUrl($Url) {
        $pattern = '#http://www.#';
        preg_match($pattern, $Url, $matches);
        if (count($matches) == 0) {
            $find_pattern = '#http://#';
            $replace_string = 'http://www.';
            $Url = preg_replace($find_pattern, $replace_string, $Url);
        }
        return $Url;
    }
}

