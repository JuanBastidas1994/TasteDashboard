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
file_put_contents("pedidosya/request.log", PHP_EOL . $mensaje, FILE_APPEND);

$obj = json_decode($request);
if (JSON_ERROR_NONE !== json_last_error()){
	$return['success']= -1;
	$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
	
	file_put_contents("pedidosya/json-error.log", PHP_EOL . $return['mensaje']." ".$request, FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}



if(!isset($obj->referenceId)){
    $return['success']= 0;
	$return['mensaje']= "El Json no es proporcionado por PedidosYa";
	
	file_put_contents("pedidosya/json-no-pedidosya.log", PHP_EOL . $return['mensaje']." ".$request, FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
} 

$shippingId= $obj->id;
$Infostatus = $obj->data->status;
$estado = "NO_STATUS";

$orden = $Clordenes->get_orden_by_token($shippingId);
$cod_orden = $orden['cod_orden'];
$cod_sucursal = $orden['cod_sucursal'];
$cod_empresa = $orden['cod_empresa'];
 
switch ($Infostatus) {
    case "IN_PROGRESS":        //El conductor ha aceptado el pedido y está en camino para recoger el paquete.
        $estado = "ASIGNADA";
        $Clordenes->asignarMotorizadoGacela($cod_orden, "Repartidor", "PedidosYa", "0999999999", "GGG-123", "https://dashboard.mie-commerce.com/assets/img/pedidosya.png", "0999999999");
        $Clordenes->orderHistorial($cod_orden, "ORDEN_ACEPTADA", $fecha);
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ASIGNADA");
        break;
    case "NEAR_PICKUP":       //El conductor ha llegado a la tienda y está listo para recoger el paquete.
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegue a la tienda');
        $Clordenes->orderHistorial($cod_orden, "PUNTO_RECOGIDA", $fecha);
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_RECOGIDA");
        break;    
    case "PICKED_UP":      //El conductor ha salido de la tienda y se dirige a entregar el paquete al cliente.
        $estado = "ENVIANDO";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'En camino a entregar el pedido');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENVIANDO");
        break;
    case "NEAR_DROPOFF":      //El conductor ha llegado al lugar de entrega
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegue al lugar de entrega');
        $Clordenes->orderHistorial($cod_orden, "PUNTO_ENTREGA", $fecha);
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_ENTREGA");
        break;
    case "COMPLETED":       //El conductor ha entregado correctamente el paquete.
        $estado = "ENTREGADA";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Entregue correctamente el paquete');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENTREGADA");
        break;    
    case "CANCELLED":   //El conductor no pudo entregar el paquete.
        $estado = "NO_ENTREGADA";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'No se pudo entregar el paquete');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "NO_ENTREGADA");
        $motivo = ($obj->data->cancelReason !== null) ? $obj->data->cancelReason : "";
        $Clordenes->setCourierCanceled($cod_orden, 5, $shippingId, $motivo);
        $Clordenes->orderHistorial($cod_orden, "CANCELADA_COURIER", $fecha);
        break;     
}

file_put_contents("pedidosya/".$Infostatus.".log", PHP_EOL . $mensaje, FILE_APPEND);
    
    //RESPUESTA FINAL
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