<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if (!isLogin()) {
    header("location:login.php");
    exit;
}

$Clempresas = new cl_empresas(NULL);
$session = getSession();
$comercios = $Clempresas->getComercios($session['cod_empresa']);
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <?php css_mandatory(); ?>
    <style>
        .indicador { border: 1px solid #e0e6ed; border-radius: 8px; padding: 18px 20px; height: 100%; }
        .indicador .ind-titulo { color: #888ea8; font-size: 13px; margin-bottom: 6px; }
        .indicador .ind-valor { font-size: 28px; font-weight: bold; color: #3b3f5c; }
        .indicador .ind-nota { color: #888ea8; font-size: 12px; }
        .indicador.taste { border-color: #1b55e2; background: #f1f5ff; }
        .indicador.taste .ind-valor { color: #1b55e2; }
        #tablaCobro tfoot th { text-align: right; border-top: none; }
        #tablaCobro tfoot tr.total-final th { font-size: 18px; border-top: 2px solid #3b3f5c; }
    </style>
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

                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-12">
                                <h4>Cobro de envíos a comercios</h4>
                                <hr />
                            </div>

                            <!-- FILTROS -->
                            <div class="row">
                                <div class="col-md-4 col-12 mb-md-0 mb-4">
                                    <label>Empresa <span class="asterisco">*</span></label>
                                    <select id="cmbComercio" class="form-control basic">
                                        <option value="">Seleccione una empresa</option>
                                        <?php foreach ($comercios as $c): ?>
                                            <option value="<?= $c['cod_empresa'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Fecha inicio <span class="asterisco">*</span></label>
                                    <input type="date" id="txtFechaInicio" class="form-control" value="<?= date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Fecha fin <span class="asterisco">*</span></label>
                                    <input type="date" id="txtFechaFin" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4 d-flex align-items-end">
                                    <button class="btn btn-primary btn-block" id="btnGenerar" type="button">Generar</button>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4 d-flex align-items-end">
                                    <button class="btn btn-success btn-block" id="btnExcel" type="button" disabled>Exportar Excel</button>
                                </div>
                            </div>

                            <div id="resultado" style="display: none;">
                                <!-- INDICADORES -->
                                <div class="row mt-4">
                                    <div class="col-md-4 col-12 mb-3">
                                        <div class="indicador">
                                            <div class="ind-titulo">Órdenes de esta empresa</div>
                                            <div class="ind-valor" id="indOrdenesEmpresa">0</div>
                                            <div class="ind-nota" id="indNombreEmpresa"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <div class="indicador">
                                            <div class="ind-titulo">Órdenes de todas las empresas</div>
                                            <div class="ind-valor" id="indOrdenesTodas">0</div>
                                            <div class="ind-nota">Todos los comercios de tu flota</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <div class="indicador taste">
                                            <div class="ind-titulo">Total para Taste</div>
                                            <div class="ind-valor" id="indTotalTaste">$0.00</div>
                                            <div class="ind-nota" id="indFormulaTaste"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DETALLE -->
                                <div class="table-responsive mb-4">
                                    <table id="tablaCobro" class="table style-3 table-hover" style="margin-top: 10px !important;">
                                        <thead>
                                            <tr>
                                                <th>Orden</th>
                                                <th>Fecha</th>
                                                <th>Motorizado</th>
                                                <th>Estado</th>
                                                <th class="text-right">Subtotal</th>
                                                <th class="text-right">IVA</th>
                                                <th class="text-right">Precio envío</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cobroBody">
                                        </tbody>
                                        <tfoot id="cobroFooter">
                                        </tfoot>
                                    </table>
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
    <script src="assets/js/pages/flota_reporte_cobro.js?v=1" type="text/javascript"></script>
</body>
</html>
