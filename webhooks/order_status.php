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
file_put_contents("gacela/request.log", PHP_EOL . $mensaje, FILE_APPEND);

$obj = json_decode($request);
if (JSON_ERROR_NONE !== json_last_error()){
	$return['success']= -1;
	$return['mensaje']= "El Json de entrada no tiene un formato correcto.";
	
	file_put_contents("gacela/json-error.log", PHP_EOL . $return['mensaje']." ".$request, FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
}

if(!isset($obj->order_token)){
    $return['success']= 0;
	$return['mensaje']= "El Json no es proporcionado por Gacela";
	
	file_put_contents("gacela/json-no-gacela.log", PHP_EOL . $return['mensaje']." ".$request, FILE_APPEND);
    header("Content-type:application/json; charset=utf-8");
    echo json_encode($return);
    exit();
} 

$order_token= $obj->order_token;
$Infostatus = $obj->current_status;
$status = $Infostatus->code;
$created_at = $Infostatus->created_at;
$estado = "NO_STATUS";

$orden = $Clordenes->get_orden_by_token($order_token);
$cod_orden = $orden['cod_orden'];
$cod_sucursal = $orden['cod_sucursal'];
$cod_empresa = $orden['cod_empresa'];

switch ($status) {
    case "ASSIGNED":        //El conductor ha aceptado el pedido y está en camino para recoger el paquete.
        $estado = "ASIGNADA";
        $Infomotorizado= $obj->driver;
        $Clordenes->asignarMotorizadoGacela($cod_orden, $Infomotorizado->name,$Infomotorizado->lastname,$Infomotorizado->document,$Infomotorizado->plate,$Infomotorizado->photo,$Infomotorizado->phone);
        $Clordenes->orderHistorial($cod_orden, "ORDEN_ACEPTADA", $fecha);
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ASIGNADA");
        break;
    case "AT_PICKUP":       //El conductor ha llegado a la tienda y está listo para recoger el paquete.
        if(isset($obj->driver)){
            $Infomotorizado= $obj->driver;
            $Clordenes->reGuardarMotorizadoGacela($cod_orden, $Infomotorizado->name,$Infomotorizado->lastname,$Infomotorizado->document,$Infomotorizado->plate,$Infomotorizado->photo,$Infomotorizado->phone);
        }
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegue a la tienda');
        $Clordenes->orderHistorial($cod_orden, "PUNTO_RECOGIDA", $fecha);
        
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_RECOGIDA");
        break;    
    case "DELIVERING":      //El conductor ha salido de la tienda y se dirige a entregar el paquete al cliente.
        $estado = "ENVIANDO";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'En camino a entregar el pedido');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENVIANDO");
        break;
    case "AT_DROPOFF":      //El conductor ha llegado al lugar de entrega
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Llegue al lugar de entrega');
        $Clordenes->orderHistorial($cod_orden, "PUNTO_ENTREGA", $fecha);
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "PUNTO_ENTREGA");
        break;
    case "DELIVERED":       //El conductor ha entregado correctamente el paquete.
        $estado = "ENTREGADA";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'Entregue correctamente el paquete');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "ENTREGADA");
        break;    
    case "NOT_DELIVERED":   //El conductor no pudo entregar el paquete.
        $estado = "NO_ENTREGADA";
        $Clordenes->set_estado($cod_orden, $estado);
        $Clordenes->updateProcesoMotorizado($cod_orden, 'No se pudo entregar el paquete');
        addOrdenFirebase($cod_empresa, $cod_orden, $cod_sucursal, "NO_ENTREGADA");
        $motivo = ($Infostatus->observation !== null) ? $Infostatus->observation : "";
        $Clordenes->setCourierCanceled($cod_orden, 1, $order_token, $motivo);
        $Clordenes->orderHistorial($cod_orden, "CANCELADA_COURIER", $fecha);
        break;     
}

file_put_contents("gacela/".$status.".log", PHP_EOL . $mensaje, FILE_APPEND);
    
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