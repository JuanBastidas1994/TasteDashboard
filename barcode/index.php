<?php
require './BarcodeBase.php';
require './barcode128.php';

if(isset($_GET['id'])){
    $code = $_GET['id'];
}else{
    $code = "123456789";
}

$bar = new emberlabs\Barcode\Code128();
$bar->setData($code);
$bar->setDimensions(300, 150);
$bar->draw();
$b64 = $bar->base64();

header("Content-type: image/jpeg");
echo base64_decode($b64);
//bcode_img64($b64);
?>