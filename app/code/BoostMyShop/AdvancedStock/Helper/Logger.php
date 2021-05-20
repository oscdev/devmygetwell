<?php

namespace BoostMyShop\AdvancedStock\Helper;

class Logger
{

    const kLogReservation = 'reservation';
    const kLogInventory = 'inventory';
    const kLogShipment = 'shipment';
    const kLogGeneral = 'general';
    const kLogRouting = 'routing';
    const kLogInventoryCore = 'inventory_core';

    public function log($msg, $type = self::kLogGeneral)
    {
        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/advancedstock_'.$type.'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($msg);*/
        return;
    }

}