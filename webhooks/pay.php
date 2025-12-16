<?php
$alias = $_GET["id"];
require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
error_reporting(E_ALL);
$fecha = fecha();

$ClEmpresas = new cl_empresas();
$empresa = $ClEmpresas->getByAlias($alias);
$apikey = $empresa["api_key"];

$request = file_get_contents("php://input");
$log_request = $request;
$mensaje = $fecha.' - '.$log_request;
if(!file_put_contents($alias . "/request.log", PHP_EOL . $mensaje, FILE_APPEND))
    $return["msg_log"] = "no guardó log";
else
    $return["msg_log"] = "guardó log";


$obj = json_decode($request, true);
if (JSON_ERROR_NONE !== json_last_error()){
	$return['success']= -1;
	$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
	
	file_put_contents($alias . "/json-error.log", PHP_EOL . $return['mensaje']." ".$request, FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}

if(isset($obj["transaction"])) {
    if(($obj["transaction"]["status"] == 1)) {
        $preorden = $obj["transaction"]["order_description"];
        sendPreorden($preorden, $request);
    }
}

function sendPreorden($cod_preorden, $json) {
    
}

header("Content-type:application/json; charset=utf-8");
echo json_encode($return);