<?php

// Captura los datos POST
$id = isset($_GET['id']) ? $_GET['id'] : "";
$cres = isset($_POST['cres']) ? $_POST['cres'] : "";
$status = isset($_POST['transStatus']) ? $_POST['transStatus'] : "";

$paymentData = [
    'preorder_id' => $id,
    'status' => $status,
    'cres' => $cres
];
$queryParams = http_build_query($paymentData);

//Armar la ruta
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$requestUri = '/#/checkout/paymentprocess?' . $queryParams;
if($host !== 'localhost')
    $redirectUrl = $protocol . '://' . $host . $requestUri;
else
    $redirectUrl = 'http://localhost:3000'.$requestUri;

// Redirige a la ruta de React con los parámetros GET
header("Location: $redirectUrl");
exit();

?>