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

$skuArr = ['BLC-0718M', 'BLC-0718L', 'BLC-0718XL', 'BLC-0718XXL', 'BLC-0718', 'BLC-I-02L', 'BLC-I-02M', 'BLC-I-02S', 'BLC-I-02', 'BLC-A02-S', 'BLC-A02-M', 'BLC-A02-L', 'BLC-A02-XL', 'BLC-A02-XXL', 'BLC-A02', 'BLC-A15-2', 'BLC-A15-3', 'BLC-A15', 'BLC-C03-2', 'BLC-C03-3', 'BLC-C03', 'BLC-C07-S', 'BLC-C07-M', 'BLC-C07-L', 'BLC-C07-XL', 'BLC-C07', 'BLC-RX741-S', 'BLC-RX741-M', 'BLC-RX741-L', 'BLC-RX741-XL', 'BLC-RX741-XXL', 'BLC-RX741', 'BLC-BBP55', 'BLC-BBP12', 'BLC-BBP55', 'BLC-6017A', 'BLC-6017B', 'BLC-6017C', 'BLC-6015', 'BLC-LP943S', 'BLC-LP943M', 'BLC-LP943L', 'BLC-LP943SL', 'BLC-LP943', 'BLC-1057CP', 'BLC-1057CB', 'BLC-1057CG', 'BLC-1057C', 'BLC-TB-SB-65-17-12-OR', 'BLC-TB-SB-65-20-12-LG', 'BLC-TB-SB-65-23-12-BL', 'BLC-TB-SB-65-14-12-PE', 'BLC-0620S', 'BLC-0620M', 'BLC-0620V', 'BLC-I-01L', 'BLC-I-01M', 'BLC-I-01S', 'BLC-I-01', 'BLC-A03-S', 'BLC-A03-M', 'BLC-A03-L', 'BLC-A03-XL', 'BLC-A03-XXL', 'BLC-A03', 'BLC-A07-1', 'BLC-A07-3', 'BLC-A07', 'BLC-A11-4', 'BLC-A11-5', 'BLC-A11', 'BLC-B02-1', 'BLC-B02-2', 'BLC-B02-3', 'BLC-B02', 'BLC-C08-S', 'BLC-C08-M', 'BLC-C08-L', 'BLC-C08', 'BLC-RX715-S', 'BLC-RX715-M', 'BLC-RX715-L', 'BLC-RX715-XL', 'BLC-RX715-XXL', 'BLC-RX715', 'BLC-INP-S2', 'BLC-INP-S4', 'BLC-INP', 'BLC-3626', 'BLC-3626A', 'BLC-3626', 'BLC-LP710M', 'BLC-LP710L', 'BLC-LP710', 'BLC-LP941M', 'BLC-LP941XL', 'BLC-LP941XXL', 'BLC-LP941', 'BLC-LP773M', 'BLC-LP773L', 'BLC-LP773XL', 'BLC-LP773', 'BLC-0304M', 'BLC-0304L', 'BLC-0304V', 'BLC-KE821-S', 'BLC-KE821-M', 'BLC-KE821-L', 'BLC-KE821-XL', 'BLC-KE821', 'BLC-J05S', 'BLC-J05U', 'BLC-T-J05-U', 'BLC-T-J05-S', 'BLC-J05', 'BLC-HSM', 'BLC-HSS', 'BLC-HS', 'BLC-A01-S', 'BLC-A01-M', 'BLC-A01-L', 'BLC-A01-XL', 'BLC-A01-XXL', 'BLC-A01', 'BLC-A12U', 'BLC-A12XXL', 'BLC-A12', 'BLC-B04-1', 'BLC-B04-2', 'BLC-B04-3', 'BLC-B04', 'BLC-C05-S', 'BLC-C05-M', 'BLC-C05-L', 'BLC-C05-XL', 'BLC-C05', 'BLC-SAN-BB', 'BLC-SAN-LG', 'BLC-SAN-OL', 'BLC-SAN', 'BLC-T-D03-S', 'BLC-T-D03-M', 'BLC-T-D03-L', 'BLC-T-D03-XL', 'BLC-TYN-D03', 'BLC-OEHB-FLA-M', 'BLC-OEHB-FLA-R', 'BLC-OEHB-FLA-XL', 'BLC-OEHB-FLA', 'BLC-RX700-S', 'BLC-RX700-M', 'BLC-RX700-L', 'BLC-RX700-XL', 'BLC-RX700-XXL', 'BLC-RX700', 'BLC-3615', 'BLC-3615A', 'BLC-3615', 'BLC-1041B', 'BLC-1041C', 'BLC-1041D', 'BLC-1041E', 'BLC-1041', 'BLC-LP944S', 'BLC-LP944', 'BLC-1071A', 'BLC-1071B', 'BLC-1071', 'BLC-1081-45', 'HQ-X8VL-KDKJ', 'BLC-1081', 'BLC-C01-2', 'BLC-C01-1', 'BLC-C01', 'BLC-A06-1', 'BLC-A06-2', 'BLC-A06U', 'BLC-A09S', 'BLC-A09M', 'BLC-A09L', 'BLC-A09XL', 'BLC-A09', 'BLC-RX728-S', 'BLC-RX728-M', 'BLC-RX728-L', 'BLC-RX728-XL', 'BLC-RX728-XXL', 'BLC-RX728', 'BLC-IBR19', 'BLC-INR27', 'BLC-INR27', 'BLC-1042B', 'BLC-1042C', 'BLC-1042D', 'BLC-1042E', 'BLC-A-375 -1', 'BLC-A-375', 'BLC-A-375-2', 'BLC-A-375 -1', 'BLC-H1054RD', 'BLC-H1054GN', 'BLC-H1054BL', 'BLC-H1054', 'BLC-6601MED', 'BLC-6601SOF', 'BLC-6601', 'BLC-0705M', 'BLC-0705L', 'BLC-0705XL', 'BLC-0705XXL', 'BLC-0705', 'BLC-CBR-4INCH', 'BLC-CBR-5INCH', 'BLC-CBR-5.5INCH', 'BLC-CBR', 'BLC-26020', 'BLC-26030', 'BLC-26040', 'BLC-26050', 'BLC-26060', 'BLC-26010', 'BLC-260', 'BLC-25054-B', 'BLC-25054-G', 'BLC-25054-R', 'BLC-25073-B', 'BLC-25073-G', 'BLC-25073-R', 'BLC-EM', 'BLC-A10-XL', 'BLC-A10-L', 'BLC-A10-M', 'BLC-A10-S', 'BLC-A10', 'BLC-ITB-60CM', 'BLC-ITB-75CM', 'BLC-ITB', 'BLC-11968', 'BLC-11698', 'BLC-11670', 'BLC-11971', 'BLC-11972', 'BLC-11973', 'BLC-11974', 'BLC-1197', 'BLC-FB-20CM', 'BLC-FB-25CM', 'BLC-FB', 'BLC-ADR-M', 'BLC-ADR-L', 'BLC-ADR-XL', 'BLC-ADR-PS', 'BLC-ADR', 'BLC-D01S', 'BLC-D01M', 'BLC-D01L', 'BLC-D01XL', 'BLC-D01', 'BLC-F13-L-R', 'BLC-F13-L-L', 'BLC-F13-M-R', 'BLC-F13-M-L', 'BLC-F13-S-R', 'BLC-F13-S-L', 'BLC-F13', 'BLC-T-D01-S', 'BLC-M10469', 'BLC-L10469', 'BLC-T-D01-XL', 'BLC-10469', 'BLC-T-E10-S', 'BLC-T-E10-M', 'BLC-T-E10-L', 'BLC-T-E10-XL', 'BLC-TYN-E10', 'BLC-T-D08S', 'BLC-T-D08M', 'BLC-T-D08L', 'BLC-T-D08XL', 'BLC-T-D08', 'BLC-1107A', 'BLC-1107B', 'BLC-MSI-1057', 'BLC-ADPU-M', 'BLC-ADPU-XL', 'BLC-ADPU', 'BLC-MCSTL-S', 'BLC-MCSTL-M', 'BLC-MCSTL-L', 'BLC-MCSTL-XL', 'BLC-MCSTL-XS', 'BLC-MCSTL-XXL', 'BLC-MCSTL-XXXL', 'BLC-MCSTL', 'BLC-CAS-S', 'BLC-CAS-M', 'BLC-CAS-L', 'BLC-CAS-XL', 'BLC-CAS-XXL', 'BLC-CAS', 'BLC-302-1', 'BLC-302-2', 'BLC-302', 'BLC-12743', 'BLC-12744', 'BLC-12745', 'BLC-12746', 'BLC-12747', 'BLC-12748', 'BLC-12749', 'BLC-1274', 'BLC-20324', 'BLC-20364', 'BLC-2032', 'BLC-21431', 'BLC-21432', 'BLC-21433', 'BLC-21434', 'BLC-PRTL', 'BLC-23110', 'BLC-23120', 'BLC-23130', 'BLC-23140', 'BLC23150', 'BLC-23110', 'BLC-F01-S', 'BLC-F01-M', 'BLC-F01-L', 'BLC-F01-XL', 'BLC-F01', 'BLC-501M', 'BLC-501L', 'BLC-501XL', 'BLC-0501', 'BLC-PWR-1.6CM', 'BLC-PWR-1.8CM', 'BLC-PWR-2.1CM', 'BLC-PWR', 'BLC-FR-F90CM', 'BLC-FR-F45CM', 'BLC-FR-S30CM', 'BLC-FR-S45CM', 'BLC-FR-S90CM', 'BLC-FR-H33CM', 'BLC-FR-H45CM', 'BLC-FR-H60CM', 'BLC-FR', 'BLC-TBG-M', 'BLC-TBG-L', 'BLC-TBG', 'BLC-REB-L0', 'BLC-REB-MG', 'BLC-REB-HB', 'BLC-RB-1', 'BLC-BP-TBR', 'BLC-BP_WB', 'BLC-BP-01', 'BLC-SP-22160', 'BLC-SP-27314', 'BLC-SP', 'BLC-SSSH-30CM', 'BLC-SSSH-60CM', 'BLC-SSSH-75CM', 'BLC-SSSH', 'BLC-12010', 'BLC-12011', 'BLC-1201', 'BLC-C04-2', 'BLC-C04-3', 'BLC-C04-4', 'BLC-C04-5', 'BLC-C04-6', 'BLC-C04', 'BLC-UB30', 'BLC-UB60', 'BLC-FHU', 'BLC-B03-2', 'BLC-B03-3', 'BLC-B03', 'BLC-RHC-2600', 'BLC-RHC-2620', 'BLC-RHC', 'BLC-D07-1', 'BLC-D07-2', 'BLC-D07', 'BLC-F04-6', 'BLC-F04-7', 'BLC-F04-3', 'BLC-F04-9', 'BLC-F04-2', 'BLC-F08-U', 'BLC-F08-XXL', 'BLC-F08U', 'BLC-M2201', 'BLC-W7022', 'BLC-W6302', 'BLC-TYN-I59', 'BLC-T-E11-S', 'BLC-T-E11-M', 'BLC-T-E11-L', 'BLC-T-E11-XL', 'BLC-TYN-E11', 'BLC-1105A', 'BLC-1105B', 'BLC-1105C', 'BLC-3060C', 'BLC-4076B', 'BLC-4076C', 'BLC-4076D', 'BLC-MSI-1021', 'BLC-MCSKL-XS', 'BLC-MCSKL-S', 'BLC-MCSKL-M', 'BLC-MCSKL-L', 'BLC-MCSKL-XL', 'BLC-MCSKL-XXL', 'BLC-MCSKL-XXXL', 'BLC-MCSKL', 'BLC-303-1', 'BLC-303-2', 'BLC-303', 'BLC-KTSS-6', 'BLC-KTSS-8', 'BLC-KTSS-10', 'BLC-KTSS-12', 'BLC-KTSS', '20324', '20334', '20344', '20354', '20364', '20374', '20384', 'BLC-LFER', 'BLC-21010', 'BLC-21020', 'BLC-21030', 'BLC-21040', 'BLC-21050', 'BLC-21060', 'BLC-21070', 'BLC-210', 'BLC-1105-L', 'BLC-1105-XL', 'BLC-1105V', 'BLC-NMG-NMC-2', 'BLC-NMG-NMS-1', 'BLC-NMC-2', 'BLC-26107', 'BLC-26100', 'BLC-26101', 'BLC-26102', 'BLC-2610', 'BLC-25811', 'BLC-25821', 'BLC-25831', 'BLC-25841', 'BLC-25851', 'BLC-25861', 'BLC-25871', 'BLC-258', 'BLC-A08-S', 'BLC-A08-M', 'BLC-A08-L', 'BLC-A08-XL', 'BLC-A08', 'BLC-WSH-30CM', 'BLC-WSH-60CM', 'BLC-WSH-75CM', 'BLC-WSH', 'BLC-C10-S', 'BLC-C10-M', 'BLC-C10-L', 'BLC-C10-XXL', 'BLC-C10', 'BLC-6030B', 'BLC-6030C', 'BLC-6030A', 'BLC-6030', 'BLC-F05-S', 'BLC-F05-M', 'BLC-F05-L', 'BLC-F05-XL', 'BLC-F05', 'BLC-F09-M', 'BLC-F09-S', 'BLC-F09-L', 'BLC-F09', 'BLC-G02-S', 'BLC-G02-M', 'BLC-G02-L', 'BLC-G02', 'BLC-FAD-M', 'BLC-FAD-L', 'BLC-FAD-XL', 'BLC-FAD', 'BLC-F04-19-S', 'BLC-F04-19-M', 'BLC-F04-19-L', 'BLC-F04-19-XL', 'BLC-F04-19-XXL', 'BLC-F04-S', 'BLC-F04-M', 'BLC-F04-L', 'BLC-F04-XL', 'BLC-F04-19', 'BLC-1104A', 'BLC-1104B', 'BLC-1104C', 'BLC-1104A', 'BLC-1106A', 'BLC-1106B', 'BLC-MSI-1056', 'BLC-PHS-PWS', 'BLC-PHS-PWUS', 'BLC-PHS-XS', 'BLC-PHS-S', 'BLC-PHS-M', 'BLC-PHS-L', 'BLC-PHS-XL', 'BLC-PHS-XXL', 'BLC-PHS-XXXL', 'BLC-PHS', '12771', '12772', '12773', '12774', '12775', 'BLC-DP', 'BLC-HW13', 'BLC-HW25', 'BLC-HWB28', 'BLC-HW28', 'BLC-HW2030', 'BLC-HWV', 'BLC-HW13', 'BLC-21303', 'BLC-21313', 'BLC-21304', 'BLC-21314', 'BLC-213', 'BLC-23305', 'BLC-23304', 'BLC-23307', 'BLC-23306', 'BLC-23323', 'BLC-12424', 'BLC-233', 'BLC-G11-L', 'BLC-G11-U', 'BLC-G11', 'BLC-H1042RD', 'BLC-H1042GN', 'BLC-H1042BL', 'BLC-H1042BK', 'BLC-H1042', 'BLC-0101-L', 'BLC-0101-M', 'BLC-0101-XL', 'BLC-0101', 'BLC-11853', 'BLC-11854', 'BLC-11855', 'BLC-11856', 'BLC-11857', 'BLC-1185', 'BLC-25870', 'BLC-25871', 'BLC-25872', 'BLC-2587', 'BLC-A019M', 'BLC-A019L', 'BLC-A019XXL', 'BLC-A019', 'BLC-C11-R', 'BLC-C11-L', 'BLC-C11', 'BLC-A14-2', 'BLC-A14-3', 'BLC-A14', 'BLC-E01-1', 'BLC-E01-2', 'BLC-E01', 'BLC-F06-1', 'BLC-F06-2', 'BLC-F06', 'BLC-F10-S', 'BLC-F10-M', 'BLC-F10-L', 'BLC-F10-XL', 'BLC-F10', 'BLC-WAD-M', 'BLC-WAD-L', 'BLC-WAD-XL', 'BLC-WAD', 'BLC-04-24-S', 'BLC-04-24-M', 'BLC-04-24-L', 'BLC-04-24-XL', 'BLC-04-24-XXL', 'BLC-F04-24', 'BLC-MV', 'BLC-MVP', 'BLC-MV', 'BLC-UK-S', 'BLC-UK-M', 'BLC-UK-L', 'BLC-UK-XL', 'BLC-UK', 'BLC-105-A', 'BLC-105-B', 'BLC-105-C', 'BLC-105-D', 'BLC-105-E', 'BLC-105-F', 'BLC-105-G', 'BLC-105-H', 'BLC-105', 'BLC-DRS-0.5', 'BLC-DRS-1.0', 'BLC-DRS-1.5', 'BLC-DRS-2.0', 'BLC-DRS-2.5', 'BLC-DRS', 'BLC-405A', 'BLC-405B', 'BLC-405C', 'BLC-405', 'BLC-MEDIG-S', 'BLC-MEDIG-M', 'BLC-MEDIG-L', 'BLC-MEDIG', 'BLC-CL-12771', 'BLC-CL-12772', 'BLC-CL-12773', 'BLC-CL-12774', 'BLC-CL-12775', 'BLC-CL-12776', 'BLC-CL-12777', 'BLC-CL', '20811', '20821', '20831', '20841', 'BLC-208', 'BLC-21731', 'BLC-21732', 'BLC-21733', 'BLC-21734', 'BLC-21735', 'BLC-21736', 'BLC-2173', 'BLC-7236B', 'BLC-7236G', 'BLC-7236R', 'BLC-7236', 'BLC-C06-S', 'BLC-C06-M', 'BLC-C06-L', 'BLC-C06'];


