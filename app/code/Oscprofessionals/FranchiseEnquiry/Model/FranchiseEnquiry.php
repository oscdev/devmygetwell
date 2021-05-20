<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Model;
use Magento\Framework\Model\AbstractModel;
class FranchiseEnquiry extends AbstractModel {
	protected function _construct() {
		$this->_init('Oscprofessionals\FranchiseEnquiry\Model\ResourceModel\FranchiseEnquiry');
	}
}