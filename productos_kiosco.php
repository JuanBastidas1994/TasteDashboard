<?php
require_once "funciones.php";
require_once "clases/cl_noticias.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clnoticias = new cl_noticias(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
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
        <!-- Modal -->
        <div class="modal fade" id="modalProductCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveCustom">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Productos en el local</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-column text-center"> ID </th>
                                            <th class="text-center">Image</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Visible en tienda</th>
                                            <th class="text-center">Precios personalizados</th>
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

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <script src="assets/js/pages/productos_kiosco.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script id="productos-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr>
                <td>{{cod_producto}}</td>
                <td>
                    <img src="{{image_min}}" width="100">
                </td>
                <td>{{nombre}}</td>
                <td class="text-right">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" class="precioProducto form-control" placeholder="Precio" aria-label="Precio" aria-describedby="basic-addon1" value="{{precio}}">
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge badge-{{colorStatus estado}}">{{textStatus estado}}</span>
                </td>
                <td>
                    <ul class="table-controls">
                        <li>
                            <label class="switch s-icons s-outline s-outline-success m-0">
                                <input type="checkbox" class="visible" {{#eq visible 'A'}} checked {{/eq}}>
                                <span class="slider round"></span>
                            </label>
                        </li>
                    </ul>
                </td>
                <td>
                    <ul class="table-controls">
                        <li>
                            <label class="switch s-icons s-outline s-outline-success m-0">
                                <input type="checkbox" class="custom" {{#eq is_custom "1"}} checked {{/eq}} data-producto='{{objectToJson this}}'>
                                <span class="slider round"></span>
                            </label>
                        </li>
                    </ul>
                </td>
                <td>
                    <ul class="table-controls">
                        <li>
                            <a href="javascript:void(0);" class="btnSaveProduct bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Guardar" data-producto='{{objectToJson this}}'>
                                <i data-feather="save"></i>
                            </a>
                        </li>
                        
                        <li class="is-custom {{#eq is_custom "0"}} d-none {{/eq}} ">
                            <a href="javascript:void(0);" class="btnCustomProduct bs-tooltip" data-toggle="tooltip" data-placement="top" title="Personalizar" data-original-title="Personalizar" data-producto='{{objectToJson this}}'>
                                <i data-feather="settings"></i>
                            </a>
                        </li>
                        <li class="is-custom {{#eq is_custom "0"}} d-none {{/eq}} ">
                            <a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="Precios diferentes en las sucursales" data-original-title="Precios diferentes en las sucursales">
                                <i data-feather="alert-triangle" class="text-warning"></i>
                            </a>
                        </li>
                        
                    </ul>
                </td>
            </tr>
        {{/each}}
    </script>
    
    <script id="modal-body-template" type="text/x-handlebars-template">
        <div class="row">
            <div class="col-2">
                <input type="text" value="{{producto.cod_producto}}" id="productCustomId">
                <img src="{{producto.image_min}}" class="img-fluid">
            </div>
            <div class="col">
                <div class="table-responsive">
                    <table id="sucursales" class="table style-3  table-hover">
                        <thead>
                            <tr>
                                <th>Sucursales</th>
                                <th>Precio</th>
                                <th>Visible en tienda</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{#each data}}
                                <tr>
                                    <td>{{nombre}}</td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" class="form-control precioCustom" placeholder="Precio" aria-label="Precio" aria-describedby="basic-addon1" value="{{precio}}" data-office="{{cod_sucursal}}">
                                        </div>
                                    </td>
                                    <td>
                                        <ul class="table-controls">
                                            <li>
                                                <label class="switch s-icons s-outline s-outline-success m-0">
                                                    <input type="checkbox" class="estadoCustom" {{#eq estado 'A'}} checked {{/eq}} >
                                                    <span class="slider round"></span>
                                                </label>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            {{/each}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </script>
</body>

</html>