<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_contifico.php";
require_once "../clases/cl_empresas.php";

$session = getSession();
$ClContifico = new cl_contifico($session["cod_empresa"]);
$ClEmpresas = new cl_empresas();
error_reporting(E_ALL);

controller_create();

function getRucs() {
    global $ClEmpresas;
    global $session;

    $rucs = $ClEmpresas->getRucs($session["cod_empresa"]);
    if($rucs) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de RUCs";
        $return['data'] = $rucs;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay RUCs";
    return $return;
}

function getFacturas() {
    global $ClContifico;
    extract($_GET);

    $documentos = $ClContifico->getDocumentos($ruc, $fecha_inicio." 00:00:00", $fecha_fin." 23:59:59");
    if($documentos) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de documentos";
        $return['data'] = $documentos;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "No hay documentos";
    return $return;
}

function getOrdenesNoFActuradas() {
    global $ClContifico;
    extract($_GET);

    $documentos = $ClContifico->getOrdenesNoFActuradas($ruc, $fecha_inicio." 00:00:00", $fecha_fin." 23:59:59");
    if($documentos) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de órdenes";
        $return['data'] = $documentos;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "No hay órdenes sin facturar";
    return $return;
}
?>