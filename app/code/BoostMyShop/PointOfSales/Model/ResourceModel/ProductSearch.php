<?php

namespace BoostMyShop\PointOfSales\Model\ResourceModel;


class ProductSearch extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $this->addAttributeToSelect(['name', 'sku', 'price', 'image', 'base_image']);

        return $this;
    }

    public function search($queryString)
    {
        $queryString = trim($queryString);

        $this->addAttributeToFilter([
            ['attribute' => 'name', 'like' => '%'.$queryString.'%'],
            ['attribute' => 'sku', 'like' => '%'.$queryString.'%']
        ]);

        $this->addFieldToFilter('type_id', ['in' => ['simple', 'virtual']]);

        $this->setPageSize(50)->setCurPage(1);

        return $this;
    }

}
