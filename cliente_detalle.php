<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_clientes.php";
require_once "clases/cl_notificaciones.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
//$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$Clnotificaciones = new cl_notificaciones(NULL);
$session = getSession();

$cod_empresa = $session['cod_empresa']; 
$cod_rol = $session['cod_rol'];
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$showCred = "";
$btnSendCred = "";
if($cod_rol < 3){
  $showCred = '<a href="javascript:void(0);" class="btn btn-success btnAddCred"> Agregar Crédito </a>';
  $btnSendCred = '<button type="button" class="btn btn-primary" id="btnAgregarCredito">Agregar Crédito</button>';
}
$rutaFidelizacion = "";

$imagen = url_sistema.'/assets/img/200x200.jpg';
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $rutaFidelizacion = "cliente_puntos.php?id=$id";
  if($cod_empresa == 78)
    $rutaFidelizacion = "cliente_puntos_maga.php?id=$id";
  $usuario = $Clusuarios->get2($id);
  if($usuario){
    $estado = $usuario['estado'];
	
    $nombre = $usuario['nombre'].' '.$usuario['apellido'];
    $correo = $usuario['correo'];
    $num_documento = $usuario['num_documento'];
    $fecha = fechaLatinoShort($usuario['fecha_nacimiento']);

    /*FIDELIZACION*/
    $clientes = new cl_clientes($num_documento);
    if($clientes->get()){
        $ClientId = $clientes->cod_cliente;
        $fidelizacionDinero = number_format($clientes->GetDinero(),2);
        $fidelizacionPuntos= $clientes->GetPuntos();
        $fidelizacionSaldo = number_format($clientes->GetSaldo(),2);
        $nivel = $clientes->getNivel($fidelizacionPuntos);

        $nivelPosicion = $nivel['posicion'] + 1;
        $nivelNombre = strtoupper(html_entity_decode($nivel['nombre']));
    }else{
      $ClientId = 0;
      $fidelizacionDinero = 0;
      $fidelizacionPuntos = 0;
      $fidelizacionSaldo = 0;
      $nivelPosicion = 1;
      $nivelNombre = "";
    }

    /*UBICACION*/
    $latitud =  "";
    $longitud =  "";

    /*NOTIFICACIONES*/
    $htmlTiposNotificaciones = "";
    $notificaciones = $Clnotificaciones->getTipoNotificacionUsuario();
    if($notificaciones){
        foreach ($notificaciones as $tipo) {
            $htmlTiposNotificaciones.='<option value="'.strtolower($tipo['cod_notificacion_tipo']).'">'.ucwords(strtolower($tipo['tipo'])).'</option>';
        }
    }
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
<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
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

        <div class="modal" tabindex="-1" role="dialog" id="modalCredito">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Agregar Crédito</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-12">
                    <label>Cantidad ($)</label>
                    <input type="number" id="txtAddCredito" placeholder="10" class="form-control">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <?=$btnSendCred?>
              </div>
            </div>
          </div>
        </div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><a id="btnBack" data-module-back="clientes.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Clientes</span></a>
                    </div>
                    <h3 id="titulo"><?php echo $nombre; ?></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_usuario != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="mail"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Enviar Correo</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Anular</span>
                      </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                      <!-- Datos de Facturacion -->
                      <div id="binfo" class="widget-content widget-content-area br-6">
                          <div><h4>Informaci&oacute;n <?php echo $usuario['cod_usuario']." - ".$ClientId; ?></h4></div>
                          <div class="row">
                            <div class="col-md-12"><span><b>Nombre: </b></span><?php echo $nombre; ?></div>
                            <div class="col-md-12"><span><b>Cedula/Ruc: </b></span><span id="cedulaNueva"><?php echo $usuario['num_documento']; ?></span></div>
                            <div class="col-md-12"><span><b>Correo: </b></span><?php echo $usuario['correo']; ?></div>
                            <div class="col-md-12"><span><b>Telef&oacute;no: </b></span><?php echo $usuario['telefono']; ?></div>
                            <div class="col-md-12"><span><b>Direcci&oacute;n: </b></span><?php echo $usuario['direccion']; ?></div>
                            <div class="col-md-12"><span><b>Fecha de Nacimiento: </b></span><?php echo $fecha; ?></div>
                          </div>
                          <div class="row" style="text-align: right;">
                            <div class="col-md-12">
                              <button class="btn btn-danger btn-sm" id="btnAbrirBloqueo">Bloquear</button>
                              <button class="btn btn-primary btn-sm" id="btnEditarInfo">Editar</button>
                            </div>
                          </div>
                      </div>

                      <div id="bloqueInfo" class="widget-content widget-content-area br-6" style="margin-top: 15px; display: none;">
                          <form name="frmInfo" id="frmInfo" autocomplete="off">
                            <input name="cod_usuario" id="cod_usuario" type="hidden" value="<?php echo $usuario['cod_usuario'];?>">
                            <div><h4>Editar Informaci&oacute;n</h4></div>
                            <div class="row">
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Nombre: </b></span>
                                <input name="txt_nombres" class="form-control" type="text" value="<?php echo $usuario['nombre']; ?>" required>
                              </div> 
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Apellidos: </b></span>
                                <input name="txt_apellidos" class="form-control" type="text" value="<?php echo $usuario['apellido']; ?>" required>
                              </div> 
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Cedula/Ruc: </b></span>
                                <input name="txt_cedula" id="txt_cedula" class="form-control" type="text" value="<?php echo $usuario['num_documento']; ?>" required>
                              </div>
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Telef&oacute;no: </b></span>
                                <input name="txt_telefono" class="form-control" type="text" value="<?php echo $usuario['telefono']; ?>" placeholder="Tel&eacute;fono">
                              </div>
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Direcci&oacute;n: </b></span>
                                <input name="txt_direccion" class="form-control" type="text" value="<?php echo $usuario['direccion']; ?>" placeholder="Direcci&oacute;n">
                              </div>
                              <div class="col-md-8" style="margin-bottom: 10px;">
                                <span><b>Fecha de Nacimiento: </b></span>
                                <input id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" type="date" value="<?php echo $usuario['fecha_nacimiento'] ?>" required>
                              </div>
                            </div>
                            <div class="row" style="text-align: right;">
                              <div class="col-12">
                                <button class="btn btn-default btn-sm" id="btnCancelarInfo">Cancelar</button>
                                <button class="btn btn-primary btn-sm" id="btnGuardarInfo">Guardar</button>
                              </div>
                            </div>
                          </form>
                      </div>

                      <div id="bloqueo" class="widget-content widget-content-area br-6" style="margin-top: 15px; display: none;">
                        <div><h4>Motivo de Bloqueo</h4></div>
                        <div class="row">
                          <div class="col-md-12" style="margin-bottom: 10px;">
                            <textarea class="form-control" name="txt_motivo" id="txt_motivo" placeholder="Motivo"></textarea>
                          </div> 
                        </div>
                        <div class="row" style="text-align: right;">
                          <div class="col-12">
                            <button class="btn btn-default btn-sm" id="btnCancelarEstado">Cancelar</button>
                            <button class="btn btn-danger btn-sm" id="btnEditarEstado">Bloquear</button>
                          </div>
                        </div>
                      </div>

                      <!-- Ubicacion -->
                      <div class="widget-content widget-content-area br-6" style="margin-top: 15px; display:none;">
                          <div><h4>Ubicaci&oacute;n</h4></div>
                          <div class="row">
                            <div id="mapa" class="gllpMap" style="margin-left: 0; width: 100%; height: 250px;" data-latitud="<?php echo $latitud; ?>" data-longitud="<?php echo $longitud; ?>">Google Maps</div>
                          </div>
                      </div>


                      <!-- TimeLine -->
                      <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                          <div><h4>Fidelizaci&oacute;n</h4></div>
                          <div class="row">
                              <!-- <div class="col-sm-8 col-7">
                                  <p class="">Puntos: </p>
                              </div>
                              <div class="col-sm-4 col-5">
                                  <p class=""><?php echo $fidelizacionPuntos; ?> puntos</p>
                              </div>
                              <div class="col-sm-8 col-7">
                                  <p class=" discount-rate">Dinero : </p>
                              </div>
                              <div class="col-sm-4 col-5">
                                  <p class="">$<?php echo $fidelizacionDinero; ?></p>
                              </div>
                              <div class="col-sm-8 col-7">
                                  <p class="">Saldo: </p>
                              </div>
                              <div class="col-sm-4 col-5">
                                  <p class="">$<?php echo $fidelizacionSaldo; ?></p>
                              </div>
                              <div class="col-sm-8 col-7">
                                  <p class="">Nivel </p>
                              </div>
                              <div class="col-sm-4 col-5">
                                  <p class=""><?php echo $nivelPosicion." - ".$nivelNombre; ?></p>
                              </div> -->
                              <div class="col-sm-12 col-12">
                                  <a href="<?php echo $rutaFidelizacion; ?>" class="btn btn-primary" target="_blank"> Ver detalle </a>
                                  <?=$showCred?>
                              </div>
                          </div>
                      </div>
                    
                      <!-- Ubicacion -->
                      <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                          <div><h4>Notificar</h4></div>
                          <div class="row">
                                <div class="col-12">
                                  <p>Tipo</p>
                                  <select class="form-control" name="cmbTipoNotificacion" id="cmbTipoNotificacion">
                                      <?= $htmlTiposNotificaciones?>
                                  </select>
                                </div>
                              <div class="col-12" style="display:none;">
                                <p>From:</p>
                                <textarea id="demo1"></textarea>
                              </div>
                              <div class="col-12">
                                <p style="margin-top: 10px;">Mensaje:</p>
                                <div id="containerEmoji"></div>
                              </div>
                              
                              <div class="col-sm-12 col-12" style="text-align: right;">
                                  <a style="margin-top: 10px;" class="btn btn-primary btnNotificar" id="btnNotificar" data-usuario="<?php echo $id;?>">Notificar</a>
                              </div>
                          </div>                          
                      </div>                      
                    </div>

                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Ordenes --> 
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>&Oacute;rdenes</h4></div>
                            <?php
                            $cod_empresa = $session['cod_empresa'];
                            $query = "SELECT ca.estado, count(ca.estado) as numero
                                  FROM tb_orden_cabecera ca
                                  WHERE ca.cod_usuario = $id
                                  AND ca.cod_empresa = $cod_empresa 
                                  GROUP BY ca.estado
                                  ORDER BY numero DESC";
                              $resp = Conexion::buscarVariosRegistro($query);
                              foreach ($resp as $items) {
                                  echo '<p><b>'.$items['estado'].':</b>&nbsp;'.$items['numero'].'</p>';
                              } 
                            ?>

                              <div class="x_content">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>Fecha</th>
                                      <th>sucursal</th>
                                      <th>Total</th>
                                      <th>tipo</th>
                                      <th>Estado</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  $resp = $Clordenes->listaByUsuario($id);
                                  foreach ($resp as $lista) {
                                    $fechaOrden = fechaLatinoShort($lista['fecha']);
                                    $badge='primary';
                                    if($lista['estado'] == 'I')
                                        $badge='danger';
                                    else if($lista['estado'] == "ENTREGADA")
                                        $badge='success';
                                    else if($lista['estado'] == "ASIGNADA")
                                        $badge='warning';
                                    else if($lista['estado'] == "CANCELADA")
                                         $badge='danger';  

                                    $envio = "Delivery";
                                    if($lista['is_envio'] == 0){
                                        $envio = "Pickup";
                                    } 

                                      echo '<tr>
                                        <td>'.$fechaOrden.'</td>
                                        <td>'.$lista['sucursal'].'</td>
                                        <td>$'.number_format($lista['total'],2).'</td>
                                        <td>'.$envio.'</td>
                                        <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($lista['estado']).'</span></td>
                                        <td><a href="orden_detalle.php?id='.$lista['cod_orden'].'" target="_blank"><i data-feather="eye"></i></a></td>
                                      </tr>';
                                  }

                                  ?>
                                  </tbody>
                                </table>  

                              </div>  
                             
                        </div>

                        <!-- Productos mas comprados --> 
                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                          <div><h4>Producto m&aacute;s comprado</h4></div>
                              <div class="x_content">
                                <table class="table style-3 table-hover">
                                  <thead>
                                    <tr>
                                      <th>&nbsp;</th>
                                      <th>Producto</th>
                                      <th>Veces compradas</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  $query = "SELECT DISTINCT p.*, count(p.cod_producto) as num_compras
                                    FROM tb_orden_cabecera oc, tb_orden_detalle od, tb_productos p
                                    WHERE oc.cod_orden = od.cod_orden
                                    AND od.cod_producto = p.cod_producto
                                    AND oc.cod_usuario = $id
                                    AND oc.cod_empresa = ".$cod_empresa."
                                    AND oc.estado NOT IN ('ANULADA')
                                    GROUP BY p.cod_producto";
                                  $resp = Conexion::buscarVariosRegistro($query);
                                  foreach ($resp as $productos) {
                                      $imagen = $files.$productos['image_min'];
                                      echo '<tr>
                                        <td><img src="'.$imagen.'" class="profile-img" alt=""></td>
                                        <td>'.$productos['nombre'].'</td>
                                        <td>'.$productos['num_compras'].'</td>
                                      </tr>';
                                  }

                                  ?>
                                  </tbody>
                                </table>  

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
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAHa67r_2hPqR_URtU8zsibmJx9Ahq7yGQ"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/orden_detalle.js" type="text/javascript"></script>
    <script src="assets/js/pages/emoji.js" type="text/javascript"></script>
    <script src="assets/js/pages/cliente_detalle.js" type="text/javascript"></script>
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>