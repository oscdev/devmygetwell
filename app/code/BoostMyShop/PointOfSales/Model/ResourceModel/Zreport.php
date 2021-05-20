<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel;


class Zreport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('', '');
    }


    public function getTotalSales($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order'), array(
                    new \Zend_Db_Expr('SUM(base_subtotal) + SUM(base_shipping_amount) as sub_total')
                )
            )
            ->where('store_id = '.$settings['store_id'])
            ->where('created_at >= "'.$settings['from'].'"')
            ->where('created_at <= "'.$settings['to'].'"')
        ;

        $result = $this->getConnection()->fetchOne($select);
        return $result;
    }

    public function getTotalReturn($settings)
    {
        return 0;
    }

    public function getTotalTax($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order'), array(
                    new \Zend_Db_Expr('SUM(base_tax_amount) as taxes')
                )
            )
            ->where('store_id = '.$settings['store_id'])
            ->where('created_at >= "'.$settings['from'].'"')
            ->where('created_at <= "'.$settings['to'].'"')
        ;

        $result = $this->getConnection()->fetchOne($select);
        return $result;
    }

    public function getTaxAmountPerPercent($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('sales_order_item'), array('tax_percent', new \Zend_Db_Expr('SUM(base_tax_amount) as total')))
            ->where('store_id = '.$settings['store_id'])
            ->where('created_at >= "'.$settings['from'].'"')
            ->where('created_at <= "'.$settings['to'].'"')
            ->where('tax_percent > 0')
            ->group('tax_percent')
        ;

        $result = $this->getConnection()->fetchAll($select);
        return $result;
    }

    public function getPaymentsPerMethods($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('bms_pointofsales_order_payment'), array('method', new \Zend_Db_Expr('SUM(amount) as total')))
            //->where('store_id = '.$settings['store_id'])
            ->where('created_at >= "'.$settings['from'].'"')
            ->where('created_at <= "'.$settings['to'].'"')
            //->where('tax_percent > 0')
            ->group('method')
        ;

        $result = $this->getConnection()->fetchAll($select);
        return $result;
    }

    public function getTransactionsPerMethods($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('bms_pointofsales_order_payment'), array('method', new \Zend_Db_Expr('count(*) as total')))
            //->where('store_id = '.$settings['store_id'])
            ->where('created_at >= "'.$settings['from'].'"')
            ->where('created_at <= "'.$settings['to'].'"')
            //->where('tax_percent > 0')
            ->group('method')
        ;

        $result = $this->getConnection()->fetchAll($select);
        return $result;
    }

    public function getSales($settings)
    {
        $select = $this->getConnection()
            ->select()
            ->from(array('o' => $this->getTable('sales_order')), array('increment_id','grand_total'))
            ->joinLeft($this->getTable('bms_pointofsales_order_payment'), 'entity_id = order_id', array('method', 'amount'))
            ->where('store_id = '.$settings['store_id'])
            ->where('o.created_at >= "'.$settings['from'].'"')
            ->where('o.created_at <= "'.$settings['to'].'"')
            ->order('entity_id asc')
        ;

        $result = $this->getConnection()->fetchAll($select);
        return $result;

    }



}
