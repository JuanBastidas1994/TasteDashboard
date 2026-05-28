<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}

$session      = getSession();
$clsucursales = new cl_sucursales(NULL);
$cod_rol      = $session["cod_rol"];
$cod_sucursal = 0;
$selectedAll  = "selected";
$enabled      = "";

if ($cod_rol == 3) {
    $enabled      = "disabled";
    $selectedAll  = "";
    $cod_sucursal = $session["cod_sucursal"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 22px 24px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card .stat-value { font-size: 2.4rem; font-weight: 700; line-height: 1.1; }
        .stat-card .stat-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
        .stat-card .stat-delta { font-size: 0.82rem; margin-top: 6px; }
        .stat-card svg { width: 48px; height: 48px; opacity: 0.7; }
        .bg-orange { background: linear-gradient(135deg, #f6a623, #e2890a); }
        .bg-golden { background: linear-gradient(135deg, #c8962a, #a07820); }
        .tip-box {
            background: #fff8e7;
            border-left: 4px solid #f6a623;
            border-radius: 6px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .tip-box svg { color: #f6a623; min-width: 22px; }
    </style>
</head>

<body>
    <?php echo top(); ?>
    <?php echo navbar(); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php echo sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="col-md-12" style="margin-top:25px;">
                    <h3>Carritos en Abandono</h3>
                    <p class="text-muted">Visualiza la cantidad de carritos abandonados y su <strong>monto</strong> estimado.</p>
                </div>

                <!-- Filtros -->
                <div class="row mt-3">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label>Sucursal</label>
                            <select class="form-control" id="cmb_sucursal" <?= $enabled ?>>
                                <option value="0" <?= $selectedAll ?>>Todas las sucursales</option>
                                <?php foreach ($clsucursales->all() as $suc): ?>
                                    <option value="<?= $suc['cod_sucursal'] ?>"
                                        <?= ($suc['cod_sucursal'] == $cod_sucursal) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($suc['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>Período</label>
                            <select class="form-control" id="cmb_periodo">
                                <option value="mes_actual">Mes Actual</option>
                                <option value="mes_anterior">Mes Anterior</option>
                                <option value="ultimos_3_meses">Últimos 3 meses</option>
                                <option value="ultimos_6_meses">Últimos 6 meses</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stat cards -->
                <div class="row mt-2" id="seccionStats" style="display:none;">
                    <div class="col-md-6 layout-spacing">
                        <div class="stat-card bg-orange">
                            <div>
                                <div class="stat-label">Carritos Abandonados</div>
                                <div class="stat-value" id="statCount">0</div>
                                <div class="stat-delta" id="statDeltaCount"></div>
                            </div>
                            <i data-feather="shopping-cart"></i>
                        </div>
                    </div>
                    <div class="col-md-6 layout-spacing">
                        <div class="stat-card bg-golden">
                            <div>
                                <div class="stat-label">Dinero abandonado</div>
                                <div class="stat-value" id="statAmount">$0</div>
                                <div class="stat-delta" id="statDeltaAmount"></div>
                            </div>
                            <i data-feather="dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row" id="seccionCharts" style="display:none;">
                    <div class="col-md-5 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <h5 class="mb-1">Distribución de Carritos</h5>
                            <div id="chartDonut"></div>
                        </div>
                    </div>
                    <div class="col-md-7 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <h5 class="mb-1">Valor de Carritos Abandonados</h5>
                            <div id="chartLine"></div>
                        </div>
                    </div>
                </div>

                <!-- Tip -->
                <div class="row" id="seccionTip" style="display:none;">
                    <div class="col-md-12 layout-spacing">
                        <div class="tip-box">
                            <i data-feather="info"></i>
                            <span id="txtTip"></span>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/pages/reporte_carritos_abandonados.js?v=1"></script>
</body>

</html>
