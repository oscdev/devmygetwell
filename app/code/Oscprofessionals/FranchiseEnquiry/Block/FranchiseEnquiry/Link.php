<?php
/**
 * FranchiseEnquiry
 *
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Block\FranchiseEnquiry;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
class Link extends Template {
    protected $registry;
    protected $product;
    public function __construct(
        Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }

}