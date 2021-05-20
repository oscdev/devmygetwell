<?php

namespace BoostMyShop\PointOfSales\Block\Payments;

use Magento\Backend\Block\Widget\Grid\Column;

class Index extends \BoostMyShop\PointOfSales\Block\Widget\Grid
{
    protected $_paymentCollectionFactory;
    protected $_userCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Payment\CollectionFactory $paymentCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_paymentCollectionFactory = $paymentCollectionFactory;
        $this->_userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('paymentGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Payments'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_paymentCollectionFactory->create()->joinOrder();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', ['header' => __('Date'), 'type' => 'datetime', 'index' => 'created_at']);
        $this->addColumn('method', ['header' => __('Method'), 'index' => 'method']);
        $this->addColumn('amount', ['header' => __('Total'), 'type' => 'currency', 'index' => 'amount']);
        $this->addColumn('user_id', ['header' => __('User'), 'index' => 'user_id', 'type' => 'options', 'options' => $this->getUsersOptions()]);
        $this->addColumn('comments', ['header' => __('Comments'), 'index' => 'comments']);

        return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/payments/index');
    }

    public function getUsersOptions()
    {
        foreach($this->_userCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getusername();
        }
        return $options;
    }

}
