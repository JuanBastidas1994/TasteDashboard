<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_reporte_descuentos.php";
$Clempresas = new cl_empresas();
$clreporteDescuentos = new cl_reporte_descuentos();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function lista_ordenes(){
    global $session;
    global $clreporteDescuentos;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    
    /*CONTENIDO TABLA REPORTE POR PRODUCTO*/
    $tablaReporteProductos = "";
    $acum=0;
    $info=$clreporteDescuentos->ordenes_fecha($cod_empresa, $sucursal, $f_inicio, $f_fin);
    if($info)
    {
        foreach ($info as $orden) {
          

            $tablaReporteProductos.='<tr>
                        <td>'.$orden['nombre'].'</td>
                        <td>'.$orden['desc_text'].'</td>
                        <td>'.$orden['total_ordenes'].'</td>
                        <td>$ '.number_format($orden['total_precio'], 2).'</td>
                    </tr>';

        }
    }
    else {
        $return['success'] = 0;
        $return['mensaje'] = "No hay registros de descuentos aplicados al producto en este rango de fechas";
        $return['result'] = $info;
        return $return;
    }
    
    
    
    /*CONTENIDO TABLA REPORTE POR CUPONES*/
    $tablaReporteCupones = "";
    $acum=0;
    $info=$clreporteDescuentos->ordenes_fecha2($cod_empresa, $sucursal, $f_inicio, $f_fin);
    if($info)
    {
        foreach ($info as $orden) {
          

            $tablaReporteCupones.='<tr>
                        <td>'.$orden['cod_descuento'].'</td>
                        <td>'.$orden['desc_text'].'</td>
                        <td>'.$orden['total_ordenes'].'</td>
                        <td>$ '.number_format($orden['total_precio'], 2).'</td>
                    </tr>';

        }
    }
    else {
        $tablaReporteCupones = "
        No hay registros de descuentos aplicados al cupon en este rango de fechas
        ";
    }


    /*FIN CONTENIDO TAB DOS*/
    

    /*CONTENIDO TAB UNO*/
        $return['success'] = 1;
     //   $return['tabla'] = $tabla;
        $return['tablaReporteProductos'] = $tablaReporteProductos;
        $return['tablaReporteCupones'] = $tablaReporteCupones;

    return $return;
}

function topMenosVentas(){
    global $Clempresas;
    $htmlDatos = "";
    $d = strtotime("today");
    $domingo_anterior = date("Y-m-d", strtotime("last sunday midnight",$d))." 23:59:59";
    $lunes_anterior = date("Y-m-d H:i:s", strtotime("last monday midnight",strtotime("last sunday midnight",$d)));

    $datos = $Clempresas->ventasSemanales($lunes_anterior, $domingo_anterior);
    if($datos){
        foreach ($datos as $dato) {
            $logo = url_sistema."assets/empresas/".$dato['alias']."/logo.jpg";
            $htmlDatos.='   <div class="transactions-list">
                                <div class="t-item">
                                    <div class="t-company-name">
                                        <div class="t-icon">
                                            <div class="avatar avatar-xl">
                                                <img src="'.$logo.'" alt="img" height="30">
                                            </div>
                                        </div>
                                        <div class="t-name">
                                            <h4>'.$dato['nombre'].'</h4>
                                            <p class="meta-date"><a class="text-primary" href="reporte_empresa_ventas.php?id='.$dato['alias'].'" target="_blank">Ver reporte</a></p>
                                        </div>
                                    </div>
                                    <div class="t-rate rate-inc">
                                        <p><span>+$'.number_format($dato['total'], 2).'</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg></p>
                                    </div>
                                </div>
                            </div>';
        }
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['html'] = $htmlDatos;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    return $return;
}
?>