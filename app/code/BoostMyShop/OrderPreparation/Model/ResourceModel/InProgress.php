<?php

namespace BoostMyShop\OrderPreparation\Model\ResourceModel;


class InProgress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_orderpreparation_inprogress', 'ip_id');
    }

    public function getIdFromShipmentReference($shipmentReference)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getTable('sales_shipment'),array('entity_id'))
            ->where('increment_id = "'.$shipmentReference.'"');
        $shipmentId = $connection->fetchOne($select);

        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable(), array('ip_id'))
            ->where('ip_shipment_id = '.$shipmentId);
        $ipId = $connection->fetchOne($select);

        return $ipId;
    }

}
