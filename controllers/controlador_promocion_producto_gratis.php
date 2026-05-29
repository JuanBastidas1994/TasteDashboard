<?php
require_once "../funciones.php";
require_once "../clases/cl_promocion_producto_gratis.php";
require_once "../clases/cl_sucursales.php";

$session      = getSession();
$ClPG         = new cl_promocion_producto_gratis();
$ClSucursales = new cl_sucursales(NULL);

controller_create();

function guardar()
{
    global $ClPG, $ClSucursales;

    if (count($_POST) === 0) {
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }

    $cod_producto    = intval($_POST['cod_producto']         ?? 0);
    $producto_nombre = trim($_POST['txt_producto_nombre']    ?? '');
    $monto_minimo    = floatval($_POST['txt_monto_minimo']   ?? 0);
    $fecha_inicio    = trim($_POST['fecha_inicio']           ?? '');
    $fecha_fin       = trim($_POST['fecha_fin']              ?? '');
    $is_web          = intval($_POST['is_web']               ?? 1);
    $is_app          = intval($_POST['is_app']               ?? 1);
    $titulo          = trim($_POST['txt_titulo']             ?? '');
    $chkSucursales   = $_POST['chkSucursal']                 ?? [];
    $txt_crop        = $_POST['txt_crop']                    ?? '';
    $imagen_actual   = trim($_POST['imagen_actual']          ?? '');

    if ($cod_producto <= 0)    return ['success' => 0, 'mensaje' => 'Selecciona un producto'];
    if (!$producto_nombre)     return ['success' => 0, 'mensaje' => 'Ingresa el nombre del producto'];
    if ($monto_minimo <= 0)    return ['success' => 0, 'mensaje' => 'Ingresa un monto mínimo válido mayor a 0'];
    if (empty($chkSucursales)) return ['success' => 0, 'mensaje' => 'Selecciona al menos una sucursal'];

    // Imagen
    $imagen = $imagen_actual;
    if ($txt_crop !== '') {
        $nombre_nuevo = 'pg_' . time() . '.jpg';
        base64ToImage($txt_crop, $nombre_nuevo);
        $imagen = $nombre_nuevo;
    }

    $data = [
        'cod_producto'    => $cod_producto,
        'producto_nombre' => $producto_nombre,
        'monto_minimo'    => $monto_minimo,
        'imagen'          => $imagen,
        'fecha_inicio'    => $fecha_inicio ?: date('Y-m-d'),
        'fecha_fin'       => $fecha_fin    ?: '2099-12-31',
        'is_web'          => $is_web,
        'is_app'          => $is_app,
        'estado'          => 'A',
        'titulo'          => $titulo,
    ];

    $seleccionadas   = array_map('intval', $chkSucursales);
    $todasSucursales = $ClSucursales->lista();
    $errores         = 0;

    foreach ($todasSucursales as $suc) {
        $cod = intval($suc['cod_sucursal']);
        if (in_array($cod, $seleccionadas)) {
            if (!$ClPG->upsert($cod, $data)) $errores++;
        } else {
            $existing = $ClPG->getBySucursal($cod);
            if ($existing) $ClPG->setEstado($cod, 'I');
        }
    }

    if ($errores > 0) {
        return ['success' => 0, 'mensaje' => "Ocurrieron errores al guardar $errores sucursal(es)"];
    }

    return ['success' => 1, 'mensaje' => 'Configuración guardada correctamente'];
}
