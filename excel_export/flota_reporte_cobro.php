<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_flota_cobro.php";

require_once "SimpleXLSXGen.php";
use Shuchkin\SimpleXLSXGen;

if (!isLogin()) {
    header("location:../login.php");
    exit;
}

$cod_empresa = $_GET['cod_empresa'] ?? '';
$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin = $_GET['fechaFin'] ?? '';

$clReporte = new cl_reporte_flota_cobro();
$error = $clReporte->validarFiltros($cod_empresa, $fechaInicio, $fechaFin);
if ($error) {
    echo $error;
    exit;
}

$cod_empresa = intval($cod_empresa);
$empresa = $clReporte->nombreEmpresa($cod_empresa);
$reporte = $clReporte->generar($cod_empresa, $fechaInicio, $fechaFin);
$ind = $reporte['indicadores'];
$tot = $reporte['totales'];

$dinero = function ($v) {
    return '<style nf="&quot;$&quot;#,##0.00">' . number_format($v, 2, '.', '') . '</style>';
};
$encabezado = function ($t) {
    return '<style bgcolor="#1B55E2" color="#FFFFFF"><b>' . $t . '</b></style>';
};

// Construir matriz para XLSX
$data = [];
$data[] = ['<b>Reporte de cobro de envíos</b>'];
$data[] = ['Empresa', $empresa];
$data[] = ['Rango', $fechaInicio . ' al ' . $fechaFin];
$data[] = [];

// Indicadores
$data[] = [$encabezado('Indicadores'), $encabezado('')];
$data[] = ['Órdenes de esta empresa', $ind['ordenes_empresa']];
$data[] = ['Órdenes de todas las empresas', $ind['ordenes_todas']];
$data[] = ['Total para Taste (' . $ind['ordenes_todas'] . ' x $' . number_format($ind['tarifa_taste'], 2) . ')', $dinero($ind['total_taste'])];
$data[] = [];

// Detalle
$data[] = array_map($encabezado, ['Orden', 'Fecha', 'Sucursal', 'Motorizado', 'Estado', 'Subtotal', 'IVA', 'Precio envío']);
foreach ($reporte['detalle'] as $d) {
    $data[] = [
        $d['cod_orden'],
        $d['fecha'],
        $d['sucursal'],
        $d['motorizado'],
        $d['estado'],
        $dinero($d['subtotal']),
        $dinero($d['iva']),
        $dinero($d['envio']),
    ];
}
$data[] = [];

// Totales con desglose de IVA
$data[] = ['', '', '', '', '', '', '<b>Subtotal 0%</b>', $dinero($tot['subtotal_0'])];
$data[] = ['', '', '', '', '', '', '<b>Subtotal IVA</b>', $dinero($tot['subtotal_iva'])];
$data[] = ['', '', '', '', '', '', '<b>IVA</b>', $dinero($tot['iva'])];
$data[] = ['', '', '', '', '', '', '<b>TOTAL A COBRAR</b>', '<b>' . $dinero($tot['total']) . '</b>'];

// Crear xlsx
$xlsx = SimpleXLSXGen::fromArray($data, 'Cobro envíos')
    ->setColWidth(1, 38)
    ->setColWidth(2, 20)
    ->setColWidth(3, 22)
    ->setColWidth(4, 28)
    ->setColWidth(5, 12)
    ->setColWidth(6, 12)
    ->setColWidth(7, 18)
    ->setColWidth(8, 14);

// Descargar
$nombreArchivo = preg_replace('/[^A-Za-z0-9_-]+/', '_', $empresa ?: 'empresa');
$xlsx->downloadAs('cobro_envios_' . $nombreArchivo . '_' . $fechaInicio . '_' . $fechaFin . '.xlsx');
