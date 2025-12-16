<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
$session = getSession();

$ClEmpresas = new cl_empresas();
$empresa = $ClEmpresas->get($session["cod_empresa"]);
if ($empresa) {
    $apikey = $empresa["api_key"];
}

if (!isLogin()) {
    header("location:login.php");
}

/* if(!userGrant()){
    header("location:index.php");
} */

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="gb18030">
    <?php css_mandatory(); ?>
    <style type="text/css">
        .dropify-wrapper {
            display: block;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            width: 100%;
            max-width: 100%;
            height: 110px !important;
            padding: 5px 10px;
            font-size: 14px;
            line-height: 22px;
            color: #777;
            background-color: #fff;
            background-image: none;
            text-align: center;
            border: 0 !important;
            -webkit-transition: border-color .15s linear;
            transition: border-color .15s linear;
        }

        .respGalery>div {
            margin-top: 15px;
        }

        .croppie-container .cr-boundary {
            background-image: url(assets/img/transparent.jpg);
            background-position: center;
            background-size: cover;
        }
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

        <!-- Modal -->
        <div class="modal fade" id="modalPreorden" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleModal">
                            Preorden 
                            <a href="javascript:void(0);" class="bs-tooltip copyPreorder copy" data-clipboard-action="copy" data-clipboard-text="js" data-toggle="tooltip" data-placement="top" data-original-title="Copiar" id="copyPreorder">
                                <i data-feather="copy"></i>
                            </a>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea id="txtJson" style="display: none;"></textarea>
                        <pre id="json-display"></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success">Copiar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Pre órdenes</h4>
                                    <input type="hidden" id="apiEmpresa" value="<?= $apikey ?>">
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>

                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID preorden</th>
                                            <th>Nombre</th>
                                            <th>N. orden</th>
                                            <th>Monto ($)</th>
                                            <th>Motivo fallo</th>
                                            <th>PaymentID</th>
                                            <th>Fecha creación</th>
                                            <th>Fecha actualización</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- LISTA DE FACTURAS -->
                                    </tbody>
                                </table>
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

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <script src="assets/js/clipboard/clipboard.min.js"></script>

    <!-- JSON VIEWER -->
    <script src="./assets/js/libs/json-viewer/dist/jquery.json-editor.min.js"></script>

    <script src="./assets/js/pages/preordenes.js?v=0"></script>
</body>

</html>