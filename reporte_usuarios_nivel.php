<?php
require_once "funciones.php";
require_once "clases/cl_clientes.php";
require_once "clases/cl_reporte_usuarios_nivel.php";

if(!isLogin()){
    header("location:login.php");
}

$Clclientes = new cl_clientes(NULL);
$Clreporte = new cl_reporte_usuarios_nivel(NULL);
$session = getSession();
$cod_empresa = $session['cod_empresa'];
// if(!userGrant()){
//     header("location:index.php");
// }
$listaNiveles = $Clreporte->getNivelesByEmpresa($cod_empresa);
$html_niveles = '<option value="">Todos los niveles</option>';
foreach ($listaNiveles as $nivel) {
    $html_niveles.= '<option value="'.$nivel['posicion'].'">'.$nivel['nombre'].'</option>';
}

$cantidades = $Clreporte->cantidadByNivel($cod_empresa);
$html_cantidad = "";
foreach ($cantidades as $cantidad) {
    $html_cantidad.= '<p><b>'.$cantidad['nombre'].': </b>'.$cantidad['cantidad'].'</p>';
}

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
      <style type="text/css">
      
      .respGalery > div {
          margin-top: 15px;
      }
    </style>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/elements/alert.css">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>
<body>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(); ?>
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
                
                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-xs-12">
                                    <h4>Reporte usuarios por nivel</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-xs-12">
                                    <h4>Niveles</h4>
                                    <select class="form-control" name="cmb_niveles" id="cmb_niveles">
                                        <?= $html_niveles?>
                                    </select>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>

                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <?= $html_cantidad?>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>N&uacute;m. documento</th>
                                                <th>Nivel</th>
                                                <th>Dinero</th>
                                                <th>Puntos</th>
                                                <th>Saldo</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bloqueClientes">
                                            
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
    <!-- Mapas -->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAHa67r_2hPqR_URtU8zsibmJx9Ahq7yGQ&libraries=places"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/reporte_usuarios_nivel.js?v=0" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>