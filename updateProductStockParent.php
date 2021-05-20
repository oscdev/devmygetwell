<?php
$allowedIp = array('49.248.5.146');
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
     $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
     $remoteIp = $_SERVER['REMOTE_ADDR'];
}
if (!in_array($remoteIp,$allowedIp)) {
    exit('Not Allowed');
}

use Magento\Framework\App\Bootstrap;
 
require __DIR__ . '/app/bootstrap.php';
 
$params = $_SERVER;
 
$bootstrap = Bootstrap::create(BP, $params);
 
$obj = $bootstrap->getObjectManager();
 
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');



$resource = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();


$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$skuArr = ['BLC-0718', 'BLC-I-02', 'BLC-A02', 'BLC-A15', 'BLC-C03', 'BLC-C07', 'BLC-RX741', 'BLC-BBP55', 'BLC-6015', 'BLC-LP943', 'BLC-1057C', 'BLC-TB-SB-65-14-12-PE', 'BLC-0620V', 'BLC-I-01', 'BLC-A03', 'BLC-A07', 'BLC-A11', 'BLC-B02', 'BLC-C08', 'BLC-RX715', 'BLC-INP', 'BLC-3626', 'BLC-LP710', 'BLC-LP941', 'BLC-LP773', 'BLC-0304V', 'BLC-KE821', 'BLC-J05', 'BLC-HS', 'BLC-A01', 'BLC-A12', 'BLC-B04', 'BLC-C05', 'BLC-SAN', 'BLC-TYN-D03', 'BLC-OEHB-FLA', 'BLC-RX700', 'BLC-3615', 'BLC-1041', 'BLC-LP944', 'BLC-1071', 'BLC-1081', 'BLC-C01', 'BLC-A06U', 'BLC-A09', 'BLC-RX728', 'BLC-INR27', 'BLC-A-375 -1', 'BLC-H1054', 'BLC-6601', 'BLC-0705', 'BLC-CBR', 'BLC-260', 'BLC-EM', 'BLC-A10', 'BLC-ITB', 'BLC-1197', 'BLC-FB', 'BLC-ADR', 'BLC-D01', 'BLC-F13', 'BLC-10469', 'BLC-TYN-E10', 'BLC-T-D08', 'BLC-MSI-1057', 'BLC-ADPU', 'BLC-MCSTL', 'BLC-CAS', 'BLC-302', 'BLC-1274', 'BLC-2032', 'BLC-PRTL', 'BLC-23110', 'BLC-F01', 'BLC-0501', 'BLC-PWR', 'BLC-FR', 'BLC-TBG', 'BLC-RB-1', 'BLC-BP-01', 'BLC-SP', 'BLC-SSSH', 'BLC-1201', 'BLC-C04', 'BLC-FHU', 'BLC-B03', 'BLC-RHC', 'BLC-D07', 'BLC-F04-2', 'BLC-F08U', 'BLC-TYN-I59', 'BLC-TYN-E11', 'BLC-3060C', 'BLC-MSI-1021', 'BLC-MCSKL', 'BLC-303', 'BLC-KTSS', 'BLC-LFER', 'BLC-210', 'BLC-1105V', 'BLC-NMC-2', 'BLC-2610', 'BLC-258', 'BLC-A08', 'BLC-WSH', 'BLC-C10', 'BLC-6030', 'BLC-F05', 'BLC-F09', 'BLC-G02', 'BLC-FAD', 'BLC-F04-19', 'BLC-1104A', 'BLC-MSI-1056', 'BLC-PHS', 'BLC-DP', 'BLC-HW13', 'BLC-213', 'BLC-233', 'BLC-G11', 'BLC-H1042', 'BLC-0101', 'BLC-1185', 'BLC-2587', 'BLC-A019', 'BLC-C11', 'BLC-A14', 'BLC-E01', 'BLC-F06', 'BLC-F10', 'BLC-WAD', 'BLC-F04-24', 'BLC-MV', 'BLC-UK', 'BLC-105', 'BLC-DRS', 'BLC-405', 'BLC-MEDIG', 'BLC-CL', 'BLC-208', 'BLC-2173', 'BLC-1042', ' BLC-7236','BLC-C06'];


// $registry = $obj->get('\Magento\Framework\Registry');
// $registry->register('isSecureArea', true);
foreach ($skuArr as $sku) {
  $productRepository = $obj->get('\Magento\Catalog\Model\ProductRepository');
  
  try {
      $product = $productRepository->get($sku);
        $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 1, 2, 0.0000, 1)";
        $connection->query($sql);
        $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 2, 3, 0.0000, 1)";
        $connection->query($sql);
        $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 3, 4, 0.0000, 1)";
        $connection->query($sql);
        $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 4, 5, 0.0000, 1)";
        $connection->query($sql);
      echo $product->getId() . "<br>";

  } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
      $product = false;
      echo '<b>'.$sku.' Not Exists</b><br/>';
      continue;
  }
}
?>