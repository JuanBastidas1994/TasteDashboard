<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_app_versiones.php";
$ClappVersiones = new cl_app_versiones();

$session = getSession();

controller_create();

function crear(){
    global $ClappVersiones;
    
    extract($_POST);
    $ClappVersiones->name = $txt_name;
    $ClappVersiones->code = $txt_code;
    $ClappVersiones->texto = $txt_texto;
    $ClappVersiones->obligatorio = $txt_obligatorio;
    $ClappVersiones->aplicacion = $cmb_aplicacion;
    $ClappVersiones->descripción = $txt_descripcion;

    if($ClappVersiones->crear($cod_empresa)){
        $return['success'] = 1;
        $return['mensaje'] = "Versión creada correctamente";
        
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al crear la versión";
    }
    return $return;
}

function eliminar(){
    global $ClappVersiones;
    extract($_GET);

    if($ClappVersiones->eliminar($cod_empresa_version)){
        $return['success'] = 1;
        $return['mensaje'] = "Versión eliminada";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar";
    }

    return $return;
}
?>