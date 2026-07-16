<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_historico.php";

$session     = getSession();
$Clhistorico = new cl_reporte_historico();

controller_create();

function getResumenGeneral() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getResumenGeneral($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}

function getTiposEntrega() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getTiposEntrega($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}

function getClientes() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getClientes($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}

function getProductos() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getProductos($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}

function getMedios() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getMedios($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}

function getHoras() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']         ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getHoras($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}
?>
