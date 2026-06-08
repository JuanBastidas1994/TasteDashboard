<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_reporte_historico.php";

if (!isLogin()) {
    header("location:login.php");
}

$clsucursales = new cl_sucursales(NULL);
$clhistorico  = new cl_reporte_historico();
$session      = getSession();
$cod_rol      = $session['cod_rol'];
$cod_sucursal = $cod_rol == 3 ? $session['cod_sucursal'] : 0;
$enabled      = $cod_rol == 3 ? "disabled" : "";

$anios        = $clhistorico->getAnios($session['cod_empresa']);
$anio_actual  = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="plugins/apex/apexcharts.css" rel="stylesheet">
    <style>
        .kpi-card            { border-radius: 8px; padding: 18px 22px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
        .kpi-card .kpi-label { font-size: 12px; color: #888ea8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .kpi-card .kpi-value { font-size: 26px; font-weight: 700; color: #0e1726; }
        .sinDatos            { display: none; text-align: center; color: #888ea8; padding: 40px 0; }
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
                    <h3>Reporte Histórico</h3>
                    <p class="text-muted" style="font-size:13px;">Datos basados en información colapsada por mes.</p>
                </div>

                <!-- Filtros -->
                <div class="row mt-2">
                    <div class="col-md-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="form-row align-items-end">

                                <div class="form-group col-md-3 col-sm-6">
                                    <label>Año</label>
                                    <select class="form-control basic" id="cmb_anio">
                                        <?php if (empty($anios)): ?>
                                            <option value="<?= $anio_actual ?>"><?= $anio_actual ?></option>
                                        <?php else: ?>
                                            <?php foreach ($anios as $a): ?>
                                                <option value="<?= $a['anio'] ?>"
                                                    <?= $a['anio'] == $anio_actual ? 'selected' : '' ?>>
                                                    <?= $a['anio'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-3 col-sm-6">
                                    <label>Sucursal</label>
                                    <select class="form-control basic" id="cmb_sucursal" <?= $enabled ?>>
                                        <option value="0">Todas las sucursales</option>
                                        <?php
                                        $suc_lista = $clsucursales->lista();
                                        foreach ($suc_lista as $suc):
                                            $sel = ($suc['cod_sucursal'] == $cod_sucursal) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $suc['cod_sucursal'] ?>" <?= $sel ?>>
                                                <?= htmlspecialchars($suc['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-6" style="margin-bottom:16px;">
                                    <button class="btn btn-primary w-100" id="btnGenerar">
                                        <i data-feather="bar-chart-2"></i> Generar reporte
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados -->
                <div id="seccionResultados" style="display:none;">

                    <!-- KPI Cards -->
                    <div class="row mb-3">
                        <div class="col-md-4 layout-spacing">
                            <div class="kpi-card">
                                <div class="kpi-label">Ventas Totales</div>
                                <div class="kpi-value" id="kpiVentas">—</div>
                            </div>
                        </div>
                        <div class="col-md-4 layout-spacing">
                            <div class="kpi-card">
                                <div class="kpi-label">Órdenes Entregadas</div>
                                <div class="kpi-value" id="kpiOrdenes">—</div>
                            </div>
                        </div>
                        <div class="col-md-4 layout-spacing">
                            <div class="kpi-card">
                                <div class="kpi-label">Ticket Promedio</div>
                                <div class="kpi-value" id="kpiTicket">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="row">
                        <div class="col-md-12 layout-spacing">
                            <div class="widget-content widget-content-area br-6">

                                <ul class="nav nav-tabs mb-4" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#tab-ventas" role="tab">
                                            <i data-feather="trending-up"></i> Ventas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-entrega" role="tab">
                                            <i data-feather="truck"></i> Tipo de Entrega
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-clientes" role="tab">
                                            <i data-feather="users"></i> Clientes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-productos" role="tab">
                                            <i data-feather="package"></i> Productos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-canales" role="tab">
                                            <i data-feather="pie-chart"></i> Canales & Horas
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <!-- TAB 1: Ventas por mes por sucursal -->
                                    <div class="tab-pane fade show active" id="tab-ventas" role="tabpanel">
                                        <h6 class="text-muted mb-3">Ventas por mes — desglosado por sucursal</h6>
                                        <div id="chartVentas"></div>
                                        <div class="sinDatos" id="sinVentas">No hay datos para el período seleccionado</div>
                                    </div>

                                    <!-- TAB 2: Tipo de entrega -->
                                    <div class="tab-pane fade" id="tab-entrega" role="tabpanel">
                                        <h6 class="text-muted mb-3">Monto de ventas por tipo de entrega — por mes</h6>
                                        <div id="chartEntrega"></div>
                                        <div class="sinDatos" id="sinEntrega">No hay datos para el período seleccionado</div>
                                    </div>

                                    <!-- TAB 3: Clientes nuevos vs recurrentes -->
                                    <div class="tab-pane fade" id="tab-clientes" role="tabpanel">
                                        <h6 class="text-muted mb-3">Clientes nuevos vs recurrentes — por mes</h6>
                                        <div id="chartClientes"></div>
                                        <div class="sinDatos" id="sinClientes">No hay datos para el período seleccionado</div>
                                    </div>

                                    <!-- TAB 4: Top productos -->
                                    <div class="tab-pane fade" id="tab-productos" role="tabpanel">
                                        <h6 class="text-muted mb-3">Top 10 productos más vendidos (unidades)</h6>
                                        <div id="chartProductos"></div>
                                        <div class="sinDatos" id="sinProductos">No hay datos para el período seleccionado</div>
                                    </div>

                                    <!-- TAB 5: Canales & Horas -->
                                    <div class="tab-pane fade" id="tab-canales" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <h6 class="text-muted mb-3">Ventas por canal / plataforma</h6>
                                                <div id="chartMedios"></div>
                                                <div class="sinDatos" id="sinMedios">No hay datos</div>
                                            </div>
                                            <div class="col-md-7">
                                                <h6 class="text-muted mb-3">Órdenes por hora del día</h6>
                                                <div id="chartHoras"></div>
                                                <div class="sinDatos" id="sinHoras">No hay datos</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /seccionResultados -->

            </div>
            <?php footer() ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/pages/reporte_historico.js?v=1"></script>
</body>
</html>
