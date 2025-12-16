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

    if(!is_array($origen)){
        $datos = $clreporteVentas->getOrigenes($cod_empresa);
        foreach ($datos as $dato) {
            $d[] = $dato['medio_compra'];
        }
        $origenes = agregaComillasEnArray($d);
    }
    else{
        $origenes = agregaComillasEnArray($origen);
    }

    /*CONTENIDO TAB TRES*/
    $tablaProductos ="";
    $info=$clreporteVentas->lista_productos_ingresos($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes);
    if($info)
    {
        foreach ($info as $productos) {
            $files = url_sistema.'assets/empresas/'.$alias.'/';
            $imagen = $files.$productos['image_min'];
            $tablaProductos.='<tr>
                        <td>
                            <div class="td-content product-name"><img class="imground" src="'.$imagen.'" alt="product">
                                '.$productos['nombre'].'
                            </div>
                        </td>
                        <td><div class="td-content"><span class="discount-pricing">'.$productos['total'].'</span></div></td>
                        <td><div class="td-content"><span class="pricing">$'.number_format($productos['dinero'],2).'</span></div></td>
                        
                    </tr>';
        }
    }
    /*FIN CONTENIDO TAB TRES*/
    
    /*CONTENIDO TAB DOS*/
    $tabla="";
    $acum=0;
    $info=$clreporteVentas->ordenes_fecha($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes);
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

            $TipoEntrega = "Pickup";
            if($orden['is_envio'] == 1)
                $TipoEntrega = "Envío";


            $textformasPago = "";
            //FORMAS DE PAGO
            $textformasPago = "";
            $tipoformasPagos = $clreporteVentas->getOrdenTipoPagos($orden['cod_orden']);
            foreach ($tipoformasPagos as $tipoformasPago) {
                $textformasPago.= $tipoformasPago["descripcion"] . ",";
            }
            $textformasPago = substr($textformasPago, 0, strlen($textformasPago) -1);

            $tabla.='<tr>
                        <td>'.$orden['cod_orden'].'</td>
                        <td>'.$orden['nombre'].' '.$orden['apellido'].'</td>
                        <td>'.$orden['fecha'].'</td>
                        <td>'.$TipoEntrega.'</td>
                        <td>$ '.$orden['descuento'].'</td>
                        <td>$ '.$orden['envio'].'</td>
                        <td>$ '.$orden['subtotal'].'</td>
                        <td>$ '.$orden['iva'].'</td>
                        <td>$ '.$orden['total'].'</td>
                        <td>'.$textformasPago.'</td>
                        <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($orden['estado']).'</span></td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li>
                                    <a href="orden_detalle.php?id='.$orden['cod_orden'].'" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles">
                                        <i data-feather="eye"></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>';

          $acumSubtotal = $acumSubtotal + $orden['subtotal'];
          $acumDescuento = $acumDescuento + $orden['descuento'];
          $acumEnvio = $acumEnvio + $orden['envio'];
          $acumIva = $acumIva + $orden['iva'];
          $acumTotal = $acumTotal + $orden['total'];
        }
    }
    else {
        $return['success'] = 0;
        $return['mensaje'] = "No hay registros de ventas en este rango de fechas";
        return $return;
    }

    $iva = $acumIva;
    $subtotal = $acumSubtotal;
    $total = $acumTotal;
    $envio = $acumEnvio;
    $descuento = $acumDescuento;
    /*FIN CONTENIDO TAB DOS*/

    /*CONTENIDO TAB UNO*/
    //Determinar diferencia de meses
    $fechainicial = new DateTime($f_inicio);
    $fechafinal = new DateTime($f_fin);
    $diferencia = $fechainicial->diff($fechafinal);
    $cantMeses = ( $diferencia->y * 12 ) + $diferencia->m;
    $return['cantMeses'] = $cantMeses;
    
    $conteMeses="";
    $c=0;
    $i=0;
    $mes=[];
    for($x=1;$x<=$cantMeses;$x++)
    {
          $aux = $fechainicial->format('Y-m').'-01';
          $fechainicial = new DateTime($aux);
          
          $resp=$clreporteVentas->datos_grafico($cod_empresa, $fechainicial->format('n'), $sucursal, $fechainicial->format('Y'), $origenes);
           if($resp){
                foreach ($resp as $venta) {
                    if($venta['monto']!=0)
                    {
                        $data[] = number_format($venta['monto'],2,".","");
                        $mesTexto = mesTextOnly($venta['mes']).'/'.$fechainicial->format('y');
                        $mes[] = $mesTexto; 
                        $conteMeses.='<tr>
                                  <td style="text-align: center;">'.$mesTexto.'</td>
                                  <td style="text-align: center;">$ '.number_format($venta['monto'],2).'</td>
                                  <tr>';
                    }
                 }
           }
           else{
            $data[] = number_format(0,2);
           } 
        
        $fechainicial = $fechainicial->add(new DateInterval("P1M"));
    }
     $labels = $mes;
     $serie[$i]['name'] = "Ventas";
     $serie[$i]['data'] = $data;
   

     if(!isset($serie))
        $serie = [];

    $reporteVentas = json_encode($serie);
    
    //Determinar formas de pago
    $formasPago="";
    $info=$clreporteVentas->total_formasPago($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes);
    if($info)
    {
        $tpag = [];
        $tpag2 = [];
        $tipoPagos = $clreporteVentas->getTipoPagos();
        foreach ($tipoPagos as $tipoPago) {
            $tpag[] = $tipoPago['cod_forma_pago'];
            $tpag2[$tipoPago['cod_forma_pago']] = $tipoPago['descripcion'];
        }
        foreach ($info as $fp) {
            $fpago = $fp['forma_pago'];
            if(!in_array($fp['forma_pago'], $tpag)){
                $fpago = "Desconocida";
            }
            else{
                $fpago = $tpag2[$fp['forma_pago']];
            }
            $formasPago.='
                    <tr><td>'.$fpago.'</td><td>$'.number_format($fp['total'],2).'</td></tr>
                    ';
        }
    }
    
    //Determinar Medio de Origen
    $listaOrigen = "";
    $info=$clreporteVentas->medio_origen($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes);
    if($info)
    {
        foreach ($info as $medio) {
            $medio_compra = $medio['medio_compra'];
            if($medio_compra == "")
                $medio_compra = "DESCONOCIDO";
            $listaOrigen.='
                    <tr><td>'.strtoupper($medio_compra).'</td><td>$'.number_format($medio['total'],2).'</td></tr>
                    ';
        }
    }
    
    $return['reporte'] = $reporteVentas;
    $return['labelsChart'] = $labels;
    $return['reporte2'] = $serie;
    $return['conteMeses'] = $conteMeses;
    $return['formasPago'] = $formasPago;
    $return['listaOrigen'] = $listaOrigen;
    

    /*CONTENIDO TAB UNO*/
        $return['success'] = 1;
        $return['tabla'] = $tabla;
        $return['tablaProductos'] = $tablaProductos;
        $return['subtotal'] = number_format($subtotal,2);
        $return['iva'] = number_format($iva,2);
        $return['total'] = number_format($total,2);  
        $return['envio'] = number_format($envio,2);  
        $return['descuento'] = number_format($descuento,2);  
 
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

