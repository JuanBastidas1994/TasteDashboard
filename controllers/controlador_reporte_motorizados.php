<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_reporte_ventas.php";
$Clempresas = new cl_empresas();
$Clsucursales = new cl_sucursales();
$clreporteVentas = new cl_reporte_ventas();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getOffices(){
    global $Clsucursales;
    $offices = $Clsucursales->lista();
    if($offices){
        $return['success'] = 1;
        $return['mensaje'] = "Lista de sucursales";
        $return['data'] = $offices;
        return $return;
    }
    $return['success'] = 0;
    $return['mensaje'] = "No hay sucursales";
    return $return;
}

function getReport(){
    global $session;
    $cod_empresa = $session["cod_empresa"];
    extract($_POST);
    $filter = "";
    if($cod_sucursal > 0)
        $filter = " AND oc.cod_sucursal = $cod_sucursal";

    $query = "SELECT oc.cod_orden, oc.estado, CONCAT(om.nombre, ' ', om.apellido) as motorizado, ma.fecha_asignacion, ma.fecha_salida, ma.fecha_llegada
                FROM tb_orden_cabecera oc, tb_orden_motorizado om, tb_motorizado_asignacion ma
                WHERE oc.cod_orden = om.cod_orden
                AND oc.cod_orden = ma.cod_orden
                AND oc.cod_empresa = $cod_empresa
                AND oc.cod_courier = 99
                AND DATE(oc.fecha) BETWEEN '$fInicio' AND '$fFin'
                $filter
                ORDER BY oc.cod_orden DESC";
    $orders = Conexion::buscarVariosRegistro($query);
    if($orders){
        $return['success'] = 1;
        $return['mensaje'] = "Lista órdenes";
        $return['data'] = $orders;
        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "No hay órdenes";
    return $return;
}
?>