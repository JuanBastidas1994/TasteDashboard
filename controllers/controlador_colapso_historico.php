<?php
require_once "../funciones.php";
require_once "../clases/cl_colapso_historico.php";

$session   = getSession();
$Clcolapso = new cl_colapso_historico();

controller_create();

function getEstadoColapso() {
    global $Clcolapso, $session;
    $anio_limite = intval(date('Y')) - 2;
    $data = $Clcolapso->getEstado($session['cod_empresa'], $anio_limite);
    return ['success' => 1, 'data' => $data];
}

function ejecutarColapso() {
    global $Clcolapso, $session;
    $anio_limite = intval(date('Y')) - 2;

    try {
        $Clcolapso->ejecutarColapsoAnual($session['cod_empresa'], $anio_limite);
        return ['success' => 1, 'mensaje' => 'Colapso anual ejecutado correctamente'];
    } catch (Exception $e) {
        return ['success' => 0, 'mensaje' => 'Error al ejecutar el colapso: ' . $e->getMessage()];
    }
}
?>
