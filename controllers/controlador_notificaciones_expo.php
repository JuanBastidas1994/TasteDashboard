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
    if (isset($cod_producto)) {
        $data['cod_producto'] = $cod_producto;
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

function historial()
{
    global $ClNotificacionesExpo;

    $return['success'] = 1;
    $return['data'] = $ClNotificacionesExpo->historial();
    return $return;
}
?>
