<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_notificaciones_expo.php";

$ClNotificacionesExpo = new cl_notificaciones_expo();
$session = getSession();

controller_create();

function enviarPromo()
{
    global $ClNotificacionesExpo;

    if (!isset($_POST['titulo']) || !isset($_POST['descripcion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $data = [];
    if (isset($cod_promocion)) {
        $data['cod_promocion'] = $cod_promocion;
    }

    return $ClNotificacionesExpo->enviar('promo', $titulo, $descripcion, $data);
}

function enviarProductoNuevo()
{
    global $ClNotificacionesExpo;

    if (!isset($_POST['titulo']) || !isset($_POST['descripcion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $data = [];
    if (isset($alias)) {
        $data['alias'] = $alias;
    }

    return $ClNotificacionesExpo->enviar('producto_nuevo', $titulo, $descripcion, $data);
}

function enviarEvento()
{
    global $ClNotificacionesExpo;

    if (!isset($_POST['titulo']) || !isset($_POST['descripcion'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    return $ClNotificacionesExpo->enviar('evento', $titulo, $descripcion);
}

/* Mensaje libre a UN usuario: campana de clientes.php, cliente_detalle.php y usuario_detalle.php */
function enviarUsuario()
{
    global $ClNotificacionesExpo;

    $cod_usuario = isset($_POST['cod_usuario']) ? (int)$_POST['cod_usuario'] : 0;
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    if (!$cod_usuario || $mensaje === '') {
        $return['success'] = 0;
        $return['mensaje'] = "Escribe un mensaje";
        return $return;
    }

    return $ClNotificacionesExpo->enviarAUsuario($cod_usuario, $mensaje);
}

function historial()
{
    global $ClNotificacionesExpo;

    $return['success'] = 1;
    $return['data'] = $ClNotificacionesExpo->lista(50);
    return $return;
}
?>
