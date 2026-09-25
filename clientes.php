<?php
require_once "funciones.php";
require_once "clases/cl_clientes.php";
require_once "clases/cl_usuarios.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clclientes = new cl_clientes(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
</head>
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
                            <div class="col-xl-12 col-md-12 col-sm-8 col-8">
                                <h4>Clientes</h4>
                            </div>

                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <hr />
                            </div>
                        </div>

                        <div class="table-responsive mb-4 mt-4">
                            <table id="table-clientes" class="table style-3 table-hover" data-order='[[ 5, "desc"]]'>
                                <thead>
                                    <tr>
                                        <th>Nombres</th>
                                        <th>N&#186; de Identificaci&oacute;n</th>
                                        <th>Correo</th>
                                        <th>Tel&eacute;fono</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Fecha Creación</th>
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



<!-- Modal: notificar a un cliente (push) -->
<div class="modal fade" id="notificarUsuarioModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notificar a <span id="notificarUsuarioNombre"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <label>Mensaje</label>
                <textarea id="notificarUsuarioMensaje" class="form-control" rows="3" maxlength="200" placeholder="Ej: Tenemos una sorpresa para ti en tu próximo pedido"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEnviarNotificacionUsuario">Enviar</button>
            </div>
        </div>
    </div>
</div>

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<?php js_mandatory(); ?>
<script src="assets/js/pages/cliente_detalle.js" type="text/javascript"></script>
<script src="assets/js/pages/notificar_usuario.js?v=1" type="text/javascript"></script>
<script>
    $(function() {
        loadDatatable();
    });

    function loadDatatable() {
        //config = DatatableConfig();
        $('#table-clientes').dataTable({
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                        extend: 'excel',
                        className: 'btn',
                        action: function(e, dt, node, config) {
                            // Redirige al PHP que genera el Excel real
                            window.location.href = 'excel_export/clientes.php';
                        }
                    },
                    // { extend: 'copy', className: 'btn' },
                    // { extend: 'csv', className: 'btn' },
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
            //oLanguage: config.oLanguage,
            ajax: {
                url: './controllers/controlador_clientes.php?metodo=datatable',
                type: 'GET',
                error: function(e) {
                    console.log(e);
                },
                complete: function() {
                    feather.replace();
                }
            }
        });
    }
    /*
    $('#style-3').DataTable( {
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
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
        "pageLength": 7 
    } );*/
</script>
<!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>