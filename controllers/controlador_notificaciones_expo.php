<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_notificaciones_expo.php";
require_once "../clases/cl_promociones_nueva.php";
require_once "../clases/cl_productos.php";

$ClNotificacionesExpo = new cl_notificaciones_expo();
$session = getSession();

controller_create();

/* Audiencia que llega del formulario de notificaciones.php */
function audienciaDesdePost()
{
    global $ClNotificacionesExpo;
    $tipo = isset($_POST['audiencia']) ? $_POST['audiencia'] : 'todos';
    $valor = null;
    if ($tipo === 'inactivos') $valor = isset($_POST['dias_inactivo']) ? $_POST['dias_inactivo'] : 0;
    if ($tipo === 'sucursal') $valor = isset($_POST['cod_sucursal']) ? $_POST['cod_sucursal'] : 0;
    return $ClNotificacionesExpo->normalizarAudiencia($tipo, $valor);
}

/* Campaña desde notificaciones.php: inmediata o programada */
function crearNotificacion()
{
    global $ClNotificacionesExpo;

    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'evento';
    if ($titulo === '' || $mensaje === '') {
        return ['success' => 0, 'mensaje' => 'Escribe el título y el mensaje'];
    }

    $audiencia = audienciaDesdePost();
    if (!$audiencia) {
        return ['success' => 0, 'mensaje' => 'Revisa la audiencia seleccionada'];
    }

    // Lo que la app necesita para abrir la pantalla correcta al tocar la notificación
    $data = [];
    if ($tipo === 'promo') {
        $ClPromociones = new cl_promociones_nueva();
        $cod_promocion = isset($_POST['cod_promocion']) ? (int)$_POST['cod_promocion'] : 0;
        if (!$cod_promocion || !$ClPromociones->obtener($cod_promocion)) {
            return ['success' => 0, 'mensaje' => 'Selecciona la promoción'];
        }
        $data['cod_promocion'] = $cod_promocion;
    } elseif ($tipo === 'producto_nuevo') {
        $Clproductos = new cl_productos(NULL);
        $alias = isset($_POST['alias']) ? $_POST['alias'] : '';
        $producto = [];
        if ($alias === '' || !$Clproductos->getArrayByAlias(addslashes($alias), $producto)) {
            return ['success' => 0, 'mensaje' => 'Selecciona el producto'];
        }
        $data['alias'] = $alias;
    } else {
        $tipo = 'evento';
    }

    $fechaProgramada = null;
    if (isset($_POST['cuando']) && $_POST['cuando'] === 'programar') {
        $fecha = isset($_POST['fecha_programada']) ? strtotime($_POST['fecha_programada']) : false;
        if (!$fecha) {
            return ['success' => 0, 'mensaje' => 'Indica la fecha y hora de envío'];
        }
        $fechaProgramada = date('Y-m-d H:i:00', $fecha);
    }

    return $ClNotificacionesExpo->crearCampana($tipo, $titulo, $mensaje, $data, $audiencia, $fechaProgramada);
}

function contarAudiencia()
{
    global $ClNotificacionesExpo;
    $audiencia = audienciaDesdePost();
    if (!$audiencia) {
        return ['success' => 0, 'total' => 0];
    }
    return ['success' => 1, 'total' => $ClNotificacionesExpo->contarAudiencia($audiencia)];
}

function cancelarProgramada()
{
    global $ClNotificacionesExpo;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    return $ClNotificacionesExpo->cancelarProgramada($id);
}

function guardarRecompra()
{
    global $ClNotificacionesExpo;

    $activo = isset($_POST['recompra_activo']) && $_POST['recompra_activo'] == 1;
    $dias = isset($_POST['recompra_dias']) ? (int)$_POST['recompra_dias'] : 0;
    $titulo = isset($_POST['recompra_titulo']) ? trim($_POST['recompra_titulo']) : '';
    $mensaje = isset($_POST['recompra_mensaje']) ? trim($_POST['recompra_mensaje']) : '';

    if ($dias < 10 || $dias > 180) {
        return ['success' => 0, 'mensaje' => 'Los días sin pedir deben estar entre 10 y 180'];
    }
    if ($titulo === '' || $mensaje === '') {
        return ['success' => 0, 'mensaje' => 'Escribe el título y el mensaje del recordatorio'];
    }

    return $ClNotificacionesExpo->guardarConfigRecompra($activo, $dias, $titulo, $mensaje)
        ? ['success' => 1, 'mensaje' => 'Recordatorio de recompra guardado']
        : ['success' => 0, 'mensaje' => 'No se pudo guardar'];
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
?>
