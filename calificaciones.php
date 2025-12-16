<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_clientes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();

$cod_empresa = $session['cod_empresa']; 
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$calificacion = 0;
$query = "SELECT AVG(oc.calificacion) as promedio, s.nombre
        FROM tb_orden_calificacion oc, tb_orden_cabecera c
        WHERE c.cod_orden = oc.cod_orden
        AND c.cod_empresa = ".$cod_empresa;
$resp = Conexion::buscarRegistro($query);
if($resp){
  $calificacion = $resp['promedio'];
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
    <!--<link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
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
                <div class="col-md-8" >
                    <div><a id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></a>
                    </div>
                    <h3 id="titulo">Calificaciones</h3>
                </div>
                <div class="col-md-4">
                  <div>
                      <label style="margin: 0;">Promedio Total</label>
                      <div class="review" data-rating-stars="5" data-rating-value="<?php echo $calificacion; ?>" data-rating-half="true" data-rating-readonly="true" style="font-size: 30px;"></div>
                  </div>
                </div>
              </div>
                <div class="row layout-top-spacing">
                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Lista de los mejores -->
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Los mejores</h4></div>
                          <div class="x_content">
                                <table id="style-3" class="table style-3  table-hover">
                                  <thead>
                                    <tr>
                                      <th>Calificacion</th>
                                      <th>Texto</th>
                                      <th>Fecha</th>
                                      <th>Tipo</th>
                                      <th>Sucursal</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  $query = "SELECT oc.calificacion, oc.texto, c.fecha, c.is_envio, c.cod_orden, s.nombre as sucursal
                                          FROM tb_orden_calificacion oc, tb_orden_cabecera c, tb_sucursales s
                                          WHERE c.cod_orden = oc.cod_orden
                                          AND oc.calificacion >= 3.5
                                          AND c.cod_empresa = $cod_empresa
                                          AND c.cod_sucursal = s.cod_sucursal
                                          ORDER BY oc.calificacion DESC, c.fecha DESC";
                                  $resp = Conexion::buscarVariosRegistro($query);
                                  foreach ($resp as $items) {
                                    $envio = "Delivery";
                                    if($items['is_envio'] == 0){
                                        $envio = "Pickup";
                                    } 

                                      echo '<tr>
                                        <td>'.$items['calificacion'].'
                                        <div class="review" data-rating-stars="5" data-rating-value="'.$items['calificacion'].'" data-rating-half="true" data-rating-readonly="true"></div>
                                        </td>
                                        <td>'.$items['texto'].'</td>
                                        <td>'.datetimeShort($items['fecha']).'</td>
                                        <td>'.$envio.'</td>
                                        <td>'.$items['sucursal'].'</td>
                                        <td>
                                          <ul class="table-controls">
                                            <li><a href="orden_detalle.php?id='.$items['cod_orden'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><i data-feather="eye"></i></a></li>
                                          </ul>
                                        </td>
                                      </tr>';
                                  }

                                  ?>
                                  </tbody>
                                </table>  

                              </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Ordenes --> 
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Los peores</h4></div>
                              <div class="x_content">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>Calificacion</th>
                                      <th>Texto</th>
                                      <th>Fecha</th>
                                      <th>Tipo</th>
                                      <th>Sucursal</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  $query = "SELECT oc.calificacion, oc.texto, c.fecha, c.is_envio, c.cod_orden, s.nombre as sucursal
                                          FROM tb_orden_calificacion oc, tb_orden_cabecera c, tb_sucursales s
                                          WHERE c.cod_orden = oc.cod_orden
                                          AND oc.calificacion < 3.5
                                          AND c.cod_empresa = $cod_empresa
                                          AND c.cod_sucursal = s.cod_sucursal
                                          ORDER BY oc.calificacion ASC, c.fecha DESC";
                                  $resp = Conexion::buscarVariosRegistro($query);
                                  foreach ($resp as $items) {
                                      $envio = "Delivery";
                                      if($items['is_envio'] == 0){
                                          $envio = "Pickup";
                                      } 

                                      echo '<tr>
                                        <td>'.$items['calificacion'].'
                                        <div class="review" data-rating-stars="5" data-rating-value="'.$items['calificacion'].'" data-rating-half="true" data-rating-readonly="true"></div>
                                        </td>
                                        <td>'.$items['texto'].'</td>
                                        <td>'.datetimeShort($items['fecha']).'</td>
                                        <td>'.$envio.'</td>
                                        <td>'.$items['sucursal'].'</td>
                                        <td>
                                          <ul class="table-controls">
                                            <li><a href="orden_detalle.php?id='.$items['cod_orden'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><i data-feather="eye"></i></a></li>
                                          </ul>
                                        </td>
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
    <script src="assets/js/rating.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>