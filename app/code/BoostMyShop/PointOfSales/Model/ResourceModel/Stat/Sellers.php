<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Stat;


class Sellers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('', '');
    }

    public function getItems($storeId, $from, $to)
    {


        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order'), array(new \Zend_Db_Expr('SUM(base_grand_total) as total')))
            ->join(array('om' => $this->getTable('bms_pointofsales_order_manager')), 'entity_id = order_id', array())
            ->join(array('u' => $this->getTable('admin_user')), 'u.user_id = om.user_id', array(new \Zend_Db_Expr('concat(firstname, " ", lastname) as user')))
            ->where('created_at >= "'.$from.'"')
            ->where('store_id = '.$storeId)
            ->where('created_at <= "'.$to.'"')
            ->group('u.user_id')
        ;

        $result = $this->getConnection()->fetchAll($select);

        return $result;
    }


}
