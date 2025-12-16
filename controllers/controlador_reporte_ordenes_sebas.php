<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_reporte_ordenes_sebas.php";

$ClReporte = new cl_reporte_ordenes();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getReport() {
    global $ClReporte;

    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $ClReporte->tipoPago = $tipoReporte;
    $ClReporte->fechaInicio = $fechaInicio;
    $ClReporte->fechaFin = $fechaFin;
    $ClReporte->estado = "";

    if($tipoReporte == "totales") {
        $reporte = $ClReporte->getReporteTotales();
    }
    else if($tipoReporte == "ENTREGADA") {
        $ClReporte->estado = $tipoReporte;
        $reporte = $ClReporte->getReporteTotales();
    }
    else {
        $reporte = $ClReporte->getReportePorTipoPago();
    }

    if($reporte) {
        $return['success'] = 1;
        $return['mensaje'] = "Reporte obtenido";
        $return['data'] = $reporte;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay datos";
    return $return;
}
?>