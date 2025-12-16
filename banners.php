<?php
require_once "funciones.php";
require_once "clases/cl_banners.php";

if (!isLogin()) {
    header("location:login.php");
}

$clBanner = new cl_banners(NULL);
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
            height: 200px !important;
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
</head>

<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR BANNER (1920x700 px)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0" />
                    <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">
                        <div class="x_content">
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="upload mt-1 pr-md-1">
                                        <input type="file" name="img_profile" id="input-file-max-fs" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="1M" />
                                        <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                
                                <div class="col-md-9 col-sm-9 col-xs-12 d-none" style="margin-bottom:10px;">
                                    <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Escribe el titulo" name="txt_titulo" id="txt_titulo" class="form-control" autocomplete="off" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-9 col-sm-9 col-xs-12 d-none" style="margin-bottom:10px;">
                                    <label>Subt&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Escribe el subtitulo" name="txt_subtitulo" id="txt_subtitulo" class="form-control" autocomplete="off" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4 col-sm-4 col-xs-12 d-none" style="margin-bottom:10px;">
                                    <label>Texto Grande (60Pts) <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Ej. 15% Descuento" name="txt_descuento" id="txt_descuento" class="form-control" autocomplete="off" />
                                </div>

                                <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                    <label>Texto del Bot&oacute;n <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Boton" name="txt_text_boton" id="txt_text_boton" class="form-control" autocomplete="off" />
                                </div>

                                <div class="col-md-5 col-sm-5 col-xs-12" style="margin-bottom:10px;">
                                    <label>URL del Bot&oacute;n <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="http://" name="txt_url" id="txt_url" class="form-control" autocomplete="off" />
                                </div>



                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="offset-lg-7 col-lg-5 col-12">
                                        <label>Estado</label>
                                        <div>
                                            <label class="switch s-icons s-outline  s-outline-success mr-2">
                                                <input type="checkbox" name="ckEstado" id="ckEstado" checked="checked">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
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
                                    <h4>Banners</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <button class="btn btn-primary" data-toggle="modal" id="btnOpenModal">Nuevo Banner</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>

                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3 table-hover dt-responsive">
                                    <!--  <table class="table style-3 table-hover dt-responsive">-->
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lstBanners" class="connectedSortable">
                                        <?php
                                        $resp = $clBanner->lista();
                                        foreach ($resp as $banner) {
                                            $imagen = $files . $banner['image_min'];
                                            $badge = 'primary';
                                            if ($banner['estado'] == 'I')
                                                $badge = 'danger';
                                            echo '<tr id="' . $banner['cod_banner'] . '"  data-codigo="' . $banner['cod_banner'] . '">
                                                    <td><img src="' . $imagen . '" class="profile-img" alt="Imagen" style="width: 250px; height: auto;"></td>
                                                    <td class="text-center"><span class="shadow-none badge badge-' . $badge . '">' . getEstado($banner['estado']) . '</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="' . $banner['cod_banner'] . '" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>

                                                            <li><a href="javascript:void(0);" data-value="' . $banner['cod_banner'] . '" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                                        </ul>
                                                    </td>
                                                </tr>';
                                        }
                                        ?>
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
    <script src="assets/js/pages/banners.js?v=0" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $('#style-3').DataTable({
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [{
                        extend: 'copy',
                        className: 'btn'
                    },
                    {
                        extend: 'csv',
                        className: 'btn'
                    },
                    {
                        extend: 'excel',
                        className: 'btn'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn'
                    },
                    {
                        extend: 'print',
                        className: 'btn'
                    }
                ]
            },
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                "sInfoEmpty": "Mostrando pag. 1",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
                "sLengthMenu": "Resultados :  _MENU_",
                "sEmptyTable": "No se encontraron resultados",
                "sZeroRecords": "No se encontraron resultados",
                "buttons": {
                    "copy": "Copiar",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir",
                    "create": "Crear",
                    "edit": "Editar",
                    "remove": "Remover",
                    "upload": "Subir"
                }
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7,
            "order": [
                [3, "asc"]
            ],
            responsive: true
        });
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>