<?php
 ini_set('memory_limit','1600M');
include('app/bootstrap.php');

use Magento\Framework\App\Bootstrap;

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
 $state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

 $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
/** Apply filters here */
$products = $productCollection->load();

  foreach ($products as $product)
  {
	  $product_id= $product->getId();
	  
	  
	   $loader = $objectManager->get('Magento\Catalog\Model\ProductFactory');
	  
	 
        $product = $loader->create()->load($product_id);
		$product->save();
		
  }