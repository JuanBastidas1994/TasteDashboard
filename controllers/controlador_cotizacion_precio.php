<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_cotizacion_precio.php";

$session = getSession();

controller_create();

function getSucursales() {
    $ClSucursales = new cl_sucursales();
    $sucursales = $ClSucursales->lista();
    if($sucursales) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de sucursales";
        $return['data'] = $sucursales;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "No hay sucursales";
    return $return;
}

function getCotizaciones() {
    extract($_GET);

    $sucursal = isset($sucursal) ? $sucursal : '';

    $ClCotizacionPrecio = new cl_cotizacion_precio();
    $cotizaciones = $ClCotizacionPrecio->lista($sucursal);
    if($cotizaciones) {
        $return['success'] = 1;
        $return['mensaje'] = "Lista de cotizaciones";
        $return['data'] = $cotizaciones;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "No hay resultados para los filtros seleccionados";
    return $return;
}
?>
