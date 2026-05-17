<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_promociones.php";
$Clpromociones = new cl_promociones(NULL);
$session = getSession();

controller_create();

function crearNew(){
    require_once "../clases/cl_promociones_nueva.php";
    $Clpromociones = new cl_promociones_nueva();

    if(count($_POST) == 0){
        return [ 'success' => 0, 'mensaje' => 'Falta informacion' ];
    }

    extract($_POST);

    // ── Validaciones de fecha ───────────────────────────────────────────────
    if (!fechaValida($fecha_inicio)) {
        return [ 'success' => 0, 'mensaje' => 'Fecha de inicio es incorrecta' ];
    }
    if (!fechaValida($fecha_fin)) {
        return [ 'success' => 0, 'mensaje' => 'Fecha Fin es incorrecta' ];
    }

    $inicio = new DateTime($fecha_inicio);
    $fin    = new DateTime($fecha_fin);
    if ($fin < $inicio) {
        return [ 'success' => 0, 'mensaje' => 'La fecha fin no puede ser menor que la fecha de inicio' ];
    }

    // ── Validación de productos participantes ──────────────────────────────
    // Monto mínimo NO necesita productos participantes, los demás sí
    if ($cmb_tipo_descuento !== 'monto_minimo' && !isset($_POST['cmb_productos'])) {
        return [ 'success' => 0, 'mensaje' => 'Debes marcar productos' ];
    }

    // ── Validaciones generales ─────────────────────────────────────────────
    if (!isset($_POST['chkSucursal'])) {
        return [ 'success' => 0, 'mensaje' => 'Debes marcar sucursales' ];
    }
    if ($is_recurrencia && !isset($_POST['dias'])) {
        return [ 'success' => 0, 'mensaje' => 'Para las promociones recurrentes debes habilitar los días necesarios' ];
    }
    if ($is_tipo_entrega && !isset($_POST['tipo_entrega'])) {
        return [ 'success' => 0, 'mensaje' => 'Si marcas algunos tipos de entrega debes marcar cuales, caso contrario escoge Todos los tipos' ];
    }

    // ── Validaciones específicas por tipo ──────────────────────────────────
    if ($cmb_tipo_descuento === '0' || $cmb_tipo_descuento == 0) {
        // Porcentaje: debe tener un número válido
        if (!isset($porcentaje_descuento) || !is_numeric($porcentaje_descuento) || floatval($porcentaje_descuento) <= 0) {
            return [ 'success' => 0, 'mensaje' => 'Debes ingresar un porcentaje válido mayor a 0' ];
        }
    }
    if (in_array($cmb_tipo_descuento, ['compra_x_lleva_y', 'monto_minimo'])) {
        // Ambos necesitan producto regalo
        if (!isset($producto_regalo) || !$producto_regalo) {
            return [ 'success' => 0, 'mensaje' => 'Debes seleccionar un producto regalo' ];
        }
    }
    if ($cmb_tipo_descuento === 'monto_minimo') {
        if (!isset($monto_minimo) || !is_numeric($monto_minimo) || floatval($monto_minimo) <= 0) {
            return [ 'success' => 0, 'mensaje' => 'Debes ingresar un monto mínimo válido mayor a 0' ];
        }
    }

    // ── Calcular is_porcentaje / valor / texto según el tipo ───────────────
    // IGUAL QUE ANTES para Porcentaje, 2x1, 3x2, 4x3, 5x4
    // NUEVO para compra_x_lleva_y y monto_minimo
    $is_porcentaje = 1;
    $valor = 0;
    $texto = '';

    if ($cmb_tipo_descuento == 0) {
        // Porcentaje — sin cambios respecto al original
        $is_porcentaje = 1;
        $valor = $porcentaje_descuento;
        $texto = round($porcentaje_descuento) . '%';

    } elseif (is_numeric($cmb_tipo_descuento) && $cmb_tipo_descuento > 0) {
        // 2x1, 3x2, 4x3, 5x4 — sin cambios respecto al original
        $is_porcentaje = 0;
        $valor = 100;
        $texto = $cmb_tipo_descuento . "x" . (intval($cmb_tipo_descuento) - 1);

    } elseif ($cmb_tipo_descuento === 'compra_x_lleva_y') {
        // NUEVO: Compra X lleva Y
        $is_porcentaje = 0;
        $valor = 0;
        $texto = 'compra_x_lleva_y';

    } elseif ($cmb_tipo_descuento === 'monto_minimo') {
        // NUEVO: Monto mínimo
        $is_porcentaje = 0;
        $valor = floatval($monto_minimo);
        $texto = 'monto_minimo';
    }

    $estado = isset($_POST['chk_estado']) ? 'A' : 'I';

    // ── Asignar datos a la clase ───────────────────────────────────────────
    // Sin cambios respecto al original
    $Clpromociones->descripcion   = $descripcion;
    $Clpromociones->is_porcentaje = $is_porcentaje;
    $Clpromociones->cantidad      = is_numeric($cmb_tipo_descuento) ? $cmb_tipo_descuento : 0;
    $Clpromociones->valor         = $valor;
    $Clpromociones->texto         = $texto;
    $Clpromociones->fecha_inicio  = $fecha_inicio;
    $Clpromociones->fecha_fin     = $fecha_fin;
    $Clpromociones->is_recurrente = $is_recurrencia;
    $Clpromociones->estado        = $estado;

    // ── Manejo de imagen ─────────────────────────────────────────────────────
    $txt_crop         = $_POST['txt_crop'] ?? '';
    $txt_delete_imagen = $_POST['txt_delete_imagen'] ?? '0';
    $imagen_actual    = '';

    if (isset($_POST['id'])) {
        $row = Conexion::buscarRegistro(
            "SELECT imagen FROM promociones WHERE cod_promocion = :cod AND cod_empresa = :emp LIMIT 1",
            [':cod' => $_POST['id'], ':emp' => $Clpromociones->cod_empresa]
        );
        if ($row) $imagen_actual = $row['imagen'];
    }

    if ($txt_delete_imagen == '1') {
        if ($imagen_actual) deleteFile($imagen_actual);
        $Clpromociones->imagen = '';
    } elseif ($txt_crop !== '') {
        $nombre_nuevo = 'promo_' . uniqid() . '.jpg';
        base64ToImage($txt_crop, $nombre_nuevo);
        if ($imagen_actual) deleteFile($imagen_actual);
        $Clpromociones->imagen = $nombre_nuevo;
    } else {
        $Clpromociones->imagen = $imagen_actual;
    }

    // ── Imagen obligatoria para compra_x_lleva_y y monto_minimo ──────────────
    if (in_array($cmb_tipo_descuento, ['compra_x_lleva_y', 'monto_minimo']) && $Clpromociones->imagen === '') {
        return [ 'success' => 0, 'mensaje' => 'Debes subir una imagen para este tipo de promoción' ];
    }

    $productos  = $cmb_productos ?? [];
    $sucursales = $chkSucursal;
    $isNewPromotion = false;

    try {
        // 🔐 Iniciar transacción
        Conexion::beginTransaction();

        // ── Crear o Editar promoción ───────────────────────────────────────
        // Sin cambios respecto al original
        if (!isset($_POST['id'])) {
            if (!$Clpromociones->crear($cod_promocion)) {
                throw new Exception('Error al crear la promocion');
            }
            $isNewPromotion = true;
        } else {
            $cod_promocion = $_POST['id'];
            $Clpromociones->cod_promocion = $cod_promocion;
            if (!$Clpromociones->editar()) {
                throw new Exception('Error al editar la promocion');
            }
        }

        // ── Asociar productos participantes ───────────────────────────────
        // Sin cambios respecto al original
        $Clpromociones->asociar_productos($cod_promocion, $productos);

        // ── Asociar sucursales ────────────────────────────────────────────
        // Sin cambios respecto al original
        $Clpromociones->asociar_sucursales($cod_promocion, $sucursales);

        // ── Asociar Recurrencia ───────────────────────────────────────────
        // Sin cambios respecto al original
        if ($is_recurrencia)
            $Clpromociones->asociar_recurrencia($cod_promocion, $dias);
        else
            $Clpromociones->eliminar_recurrencia($cod_promocion);

        // ── Tipo de Entrega ───────────────────────────────────────────────
        // Sin cambios respecto al original
        if ($is_tipo_entrega == 0)
            $tipo_entrega = [];
        $Clpromociones->asociar_tipo_entrega($cod_promocion, $tipo_entrega);

        // ── NUEVO: Producto regalo (Compra X lleva Y / Monto mínimo) ──────
        if (in_array($cmb_tipo_descuento, ['compra_x_lleva_y', 'monto_minimo'])) {
            $Clpromociones->asociar_recompensas($cod_promocion, [[
                'cod_producto_regalo' => $producto_regalo,
                'cantidad_regalo'     => 1
            ]]);
        } else {
            // Si cambiaron el tipo, limpiar recompensas anteriores
            $Clpromociones->asociar_recompensas($cod_promocion, []);
        }

        // ✅ Confirmar cambios
        Conexion::commit();

        return [
            'success' => 1,
            'mensaje' => 'Promoción actualizada correctamente',
            'id'      => $cod_promocion,
            'new'     => $isNewPromotion
        ];

    } catch (Exception $e) {

        // ❌ Revertir todo
        if (Conexion::obtenerConexion()->inTransaction()) {
            Conexion::rollBack();
        }

        return [
            'success' => 0,
            'mensaje' => $e->getMessage()
        ];
    }
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

function datatable_promocion(){
    global $session;
    $cod_empresa = $session['cod_empresa'];
    $cod_promocion = isset($_GET['cod_promocion']) ? intval($_GET['cod_promocion']) : 0;

    if ($cod_promocion == 0) {
        echo json_encode(['error' => 'Falta informacion']);
        exit;
    }

    $exists = Conexion::buscarRegistro(
        "SELECT cod_promocion FROM promociones WHERE cod_promocion = :cod AND cod_empresa = :emp LIMIT 1",
        [':cod' => $cod_promocion, ':emp' => $cod_empresa]
    );
    if (!$exists) {
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $query = "SELECT ca.cod_orden, ca.fecha, ca.total, ca.is_envio, ca.referencia, ca.estado, ca.is_programado, ca.hora_retiro,
                    CONCAT(u.nombre, ' ', u.apellido) as cliente, u.telefono as phone, s.nombre as sucursal,
                    GROUP_CONCAT(fp.descripcion SEPARATOR ', ') AS formas_pago
            FROM tb_orden_cabecera ca
            JOIN tb_usuarios u ON ca.cod_usuario = u.cod_usuario
            JOIN tb_sucursales s ON s.cod_sucursal = ca.cod_sucursal
            JOIN tb_orden_pagos op ON op.cod_orden = ca.cod_orden
            JOIN tb_formas_pago fp ON fp.cod_forma_pago = op.forma_pago
            WHERE ca.estado NOT IN('CREADA')
            AND ca.cod_empresa = {$cod_empresa}
            AND ca.cod_orden IN (SELECT cod_orden FROM tb_orden_detalle WHERE cod_promocion = {$cod_promocion})
            GROUP BY ca.cod_orden";

    $table = "($query) temp";
    $primaryKey = 'cod_orden';
    $columns = array(
        array('dt' => 0, 'db' => 'cod_orden'),
        array('dt' => 1, 'db' => 'cliente'),
        array('dt' => 2, 'db' => 'sucursal'),
        array('dt' => 3, 'db' => 'fecha',
            'formatter' => function($d, $row) {
                return fechaHoraLatinoShort($d);
            }
        ),
        array('dt' => 4, 'db' => 'total',
            'formatter' => function($d, $row) {
                return '$' . number_format($d, 2, '.', ',');
            }
        ),
        array('dt' => 5, 'db' => 'formas_pago'),
        array('dt' => 6, 'db' => 'is_envio',
            'formatter' => function($d, $row) {
                if ($d == 0) return "Pickup";
                if ($d == 1) return "Delivery";
                if ($d == 2) return "En mesa";
                return "Pickup";
            }
        ),
        array('dt' => 7, 'db' => 'is_programado',
            'formatter' => function($d, $row) {
                if ($d == 1) return fechaHoraLatinoShort($row['hora_retiro']);
                return 'Lo más pronto posible';
            }
        ),
        array('dt' => 8, 'db' => 'phone'),
        array('dt' => 9, 'db' => 'estado',
            'formatter' => function($d, $row) {
                $colors = ['ENTREGADA' => 'success', 'ASIGNADA' => 'warning', 'CANCELADA' => 'danger', 'ANULADA' => 'danger'];
                $badge = $colors[$row['estado']] ?? 'primary';
                return '<span class="shadow-none badge badge-' . $badge . '">' . $row['estado'] . '</span>';
            }
        ),
        array('dt' => 10, 'db' => 'cod_orden',
            'formatter' => function($d, $row) {
                return '<ul class="table-controls">
                    <li><a target="_blank" href="orden_detalle.php?id=' . $row['cod_orden'] . '" title="Ver orden"><i data-feather="eye"></i></a></li>
                </ul>';
            }
        ),
        array('dt' => 11, 'db' => 'hora_retiro'),
    );

    $sql_details = array(
        'type' => 'mysql',
        'user' => usuario,
        'pass' => contrasena,
        'db'   => db,
        'host' => servidor
    );
    require('../plugins/table/datatable/ssp.class.php');
    return SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns);
}

function delete(){
	if(!isset($_GET['cod_promocion'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	$cod_promocion = $_GET['cod_promocion'];

    require_once "../clases/cl_promociones_nueva.php";
    $Clpromociones = new cl_promociones_nueva();
    try {
        $rowImg = Conexion::buscarRegistro(
            "SELECT imagen FROM promociones WHERE cod_promocion = :cod AND cod_empresa = :emp LIMIT 1",
            [':cod' => $cod_promocion, ':emp' => $Clpromociones->cod_empresa]
        );
        if (!$Clpromociones->eliminar($cod_promocion)) {
            throw new Exception('No se pudo eliminar la promoción');
        }
        if ($rowImg && $rowImg['imagen']) {
            deleteFile($rowImg['imagen']);
        }
        return [
            'success' => 1,
            'mensaje' => 'Promoción eliminada correctamente'
        ];
    } catch (Exception $e) {
        return [
            'success' => 0,
            'mensaje' => $e->getMessage()
        ];
    }
}


?>