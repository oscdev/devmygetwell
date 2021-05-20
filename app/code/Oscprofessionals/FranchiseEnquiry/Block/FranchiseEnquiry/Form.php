<?php
/*
* @author      Oscprofessionals Team (support@oscprofessionals.com)
* @copyright   Copyright (c) 2017 Oscprofessionals (http://www.oscprofessionals.com)
* @category    Oscprofessionals
* @package     Oscprofessionals_FranchiseEnquiry
*/
namespace Oscprofessionals\FranchiseEnquiry\Block\FranchiseEnquiry;

use Magento\Framework\View\Element\Context;

class Form extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Block\Data $directoryBlock,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryBlock = $directoryBlock;
    }
    public function getCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }
    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }
    public function getCountryAction()
    {
        return $this->getUrl('extension/extension/country', ['_secure' => true]);
    }
}
