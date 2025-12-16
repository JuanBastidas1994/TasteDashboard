<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_usuarios.php";
$Clusuarios = new cl_usuarios();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getIntentosPagos() {
    global $Clusuarios;
    extract($_POST);
    if(!isset($fechaInicio) || !isset($fechaFin)) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta información";
        return $return;
    }

    $intentos = $Clusuarios->getIntentosPagos($fechaInicio, $fechaFin);
    if(!$intentos) {
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
        return $return;
    }

    foreach ($intentos as &$intento) {

        $intento["detalle"] = "No hay detalle";
        if($intento["cod_proveedor_botonpagos"] == 2) {
            $detalle = json_decode($intento["json"], true);
            $intento["detalle"] = $detalle["transaction"]["message"];
        }

        $intento["fraude"] = $intento["fraude"] == 1? "Sí" : "No";
        
        $intento["badge"] = "success";
        if($intento["tipo"] == "failure")
            $intento["badge"] = "danger";
    }

    $return['success'] = 1;
    $return['mensaje'] = "Lista de intentos de pagos";
    $return['data'] = $intentos;
    return $return;
}
?>