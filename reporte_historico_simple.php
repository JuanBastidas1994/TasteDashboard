<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_reporte_historico_simple.php";

if (!isLogin()) {
    header("location:login.php");
}

$clsucursales = new cl_sucursales(NULL);
$clhistorico  = new cl_reporte_historico_simple();
$session      = getSession();
$cod_rol      = $session['cod_rol'];
$cod_sucursal = $cod_rol == 3 ? $session['cod_sucursal'] : 0;
$enabled      = $cod_rol == 3 ? "disabled" : "";

$anios       = $clhistorico->getAnios($session['cod_empresa']);
$anio_actual = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .sinDatos { display: none; text-align: center; color: #888ea8; padding: 40px 0; }
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
                    <h3>Reporte Histórico Simple</h3>
                    <p class="text-muted" style="font-size:13px;">Tabla con la información colapsada (mensual o anual) por sucursal.</p>
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
                    <div class="row">
                        <div class="col-md-12 layout-spacing">
                            <div class="widget-content widget-content-area br-6">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tablaHistorico">
                                        <thead>
                                            <tr>
                                                <th>Período</th>
                                                <th>Sucursal</th>
                                                <th>Total Ventas</th>
                                                <th>Total Órdenes</th>
                                                <th>Pickup</th>
                                                <th>Delivery</th>
                                                <th>Mesa</th>
                                                <th>Clientes Nuevos</th>
                                                <th>Clientes Recurrentes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyHistorico"></tbody>
                                    </table>
                                    <div class="sinDatos" id="sinDatos">No hay datos para el período seleccionado</div>
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
    <script src="assets/js/pages/reporte_historico_simple.js?v=1"></script>
</body>
</html>
