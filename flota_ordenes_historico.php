<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_usuarios.php";

if (!isLogin()) {
    header("location:login.php");
    exit;
}

$Clempresas = new cl_empresas(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$empresa = $Clempresas->get($session['cod_empresa']);
if (!$empresa) {
    header("location:login.php");
    exit;
}

$apikey = $empresa['api_key'] ?? '';
$comercios = $Clempresas->getComercios($session['cod_empresa']);
$motorizados = $Clusuarios->listaDeMotorizados();
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
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
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Histórico de pedidos</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>

                            <div class="row">
                                <input type="hidden" id="apikey_flota" value="<?= htmlspecialchars($apikey) ?>">
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Empresa</label>
                                    <select id="cmbComercio" class="form-control basic">
                                        <option value="">Todas</option>
                                        <?php foreach ($comercios as $c): ?>
                                            <option value="<?= $c['cod_empresa'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Estado</label>
                                    <select id="cmbEstado" class="form-control basic">
                                        <option value="">Todos</option>
                                        <option value="ASIGNADA">Asignada</option>
                                        <option value="ENVIANDO">Enviando</option>
                                        <option value="ENTREGADA">Entregada</option>
                                        <option value="NO_ENTREGADA">No entregada</option>
                                        <option value="ANULADA">Anulada</option>
                                        <option value="CANCELADA">Cancelada</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Motorizado</label>
                                    <select id="cmbMotorizado" class="form-control basic">
                                        <option value="">Todos</option>
                                        <?php foreach ($motorizados as $m): ?>
                                            <option value="<?= $m['cod_usuario'] ?>"><?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Fecha inicio</label>
                                    <input type="date" id="txtFechaInicio" class="form-control">
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                    <label>Fecha fin</label>
                                    <input type="date" id="txtFechaFin" class="form-control">
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-4 d-flex align-items-end">
                                    <button class="btn btn-primary btn-block" id="btnLimpiarFiltros" type="button">Limpiar</button>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table id="table-historico" class="table style-3 table-hover" style="margin-top: 10px !important;">
                                    <thead>
                                        <tr>
                                            <th>N.</th>
                                            <th>Empresa</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Motorizado</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historicoBody">
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span id="historicoResumen" class="text-muted"></span>
                                    <div>
                                        <button class="btn btn-outline-primary" id="btnPagAnterior" type="button">Anterior</button>
                                        <button class="btn btn-outline-primary" id="btnPagSiguiente" type="button">Siguiente</button>
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
    <script src="assets/js/pages/flota_ordenes_historico.js?v=1" type="text/javascript"></script>
</body>
</html>
