<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel\Stat;


class TurnOver extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('', '');
    }


    public function getTurnOver($storeId, $from, $to)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order'), array(
                                                        new \Zend_Db_Expr('SUM(base_subtotal) as sub_total'),
                                                        new \Zend_Db_Expr('SUM(base_grand_total) as grand_total'),
                                                        new \Zend_Db_Expr('SUM(base_tax_amount) as taxes'),
                                                        new \Zend_Db_Expr('SUM(base_shipping_amount) as shipping')
                                                        )
                                                        )
            ->where('store_id = '.$storeId)
            ->where('created_at >= "'.$from.'"')
            ->where('created_at <= "'.$to.'"')
        ;

        $result = $this->getConnection()->fetchRow($select);

        return $result;
    }


}
