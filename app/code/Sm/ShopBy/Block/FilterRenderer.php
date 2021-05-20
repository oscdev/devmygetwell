<?php
/**
 *
 * SM Shop By - Version 2.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\ShopBy\Block;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
	public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
		$this->assign('filter' , $filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }
    
    public function getPriceRange($filter){
    	$return = array('min' => 0 , 'max'=>0);
    	if($filter->getName() == 'Price' ){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
			/* My temp fix*/
			if (!$category)
			{
				//$category->setId()
				 return	$return = array('min' => 0 , 'max'=>10000000000);
			}   
			/* My temp fix*/
			$categoryFactory =  $objectManager->get('Magento\Catalog\Model\CategoryFactory');
			$categoryLoad = $categoryFactory->create()->load($category->getId());
			$collection = $categoryLoad->getProductCollection()->addAttributeToSelect('*')->addMinimalPrice()->addFinalPrice();
			$currency = $objectManager->create('Magento\Directory\Model\Currency'); 
			$currencySymbol = $currency->getCurrencySymbol(); 
			$priceArr = $filter->getResource()->loadPrices(10000000000);
     		$return['min'] = reset($priceArr);
     		$return['max'] = end($priceArr);
			$return['min_standard'] = $collection->getMinPrice();
     		$return['max_standard'] = $collection->getMaxPrice();
			$return['min_value'] = $return['min_standard'];
			$return['max_value'] = $return['max_standard'];
			$requestPrice = $this->getRequest()->getParam('price');
			if (!empty($requestPrice)){
				$tmp = explode('-', $requestPrice);
				$return['min_value'] = (isset($tmp[0]) && !empty($tmp[0])) ? $tmp[0] :  $return['min_standard'];
				$return['min_value'] = $return['min_value'] <  $return['min_standard'] ?   $return['min_standard'] : $return['min_value']; 
				$return['max_value'] = (isset($tmp[1]) && !empty($tmp[1])) ? $tmp[1] :  $return['max_standard'];
				$return['max_value'] = $return['max_value'] > $return['max_standard'] ?   $return['max_standard'] : $return['max_value'] - 0.01; 
			}
			$return['currency_symbol'] = $currencySymbol;
    	}
    	return $return;
    }
    
    public function getFilterUrl($filter){
    		$query = ['price'=> ''];
    	 return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}