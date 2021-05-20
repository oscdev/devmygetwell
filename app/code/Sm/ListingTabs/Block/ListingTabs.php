<?php
/**
 *
 * SM Listing Tabs - Version 2.3.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */

namespace Sm\ListingTabs\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\UrlFactory;

class ListingTabs extends \Magento\Catalog\Block\Product\AbstractProduct
{
	protected $_config = null;
    protected $_collection;
    protected $_resource;
	protected $_storeManager;
    protected $_scopeConfig;
	protected $_storeId;
	protected $_storeCode;
	protected $_catalogProductVisibility;
	protected $_review;
	protected $_sql = '';
	

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Framework\App\ResourceConnection $resource,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		\Magento\Review\Model\Review $review,
		\Magento\Catalog\Block\Product\Context $context,
        array $data = [],
		$attr = null
    ) {
        $this->_collection = $collection;
        $this->_resource = $resource;
		$this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
		$this->_catalogProductVisibility = $catalogProductVisibility;
		$this->_storeId=(int)$this->_storeManager->getStore()->getId();
		$this->_storeCode=$this->_storeManager->getStore()->getCode();
		$this->_review = $review;
		if ($context->getRequest() && $context->getRequest()->isAjax()) {
			$_cfg =  $context->getRequest()->getParam('config');
			$this->_config = (array)json_decode(base64_decode(strtr($_cfg, '-_', '+/')));
		} else {
			$this->_config = $this->_getCfg($attr, $data);
		}
        parent::__construct($context, $data);
    }
	
	public function _getCfg($attr = null , $data = null)
	{
		$defaults = [];
		$_cfg_xml = $this->_scopeConfig->getValue('listingtabs',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->_storeCode);
		if (empty($_cfg_xml)) return;
		$groups = [];
		foreach ($_cfg_xml as $def_key => $def_cfg) {
			$groups[] = $def_key;
			foreach ($def_cfg as $_def_key => $cfg) {
				$defaults[$_def_key] = $cfg;
			}
		}
		
		if (empty($groups)) return;
		$cfgs = [];
		foreach ($groups as $group) {
			$_cfgs = $this->_scopeConfig->getValue('listingtabs/'.$group.'',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->_storeCode);
			foreach ($_cfgs as $_key => $_cfg) {
				$cfgs[$_key] = $_cfg;
			}
		}

		if (empty($defaults)) return;
		$configs = [];
		foreach ($defaults as $key => $def) {
			if (isset($defaults[$key])) {
				$configs[$key] = $cfgs[$key];
			} else {
				unset($cfgs[$key]);
			}
		}
		$cf = ($attr != null) ? array_merge($configs, $attr) : $configs;
		$this->_config = ($data != null) ? array_merge($cf, $data) : $cf;
		return $this->_config;
	}

	public function _getConfig($name = null, $value_def = null)
	{
		if (is_null($this->_config)) $this->_getCfg();
		if (!is_null($name)) {
			$value_def = isset($this->_config[$name]) ? $this->_config[$name] : $value_def;
			return $value_def;
		}
		return $this->_config;
	}

	public function _setConfig($name, $value = null)
	{

		if (is_null($this->_config)) $this->_getCfg();
		if (is_array($name)) {
			$this->_config = array_merge($this->_config, $name);

			return;
		}
		if (!empty($name) && isset($this->_config[$name])) {
			$this->_config[$name] = $value;
		}
		return true;
	}
	
	public function _tagId()
	{
		return md5(serialize(array('sm_listingtabs', $this->_config)));
	}
	
	public function _isAjax()
	{
		$isAjax = $this->getRequest()->isAjax();
		$is_ajax_listing_tabs = $this->getRequest()->getPost('is_ajax_listing_tabs');
		if ($isAjax && $is_ajax_listing_tabs == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function _toHtml()
    {
		if (!(int)$this->_getConfig('enable', 1)) return ;
		if ($this->_isAjax()) {
			$datacustom_content = $this->getRequest()->getPost('datacustomcontent');
			//$template_file = "default_items.phtml";
			
			if($datacustom_content == 'data-custom-content'){
				$template_file = "default_items_v3.phtml";
			} else if($datacustom_content == 'data-custom-left'){
				$template_file = "default_items_v4.phtml";
			} else if($datacustom_content == 'data-custom-center'){
				$template_file = "default_items_v6.phtml";
			} else {
				$template_file = "default_items.phtml";
			}
		}else{
			$template_file = $this->getTemplate();
			$template_file = (!empty($template_file)) ? $template_file : "Sm_ListingTabs::default.phtml";
		}
        $this->setTemplate($template_file);
        return parent::_toHtml();
    }
	
	public function _getList (){
		$type_show = $this->_getConfig('type_show');
		$type_listing = $this->_getConfig('type_listing');
		$under_price = $this->_getConfig('under_price');
		$tabs_select = $this->_getConfig('tabs_select');
		$category_select = $this->_getConfig('category_select');
		$order_by = $this->_getConfig('order_by');
		$order_dir = $this->_getConfig('order_dir');
		$limitation = $this->_getConfig('limitation');
		$type_filter = $this->_getConfig('type_filter');
		$category_id = $this->_getConfig('category_tabs');
		$field_tabs = $this->_getConfig('field_tabs');
		$list = [];
		$cat_filter = [];
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		switch($type_filter){
			case 'categories':
				if (!empty($category_id)){
					$catids = explode(',',$category_id);
					$all_childrens = $this->_getAllChildren($catids);
					if (!empty($all_childrens)){
						$flag = true;
						foreach($all_childrens as $key => $children){
							$count = $this->_getProductsBasic($children, true);
							$cat_children = implode(',',  $children);
							if ($count > 0){
								$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')->load($key);
								$list[$key]['name_tab'] =  $object_manager->getName();
								$list[$key]['count'] = $count;
								$list[$key]['id_tab'] = $key;
								$list[$key]['cat_children'] = $cat_children;
								if ($flag){
									$list[$key]['sel'] = 'active';
									$list[$key]['products_list'] = $this->_getProductsBasic($children);
									$flag = false;
								}
							}
						}
					}
				}
			break;
			case 'fieldproducts':
				if (!empty($category_select)){
					$catids = explode(',',$category_select);
					$all_childrens = $this->_getAllChildren($catids, true);
					$count = $this->_getProductsBasic($all_childrens, true);
					if (!empty($field_tabs) && $count > 0){
						$tabs = explode(',',$field_tabs);
						$flag = true;
						foreach($tabs as $key => $tab){
							$list[$tab]['name_tab'] =  $this->getLabel($tab);
							$list[$tab]['count'] = $count;
							$list[$tab]['id_tab'] = $tab;
							$list[$tab]['cat_children'] = implode(',',$all_childrens);
							if ($flag){
								$list[$tab]['sel'] = 'active';
								$list[$tab]['products_list'] = $this->_getProductsBasic($all_childrens, false, $tab);
								$flag = false;
							}
						}
					}
				}
			break;
		}
		
		return $list;
	}	
	
	public function _ajaxLoad(){
		$catids = $this->getRequest()->getPost('catids');
		$tab_id = $this->getRequest()->getPost('tab_id');
		$type_filter = $this->_getConfig('type_filter');
		if ($type_filter == 'fieldproducts'){
			return  $this->_getProductsBasic($catids, false, $tab_id);
		}else{
			return $this->_getProductsBasic($catids);
		}
		
	}
	
	public function getLabel($filter)
	{
		switch ($filter) {
			case 'name':
				return __('Name');
			case 'entity_id':
				return __('Id');
			case 'price':
				return __('Price');
			case 'lastest_products':
				return __('New Products');
			case 'num_rating_summary':
				return __('Top Rating');
			case 'num_reviews_count':
				return __('Most Reviews');
			case 'num_view_counts':
				return __('Most Viewed');
			case 'ordered_qty':
				return __('Most Selling');
		}
	}
	
	private function _getAllChildren($catids, $group = false) {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$list = [];
		$cat_tmps = '';
		!is_array($catids) && $catids = preg_split('/[\s|,|;]/', $catids, -1, PREG_SPLIT_NO_EMPTY);
		if (!empty($catids) && is_array($catids)){
			foreach($catids as $i => $catid ) {
				$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')->load($catid);
				if ($group){
					$cat_tmps .= $object_manager->getAllChildren().($i < count($catids) - 1 ? ',' : '');
				}else{
					$list[$catid] = $object_manager->getAllChildren(true);
				}
				
			}
			if ($group){
				if (!empty($cat_tmps)){
					$list = explode(',',$cat_tmps);
					return array_unique($list);
				}
			}
		}
		return $list;
	}
	
	public function _getOrderFields(& $collection , $tab = false) {
		$order_by = $tab ? $tab : $this->_getConfig('order_by');
		$order_dir = $this->_getConfig('order_dir');
		switch ($order_by) {
			default:
			case 'entity_id':
			case 'name':
				$collection->setOrder($order_by, $order_dir);
			case 'created_at':
				$tab ? $collection->setOrder($order_by, 'DESC' ) : $collection->setOrder($order_by, $order_dir);
				break;
			case 'price':
				$collection->getSelect()->order('final_price ' . $order_dir . '');
				break;
			case 'num_rating_summary':
				$tab ? $collection->getSelect()->order('num_rating_summary DESC') : $collection->getSelect()->order('num_rating_summary ' . $order_dir . '');
				break;
			case 'num_reviews_count':
				$tab ? $collection->getSelect()->order('num_reviews_count DESC') : $collection->getSelect()->order('num_reviews_count ' . $order_dir . '');
				break;
			case 'num_view_counts':
				$tab ? $collection->getSelect()->order('num_view_counts DESC') : $collection->getSelect()->order('num_view_counts ' . $order_dir . '');
				break;
			case 'ordered_qty':
				$tab ?  $collection->getSelect()->order('ordered_qty DESC') :  $collection->getSelect()->order('ordered_qty ' . $order_dir . '');
				break;
			
		}
		
		return $collection;
	}
	
	public function _getProductsBasic($catids = null, $count = false , $tab = false)
	{
		$type_filter = $this->_getConfig('type_filter');
		$limit = $this->_getConfig('limitation');    
		$type_listing = $this->_getConfig('type_listing');   
		$under_price = $this->_getConfig('under_price', '4.99'); 	
        $catids =  $catids == null ? $this->_getConfig('category_tabs') : $catids;
		!is_array($catids) && $catids = preg_split('/[\s|,|;]/', $catids, -1, PREG_SPLIT_NO_EMPTY);
		$collection = clone $this->_collection;
		$attributes = $this->_catalogConfig->getProductAttributes();
		$collection->clear()->getSelect()
			->reset(\Magento\Framework\DB\Select::WHERE)
			->reset(\Magento\Framework\DB\Select::ORDER)
			->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
			->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
			->reset(\Magento\Framework\DB\Select::GROUP);
		if ($type_listing == 'under'){
			$collection->addPriceDataFieldFilter('%s < %s', ['final_price', $under_price]);
		}
		$collection->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
			->addAttributeToSelect('special_from_date')
			->addAttributeToSelect('special_to_date')
			->addUrlRewrite()
			->setStoreId($this->_storeId);
	    if ($type_listing == 'deals'){
			$now = date('Y-m-d');
			$collection->addAttributeToFilter('special_price', ['neq' => ''])
				->addAttributeToFilter([
				[
					'attribute' => 'special_from_date',
					'lteq' => date('Y-m-d G:i:s', strtotime($now)),
					'date' => true,
				],
				[
					'attribute' => 'special_to_date',
					'gteq' => date('Y-m-d G:i:s', strtotime($now)),
					'date' => true,
				]
			]);
		}
		$collection->addAttributeToFilter('is_saleable', 1, 'left');
		(!empty($catids) && $catids) ? $collection->addCategoriesFilter(['in' => $catids]) : '';	
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		$this->_getViewedCount($collection);
		$this->_getOrderedQty($collection);
		$this->_getReviewsCount($collection);
		$tab ? $this->_getOrderFields($collection , $tab) : $this->_getOrderFields($collection);
		$collection->clear();
		$collection->getSelect()->distinct(true);
		if ($count){
			return $collection->count();
		}
		$start = (int)$this->getRequest()->getPost('ajax_listingtabs_start');
		if (!$start) $start = 0;
		$_limit = $limit;
		$_limit = $_limit <= 0 ? 0 : $_limit;
		$collection->getSelect()->limit($_limit, $start);
		return  $collection;
	}
	
	
	
	
	private function _getOrderedQty(& $collection) {
		$connection  = $this->_resource->getConnection();
		$select = $connection
            ->select()
            ->from($connection->getTableName('sales_order_item'), array('product_id', 'ordered_qty' => 'SUM(`qty_ordered`)'))
            ->group('product_id');

        $collection->getSelect()
            ->joinLeft(array('bs' => $select),
                'bs.product_id = e.entity_id');			
        return $collection;
	}
	
	private function _getViewedCount(& $collection) {
		$connection  = $this->_resource->getConnection();
		$select = $connection
			->select()
			->from($connection->getTableName('report_event'), ['*', 'num_view_counts' => 'COUNT(`event_id`)'])
			->where('event_type_id = 1')
			->group('object_id');
		$collection->getSelect()
			->joinLeft(['mv' => $select],
				'mv.object_id = e.entity_id');
		return $collection;			
	}
	
	private function _getReviewsCount(& $collection)
	{	$connection  = $this->_resource->getConnection();
		$collection->getSelect()
			->joinLeft(
				["ra" => $connection->getTableName('review_entity_summary')],
				"e.entity_id = ra.entity_pk_value AND ra.store_id=" . $this->_storeId,
				[
					'num_reviews_count' => "ra.reviews_count",
					'num_rating_summary' => "ra.rating_summary"
				]
			);
		return $collection;
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {	
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $objectManager->get('\Magento\Framework\Url\Helper\Data')->getEncodedUrl($url),
            ]
        ];
    }
   
}
