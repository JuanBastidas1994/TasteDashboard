<?php
require_once "../funciones.php";
require_once "../conexion.php";

controller_create();

function listarImpresorasSucursal() {
    if (!isset($_GET['cod_sucursal'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    $cod_sucursal = intval($_GET['cod_sucursal']);
    $query = "SELECT * FROM tb_impresoras WHERE cod_sucursal = ? ORDER BY estacion_id, tipo";
    $impresoras = Conexion::buscarVariosRegistro($query, [$cod_sucursal]);

    $return['success'] = 1;
    $return['datos'] = $impresoras ?: [];
    return $return;
}

function guardarTipoImpresora() {
    if (!isset($_GET['cod_impresora']) || !isset($_GET['tipo'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    $cod_impresora = intval($_GET['cod_impresora']);
    $tipo = $_GET['tipo'] !== '' ? $_GET['tipo'] : null;

    $query = "UPDATE tb_impresoras SET tipo = ? WHERE cod_impresora = ?";
    $ok = Conexion::ejecutar($query, [$tipo, $cod_impresora]);

    $return['success'] = $ok ? 1 : 0;
    $return['mensaje'] = $ok ? "Impresora actualizada correctamente" : "No se pudo actualizar la impresora";
    return $return;
}

function eliminarImpresoraAdmin() {
    if (!isset($_GET['cod_impresora'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    $cod_impresora = intval($_GET['cod_impresora']);

    $query = "DELETE FROM tb_impresoras WHERE cod_impresora = ?";
    $ok = Conexion::ejecutar($query, [$cod_impresora]);

    $return['success'] = $ok ? 1 : 0;
    $return['mensaje'] = $ok ? "Impresora eliminada correctamente" : "No se pudo eliminar la impresora";
    return $return;
}
?>
