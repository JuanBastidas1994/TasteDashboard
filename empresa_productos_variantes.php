<?php
    require_once "funciones.php";
    require_once "clases/cl_empresas.php";
    require_once "clases/cl_sucursales.php";
    require_once "clases/cl_productos.php";

    if(!isLogin()){
        header("location:login.php");
    }

    $Clproductos = new cl_productos(NULL);
    $Clsucursales = new cl_sucursales(NULL);
    $Clemrpesas = new cl_empresas(NULL);
    $session = getSession();
    // if(!userGrant()){
    //     header("location:index.php");
    // }

    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    if(isset($_GET['id'])){
        $alias = $_GET['id'];
        $empresa = $Clemrpesas->getByAlias($alias);
        if($empresa){
            $cod_empresa = $empresa['cod_empresa'];
            $sucursales = $Clsucursales->listaByEmpresa($cod_empresa);
            if($sucursales){
                $optionSucursales = '<option value="0">Seleccione</option>';
                foreach ($sucursales as $sucursal) {
                    $optionSucursales.='<option value="'.$sucursal['cod_sucursal'].'">'.$sucursal['nombre'].'</option>';
                }
            }
            else{
                $optionSucursales = '<option value="0">No existen sucursales</option>';
            }
        }
        else{
            header("location:index.php");
        }
    }
    else{
        header("location:index.php");
    }
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
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                        <h4>Sucursales</h4>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <div class="col-md-4 col-xs-12">
                                            <select class="form-control" name="cmbSucursales" id="cmbSucursales" data-empresa="<?=$cod_empresa?>" data-alias="<?=$alias?>">
                                                <?= $optionSucursales?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <hr/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                                    <div class="widget-content widget-content-area br-6">
                                        <ul class="nav nav-tabs  mb-3 mt-3" id="lineTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="asignados-tab" data-toggle="tab" href="#asignados" role="tab" aria-controls="home" aria-selected="true"><i data-feather="menu"></i> Asignados: <span id="cantAsignados">0</span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="noasignados-tab" data-toggle="tab" href="#noasignados" role="tab" aria-controls="home" aria-selected="true"><i data-feather="menu"></i> No asignados: <span id="cantNoAsignados">0</span></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="simpletabContent">
                                            <!--CONTENIDO ASIGNADOS-->
                                            <div class="tab-pane fade show active" id="asignados" role="tabpanel" aria-labelledby="asignados-tab">
                                                <div class="col-md-4"><h4>Asignados</h4></div>
                                                <div>
                                                    <?php
                                                        $productos = $Clproductos->lista();
                                                        foreach($productos as $producto){
                                                            $imagen = $files.$producto['image_min'];
                                                            $htmlVariantes = "";
                                                            $hasVariantes = '<span class="text-danger">No tiene variantes</span>';
                                                            $variantes = $Clproductos->lista_variantes($producto['cod_producto']);
                                                            if($variantes){
                                                                $hasVariantes = '<span class="text-success"><b>Tiene variantes</b></span>';
                                                                foreach($variantes as $variante){
                                                                    $imagenV = $files.$variante['image_min'];
                                                                    $htmlVariantes .= '<div class="row">
                                                                        <div class="col-4">
                                                                            <img src="'.$imagenV.'" />
                                                                        </div>
                                                                        <div class="col-4">'.$variante['nombre'].'</div>
                                                                        <div class="col-2">$'.$variante['precio'].'</div>
                                                                        <div class="col-2">'.$variante['cod_producto'].' <a target="_blank" href="crear_productos.php?id='.$variante['alias'].'"><i data-feather="eye"></i></a></div>
                                                                    </div>'; 
                                                                }
                                                            }
                                                            
                                                            echo '<div class="row container mb-5">
                                                                <div class="col-2">
                                                                    <img src="'.$imagen.'" />
                                                                </div>
                                                                <div class="col-4">'.$producto['nombre'].'</div>
                                                                <div class="col-2">$'.$producto['precio'].'</div>
                                                                <div class="col-2">'.$producto['cod_producto'].' <a target="_blank" href="crear_productos.php?id='.$producto['alias'].'"><i data-feather="eye"></i></a></div>
                                                                <div class="col-2">'.$hasVariantes.'</div>
                                                                <div class="col-8 offset-4">'.$htmlVariantes.'</div>
                                                                <hr/>
                                                            </div>';
                                                            //var_dump($producto);
                                                        }
                                                    ?>
                                                </div>
                                                
                                                
                                                
                                                
                                                <div class="table-responsive mb-4 mt-4">
                                                    <table id="style-3" class="table style-3  table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Imagen</th>
                                                                <th>Nombre</th>
                                                                <th>Precio no tax</th>
                                                                <th>IVA</th>
                                                                <th>Precio</th>
                                                                <th>Precio comparaci&oacute;n</th>
                                                                <th>Grava IVA</th>
                                                                <th class="text-center">Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bodyAsignados">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show" id="noasignados" role="tabpanel" aria-labelledby="noasignados-tab">
                                                <div class="col-md-4"><h4>No Asignados</h4></div>
                                                <div class="table-responsive mb-4 mt-4">
                                                    <table id="style-4" class="table style-3  table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Imagen</th>
                                                                <th>Nombre</th>
                                                                <th>Precio no tax</th>
                                                                <th>IVA</th>
                                                                <th>Precio</th>
                                                                <th>Precio comparaci&oacute;n</th>
                                                                <th>Grava IVA</th>
                                                                <th class="text-center">Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bodyNoAsignados">
                                
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
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
    <script src="assets/js/pages/empresa_productos.js"></script>
    <script>
        $("document").ready(function(){

        });
        // var myTable = $('#style-3').DataTable( {
        //     dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        //     buttons: {
        //         buttons: [
        //             { extend: 'copy', className: 'btn' },
        //             { extend: 'csv', className: 'btn' },
        //             { extend: 'excel', className: 'btn' },
        //             { extend: 'pdf', className: 'btn' },
        //             { extend: 'print', className: 'btn' }
        //         ]
        //     },
        //     "oLanguage": {
        //         "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
        //         "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
        //         "sInfoEmpty": "Mostrando pag. 1",
        //         "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        //         "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        //         "sSearchPlaceholder": "Buscar...",
        //        "sLengthMenu": "Resultados :  _MENU_",
        //        "sEmptyTable": "No se encontraron resultados",
        //        "sZeroRecords": "No se encontraron resultados",
        //        "buttons": {
        //             "copy": "Copiar",
        //             "csv": "CSV",
        //             "excel": "Excel",
        //             "pdf": "PDF",
        //             "print": "Imprimir",
        //             "create": "Crear",
        //             "edit": "Editar",
        //             "remove": "Remover",
        //             "upload": "Subir"
        //         }
        //     },
        //     "stripeClasses": [],
        //     "lengthMenu": [7, 10, 20, 50],
        //     "pageLength": 10
        // } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>