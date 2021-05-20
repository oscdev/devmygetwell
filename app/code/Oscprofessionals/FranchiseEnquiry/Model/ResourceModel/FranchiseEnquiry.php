<?php
/**
 * FranchiseEnquiry
 * 
 * @author Oscprofessionals
 */
namespace Oscprofessionals\FranchiseEnquiry\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class FranchiseEnquiry extends AbstractDb {
	protected function _construct() {
		$this->_init('franchise_enquiry', 'id');
	}
}