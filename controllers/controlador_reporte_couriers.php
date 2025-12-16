<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_sucursales.php";
require_once "../clases/cl_couriers.php";
require_once "../clases/cl_reporte_couriers.php";
$Clempresas = new cl_empresas();
$Clsucursales = new cl_sucursales();
$ClCouriers = new cl_couriers();
$ClReporteCouriers = new cl_reporte_couriers();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getReportOLD(){
    global $session;
    global $Clsucursales;
    global $ClCouriers;
    global $ClReporteCouriers;

    $cod_empresa = $session["cod_empresa"];
    $couriers = $ClCouriers->getLista();
    $sucursales = $Clsucursales->lista();
    if($sucursales) {
        foreach ($sucursales as &$sucursal) {
            $tempCouriers = [];
            foreach($couriers as &$courier) {
                $ClReporteCouriers->cod_courier = $courier["cod_courier"];
                $ClReporteCouriers->cod_sucursal = $sucursal["cod_sucursal"];
                $detalles = $ClReporteCouriers->getDetalle();               
                if(count($detalles) > 0) {
                    $courier["detalle"] = $detalles;
                    $tempCouriers[] = $courier;           
                }   
            }
            $sucursal["couriers"] = $tempCouriers;
        }

        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['data'] = $sucursales;
        return $return;
    } 
    $return['success'] = 0;
    $return['mensaje'] = "no hay sucursales";
    return $return;
}

function getReport() {
    global $ClReporteCouriers;

    $POST = json_decode(file_get_contents('php://input'), true);
    extract($POST);

    $reporte = $ClReporteCouriers->getReport($cod_sucursal, $fechaInicio." 00:00:00", $fechaFin." 23:59:59");
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