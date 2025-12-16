<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_programas.php";
$Clprograma = new cl_programas();
$session = getSession();

controller_create();

function crear(){
    global $Clprograma;
    global $session;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $Clprograma->nombre = $txtNombre;
    $Clprograma->precio = $txtPrecio;
    $Clprograma->descripcion = $txtDesc;
    if(0 == $cod_programa){
        if($Clprograma->crear()){
            $return['success'] = 1;
            $return['mensaje'] = "Programa creado";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el programa";
            return $return;
        }
    }
    else{
        $Clprograma->estado = $cmbEstado;
        if($Clprograma->editar($cod_programa)){
            $return['success'] = 1;
            $return['mensaje'] = "Programa editado";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el programa";
        }
    }
    
    return $return;
}

function get(){
    global $Clprograma;
    if(!isset($_GET['cod_programa'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clprograma->get($cod_programa, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Programa encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Programa no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function aceptarPrograma(){
    global $Clprograma;
    extract($_GET);
    $mensaje = "Programa aceptado";
    if("D" == $estado)
        $mensaje = "Programa rechazado";
    $Clprograma->estado = $estado;    
    $Clprograma->precio = $precio;    
    if($Clprograma->aceptarPrograma($cod_programa_usuario)){
        $return['success'] = 1;
        $return['mensaje'] = $mensaje;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al aceptar/rechazar el registro";
    }
    return $return;
}
?>