// REPORTES DEL INDEX
function getSalesDayByDay() {
    global $session;
    global $clreporteVentas;
    
    extract($_GET);

    $cod_empresa = $session['cod_empresa'];

    $report = $clreporteVentas->getSalesDayByDay($dateStart, $dateEnd, $numDays, $cod_empresa);
    if($report) {
        $return['success'] = 1;
        $return['mensaje'] = "reporte";
        $return['data'] = $report;

        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "no hay reporte";

    return $return;
}

function getSalesDayByDay2() {
    global $session;
    global $clreporteVentas;
    
    $POST = json_decode(file_get_contents("php://input"), true);
    extract($POST);

    $cod_empresa = $session['cod_empresa'];

    $report = $clreporteVentas->getSalesDayByDay2($days, $cod_empresa);
    if($report) {
        $return['success'] = 1;
        $return['mensaje'] = "reporte";
        $return['data'] = $report;

        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "no hay reporte";

    return $return;
}

function getRanking() {
    global $session;
    global $clreporteVentas;

    $cod_empresa = $session['cod_empresa'];

    $report = $clreporteVentas->getRanking($cod_empresa);
    if($report) {
        $return['success'] = 1;
        $return['mensaje'] = "reporte";
        $return['data'] = $report;

        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "no hay reporte";

    return $return;
}

function getMonthlySales() {
    global $session;
    global $clreporteVentas;

    extract($_GET);

    $cod_empresa = $session['cod_empresa'];

    $report = $clreporteVentas->getMonthlySalesByOriginQuantity($cod_empresa, $sucursal, $fIni, $fFin);
    if($report) {
        $return['success'] = 1;
        $return['mensaje'] = "reporte";
        $return['data'] = $report;

        return $return;
    }

    $return['success'] = 0;
    $return['mensaje'] = "no hay reporte";

    return $return;
}
?>