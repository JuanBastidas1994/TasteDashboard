<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";

if(!isLogin()){
    header("location:login.php");
}

$Clcategorias = new cl_categorias(NULL);
$session = getSession();

$categorias = $Clcategorias->listaNueva($session['cod_empresa']);
function listaCategorias($data, $parent = 0, $nivel=1){
    $html = "";
    $lvl = "l".$nivel; 
    foreach ($data as $key => $categoria) {
        $cod_categoria = $categoria['cod_categoria'];
        $imagen = getImage($categoria['image_min']);
        // $imagen = $files.$categoria['image_min']."?v=".$categoria['fecha_modificacion'];
        $badge='primary';
        if($categoria['estado'] == 'I')
            $badge='danger';

        $html .= '<tr data-id="'.$cod_categoria.'" data-parent="'.$parent.'" data-level="'.$nivel.'">
            <td class="checkbox-column" data-column="name"> '.$categoria['categoria'].' </td>
            <td class="text-center">
                <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
            </td>
            <td>'.$categoria['desc_corta'].'</td>
            <td>'.$categoria['cod_categoria'].'</td>
            <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($categoria['estado']).'</span></td>
            <td class="text-center">
                <ul class="table-controls">
                    <li><a href="crear_categorias.php?id='.$categoria['alias'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                    <li><a href="javascript:void(0);" data-value="'.$categoria['cod_categoria'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                </ul>
            </td>
        </tr>';



        if(count($categoria['subcategorias'])>0){
            $html .= listaCategorias($categoria['subcategorias'], $cod_categoria, $nivel+1);
        }
    }
    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style>
        .treegrid-indent {
        width: 16px;
        height: 16px;
        display: inline-block;
        position: relative;
    }

    .treegrid-expander {
        width: 16px;
        height: 16px;
        display: inline-block;
        position: relative;
        left:-17px;
        cursor: pointer;
    }
    </style>
</head>
<body>
     <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ordenar items</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                
                <div class="modal-body">
                    <table class="table style-3  table-hover">
                        <tbody id="moveCategorias" class="connectedSortable"> 
                            <?php
                         
                            $resp = $Clcategorias->listaPadre();
                            foreach ($resp as $categoriaPadre) {
                                echo '<tr data-codigo="'.$categoriaPadre['cod_categoria'].'">
                                        <td>* '.$categoriaPadre['categoria'].' </td>
                                      </tr>';
                                $resp = $Clcategorias->listaHijos($categoriaPadre['cod_categoria']);
                                foreach ($resp as $categoriaHijos) {
                                    echo'<tr data-codigo="'.$categoriaHijos['cod_categoria'].'">
                                            <td>-- '.$categoriaHijos['categoria'].' </td>
                                        </tr>';
                                    
                                }
                               
                            }
                            ?>
                        </tbody>
                    </table>
                    
                 
                </div>
                
                
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
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
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-4 col-md-4 col-sm-8 col-8">
                                    <h4>Categor&iacute;as</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <a class="btn btn-primary"  id="btnOpenModal">Ordenar Categorias</a>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <a href="crear_categorias.php" class="btn btn-primary">Nueva Categor&iacute;a</a>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4">
                                <table id="tree-table" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th class="checkbox-column text-center"> Nombre </th>
                                                <th class="text-center">Image</th>
                                                <th>Descripcion</th>
                                                <th>Record Id</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            echo listaCategorias($categorias, 0);
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
    <script src="plugins/table/tree/tree.js"></script>
    <script src="assets/js/pages/categorias.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script>
        var myTable = $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [15, 20, 50],
            "pageLength": 15 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>