<?php
require_once "../funciones.php";

require_once "../clases/cl_ordenes.php";
$Clordenes = new cl_ordenes(NULL);
global $Clordenes;

error_reporting(E_ALL);

$request = file_get_contents("php://input");
$log_request = $request;

$fecha = fecha();
$mensaje = $fecha.' - '.$log_request;
file_put_contents("webhook_picker.log", PHP_EOL . $mensaje, FILE_APPEND);

$obj = json_decode($request);
if (JSON_ERROR_NONE !== json_last_error()){
	$return['success']= -1;
	$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
	
	file_put_contents("webhook_picker.log", PHP_EOL . $return['mensaje'], FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}

if(!isset($obj->bookingID)){
    $return['success']= 0;
	$return['mensaje']= "El Json no es proporcionado por Picker";
	
	file_put_contents("webhook_picker.log", PHP_EOL . $return['mensaje'], FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
} 

$orden = $Clordenes->get_orden_by_token($obj->bookingID);
if(!$orden){
    $return['success']= 0;
	$return['mensaje']= "bookingID no encontrado en nuestro sistema";
	
	file_put_contents("webhook_picker.log", PHP_EOL . $return['mensaje'], FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}

$cod_orden = $orden['cod_orden'];
$order_token= $obj->bookingID;
$estado = "NO_STATUS";

if(isset($obj->driverName)){        //ASIGNADA
    $estado = "ASIGNADA";
    $Clordenes->asignarMotorizadoGacela($cod_orden, $obj->driverName,"","","",$obj->driverImage->thumbnail,$obj->driverMobile);
    $Clordenes->orderHistorial($cod_orden, "ORDEN_ACEPTADA", $fecha);
    addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ASIGNADA");
}else{                          //RESTO DE ESTADOS
    $status = $obj->statusText;
    switch($status){
        case "ARRIVED_AT_PICKUP":
            $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegué a la tienda');
            $Clordenes->orderHistorial($cod_orden, "PUNTO_RECOGIDA", $fecha);
            addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_RECOGIDA");
            break;
        case "WAY_TO_DELIVER":
            $estado = "ENVIANDO";
            $Clordenes->set_estado($cod_orden, $estado);
            $Clordenes->updateProcesoMotorizado($cod_orden, 'En camino a entregar el pedido');
            addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENVIANDO");
            break;
        case "ARRIVED_AT_DELIVERY":
            $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegué al lugar de entrega');
            $Clordenes->orderHistorial($cod_orden, "PUNTO_ENTREGA", $fecha);
            addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_ENTREGA");
            break;
        case "COMPLETED":
            $estado = "ENTREGADA";
            $Clordenes->set_estado($cod_orden, $estado);
            $Clordenes->updateProcesoMotorizado($cod_orden, 'Entregué correctamente el paquete');
            addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENTREGADA");
            break;
    }
}

/*RESPUESTA FINAL*/
$return['success']= 1;
$return['mensaje']= "Hizo el proceso en la orden ".$cod_orden." - Status: ".$estado;
header("Content-type:application/json; charset=utf-8");
echo json_encode($return);

function addOrdenFirebase($cod_empresa, $id, $sucursal, $estado){
    global $Clordenes;
    $empresa = $Clordenes->getEmpresaByCodEmpresa($cod_empresa);
    if($empresa){
        $alias = $empresa['alias'];

        $ProyectId = "ptoventa-3b5ed";
        $data = '{"estado":"'.$estado.'","id":'.$id.',"sucursal":'.$sucursal.'}';
        try {
            $ch = curl_init("https://".$ProyectId.".firebaseio.com/ordenes/".$alias."/".$id.".json");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if(curl_errno($ch)){
                return curl_errno($ch);
            }
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>