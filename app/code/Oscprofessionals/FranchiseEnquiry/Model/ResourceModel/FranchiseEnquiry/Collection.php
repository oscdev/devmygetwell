<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Model\ResourceModel\FranchiseEnquiry;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(
			'Oscprofessionals\FranchiseEnquiry\Model\FranchiseEnquiry',
			'Oscprofessionals\FranchiseEnquiry\Model\ResourceModel\FranchiseEnquiry'
		);
	}
}