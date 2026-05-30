<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}

$clsucursales = new cl_sucursales(NULL);
$session      = getSession();
$alias        = $session['alias'];
$cod_rol      = $session['cod_rol'];
$enabled      = $cod_rol == 3 ? "disabled" : "";
$cod_sucursal = $cod_rol == 3 ? $session["cod_sucursal"] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .leyenda-item { display:inline-flex; align-items:center; margin-right:18px; font-size:13px; }
        .leyenda-dot  { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:5px; }
        #chartResumen { min-height: 320px; }
    </style>
</head>
<body>

    <?php top() ?>
    <?php navbar() ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php sidebar() ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="col-md-12" style="margin-top:25px;">
                    <h3>Reporte de tiempo de aceptación de pedidos</h3>
                </div>

                <!-- Filtros -->
                <div class="row mt-3">
                    <div class="col-md-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="form-row align-items-end">

                                <div class="form-group col-md-3 col-sm-6">
                                    <label>Sucursal <small class="text-muted">(aplica en Detalle)</small></label>
                                    <select class="form-control basic" id="cmb_sucursal" <?= $enabled ?>>
                                        <option value="0">Todas las sucursales</option>
                                        <?php
                                        $resp = $clsucursales->lista();
                                        foreach ($resp as $suc) {
                                            $sel = ($suc['cod_sucursal'] == $cod_sucursal) ? 'selected' : '';
                                            echo '<option value="' . $suc['cod_sucursal'] . '" ' . $sel . '>' . htmlspecialchars($suc['nombre']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-6 input-group" style="margin-bottom:10px;">
                                    <label>Fecha inicio</label>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i data-feather="calendar"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="fecha_inicio">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 input-group" style="margin-bottom:10px;">
                                    <label>Fecha fin</label>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i data-feather="calendar"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="fecha_fin">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-3 col-sm-3 col-12" style="text-align:right;">
                                    <button class="btn btn-primary" style="margin-top:30px;" id="btnReporte" data-alias="<?= $alias ?>">
                                        Generar reporte
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div id="seccionResultados" style="display:none;">
                    <div class="row">
                        <div class="col-md-12 layout-spacing">
                            <div class="widget-content widget-content-area br-6">

                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-resumen-lnk" data-toggle="tab" href="#tab-resumen" role="tab">
                                            <i data-feather="bar-chart"></i> Resumen por Sucursal
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-detalle-lnk" data-toggle="tab" href="#tab-detalle" role="tab">
                                            <i data-feather="list"></i> Detalle de pedidos
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <!-- TAB 1: Resumen visual -->
                                    <div class="tab-pane fade show active" id="tab-resumen" role="tabpanel">
                                        <div class="mb-3">
                                            <span class="leyenda-item"><span class="leyenda-dot" style="background:#1abc9c;"></span> Excelente (≤ 5 min)</span>
                                            <span class="leyenda-item"><span class="leyenda-dot" style="background:#f6a623;"></span> Aceptable (≤ 15 min)</span>
                                            <span class="leyenda-item"><span class="leyenda-dot" style="background:#e55353;"></span> Lento (> 15 min)</span>
                                        </div>
                                        <div id="chartResumen"></div>
                                        <div id="sinDatosResumen" class="text-center text-muted py-5" style="display:none;">
                                            No hay datos para el período seleccionado
                                        </div>
                                    </div>

                                    <!-- TAB 2: Tabla detalle -->
                                    <div class="tab-pane fade" id="tab-detalle" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="table-tiempos" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">ID Pedido</th>
                                                        <th class="text-center">Fecha ingreso</th>
                                                        <th class="text-center">Fecha aceptación</th>
                                                        <th class="text-center">Sucursal</th>
                                                        <th class="text-center">Tiempo</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lstTiempos"></tbody>
                                            </table>
                                        </div>

                                        <div class="px-2 py-3 text-right font-weight-bold" style="font-size:18px; color:#555;">
                                            Tiempo promedio: <span id="promedioTiempo" class="text-primary">--</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer() ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/pages/reporte_tiempo_aceptacion.js?v=3"></script>
</body>
</html>
