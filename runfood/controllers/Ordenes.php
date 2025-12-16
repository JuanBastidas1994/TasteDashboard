<?php
/*	Variables Heredadas del Index
		$method - POST, GET, PUT, DELETE, etc.
		$request - Url y variables GET
		$input - Solo metodo POST, PUT */

require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_clientes.php";
require_once "clases/cl_empresas.php";
$Clordenes = new cl_ordenes();

	if($method == "GET"){
		$num_variables = count($request);
		if($num_variables == 2){
			$cod_orden = $request[1];
			$orden = $Clordenes->get_orden_array($cod_orden);
			if($orden){
				$return['success'] = 1;
			    $return['mensaje'] = "informacion de la orden";
			    $return['data'] = $orden;
			}else{
				$return['success'] = 0;
		    	$return['mensaje'] = "No existe orden";
			}
			showResponse($return);
		}
		else if($num_variables == 3){
			if($request[1] == "anular"){
				$return = anular($request[2]);
				showResponse($return);
			}
		}
	}
	else if($method == "POST"){
		$num_variables = count($request);
		if($num_variables == 1){
			$return = crear();
			showResponse($return);
		}
		
		$return['success']= 0;
		$return['mensaje']= "Evento no existente";
		showResponse($return);
	}	
	else{
		$return['success']= 0;
		$return['mensaje']= "El metodo ".$method." para Login aun no esta disponible.";
		showResponse($return);
	}



function get($cod_orden){
	global $Clordenes;

	$orden = $Clordenes->get_orden_array($cod_orden);
	if($orden){
		$return['success'] = 1;
	    $return['mensaje'] = "Orden anulada";
	    $return['data'] = $orden;
	}else{
		$return['success'] = 0;
    	$return['mensaje'] = "No existe orden";
	}
	return $return;
}	

function crear(){
	global $Clordenes;
	global $input;
	$Clusuarios = new cl_usuarios();
	
	$datosObligatorios = array("id","num_documento","subtotal","descuento","envio","iva","total","metodoPago");
	foreach ($datosObligatorios as $key => $value) {
		if (!array_key_exists($value, $input)) {
			$return['success'] = 0;
    		$return['mensaje'] = "Falta informacion, Error: Campo $value es obligatorio";
    		$return['errorCode'] = "INFORMACION_REQUERIDA";
			return $return;
		}
	}

	extract($input);

	$usuario = $Clusuarios->getbyNumDocumento($num_documento);
	if(!$usuario){
		$return['success'] = 0;
	    $return['mensaje'] = "Cliente no existe";
	    $return['errorCode'] = "CLIENTE_INEXISTENTE";
	    return $return;
	}

	/*INICIO VERIFICAR MONTO*/
	$Clclientes = new cl_clientes($num_documento);
	$acu_monto = 0;
	foreach ($metodoPago as $pago) {
		$tipo = $pago['tipo'];
		$monto = $pago['monto'];
		$acu_monto = $acu_monto + $monto;
		if($tipo == "P"){
			$dinero = $Clclientes->GetDinero();
			if($dinero < $monto){
				$return['success'] = 0;
				$return['mensaje'] = "Dinero en Puntos insuficientes. El cliente posee $".$dinero;
				$return['errorCode'] = "DINERO_PUNTOS_INSUFICIENTE";
				return $return;
			}
		}
	}

    $acu_monto = number_format($acu_monto, 2);
    $total = number_format($total, 2);
	if(floatval($acu_monto) != floatval($total)){
		$return['success'] = 0;
		$return['montoAcu'] = $acu_monto;
		$return['totalAcu'] = $total;
		$return['mensaje'] = "El total no coincide con las formas de pago, por favor revisar.";
		$return['errorCode'] = "DESGLOSE_PAGOS_ERROR";
		return $return;
	}
	/*FIN VERIFICAR MONTOS*/

	/*INICIO VERIFICAR ID REPETIDO*/
	$existe = $Clordenes->getRunfood($id);
	if($existe){
		$return['success'] = 0;
	    $return['mensaje'] = "Orden con el Id: $id ya existe";
	    $return['errorCode'] = "ORDEN_ESTADO_CREADA";
	    return $return;
	}
	/*FIN VERIFICAR ID REPETIDO*/

	$id=0;
	$cod_usuario = $usuario['cod_usuario'];
	if($Clordenes->crear($input, $cod_usuario, $id)){
		$return['success'] = 1;
	    $return['mensaje'] = "Orden creada correctamente";
	    $return['id'] = $id;

	    //require_once "Fidelizacion.php";
	    calcular($num_documento);

	}else{
		$return['success'] = 0;
	    $return['mensaje'] = "No se pudo crear la orden, por favor intentelo nuevamente";
	    $return['errorCode'] = "ORDEN_CREATE_ERROR";
	}
	return $return;
}	


/*FUNCIONES*/
function calcular($pcedula){
	$Clempresas = new cl_empresas();
	$config = $Clempresas->getFidelizacion();
	$divisor = $config['divisor_puntos'];

	$cedula = $pcedula;
	$Clclientes = new cl_clientes($cedula);

	if(!$Clclientes->get()){
		$return['success'] = 0;
    	$return['mensaje'] = "Cliente no existente";
		return $return;
	}

	$ordenes = $Clclientes->ordenes_faltantes($cedula);
	if($ordenes){
		foreach ($ordenes as $items){
			$pago = $Clclientes->ordenes_forma_pago($items['cod_orden']);
			foreach ($pago as $fp) {
				$tipo = $fp['forma_pago'];
				$monto = $fp['monto'];
				if($tipo == "E" || $tipo == "T"){
					$resp = $Clclientes->AumentarSaldos($divisor, $cedula, $monto, $items['cod_orden']);
				}
				if($tipo == "P"){
					$resp = $Clclientes->DecrementarDinero($cedula, $monto);
				}
			}
			$Clclientes->orden_complete($items['cod_orden']);
		}
		$return['success'] = 1;
	    $return['mensaje'] = "Acumulo puntos";
	    $return['respuesta'] = $resp;
		return $return;
	}else{
		$return['success'] = 0;
    	$return['mensaje'] = "No hay nada que acumular";
		return $return;
	}
}

function anular($codigo){
	global $Clordenes;

	//$orden = $Clordenes->get($codigo);
	$orden = $Clordenes->getRunfood($codigo);
	if($orden){
		if($orden['estado'] == 'ANULADA'){
			$return['success'] = 0;
    		$return['mensaje'] = "La orden $codigo ya se encuentra anulada";
    		$return['errorCode'] = "ORDEN_ESTADO_ANULADA";
			return $return;
		}

		$cod_orden = $orden['cod_orden']; 
		$Clordenes->anularFactura($cod_orden);
		$return['success'] = 1;
	    $return['mensaje'] = "Orden anulada";
	    $return['id'] = $cod_orden;
	}else{
		$return['success'] = 0;
    	$return['mensaje'] = "No existe orden";
    	$return['errorCode'] = "ORDEN_INEXISTENTE";
	}
	return $return;
}
?>