<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_reporte_ventas.php";
$Clempresas = new cl_empresas();
$clreporteVentas = new cl_reporte_ventas();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function lista_ordenes(){
    global $session;
    global $clreporteVentas;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $tabla="";
    $info = $clreporteVentas->ordenes_diarias($sucursal, $f_inicio);
    if($info)
    {
        
            $tabla.='<table id="style-3" class="table style-3  table-hover">
                        <thead>
                            <tr>
                                <th>Fecha Creaci&oacute;n</th>
                                <th>Nombres</th>
                                <th>N&#186; de Identificaci&oacute;n</th>
                                <th>Monto</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tbInfo">';
                        foreach ($info as $orden) {
                    $tabla.='<tr>
                                <td>'.$orden['fecha'].'</td>
                                <td>'.$orden['nombre'].' '.$orden['apellido'].'</td>
                                <td>'.$orden['num_documento'].'</td>
                                <td>$ '.number_format($orden['total'], 2).'</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li><a href="orden_detalle.php?id='.$orden['cod_orden'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                                    </ul>
                                </td>
                            </tr>';
                        }
                $tabla.='</tbody>
                    </table>';
        $totales = $clreporteVentas->total_ordenes_diarias($sucursal, $f_inicio);
        $tipos_pago =  $clreporteVentas->total_ordenes_diarias_tipopago($sucursal, $f_inicio);
        
        $puntos = "0.00";
        $efectivo = "0.00";
        $tarjeta = "0.00";
        $transferencia = "0.00";
        
        foreach($tipos_pago as $tpago){
            if($tpago['forma_pago'] == "P" && $tpago['monto'] > 0){
                $puntos = $tpago['monto'];
            }
            else if($tpago['forma_pago'] == "E" && $tpago['monto'] > 0){
                $efectivo = $tpago['monto'];
            }
            else if($tpago['forma_pago'] == "T" && $tpago['monto'] > 0){
                $tarjeta = $tpago['monto'];
            }
            else if($tpago['forma_pago'] == "TB" && $tpago['monto'] > 0){
                $transferencia = $tpago['monto'];
            }
        }
        
        $bloque1.=' <p align="center"><strong>Detalle</strong></p>
                    <p><strong>Subtotal:</strong> $'.number_format($totales['subtotal'],2).'</p>
                    <p><strong>IVA:</strong> $'.number_format($totales['iva'], 2).'</p>
                    <p><strong>Env&iacute;o</strong>: $'.number_format($totales['envio'], 2).'</p>
                    <p><strong>Total:</strong> $'.number_format($totales['total'], 2).'</p>';
                    
        $bloque2.=' <p align="center"><strong>Formas de Pago</strong></p>
                    <p><strong>Efectivo:</strong> $'.number_format($efectivo, 2).'</p>
                    <p><strong>Tarjeta:</strong> $'.number_format($tarjeta, 2).'</p>
                    <p><strong>Transferencia:</strong> $'.number_format($transferencia, 2).'</p>
                    <p><strong>Puntos:</strong> $'.number_format($puntos, 2).'</p>';
        
        $pickup = $totales['cant_ordenes'] - $totales['delivery'];
        
        $bloque3.=' <p align="center"><strong>Entregas</strong></p>
                    <p><strong>Total &Oacute;rdenes:</strong> '.$totales['cant_ordenes'].'</p>
                    <p><strong>Delivery:</strong> '.$totales['delivery'].'</p>
                    <p><strong>Pickup:</strong> '.$pickup.'</p>';
        
        $return['success'] = 1;
        $return['tabla'] = $tabla;
        $return['bloque1'] = $bloque1;
        $return['bloque2'] = $bloque2;
        $return['bloque3'] = $bloque3;
        return $return;
    }
    else
    {
        $return['success'] = 0;
        $return['mensaje'] = "No se encontraron resultados, vuelva a intentarlo";
        return $return;
    }
}

