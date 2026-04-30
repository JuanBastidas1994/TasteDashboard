<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
$session = getSession();

if (!isLogin()) {
    header("location:login.php");
}

$ClEmpresas = new cl_empresas();
$empresas   = $ClEmpresas->lista();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="gb18030">
    <?php css_mandatory(); ?>
</head>

<body>
    <?php echo top() ?>
    <?php echo navbar(); ?>

    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php echo sidebar(); ?>

        <!-- Modal Ver JSON -->
        <div class="modal fade" id="modalPreorden" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Preorden
                            <a href="javascript:void(0);" class="bs-tooltip copyPreorder copy"
                               data-clipboard-action="copy" data-clipboard-text=""
                               data-toggle="tooltip" data-placement="top" data-original-title="Copiar"
                               id="copyPreorder">
                                <i data-feather="copy"></i>
                            </a>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea id="txtJson" style="display:none;"></textarea>
                        <pre id="json-display"></pre>
                    </div>
                </div>
            </div>
        </div>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">

                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Pre órdenes — Todas las empresas</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>

                            <!-- Filtros -->
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12 mb-3">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <label>Empresa</label>
                                        <select id="filtroEmpresa" class="form-control">
                                            <option value="">Todas las empresas</option>
                                            <?php if ($empresas): foreach ($empresas as $emp): ?>
                                            <option value="<?= $emp['cod_empresa'] ?>">
                                                <?= htmlspecialchars($emp['nombre']) ?>
                                            </option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <label>Estado</label>
                                        <select id="filtroEstado" class="form-control">
                                            <option value="">Todos los estados</option>
                                            <option value="CREANDO_ORDEN">CREANDO_ORDEN</option>
                                            <option value="VALIDADA">VALIDADA</option>
                                            <option value="CERRADA">CERRADA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Preorden</th>
                                            <th>Empresa</th>
                                            <th>Nombre</th>
                                            <th>N. Orden</th>
                                            <th>Monto ($)</th>
                                            <th>Motivo fallo</th>
                                            <th>PaymentID</th>
                                            <th>Fecha creación</th>
                                            <th>Fecha actualización</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                            <th style="display:none;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>

    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="./assets/js/libs/json-viewer/dist/jquery.json-editor.min.js"></script>

    <script src="./assets/js/pages/preordenes_full.js?v=1"></script>
</body>

</html>
