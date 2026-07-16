<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}

$session      = getSession();
$clsucursales = new cl_sucursales(NULL);
$cod_rol      = $session["cod_rol"];
$enabled      = $cod_rol == 3 ? "disabled" : "";
$cod_sucursal = $cod_rol == 3 ? $session["cod_sucursal"] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .metric-card { border-radius:8px; padding:18px 20px; color:#fff; }
        .metric-card .metric-val { font-size:2rem; font-weight:700; line-height:1.1; }
        .metric-card .metric-lbl { font-size:.82rem; opacity:.9; margin-bottom:3px; }
        .bg-danger-g  { background:linear-gradient(135deg,#e55353,#c0392b); }
        .bg-success-g { background:linear-gradient(135deg,#1abc9c,#16a085); }
        .bg-warning-g { background:linear-gradient(135deg,#f6a623,#e2890a); }
        .bg-info-g    { background:linear-gradient(135deg,#2980b9,#1a6fa8); }
        .bg-dark-g    { background:linear-gradient(135deg,#555,#333); }
    </style>
</head>

<body>
    <?php top(); ?>
    <?php navbar(); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="col-md-12" style="margin-top:25px;">
                    <h3>Carritos Abandonados</h3>
                    <p class="text-muted">Analiza el comportamiento de los carritos: activos, abandonados, convertidos y expirados.</p>
                </div>

                <!-- Filtros -->
                <div class="row mt-3">
                    <div class="col-md-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="form-row align-items-end">

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="f_ini">
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Fecha fin</label>
                                    <input type="date" class="form-control" id="f_fin">
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Estado</label>
                                    <select class="form-control" id="cmb_estado">
                                        <option value="">Todos</option>
                                        <option value="ACTIVO">ACTIVO</option>
                                        <option value="ABANDONADO">ABANDONADO</option>
                                        <option value="CONVERTIDO">CONVERTIDO</option>
                                        <option value="EXPIRADO">EXPIRADO</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Origen</label>
                                    <select class="form-control" id="cmb_origen">
                                        <option value="">Todos</option>
                                        <option value="WEB">WEB</option>
                                        <option value="APP">APP</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Recovery</label>
                                    <select class="form-control" id="cmb_recovery">
                                        <option value="">Todos</option>
                                        <option value="EMAIL">EMAIL</option>
                                        <option value="PUSH">PUSH</option>
                                        <option value="WHATSAPP">WHATSAPP</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Sucursal</label>
                                    <select class="form-control" id="cmb_sucursal" <?= $enabled ?>>
                                        <option value="0">Todas</option>
                                        <?php foreach ($clsucursales->all() as $suc): ?>
                                            <option value="<?= $suc['cod_sucursal'] ?>"
                                                <?= ($suc['cod_sucursal'] == $cod_sucursal) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($suc['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <label>Usuario</label>
                                    <select class="form-control" id="cmb_tipo_usuario">
                                        <option value="0">Todos</option>
                                        <option value="1">Identificado</option>
                                        <option value="2">Anónimo</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-4">
                                    <button class="btn btn-primary w-100" id="btnGenerar">
                                        <i data-feather="bar-chart-2"></i> Generar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="seccionResultados" style="display:none;">

                    <!-- Métricas -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6 layout-spacing">
                            <div class="metric-card bg-info-g">
                                <div class="metric-lbl">Total Carritos</div>
                                <div class="metric-val" id="mTotal">—</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 layout-spacing">
                            <div class="metric-card bg-danger-g">
                                <div class="metric-lbl">Abandonados</div>
                                <div class="metric-val" id="mAbandonados">—</div>
                                <div style="font-size:.82rem;opacity:.85;">Tasa: <span id="mTasaAbandono">—</span></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 layout-spacing">
                            <div class="metric-card bg-success-g">
                                <div class="metric-lbl">Convertidos</div>
                                <div class="metric-val" id="mConvertidos">—</div>
                                <div style="font-size:.82rem;opacity:.85;">Tasa: <span id="mTasaConversion">—</span></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 layout-spacing">
                            <div class="metric-card bg-warning-g">
                                <div class="metric-lbl">Monto Abandonado</div>
                                <div class="metric-val" id="mMontoAbandonado">—</div>
                                <div style="font-size:.82rem;opacity:.85;">Recuperado: <span id="mMontoConvertido">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Recovery por canal -->
                    <div class="row">
                        <div class="col-md-12 layout-spacing">
                            <div class="widget-content widget-content-area br-6 py-3">
                                <span class="font-weight-bold mr-3">Recovery por canal:</span>
                                <span id="mRecovery"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="row">
                        <div class="col-md-12 layout-spacing">
                            <div class="widget-content widget-content-area br-6">
                                <div class="table-responsive">
                                    <table id="tblCarritos" class="table style-3 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Origen</th>
                                                <th>Estado</th>
                                                <th class="text-right">Total Est.</th>
                                                <th class="text-center">Productos</th>
                                                <th>Abandonado</th>
                                                <th>Convertido</th>
                                                <th>Recuperado</th>
                                                <th>Actualizado</th>
                                                <th>Recovery</th>
                                                <th>Orden</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/reporte_carritos_abandonados.js?v=2"></script>
</body>

</html>
