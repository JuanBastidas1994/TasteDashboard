<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_promociones.php";
$Clpromociones = new cl_marketing_envios(NULL);
$session = getSession();

error_reporting(E_ALL);
controller_create();

function consultarPromoExistente(){
    global $Clpromociones;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    
     extract($_POST);
    
    $existe = false;
    $Clpromociones->fecha_inicio = $fecha_inicio;
    $Clpromociones->fecha_fin = $fecha_fin;
    for ($j=0; $j < count($cmb_sucursales); $j++) { 
        $cod_sucursal = $cmb_sucursales[$j];
        if($Clpromociones->getExistente($cod_sucursal, $promos)){
            $existe = true;
        }
        
    }
    if($existe){
        $return['success'] = 1;
        $return['mensaje'] = "Hay promo";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay promo ".$cod_sucursal;
    }
    return $return;
}

function crear(){
    global $Clpromociones;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    for ($j=0; $j < count($cmb_sucursales); $j++) { 
        $cod_sucursal = $cmb_sucursales[$j];
        if($eliminar)
            $Clpromociones->eliminarVarios($cod_sucursal);

        if($rb_descuento == 0)
            $txt_porcentaje = 100;

        if($rb_monto == 0)
            $txt_monto = 0;

        $Clpromociones->cod_sucursal = $cod_sucursal;
        $Clpromociones->fecha_inicio = $fecha_inicio;
        $Clpromociones->fecha_fin = $fecha_fin;
        $Clpromociones->porcentaje = intval($txt_porcentaje);
        $Clpromociones->monto = intval($txt_monto);
        $Clpromociones->solo_horario = isset($ckHours) ? 1 : 0;
        $Clpromociones->dias = "";

        if(isset($ckHours)) {
            $Clpromociones->dias = $dias;
        }
        
        $id = 0;
        if($Clpromociones->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Promocion creada correctamente";
            $return['id'] = $id;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la promocion, por favor vuelva a intentarlo";
        }

    }
    $return['success'] = 1;
    $return['mensaje'] = "Promociones creadas correctamente";
    return $return;
}

function get(){
    global $session;
    global $Clpromociones;
    if(!isset($_GET['cod_promocion'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clpromociones->getArray($cod_promocion, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Promocion encontrada";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Promocion no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $Clpromociones;
	if(!isset($_GET['cod_promocion']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clpromociones->set_estado($cod_promocion, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Promocion editada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la promocion";
    }
    return $return;
}

function eliminar(){
	global $Clpromociones;
	if(!isset($_GET['cod_promocion']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clpromociones->eliminar($cod_promocion);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Promocion eliminada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la promocion";
    }
    return $return;
}

?>