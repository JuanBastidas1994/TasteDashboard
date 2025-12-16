<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_reporte_ventas.php";

require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);

$Clcategorias = new cl_categorias(NULL);

$Clreportes = new cl_reporte_ventas(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
$alias = $session['alias'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="assets/css/widgets/modules-widgets.css">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .imground {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .dropify-wrapper {
            display: block;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            width: 100%;
            max-width: 100%;
            height: 110px !important;
            padding: 5px 10px;
            font-size: 14px;
            line-height: 22px;
            color: #777;
            background-color: #fff;
            background-image: none;
            text-align: center;
            border: 0 !important;
            -webkit-transition: border-color .15s linear;
            transition: border-color .15s linear;
        }

        .respGalery>div {
            margin-top: 15px;
        }

        .croppie-container .cr-boundary {
            background-image: url(assets/img/transparent.jpg);
            background-position: center;
            background-size: cover;
        }
    </style>
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
            <div class="layout-px-spacing bg-white">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Reporte de descuentos</h3>
                </div>

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                                <label>Sucursales <span class="asterisco">*</span></label>
                                                <select class="form-control  basic" id="cmb_sucursal">
                                                    <option value="0" selected="selected">Todas las sucursales</option>
                                                    <?php
                                                    $resp = $clsucursales->all();
                                                    foreach ($resp as $sucursales) {
                                                        $estado = $sucursales["estado"] == "D" ? " - <span class='text-danger'>(Eliminada)</span>" : "";
                                                        echo '<option value="' . $sucursales['cod_sucursal'] . '">' . $sucursales['nombre'] . ''.$estado.'</option>';
                                                    }

                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha inicio</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha fin</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_fin" id="fecha_fin">
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-3 col-sm-3 col-3">
                                                <button class="btn btn-primary btnReporte" style="margin-top: 30px;" data-empresa="<?= $cod_empresa ?>" data-alias="<?= $alias ?>">Generar reporte</button>
                                            </div>
                                        </div>
                                    </div>




                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>


                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing underline-content" id="Content-tabs" style="display:none">
                        <div class="widget-content widget-content-area br-6">
                            
                            <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Productos</h4>
                                            </div>
                                            <table id="style-4" class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Nombre</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody id="lstReportProducts">
                                                    <!-- LISTA DE PRODUCTOS -->
                                                </tbody>
                                            </table>
                                            </br>
                                            </br>
                                        </div>
                            </div>
                            
                            <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Cupones</h4>
                                            </div>
                                            <table id="style-4" class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Nombre</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody id="lstReportCupones">
                                                    <!-- LISTA DE CUPONES -->
                                                </tbody>
                                            </table>
                                            </br>
                                            </br>
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
    <script src="assets/js/pages/reporte_descuentos.js?v=0" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

</body>

</html>