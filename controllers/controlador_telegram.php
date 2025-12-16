<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_telegram.php";
$ClTelegram = new cl_telegram();
$session = getSession();

controller_create();

function asignar(){
    global $ClTelegram;
    if(!isset($_GET['cod_usuario']) || !isset($_GET['cod_usuario_telegram'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $asignacion = $ClTelegram->getAsignacion($cod_usuario_telegram, $cod_usuario);
    if($asignacion){
        $return['success'] = 0;
        $return['mensaje'] = "Este usuario ya esta asignado con ".$asignacion['nombre']." ".$asignacion['apellido'];
        return $return;
    }

    $resp = $ClTelegram->asignarUsuario($cod_usuario_telegram, $cod_usuario);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Usuario de telegram asignado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al asignar al usuario";
    }
    return $return;
}

function set_estado(){
    global $ClTelegram;
    if(!isset($_GET['cod_usuario']) || !isset($_GET['cod_usuario_telegram'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $resp = $ClTelegram->removerUsuario($cod_usuario_telegram, $cod_usuario);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Asignacion removida";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "no se pudo remover la asignacion, por favor vuelva a intentarlo";
    }
    return $return;
}

function set_estado_chat(){
    global $ClTelegram;
    if(!isset($_GET['cod_chat']) || !isset($_GET['activo'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $estado = "I";
    $mensaje = "Grupo Inactivado correctamente";
    if($activo == "true"){
        $estado = "A";
        $mensaje = "Grupo Activado correctamente";
    }

    $resp = $ClTelegram->grupo_set_estado($cod_chat, $estado);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = $mensaje;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = html_entity_decode("no se pudo realizar la operaci&oacute;n, por favor vuelva a intentarlo");
    }
    return $return;
}


function setBot(){
    global $session;
    global $ClTelegram;

    extract($_POST);
    $bot = $ClTelegram->getURLtoBot($txt_token);
    if($bot['ok']){
        if(!$ClTelegram->getByEmpresa($cod_empresa)){
            if($ClTelegram->crear($cod_empresa, $txt_botname, $txt_token)){
                $url = url_bot.$alias;
                $ClTelegram->addURLtoBot($txt_token, $url);
                $return['success'] = 1;
                $return['mensaje'] = html_entity_decode("Bot a&ntilde;adido correctamente");
            }else{
                $return['success'] = 0;
                $return['mensaje'] = html_entity_decode("Error al a&ntilde;adir el Bot, por favor vuelve a intentarlo");
            }
        }else{
            if($ClTelegram->editar($cod_empresa, $txt_botname, $txt_token)){
                $url = url_bot.$alias;
                $ClTelegram->addURLtoBot($txt_token, $url);
                $return['success'] = 1;
                $return['mensaje'] = html_entity_decode("Bot editado correctamente");
            }else{
                $return['success'] = 0;
                $return['mensaje'] = html_entity_decode("Error al editar el Bot, por favor vuelve a intentarlo");
            }
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = html_entity_decode("Bot no existente en telegram, por favor verificar el Token");
    }
    return $return;
}

?>