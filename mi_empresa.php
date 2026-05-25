<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$session    = getSession();
$files      = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$empresa = $Clempresas->get($session['cod_empresa']);
if (!$empresa) {
    header("location:index.php");
    exit;
}

$cod_empresa        = $empresa['cod_empresa'];
$nombre             = $empresa['nombre'];
$representante      = $empresa['representante_nombre'];
$impuesto           = $empresa['impuesto'];
$logo               = $files . $empresa['logo'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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

                <div class="col-md-12" style="margin-top:25px;">
                    <h3>Mi Empresa</h3>
                </div>

                <div class="row layout-top-spacing">

                    <!-- Datos de la empresa -->
                    <div class="col-xl-5 col-lg-6 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="text-center mb-4">
                                <img src="<?= $logo ?>" alt="<?= htmlspecialchars($nombre) ?>"
                                     style="max-height:100px; max-width:200px; object-fit:contain;">
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong>Nombre</strong></td>
                                        <td><?= htmlspecialchars($nombre) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Representante</strong></td>
                                        <td><?= htmlspecialchars($representante) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Impuesto actual</strong></td>
                                        <td>
                                            <span class="shadow-none badge badge-primary" style="font-size:1em; padding:5px 10px;">
                                                <?= $impuesto ?>%
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Editar impuesto -->
                    <div class="col-xl-5 col-lg-6 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <h4 class="mb-1">Editar Impuesto</h4>
                            <p class="text-muted mb-4" style="font-size:13px;">
                                Al actualizar el impuesto puedes elegir si mantener el precio de venta (PVP)
                                o mantener el precio base sin impuesto.
                            </p>

                            <input type="hidden" id="cod_empresa" value="<?= $cod_empresa ?>">

                            <div class="form-group">
                                <label>Porcentaje de impuesto <span class="asterisco">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="txt_impuesto" class="form-control"
                                           value="<?= $impuesto ?>" min="0" max="100" step="1">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Acción sobre los precios existentes <span class="asterisco">*</span></label>
                                <select class="form-control" id="cmb_tipo">
                                    <option value="mantener_precioNoTax">Mantener precio base (sin impuesto)</option>
                                    <option value="mantener_pvp">Mantener PVP (precio de venta al público)</option>
                                </select>
                            </div>

                            <div class="text-right mt-3">
                                <button class="btn btn-primary" id="btnActualizarImpuesto">
                                    <i data-feather="save"></i> Actualizar impuesto
                                </button>
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
    <script src="assets/js/pages/mi_empresa.js?v=1" type="text/javascript"></script>

</body>

</html>
