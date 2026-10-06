<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_reporte_flota_cobro.php";

$session = getSession();
error_reporting(E_ALL);

controller_create();

function getReporte() {
    $cod_empresa = $_POST['cod_empresa'] ?? '';
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';

    $clReporte = new cl_reporte_flota_cobro();
    $error = $clReporte->validarFiltros($cod_empresa, $fechaInicio, $fechaFin);
    if ($error) {
        return ["success" => 0, "mensaje" => $error];
    }

    $reporte = $clReporte->generar(intval($cod_empresa), $fechaInicio, $fechaFin);
    return [
        "success" => 1,
        "mensaje" => count($reporte['detalle']) > 0 ? 'Información encontrada' : 'No hay órdenes de esta empresa en este lapso de tiempo',
        "indicadores" => $reporte['indicadores'],
        "detalle" => $reporte['detalle'],
        "totales" => $reporte['totales'],
    ];
}
