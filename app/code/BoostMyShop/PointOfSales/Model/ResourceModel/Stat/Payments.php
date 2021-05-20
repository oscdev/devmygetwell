<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Stat;


class Payments extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('', '');
    }

    public function getItems($storeId, $from, $to)
    {


        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('bms_pointofsales_order_payment'), array(
                    'method',
                    new \Zend_Db_Expr('SUM(amount) as total')
                )
            )
            //->where('store_id = '.$storeId)
            ->where('created_at >= "'.$from.'"')
            ->where('created_at <= "'.$to.'"')
            ->group('method');
        ;

        $result = $this->getConnection()->fetchAll($select);

        return $result;
    }


}
