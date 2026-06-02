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
        .hora-bloque { border-radius: 6px; overflow: hidden; }
        .metric-card { border-radius: 8px; padding: 16px 20px; color: #fff; }
        .metric-card .metric-val { font-size: 1.8rem; font-weight: 700; line-height: 1.1; }
        .metric-card .metric-lbl { font-size: .82rem; opacity: .9; margin-bottom: 3px; }
        .bg-info-g    { background: linear-gradient(135deg,#2980b9,#1a6fa8); }
        .bg-success-g { background: linear-gradient(135deg,#1abc9c,#16a085); }
        .bg-warning-g { background: linear-gradient(135deg,#f6a623,#e2890a); }
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
                    <h3>Reporte de pedidos programados</h3>
                    <p class="text-muted">Consolidado de producción y lista de órdenes para el período seleccionado.</p>
                </div>

                <!-- Filtros -->
                <div class="row mt-2">
                    <div class="col-md-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="form-row align-items-end">

                                <div class="form-group col-md-3 col-sm-6">
                                    <label>Sucursal <small class="text-muted">(aplica en ambos tabs)</small></label>
                                    <select class="form-control basic" id="cmb_sucursal" <?= $enabled ?>>
                                        <option value="0">Todas las sucursales</option>
                                        <?php foreach ($clsucursales->lista() as $suc):
                                            $sel = $suc['cod_sucursal'] == $cod_sucursal ? 'selected' : '';
                                        ?>
                                            <option value="<?= $suc['cod_sucursal'] ?>" <?= $sel ?>>
                                                <?= htmlspecialchars($suc['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
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
                                    <button class="btn btn-primary btnReporte" style="margin-top:30px;" data-alias="<?= $alias ?>">
                                        Generar reporte
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métricas -->
                <div id="seccionMetricas" style="display:none;">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 layout-spacing">
                            <div class="metric-card bg-info-g">
                                <div class="metric-lbl">Pedidos programados</div>
                                <div class="metric-val" id="mPedidos">—</div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 layout-spacing">
                            <div class="metric-card bg-success-g">
                                <div class="metric-lbl">Unidades a producir</div>
                                <div class="metric-val" id="mUnidades">—</div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 layout-spacing">
                            <div class="metric-card bg-warning-g">
                                <div class="metric-lbl">Monto total</div>
                                <div class="metric-val" id="mMonto">—</div>
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
                                        <a class="nav-link active" data-toggle="tab" href="#tab-consolidado" role="tab">
                                            <i data-feather="package"></i> Consolidado de producción
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-ordenes" role="tab">
                                            <i data-feather="list"></i> Lista de órdenes
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <!-- TAB 1: Consolidado por hora -->
                                    <div class="tab-pane fade show active" id="tab-consolidado" role="tabpanel">
                                        <div id="consolidadoContent"></div>
                                        <div id="sinDatosConsolidado" class="text-center text-muted py-5" style="display:none;">
                                            No hay pedidos programados para este período
                                        </div>
                                    </div>

                                    <!-- TAB 2: Lista de órdenes -->
                                    <div class="tab-pane fade" id="tab-ordenes" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="table-ordenes" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center"># Orden</th>
                                                        <th>Cliente</th>
                                                        <th class="text-center">Hora entrega</th>
                                                        <th>Sucursal</th>
                                                        <th class="text-center">Estado</th>
                                                        <th class="text-right">Total</th>
                                                        <th class="text-center">Ver</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lstOrdenes"></tbody>
                                            </table>
                                        </div>
                                        <div id="sinDatosOrdenes" class="text-center text-muted py-4" style="display:none;">
                                            No hay órdenes en este período
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
    <script src="assets/js/pages/reporte_pedidos_programados.js?v=2"></script>
</body>
</html>
