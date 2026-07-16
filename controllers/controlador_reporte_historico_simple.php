<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_historico_simple.php";

$session     = getSession();
$Clhistorico = new cl_reporte_historico_simple();

controller_create();

function getTabla() {
    global $Clhistorico, $session;
    $anio         = intval($_POST['anio']        ?? date('Y'));
    $cod_sucursal = intval($_POST['cod_sucursal'] ?? 0);
    $data = $Clhistorico->getTabla($session['cod_empresa'], $anio, $cod_sucursal);
    return ['success' => 1, 'data' => $data];
}
?>
