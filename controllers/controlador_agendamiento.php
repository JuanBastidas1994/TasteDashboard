<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_agendamiento.php";
$Clagendamiento = new cl_agendamiento();
$session = getSession();

controller_create();

function crear(){
    global $Clagendamiento;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $Clagendamiento->cod_sucursal = $cod_sucursal;
    $Clagendamiento->cod_usuario = $cod_usuario;
    $Clagendamiento->dia = $dia;
    $Clagendamiento->hora_inicio = $hora_inicio;
    $Clagendamiento->hora_final = $hora_final;
    $id = 0;
    if($Clagendamiento->crear($id)){
        $return['success'] = 1;
        $return['mensaje'] = 'Agendamiento guardado';
        $return['id'] = $id;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al guardar la agendamiento";
    }

    $Clagendamiento->eliminarDiaIndisponibilidad();
    $eventos = $Clagendamiento->listarPorDia($cod_usuario);
    construirIndisponibilidad($eventos, $Clagendamiento);
    return $return;
}

function construirIndisponibilidad($eventos, $clagendamiento){
    $auxInicio = 0;
    foreach($eventos as $clave => $evento){
        if($clave == 0){
            $clagendamiento->crearIndisponibilidad('5:00', $eventos[$clave]['hora_inicio']);        
        }else{
            $clagendamiento->crearIndisponibilidad($auxInicio, $eventos[$clave]['hora_inicio']);
        }
        $auxInicio = $eventos[$clave]['hora_final'];
        if($clave+1 == count($eventos))
            $clagendamiento->crearIndisponibilidad($eventos[$clave]['hora_final'], "20:00" );
        
    }    
}



function actualizar(){
    global $Clagendamiento;
    global $session;
    if(count($_POST) == 0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
    }
    extract($_POST);
    $diaAnterior = $Clagendamiento->obtenerDiaId($cod_disponibilidad)['dia'];

    $Clagendamiento->cod_sucursal = $cod_sucursal;
    $Clagendamiento->hora_inicio = $hora_inicio;
    $Clagendamiento->hora_final = $hora_final;
    $Clagendamiento->dia =$dia;
    $Clagendamiento->cod_usuario = $cod_usuario;

  
    if($Clagendamiento->actualizar($cod_disponibilidad)){
        $return['success'] = 1;
        $return['mensaje'] = 'Actualizacion completada';
    }else{
        $return['success'] = 0;
        $return['mensaje'] = 'No encontrado';
    }

    $Clagendamiento->eliminarDiaIndisponibilidad();
    $eventos = $Clagendamiento->listarPorDia($cod_usuario);
    construirIndisponibilidad($eventos, $Clagendamiento);

    $Clagendamiento->dia = $diaAnterior;
    $Clagendamiento->eliminarDiaIndisponibilidad();
    $eventos = $Clagendamiento->listarPorDia($cod_usuario);
    construirIndisponibilidad($eventos, $Clagendamiento);
    
    return $return;
}

function eliminar(){
    global $Clagendamiento;
    global $session;
    if(!isset($_GET['cod_disponibilidad'])){
        $return['success'] = 0;
        $return['mensaje'] = 'Falta informacion';
        return $return;
    }
    extract($_GET);

    if($Clagendamiento->eliminarDisponibilidad($cod_disponibilidad)){
        $return['success'] = 1;
        $return['mensaje'] = 'Disponibilidad eliminada con exito';
        return $return;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = 'Error eliminando la informacion';
        return $return;
    }

    
}

function obtenerPorUsuario(){
    global $Clagendamiento;
    global $session;
    if(!isset($_GET['cod_usuario'])){
        $return['success'] = 0;
        $return['mensaje'] = 'Falta informacion';
        return $return;
    }
    extract($_GET);
    $events = $Clagendamiento->listarPorUsuario($cod_usuario);
    $arrayEventos = [];
    foreach($events as $clave=>$event){
        $x = new stdClass();
        $x->dow = $events[$clave]['dia'];
        $x->start = $events[$clave]['hora_inicio'];
        $x->end = $events[$clave]['hora_final'];
        $x->event_id = $events[$clave]['cod_disponibilidad'];
        $x->cod_sucursal = $events[$clave]['cod_sucursal'];
        array_push($arrayEventos, $x);
    }
    $return['data'] = $arrayEventos;
    return $arrayEventos;
}

function obtenerServiciosUsuario(){
    global $Clagendamiento;
    global $session;
    if(!isset($_GET['cod_usuario'])){
        $return['success'] = 0;
        $return['mensaje'] = 'Falta informacion';
        return $return;
    }
    extract($_GET);
    $Clagendamiento->cod_usuario = $cod_usuario;
    $info = $Clagendamiento->listarServiciosUsuarios();
    $return['data'] = $info;
    return $return;
}

function crearServicioUsuario(){
    global $Clagendamiento;
    global $session;

    extract($_POST);
    $Clagendamiento->cod_usuario = $cod_usuario;
    
    if($Clagendamiento->crearServiciosUsuarios($cod_servicio)){
        $return['success'] = '1';
        $return['mensaje'] = 'Procedimiento completado con exito';
        return $return;
    }else{
        $return['success'] = '0';
        $return['mensaje'] = 'Procedimiento fallo';
        return $return;
    }
}





?>