<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
//echo $session['cod_rol'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>&Oacute;rdenes</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right" style="display:none;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#crearCliente">Nueva orden</button>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>N.</th>
                                                <th>Cliente</th>
                                                <th>Sucursal</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Tipo</th>
                                                <th>Direccion</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $Clordenes->lista();
                                            foreach ($resp as $orden) {
                                                $badge='primary';
                                                if($orden['estado'] == 'I')
                                                    $badge='danger';
                                                else if($orden['estado'] == "ENTREGADA")
                                                    $badge='success';
                                                else if($orden['estado'] == "ASIGNADA")
                                                    $badge='warning';
                                                else if($orden['estado'] == "CANCELADA")
                                                    $badge='danger';

                                                $envio = "Delivery";
                                                if($orden['is_envio'] == 0){
                                                    $envio = "Pickup";
                                                }    

                                                echo '<tr>
                                                    <td>'.$orden['cod_orden'].'</td>
                                                    <td>'.$orden['nombre'].' '.$orden['apellido'].'</td>
                                                    <td>'.$orden['sucursal'].'</td>
                                                    <td>'.$orden['fecha'].'</td>
                                                    <td>$'.$orden['total'].'</td>
                                                    <td>'.$envio.'</td>
                                                    <td>'.$orden['referencia'].'</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($orden['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a target="_blank" href="simulador_orden_firebase.php?id='.$orden['cod_orden'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
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

    <script>
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
            "pageLength": 7,
            "order": [[ 0, "desc" ]]
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>