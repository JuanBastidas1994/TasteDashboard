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
                    <h3 id="titulo">Reporte de ventas</h3>
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
                                                <label>Origen</label>
                                                <select class="form-control" id="cmbOrigen" name="cmbOrigen[]" required="required" multiple="multiple">
                                                    <option value="-1">TODOS</option>
                                                    <?php
                                                    $cod_empresa = $session['cod_empresa'];
                                                    $origenes = $Clreportes->getOrigenes($cod_empresa);
                                                    foreach ($origenes as $origen) {
                                                        $nomOrigen = strtoupper($origen['medio_compra']);
                                                        if ($nomOrigen == "")
                                                            $nomOrigen = "DESCONOCIDO";
                                                        $valueOrigen = $origen['medio_compra'];
                                                        echo '<option value="' . $valueOrigen . '">' . $nomOrigen . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="text-align: right;">
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
                            <ul class="nav nav-tabs mb-3 mt-3" id="lineTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="tab" href="#tab-grafico" role="tab" aria-controls="pills-home" aria-selected="true"><i data-feather="pie-chart"></i> Vista R&aacute;pida</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="pills-contact-tab" data-toggle="tab" href="#tab-tabla" role="tab" aria-controls="pills-contact" aria-selected="false"><i data-feather="layers"></i> Tabla</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="pills-productos-tab" data-toggle="tab" href="#tab-productos" role="tab" aria-controls="pills-productos" aria-selected="false"><i data-feather="layers"></i> Productos</a>
                                </li>
                            </ul>


                            <div class="tab-content" id="pills-tabContent">

                                <div class="tab-pane fade show active" id="tab-grafico" role="tabpanel" aria-labelledby="pills-home-tab" style="height: 700px;">
                                    <div class="col-xl-8 col-lg-9 col-sm-9  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">

                                            <input type="hidden" name="RespventasMeses" id="RespventasMeses" value="">
                                            <input type="hidden" name="RespMes" id="RespMes" value="">
                                            <input type="hidden" name="nomMes" id="nomMes" value="">
                                            <div class="">
                                                <div class="">
                                                    <div class="widget-heading">
                                                        <h5 class="">Ventas</h5>
                                                        <ul class="tabs tab-pills">
                                                            <li><a href="javascript:void(0);" id="tb_1" class="tabmenu">Mensual</a></li>
                                                        </ul>
                                                    </div>

                                                    <div class="widget-content">
                                                        <div class="tabs tab-content">
                                                            <div id="content_1" class="tabcontent">
                                                                <div id="grafico"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-3 col-sm-3  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Totales por mes</h4>
                                            </div>

                                            <table class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Mes</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lstMeses">

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="widget widget-one_hybrid widget-social">
                                            <div class="widget-heading">
                                                <div class="w-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link">
                                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                                    </svg>
                                                </div>
                                                <p class="w-value">Ventas netas</p>
                                            </div>
                                            <div class="widget-content">
                                                <table class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Ventas</th>
                                                            <th class="text-center">Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lstVentasNetas">
                                                        <tr>
                                                            <td>Neta</td>
                                                            <td class="txt_subtotal">$10,00 Test</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="w-chart">
                                                    <div id="hybrid_followers1"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="widget widget-one_hybrid widget-referral">
                                            <div class="widget-heading">
                                                <div class="w-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link">
                                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                                    </svg>
                                                </div>
                                                <p class="w-value">Ventas totales</p>
                                                <h6>Formas de pago</h6>
                                            </div>
                                            <div class="widget-content">
                                                <table class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo</th>
                                                            <th class="text-center">Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lstFormasPago">
                                                        <tr>
                                                            <td>Efectivo</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tarjeta Credito</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="w-chart">
                                                    <div id="hybrid_followers1"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="widget widget-one_hybrid widget-engagement">
                                            <div class="widget-heading">
                                                <div class="w-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link">
                                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                                    </svg>
                                                </div>
                                                <p class="w-value">Ventas totales</p>
                                                <h6>Origen</h6>
                                            </div>
                                            <div class="widget-content">
                                                <table class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo</th>
                                                            <th class="text-center">Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lstOrigen">
                                                        <tr>
                                                            <td>App</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Web/Chrome</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Web/Safari</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS</td>
                                                            <td>$10,00</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="w-chart">
                                                    <div id="hybrid_followers1"></div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                                <div class="tab-pane fade" id="tab-productos" role="tabpanel" aria-labelledby="pills-profile-tab2" style="height: 800px;">

                                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Productos Mas Vendidos</h4>
                                            </div>
                                            <table id="style-3" class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Producto</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-center">Valor Precio Generado</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lstProductos">


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-tabla" role="tabpanel" aria-labelledby="pills-profile-tab2" style="height: 800px;">

                                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Ordenes</h4>
                                            </div>
                                            <table id="style-4" class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Orden</th>
                                                        <th class="text-center">Cliente</th>
                                                        <th class="text-center">Fecha</th>
                                                        <th class="text-center">Envío/Retiro</th>
                                                        <th class="text-center">Descuento</th>
                                                        <th class="text-center">Envío</th>
                                                        <th class="text-center">Subtotal</th>
                                                        <th class="text-center">IVA</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">FP</th>
                                                        <th class="text-center">Estado</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lstOrdenes">
                                                    <!-- LISTA DE ÓRDENES -->
                                                </tbody>
                                            </table>
                                            </br>
                                            </br>
                                        </div>
                                    </div>

                                    
                                    <div class="col-xs-12 col-md-12 layout-spacing">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="col-xs-12 col-md-3 bg-white layout-spacing">
                                                <table class="table style-3">
                                                    <tbody>
                                                        <tr>
                                                            <td>Descuento</td>
                                                            <td>$<span id="txt_descuento">0</span></td>
                                                        <tr>
                                                        <tr>
                                                            <td>Subtotal</td>
                                                            <td>$<span class="txt_subtotal">0</span></td>
                                                        <tr>
                                                        <tr>
                                                            <td>Envío</td>
                                                            <td>$<span id="txt_envio">0</span></td>
                                                        <tr>
                                                        <tr>
                                                            <td>I.V.A</td>
                                                            <td>$<span id="txt_iva">0</span></td>
                                                        <tr>
                                                        <tr>
                                                            <td>Total</td>
                                                            <td>$ <span id="txt_total">0</span></td>
                                                        <tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="assets/js/pages/reporte_ventas.js?v=0" type="text/javascript"></script>

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