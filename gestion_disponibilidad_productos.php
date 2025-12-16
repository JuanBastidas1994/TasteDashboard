<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
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

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                
                <div class="col-md-8" >
                    <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </a>
                    <h3 id="titulo">Disponibilidad</h3>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="cmbSucursal">
                    <?php
                    if($session['cod_rol']==2){
                        $sucursales = $Clsucursales->lista();
                        foreach ($sucursales as $suc) {
                            echo '<option value="'.$suc['cod_sucursal'].'">'.$suc['nombre'].'</option>';
                        }
                    }else{
                        $sucursales = $Clsucursales->getInfo($session['cod_sucursal']);
                        if($sucursales){
                            echo '<option value="'.$sucursales['cod_sucursal'].'">'.$sucursales['nombre'].'</option>';
                        }
                    }
                    ?>
                    </select>
                </div>

                <div class="row layout-top-spacing" style="display: block;">
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Disponibles</h4>
                                </div>
                            </div>
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3 cl-disponibles">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstDisponibles">
                                            
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>

                    <!-- AGOTADOS O PENDIENTES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Agotados</h4>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Tiempo</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstAgotados">
                                            
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
    <script src="assets/js/pages/gestion_disponibilidad_productos.js?v=0" type="text/javascript"></script>
    <script>
                     /*   var myTable = $('.cl-disponibles').DataTable( {
                        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                        buttons: {
                            buttons: []
                        },
                        "oLanguage": {
                            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                            "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                            "sInfoEmpty": "",
                            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                            "sSearchPlaceholder": "Buscar...",
                           "sLengthMenu": "Resultados :  _MENU_",
                           "sEmptyTable": "No se encontraron resultados",
                           "sZeroRecords": "No se encontraron resultados",
                           "buttons": {}
                        },
                        "stripeClasses": [],
                        "lengthMenu": [7, 10, 20, 50],
                        "pageLength": 10,
                        "bPaginate": false, //Ocultar paginación
                    } );*/
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>