<?php
require_once "../funciones.php";
require_once "../clases/cl_colapso_historico.php";

$session   = getSession();
$Clcolapso = new cl_colapso_historico();

controller_create();

function getEstadoColapso() {
    global $Clcolapso, $session;
    $cod_empresa_param = isset($_POST['cod_empresa']) ? intval($_POST['cod_empresa']) : (isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0);
    $cod_empresa = $cod_empresa_param > 0 ? $cod_empresa_param : $session['cod_empresa'];
    $anio_limite = intval(date('Y')) - 2;
    $data = $Clcolapso->getEstado($cod_empresa, $anio_limite);
    return ['success' => 1, 'data' => $data];
}

function ejecutarColapsoMensualMasivo() {
    global $Clcolapso;
    $cod_empresas = isset($_POST['cod_empresas']) ? $_POST['cod_empresas'] : [];
    $meses        = isset($_POST['meses'])        ? $_POST['meses']        : [];
    $anio         = intval(date('Y'));
    $mes_actual   = intval(date('n'));

    if (empty($cod_empresas) || empty($meses)) {
        return ['success' => 0, 'mensaje' => 'Selecciona al menos una empresa y un mes'];
    }

    $count = 0;
    foreach ($cod_empresas as $cod_empresa) {
        $cod_empresa = intval($cod_empresa);
        if ($cod_empresa <= 0) continue;
        foreach ($meses as $mes) {
            $mes = intval($mes);
            if ($mes < 1 || $mes >= $mes_actual) continue;
            $Clcolapso->ejecutarColapsoMensual($cod_empresa, $anio, $mes);
            $count++;
        }
    }

    return ['success' => 1, 'mensaje' => "Colapso mensual ejecutado: $count combinación(es) procesadas"];
}

function ejecutarColapso() {
    global $Clcolapso, $session;
    $cod_empresa = isset($_POST['cod_empresa']) && intval($_POST['cod_empresa']) > 0
        ? intval($_POST['cod_empresa'])
        : $session['cod_empresa'];
    $anio_limite = intval(date('Y')) - 2;

    try {
        $Clcolapso->ejecutarColapsoAnual($cod_empresa, $anio_limite);
        return ['success' => 1, 'mensaje' => 'Colapso anual ejecutado correctamente'];
    } catch (Exception $e) {
        return ['success' => 0, 'mensaje' => 'Error al ejecutar el colapso: ' . $e->getMessage()];
    }
}
?>
