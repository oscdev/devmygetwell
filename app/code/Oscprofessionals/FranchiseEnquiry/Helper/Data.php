<?php
/**
 * Callback
 * 
 * @author Oscprofessionals FranchiseEnquiry
 */
namespace Oscprofessionals\FranchiseEnquiry\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Store\Model\ScopeInterface;
class Data extends AbstractHelper {
	public function getConfig($key){
		return $this->scopeConfig->getValue('oscprofessionals_franchiseenquiry_configuration/'.$key, ScopeInterface::SCOPE_STORE);
	}
}