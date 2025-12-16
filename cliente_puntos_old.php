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

$imagen = url_sistema.'/assets/img/200x200.jpg';
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $usuario = $Clusuarios->get2($id);
  if($usuario){

    $estado = $usuario['estado'];
	
    $nombre = $usuario['nombre'].' '.$usuario['apellido'];
    $correo = $usuario['correo'];
    $num_documento = $usuario['num_documento'];
    $fecha = fechaLatinoShort($usuario['fecha_nacimiento']);

    $query = "SELECT oc.cod_orden, oc.fecha, s.nombre as sucursal, IF(oc.is_envio = 1, 'DELIVERY', 'PICKUP') as tipo, oc.estado, oc.total, IFNULL(n.nombre, '-') as nivel, IFNULL(cp.puntos, '0') as puntos, IFNULL(cp.dinero, '0') as dinero_ganado, IFNULL(cp.fecha_caducidad, '-') as fecha_caducidad, op.monto as dinero_utilizado
    FROM tb_orden_cabecera oc
    LEFT JOIN tb_orden_pagos op
        ON oc.cod_orden = op.cod_orden 
        AND op.forma_pago = 'P'
    INNER JOIN tb_usuarios u
        ON oc.cod_usuario = u.cod_usuario
    INNER JOIN tb_clientes c
        ON u.cod_usuario = c.cod_usuario
    INNER JOIN tb_sucursales s
        ON oc.cod_sucursal = s.cod_sucursal
    LEFT JOIN tb_clientes_puntos cp
        ON oc.cod_orden = cp.cod_orden
    LEFT JOIN tb_niveles n
        ON cp.cod_nivel = n.posicion
        AND oc.cod_empresa = n.cod_empresa
    WHERE oc.cod_usuario = $id
    AND oc.estado = 'ENTREGADA'
    AND oc.fecha > DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ORDER BY oc.cod_orden DESC";
    
    $reporte = Conexion::buscarVariosRegistro($query);

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

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div><a id="btnBack" data-module-back="cliente_detalle.php?id=<?php echo $id; ?>" style="cursor: pointer;">
                          <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Informaci&oacute;n del Cliente</span></a>
                        </div>
                        <h3 id="titulo"><?php echo $nombre; ?></h3>
                    </div>
                </div>

                <div class="layout-top-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div class="row >
                            <div class="col-12  layout-spacing">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Sucursal</th>
                                                <th>Total</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Nivel</th>
                                                <th>Puntos</th>
                                                <th>Dinero ganado</th>
                                                <th>Dinero usado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($reporte as $key => $rep) {
                                                    $dpPuntos = $rep["puntos"] > 0 ? '' : ' d-none';
                                                    $dpGanado = $rep["dinero_ganado"] > 0 ? ' outline-badge-success' : ' d-none';
                                                    $dpUtilizado = $rep["dinero_utilizado"] > 0 ? '' : ' d-none';

                                                    $icon = "star";

                                                    if(date_create($rep["fecha_caducidad"]) < date_create(fecha_only())) {
                                                        $icon = "frown";
                                                        $dpGanado.= " outline-badge-dark";
                                                    }

                                                    $htmlPuntos = " <span class='$dpPuntos' title='Caduca el {$rep["fecha_caducidad"]}'>
                                                                        {$rep["puntos"]}
                                                                        <i data-feather='$icon'></i>
                                                                    </span>";

                                                    $htmlGanado = " <span class='badge $dpGanado' title='Caduca el {$rep["fecha_caducidad"]}'>
                                                                        <i data-feather='arrow-up'></i> $ ".number_format($rep["dinero_ganado"], 2, ".", "")."
                                                                    </span>";
                                                    
                                                    $htmlUtilizado = " <span class='badge outline-badge-danger $dp2'>
                                                                            <i data-feather='arrow-down'></i> - $ ".number_format($rep["dinero_utilizado"], 2, ".", "")."
                                                                        </span>";

                                                    echo "  <tr>
                                                                <td>{$rep["fecha"]}</td>
                                                                <td>{$rep["sucursal"]}</td>
                                                                <td>$ {$rep["total"]} </td>
                                                                <td>{$rep["tipo"]}</td>
                                                                <td>
                                                                    <span class='badge badge-success'>{$rep["estado"]}</span>
                                                                </td>
                                                                <td>{$rep["nivel"]}</td>
                                                                <td>
                                                                    $htmlPuntos
                                                                </td>
                                                                <td>
                                                                    $htmlGanado
                                                                </td>
                                                                <td>
                                                                    $htmlUtilizado
                                                                </td>
                                                            </tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>