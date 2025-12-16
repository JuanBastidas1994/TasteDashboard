<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = url_sistema.'/assets/img/200x200.jpg';
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $orden = $Clordenes->get_orden_array($id);
  if($orden){
    $guia="";  
    $cod_sucursal = $orden['cod_sucursal'];
    $infoSuc = $Clsucursales->isMySucursal($cod_sucursal);
    if($orden['cod_courier']==2 && $infoSuc)
    {
        $guia= $orden['order_token'];
    }
    
    if($guia==""){
        header("location: ./index.php");
    }
    
    $Clsucursales->get($cod_sucursal);
    $InfoCiudadO = $Clsucursales->getInfoByCiudad($Clsucursales->cod_ciudad);
    $ciudadOrigen =$InfoCiudadO['nombre'];
    
    $InfoDestino = $Clordenes->get_destino_orden($id);
    $InfoCiudadD = $Clsucursales->getInfoByCiudad($InfoDestino['cod_ciudad']);
    $ciudadDestino =$InfoCiudadD['nombre'];
    
    $numOrden = str_pad($orden['cod_orden'], 6, "0", STR_PAD_LEFT);
    $estado = $orden['estado'];
    $textoEstado = getEstado($estado);
    
    $badge='primary';
    if($orden['estado'] == 'I')
        $badge='danger';
    else if($orden['estado'] == "ENTREGADA")
        $badge='success';
    else if($orden['estado'] == "ASIGNADA")
        $badge='warning';
    else if($orden['estado'] == "CANCELADA" || $orden['estado'] == "ANULADA")
        $badge='danger';
	
    $cod_usuario = $orden['cod_usuario'];
    $nombre = $orden['nombre'].' '.$orden['apellido'];
    $telefono = $orden['telefono'];
    $direccion = $orden['referencia'];
    $correo = $orden['correo'];
    $fecha = fechaLatinoShort($orden['fecha']);
    $hora = explode(" ",$orden['fecha'])[1];
    /*--NUEVO-*/
    $is_envio = $orden['is_envio'];
    $styleLinea="display:block";
    if ($is_envio == 0)
    $styleLinea = "display:none";
    /*--NUEVO-*/
    /*UBICACION*/
    $latitud =  $orden['latitud'];
    $longitud =  $orden['longitud'];

    
    /*DATOS DE FACTURACION*/
    $fact = $orden['datos_facturacion'];
    if($fact != ""){
      $datos_facturacion = json_decode($fact,true);
    }else{
      $datos_facturacion['nombre'] = "";
      $datos_facturacion['apellido'] = "";
      $datos_facturacion['cedula'] = "";
      $datos_facturacion['correo'] = "";
      $datos_facturacion['empresa'] = "";
      $datos_facturacion['telefono'] = "";
      $datos_facturacion['direccion'] = "";
    }
    

    /*DINERO*/
    $subtotal = number_format($orden['subtotal'],2);
    $descuento = number_format($orden['descuento'],2);
    $envio = number_format($orden['envio'],2);
    $iva = number_format($orden['iva'],2);
    $total = number_format($orden['total'],2);

  }else{
    header("location: ./index.php");
  }
}else{
  header("location: ./index.php");
}

