<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
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
    </style>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css" />
    <!-- END PAGE LEVEL CUSTOM STYLES -->
</head>

<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR CUPON</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">
                        <input type="text" placeholder="" name="cod_cupon" id="cod_cupon" class="form-control" required="required" autocomplete="off" value="0" />
                        <div class="x_content">
                            <div class="form-group">
                                <div class="col-md-4 col-xs-12">
                                    <div class="upload mt-1 pr-md-1">
                                        <input type="file" name="img_profile" id="input-file-max-fs" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="1M" />
                                        <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                    <label>Título
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Nombre del cupón"></span>
                                    </label>
                                    <input type="text" name="txt_codigo" id="txt_codigo" class="form-control maxlength" required="required" autocomplete="off" maxlength="25" placeholder="Ej. Cupón de cumpleaños" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                    <label>Tipo <span class="asterisco">*</span></label>
                                    <select name="cmbTipo" id="cmbTipo" class="form-control" required="required" autocomplete="off">
                                        <option value="CUMPLEANIOS">Cumpleaños</option>
                                        <option value="REGISTRO">Registro</option>
                                        <option value="NIVEL2">Nivel 2</option>
                                        <option value="NIVEL3">Nivel 3</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                    <label>Cant. días disponibles <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="7" name="txt_cantidad" id="txt_cantidad" class="form-control maxlength" required="required" autocomplete="off" maxlength="3" />
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                    <label>Estado <span class="asterisco">*</span></label>
                                    <select name="cmbEstado" id="cmbEstado" class="form-control" required>
                                        <option value="A">Activo</option>
                                        <option value="I">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                    <label>Descripción <span class="asterisco">*</span></label>
                                    <textarea class="form-control" name="txtDescripcion" id="txtDescripcion" ></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>

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

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Cupones a Clientes</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" id="btnOpenModal">Nuevo cupón</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>

                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Imagen</th>
                                            <th>Cant. días disponibles</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

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
    <script src="assets/js/pages/cupones.js" type="text/javascript"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>