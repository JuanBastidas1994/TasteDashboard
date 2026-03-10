<?php
require_once "funciones.php";

require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);

$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
$alias = $session['alias'];

$cod_empresa = $session['cod_empresa'];

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
            <div class="layout-px-spacing bg-white">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div>
                        <span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i>
                            <span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span>
                        </span>
                    </div>
                    <h3 id="titulo">Reporte productos más vendidos</h3>
                </div>

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12">
                        <div class="">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group col-md-3 col-sm-4 col-xs-12">
                                                <label>Sucursales <span class="asterisco">*</span></label>
                                                <select class="form-control  basic" id="cmb_sucursal">
                                                    <option value="0">Todas las sucursales</option>
                                                    <?php
                                                    $resp = $clsucursales->lista();
                                                    foreach ($resp as $sucursales) {
                                                        $id = $sucursales['cod_sucursal'];
                                                        $nombre = $sucursales['nombre'];
                                                        echo "<option value='{$id}'>{$nombre}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-2 col-xs-12 input-group">
                                                <label>Fecha inicio</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-2 col-xs-12 input-group" style="margin-bottom:10px;">
                                                <label>Fecha fin</label>
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_fin" id="fecha_fin">
                                                </div>
                                            </div>

                                            <div class="col-xl-2 col-md-2 col-sm-3 col-12" style="text-align: right;">
                                                <button class="btn btn-primary btnGenerar" style="margin-top: 30px;" data-empresa="<?= $cod_empresa ?>">Generar reporte</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="">
                            <div>
                                <h4>Productos</h4>
                            </div>
                            <table id="table-productos" class="table style-3">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cant. vendida</th>
                                    </tr>
                                </thead>
                                <tbody id="lstProductos">
                                </tbody>
                            </table>
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
    <script>
        $("body").on('click', ".btnGenerar", function(event) {
            event.preventDefault();
            var cod_empresa = $(this).data("empresa");
            var sucursal = $("#cmb_sucursal").val();
            var f_inicio = $("#fecha_inicio").val();
            var f_fin = $("#fecha_fin").val();

            if (f_inicio !== "" && f_fin !== "") {
                if (f_fin < f_inicio) {
                    messageDone("Las fecha fin no puede ser menor que la de incio,intentelo nuevamente ", 'error');
                    return;
                }
            } else {
                messageDone("Debe completar todos los campos, intentelo nuevamente", 'error');
                return;
            }

            var parametros = {
                "cod_empresa": cod_empresa,
                "cod_sucursal": sucursal,
                "fechaInicio": f_inicio,
                "fechaFin": f_fin
            }

            $.ajax({
                beforeSend: function() {
                    OpenLoad("Cargando datos, por favor espere...");
                },
                url: 'controllers/controlador_reporte_productos.php?metodo=getProductosMasVendidos',
                type: 'POST',
                data: parametros,
                success: function(response) {
                    if (response['success'] == 1) {
                        $('#table-productos').DataTable().destroy();
                        $("#lstProductos").html(response['tabla']);

                        // initDatatable($('#table-productos'));
                        feather.replace();
                    } else {
                        notify(response['mensaje'], "info", 2);
                        $("#lstProductos").html("");
                    }
                },
                error: function(data) {
                    console.log(data);
                },
                complete: function(resp) {
                    CloseLoad();
                }
            });
        });

        /*Combos fecha*/
        var f4 = flatpickr(document.getElementById('fecha_inicio'), {
            enableTime: false,
            dateFormat: "Y-m-d"
        });

        var f4 = flatpickr(document.getElementById('fecha_fin'), {});

        /*Combos sucursales*/
        var ss = $(".basic").select2({
            tags: true,
            enableTime: false,
            dateFormat: "Y-m-d"
        });



        function initDatatable($table) {
            $table.DataTable({
                dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                buttons: {
                    buttons: [{
                            extend: 'excel',
                            className: 'btn',
                            footer: true
                        },
                        {
                            extend: 'print',
                            className: 'btn',
                            footer: true,
                            exportOptions: {
                                columns: ':not(:last-child)' // Oculta la última columna
                            }
                        },
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
                        "excel": "Excel",
                        "print": "Imprimir",
                        "create": "Crear",
                        "edit": "Editar",
                        "remove": "Remover",
                        "upload": "Subir"
                    }
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 20,
                "order": [
                    [0, "desc"]
                ]
            });
        }
    </script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

</body>

</html>