function datetimeShort($fecha){
  $separate = explode(" ",$fecha);
  $fecha = $separate[0];
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('y', strtotime($fecha));

  $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return "$nombreMes $numeroDia/$anio ".substr($separate[1], 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
    <style type="text/css">
      .respGalery > div {
          margin-top: 15px;
      }

      .itemSucursal{
        border-radius: 6px;
        border: 1px solid #e0e6ed;
        padding: 14px 26px;
        margin-bottom: 10px;
      }

      .itemSucursal .title{
        font-size: 16px;
        font-weight: bold;
      }

      .switch.s-icons {
        height: auto;
      }

      .feather-16{
          width: 16px;
          height: 16px;
      }
    </style>
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <?php echo sidebar(); ?>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><a id="btnBack" data-module-back="ordenes.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Ordenes</span></a>
                    </div>
                    <h3 id="titulo"><?php echo ($numOrden != "") ? "Orden #".$numOrden : "Orden"; ?> <span class="shadow-none badge badge-<?php echo $badge; ?>"><?php echo $textoEstado; ?></span></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px;">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;display:none;">
                        <i class="feather-16" data-feather="mail"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Enviar Correo</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;display:none;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Anular</span>
                      </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                      
                      <!-- TimeLine -->
                      <div class="widget-content widget-content-area br-6" style="margin-top: 15px;<?php echo $styleLinea;?>" >
                          <div><h4>L&iacute;nea de Tiempo</h4></div>
                          <div class="container mt-container">
                             <ul class="modern-timeline pl-0" id="info-timeline">
                                     <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">POR RECOLECTAR</h4> 
                                                </p>
                                                <p class="t_recolectarF"><strong>Fecha: </strong> --</p>
                                                <hr>
                                                <p class="t_recolectarH"><strong>Hora: </strong> --</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">RECOLECTADO</h4> 
                                                </p>
                                                <p class="t_recolectadoF"><strong>Fecha: </strong> --</p>
                                                <hr>
                                                <p class="t_recolectadoH"><strong>Hora: </strong> --</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">EN BODEGA</h4> 
                                                </p>
                                                <p class="t_bodegaF"><strong>Fecha: </strong> --</p>
                                                <hr>
                                                <p class="t_bodegaH"><strong>Hora: </strong> --</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">EN TRANSITO</h4> 
                                                </p>
                                                <p class="t_transitoF"><strong>Fecha: </strong> --</p>
                                                <hr>
                                                <p class="t_transitoH"><strong>Hora: </strong> --</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">ZONA DE ENTREGA</h4> 
                                                </p>
                                                <p class="t_entregaF"><strong>Fecha: </strong> --</p>
                                                <hr>
                                                <p class="t_entregaH"><strong>Hora: </strong> --</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="modern-timeline-badge"></div>
                                        <div class="modern-timeline-panel">
                                            <div class="modern-timeline-preview"><img src="assets/img/509x343.jpg" alt="timeline"></div>
                                            <div class="modern-timeline-body">
                                                
                                                <p class="mb-4">
                                                    <h4 class="mb-4">ENTREGADO</h4> 
                                                </p>
                                                <p class="mb-4"><strong>Fecha: </strong> 03/02/2021</p>
                                                <hr>
                                                <p class="mb-4"><strong>Hora: </strong> 17:12</p>
                                            </div>
                                        </div>
                                    </li>
                                    
                                  
                                    
                                    <li class="position-static">
                                        <div class="modern-timeline-top"></div>
                                    </li>
                                    <li class="position-static">
                                        <div class="modern-timeline-bottom"></div>
                                    </li>
                                </ul>
                          </div>
                      </div>

                    </div>

                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Datos de Facturacion -->
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Datos de la gu&iacute;a</h4></div>
                          <div class="row mb-8">
                                <div class="col-sm-3 col-6">
                                    <p class=""><strong>Estado:</strong></p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_estado"><?php echo "Pendte"; ?></p>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <p class=""><strong>N&uacute;mero de gu&iacute;a:</strong></p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_num">--</p>
                                </div>
                                
                                <div class="col-sm-3 col-6">
                                    <p class="">Producto: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_producto">--</p>
                                </div>
                                
                                <div class="col-sm-3 col-6">
                                    <p class="">Envios: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_envios">--</p>
                                </div>
                                
                                <div class="col-sm-3 col-6">
                                    <p class="">Peso: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_peso">--</p>
                                </div>
                                
                                <div class="col-sm-3 col-6">
                                    <p class="">Nombre Cliente: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_nomC">--</p>
                                </div>
                          </div>
                          <hr>
                          <div class="row mb-8" style="margin-top:10px">
                                <div class="col-sm-3 col-6">
                                    <p class=""><strong>De:</strong></p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_deO"><?php echo strtoupper($session['alias'])." - ".$infoSuc['nombre']; ?></p>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <p class="">Ciudad Origen: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_ciuO"><?php echo $ciudadOrigen; ?></p>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <p class="">Fecha de Envio: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_fechaEO"><?php echo $fecha; ?></p>
                                </div>
                          </div>
                          <hr>
                          <div class="row">
                             <div class="col-sm-3 col-6">
                                    <p class=""><strong>Para:</strong></p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_paraO"><?php echo $nombre; ?></p>
                                </div>
                                <div class="col-sm-3 col-6">
                                    <p class="">Ciudad Destino: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_ciuD"><?php echo $ciudadDestino; ?></p>
                                </div>
                                 <div class="col-sm-3 col-6">
                                    <p class="">Direcci&oacute;n Destino: </p>
                                </div>
                                <div class="col-sm-9 col-6">
                                    <p class="info_dirD"><?php echo $direccion; ?></p>
                                </div>
                            
                          </div>
                      </div>
                        <!-- Informacion basica --> 
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Detalle</h4></div>
                          <input type="hidden" name="id" id="id" value="<?php echo $cod_producto; ?>">
                          <input type="hidden" name="url_laar" id="url_laar" value="<?php echo url_laar; ?>">
                          <input type="hidden" name="guia" id="guia" value="<?php echo $guia; ?>">
                          
                            <form name="frmSave" id="frmSave" autocomplete="off">
                              <div class="x_content">

                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>Producto</th>
                                      <th>Cant.</th>
                                      <th>Precio</th>
                                      <th>Total</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  //$resp = $Clordenes->get_detalle_orden($id);
                                  foreach ($orden['detalle'] as $detalle) {
                                    $comentariosDetalle = '';
                                    if($detalle['descripcion']!=""){
                                      $comentariosDetalle='<dl>
                                        <dt>Comentarios</dt>
                                          <dd>'.$detalle['descripcion'].'</dd>
                                        </dl>';
                                    }
                                      echo '<tr>
                                        <td>'.$detalle['nombre'].'
                                          '.$comentariosDetalle.'
                                        </td>
                                        <td>'.$detalle['cantidad'].'</td>
                                        <td>$'.number_format($detalle['precio'],2).'</td>
                                        <td>$'.number_format(($detalle['precio']*$detalle['cantidad']),2).'</td>
                                      </tr>';
                                  }

                                  ?>
                                  </tbody>
                                </table>  
                                
                                <div class="">
                                    <div class="inv--total-amounts text-sm-right">
                                        <div class="row">
                                            <div class="col-sm-8 col-7">
                                                <p class="">SubTotal: </p>
                                            </div>
                                            <div class="col-sm-4 col-5">
                                                <p class="">$<?php echo $subtotal; ?></p>
                                            </div>
                                            <div class="col-sm-8 col-7">
                                                <p class=" discount-rate">Descuento : </p>
                                            </div>
                                            <div class="col-sm-4 col-5">
                                                <p class="">$<?php echo $descuento; ?></p>
                                            </div>
                                            <div class="col-sm-8 col-7">
                                                <p class="">Envío: </p>
                                            </div>
                                            <div class="col-sm-4 col-5">
                                                <p class="">$<?php echo $envio; ?></p>
                                            </div>
                                            <div class="col-sm-8 col-7">
                                                <p class="">Impuestos: </p>
                                            </div>
                                            <div class="col-sm-4 col-5">
                                                <p class="">$<?php echo $iva; ?></p>
                                            </div>
                                            
                                            <div class="col-sm-8 col-7 grand-total-title">
                                                <h4 class="">Total : </h4>
                                            </div>
                                            <div class="col-sm-4 col-5 grand-total-amount">
                                                <h4 class="">$<?php echo $total; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                  </div>

                                </div>  
                              </form>
                              <div class="col-12" style="text-align: right;">
                              <a class="btn btn-primary" href="cliente_detalle.php?id=<?php echo $cod_usuario; ?>" style="margin-top: :8px;">
                                Ver m&aacute;s
                              </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/orden_tracking.js" type="text/javascript"></script>
    <script src="assets/js/rating.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>