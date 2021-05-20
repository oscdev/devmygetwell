<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel;


class Opening extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_pointofsales_opening', 'po_id');
    }

    public function loadByStoreDate($object, $storeId, $date)
    {
        $connection = $this->getConnection();

        //find item id
        $select = $this->getConnection()->select()->from($this->getTable('bms_pointofsales_opening'))->where('po_store_id = '.$storeId.' and po_date = "'.$date.'"');
        $itemId = $connection->fetchOne($select);

        return $this->load($object, $itemId);
    }

}
