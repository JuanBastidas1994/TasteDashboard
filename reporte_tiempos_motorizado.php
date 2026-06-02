<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);
$session = getSession();
$alias = $session['alias'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
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
                <div class="col-md-12" style="margin-top:25px;">
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle; color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Reporte de tiempos del motorizado</h3>
                </div>

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12">
                        <div class="">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                                <label>Sucursales <span class="asterisco">*</span></label>
                                                <select class="form-control basic" id="cmb_sucursal">
                                                    <option value="0">Todas las sucursales</option>
                                                    <?php
                                                    $resp = $clsucursales->lista();
                                                    foreach ($resp as $sucursal) {
                                                        echo '<option value="' . $sucursal['cod_sucursal'] . '">' . $sucursal['nombre'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha inicio</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha fin</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-md-3 col-sm-3 col-12" style="text-align: right;">
                                                <button class="btn btn-primary btnReporte" style="margin-top: 30px;" data-alias="<?= $alias ?>">Generar reporte</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>

                    <div>
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Tiempos por pedido</h4>
                                </div>
                                <table id="table-tiempos" class="table style-3">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID Pedido</th>
                                            <th class="text-center">Motorizado</th>
                                            <th class="text-center">Asignado</th>
                                            <th class="text-center">En camino</th>
                                            <th class="text-center">Entregado</th>
                                            <th class="text-center">T. Aceptar</th>
                                            <th class="text-center">T. En camino</th>
                                            <th class="text-center">T. Total</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lstTiempos">
                                    </tbody>
                                </table>
                                <br><br>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-2 w-100" style="position: sticky; bottom: 0; background: #f1f2f3;">
                        <div class="row text-center" style="font-size: 16px; font-weight: bold; color: gray;">
                            <div class="col-4">
                                Promedio aceptar: <span id="promedioAceptar">--</span>
                            </div>
                            <div class="col-4">
                                Promedio en camino: <span id="promedioCamino">--</span>
                            </div>
                            <div class="col-4">
                                Promedio total: <span id="promedioTotal">--</span>
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
    <script src="assets/js/pages/reporte_tiempos_motorizado.js?v=1" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

</body>

</html>