function lista_ordenes2(){
    global $session;
    global $clreporteVentas;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);



    /*CONTENIDO TAB DOS*/
    $tabla="";
    $acum=0;
    $info=$clreporteVentas->ordenes_fecha($sucursal, $f_inicio, $f_fin);
    if($info)
    {
        foreach ($info as $orden) {
            $badge='primary';
            if($orden['estado'] == 'I')
                $badge='danger';
            else if($orden['estado'] == "ENTREGADA")
                $badge='success';
            else if($orden['estado'] == "ASIGNADA")
                $badge='warning';
            $tabla.='<tr>
                        <td>'.$orden['cod_orden'].'</td>
                        <td>'.$orden['nombre'].' '.$orden['apellido'].'</td>
                        <td>'.$orden['fecha'].'</td>
                        <td>$ '.$orden['total'].'</td>
                        <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($orden['estado']).'</span></td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li><a href="orden_detalle.php?id='.$orden['cod_orden'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
                            </ul>
                        </td>
                    </tr>';
          $acum=$acum+$orden['total'];
        }
    }
    else
    {
        $return['success'] = 0;
        $return['mensaje'] = "No se encontraron resultados, vuelva a intentarlo";
        return $return;
    }

    $iva=$acum*0.12;
    $subtotal=$acum-$iva;
    /*FIN CONTENIDO TAB DOS*/

    /*CONTENIDO TAB UNO*/
     $mi=explode("-", $f_inicio);
    $mesInicio=$mi[1];
    $anioInicio=$mi[0];
    
    $mf=explode("-", $f_fin);
    $mesFin=$mf[1];
    $anioFin=$mf[0];
    $conteMeses="";
    $c=0;
    $i=0;
    $mes=[];
    for($x=$mesInicio;$x<=$mesFin;$x++)
    {
          $resp=$clreporteVentas->datos_grafico($x, $sucursal, $anioInicio, $anioFin,$lstSucursal);
           if($resp){
            $infoSucursal;
                if($sucursal!=0){
                    $infoSucursal=$lstSucursal;
                }
                else
                {
                    foreach ($lstSucursal as $lista) {
                      //$infoSucursal=" ".$infoSucursal." ,".$lista['nombre']." "; 
                      $infoSucursal.=" ".$lista['nombre'].", "; 

                    }
                }
            
                foreach ($resp as $venta) {
                
                if($venta['monto']!=0)
                {
                    $data[] = number_format($venta['monto'],2);
                    $mesTexto = mesTextOnly($venta['mes']);
                    $mes[] = $mesTexto; 
                $conteMeses.='<tr>
                              <td style="text-align: center;">'.mesTextOnly($venta['mes']).'</td>
                              <td style="text-align: center;">$ '.number_format($venta['monto'],2).'</td>
                              <tr>';
                }
             }
           }
           else{
            $data[] = number_format(0,2);} 
          
        $c++;
    }
     $nomMes[$i]['name'] = $mes;
     $serie[$i]['name'] = $mesTexto;
     $serie[$i]['data'] = $data;
   

     if(!isset($serie))
        $serie = [];

    $reporteVentas = json_encode($serie);
    //$reporteVentas = base64_encode($reporteVentas);

    $arrayMes = json_encode($nomMes);
    $reporteMes = base64_encode($arrayMes);

    $return['reporte'] = $reporteVentas;
    $return['reporte2'] = $serie;
    $return['conteMeses'] = $conteMeses;

    $return['nomMes'] = $arrayMes;
    $return['respMes'] = $reporteMes;


    /*CONTENIDO TAB UNO*/
        $return['success'] = 1;
        $return['tabla'] = $tabla;
        $return['subtotal'] = number_format($subtotal,2);
        $return['iva'] = number_format($iva,2);
        $return['acum'] = number_format($acum,2);  
        $return['infoSucursal'] = $infoSucursal;
 
    return $return;
}


?>