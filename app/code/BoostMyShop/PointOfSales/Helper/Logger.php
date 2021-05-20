<?php

namespace BoostMyShop\PointOfSales\Helper;

class Logger
{

    const kLogGeneral = 'general';
    const kLogPerf = 'performance';

    public function log($msg, $type = self::kLogGeneral)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pointofsales_'.$type.'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($msg);
    }

    public function logException($exception, $type = self::kLogGeneral)
    {
        $msg= $exception->getMessage().' : '.$exception->getTraceAsString();
        //$this->log($msg, $type);
    }

}