// $registry = $obj->get('\Magento\Framework\Registry');
// $registry->register('isSecureArea', true);
foreach ($skuArr as $sku) {
  $productRepository = $obj->get('\Magento\Catalog\Model\ProductRepository');
  
  try {
      $product = $productRepository->get($sku);
      $statusStockSel = "SELECT *  FROM `cataloginventory_stock_status` WHERE `product_id`=".$product->getId()." AND `website_id`=0";
      $statusStockSelResult = $connection->query($statusStockSel);
      echo $product->getQty(). " == " . $statusStockSelResult->rowCount() ."<br/>";
      if($statusStockSelResult->rowCount() > 0)
      {
        $fetchAllIdArr = $statusStockSelResult->fetchAll();

        $statusStock1Sel = "SELECT *  FROM `cataloginventory_stock_status` WHERE `product_id`=".$product->getId()." AND `website_id`=1";
        $statusStock1SelResult = $connection->query($statusStock1Sel);
        if(!$statusStock1SelResult->rowCount()) {
            $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 1, 2, ".$fetchAllIdArr[0]['qty'].", 1)";
            $connection->query($sql);
        }

        $statusStock2Sel = "SELECT *  FROM `cataloginventory_stock_status` WHERE `product_id`=".$product->getId()." AND `website_id`=2";
        $statusStock2SelResult = $connection->query($statusStock2Sel);
        if(!$statusStock2SelResult->rowCount()) {
            $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 2, 3, ".$fetchAllIdArr[0]['qty'].", 1)";
            $connection->query($sql);
        }

        $statusStock3Sel = "SELECT *  FROM `cataloginventory_stock_status` WHERE `product_id`=".$product->getId()." AND `website_id`=3";
        $statusStock3SelResult = $connection->query($statusStock3Sel);
        if(!$statusStock3SelResult->rowCount()) {
            $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 3, 4, ".$fetchAllIdArr[0]['qty'].", 1)";
            $connection->query($sql);
        }

        $statusStock4Sel = "SELECT *  FROM `cataloginventory_stock_status` WHERE `product_id`=".$product->getId()." AND `website_id`=4";
        $statusStock4SelResult = $connection->query($statusStock4Sel);
        if(!$statusStock4SelResult->rowCount()) {
            $sql = "INSERT INTO `cataloginventory_stock_status`(`product_id`, `website_id`,`stock_id`, `qty`, `stock_status`) VALUES (".$product->getId().", 4, 5, ".$fetchAllIdArr[0]['qty'].", 1)";
            $connection->query($sql);
        }

        echo $fetchAllIdArr[0]['product_id'] . ' - ' . $fetchAllIdArr[0]['qty'] . "<br>";
      }
      
  } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
      $product = false;
      echo '<b>'.$sku.' Not Exists</b><br/>';
      continue;
  }
}
?>