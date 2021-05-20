<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class BaseIndex.
 */
abstract class BaseIndex extends Action
{
    /**
     * Magento_Directory Country model Factory.
     *
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * Magento_Directory Region model Factory.
     *
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * Result Page Factory.
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Data Object Factory.
     *
     * @var DataObjectFactory
     */
    protected $objectFactory;

    /**
     * Magento Framework Registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Base Index Constructor.
     *
     * @param Action\Context    $context
     * @param CountryFactory    $countryFactory
     * @param RegionFactory     $regionFactory
     * @param PageFactory       $resultPageFactory
     * @param DataObjectFactory $objectFactory
     * @param Registry          $registry
     */
    public function __construct(
        Action\Context $context,
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        PageFactory $resultPageFactory,
        DataObjectFactory $objectFactory,
        Registry $registry
    ) {
    
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->objectFactory = $objectFactory;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Oscprofessionals_OscShop::country');
    }
}
