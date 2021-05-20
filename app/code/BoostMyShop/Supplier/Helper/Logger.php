<?php

namespace BoostMyShop\Supplier\Helper;

class Logger
{

    const kLogGeneral = 'general';

    public function log($msg, $type = self::kLogGeneral)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/supplier_'.$type.'.log');

        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($msg);
    }

    public function logException($exception, $type = self::kLogGeneral)
    {
        $msg= $exception->getMessage().' : '.$exception->getTraceAsString();
        $this->log($msg, $type);
    }

}