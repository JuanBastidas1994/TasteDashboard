<?php
	/*	Variables Heredadas del Index
		$method - POST, GET, PUT, DELETE, etc.
		$request - Url y variables GET
		$input - Solo metodo POST, PUT */

require_once "clases/cl_empresas.php";
require_once "clases/cl_clientes.php";
$Clempresas = new cl_empresas();
//$Clclientes = new cl_clientes();

$config = $Clempresas->getFidelizacion();
$divisor = $config['divisor_puntos'];

	if($method == "GET"){
		$num_variables = count($request);
		if($num_variables == 2){
			$return = getInfo($request[1]);
			showResponse($return);
		}
		if($num_variables == 3){
			if($request[1] == "calcular"){
				$return = calcular($request[2]);
				showResponse($return);
			}
		}

		$return['success']= 0;
		$return['mensaje']= "Evento no existente";
		showResponse($return);
	}
	else{
		$return['success']= 0;
		$return['mensaje']= "El metodo ".$method." para puntos no esta disponible.";
		showResponse($return);
	}


/*FUNCIONES*/
function calcular($pcedula){
	global $divisor;
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

function getInfo($cedula){
	global $config;
	$clientes = new cl_clientes($cedula);
	if($clientes->get()){
		$nivel = $clientes->getDataNivel();
        $data['total_dinero'] = number_format($clientes->GetDinero(),2);
        $data['total_puntos'] = $clientes->GetPuntos();
        $data['total_saldo'] = floor($clientes->GetSaldo());


        //$data['cant_referidos'] = $config['cant_referidos'];
        //$data['valor_referido_receptor'] = $config['valor_referido_receptor'];
        //$data['cod_para_referir'] = $clientes['cod_para_referir'];
        
        $data['cod_nivel'] = $nivel['cod_nivel'];
        $data['num_nivel'] = $nivel['posicion'];
        $data['nivel'] = strtoupper(html_entity_decode($nivel['nombre']));
        
        //$data['cant_ya_referidos'] = $clientes->getNumReferidos();

        $fecha_act = date_create(fecha());
		$fecha_act = date_format($fecha_act, "d-m-Y H:i");
		$data['fecha_act'] = $fecha_act;
        
        $return['success'] = 1;
        $return['mensaje'] = "Informacion actualizada";

        /*CLIENTE*/
        $return["cliente"]['nombre'] = $clientes->nombre;
        $return["cliente"]['num_documento'] = $clientes->cedula;
        $return["cliente"]['fecha_nac'] = $clientes->fecha_nac;

        $return["data"] = $data;
	}else{
		$return['success'] = 0;
        $return['mensaje'] = "Cliente no existe";
        $return['errorCode'] = "CLIENTE_INEXISTENTE";
	}
	return $return;
}
?>