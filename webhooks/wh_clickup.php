<?php
$request = file_get_contents("php://input");
$log_request = $request;

$fecha = date('Y-m-d H:i:s');
$mensaje = $fecha.' - '.$log_request;
file_put_contents("webhook_clickup.log", PHP_EOL . $mensaje, FILE_APPEND);

?>