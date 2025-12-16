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

        <div class="modal" tabindex="-1" role="dialog" id="modalUnidades">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Escoja la unidad de medida</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-5">
                        <select id="cmbUnidadMedida" class="form-control"></select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnGuardarUnidad">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Ingredientes</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="col-12">
                                        <h5>Ingredientes Contífico</h5>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar..." aria-describedby="resetSearch" id="search">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="resetSearch">
                                                    <i data-feather="x"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p>Página actual:
                                            <span class="currentpage">1</span>
                                        </p>
                                        <button class="btn btn-primary btnPrev" disabled>
                                            <i data-feather="chevron-left"></i>
                                        </button>
                                        <button class="btn btn-primary btnNext" disabled>
                                            <i data-feather="chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="table-responsive mb-4 mt-4">
                                        <table id="style-3" class="table style-3  table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th> Nombre </th>
                                                    <th>ID</th>
                                                </tr>
                                            </thead>
                                            <tbody id="lstProductoContifico">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center">
                                        <p>Página actual:
                                            <span class="currentpage">1</span>
                                        </p>
                                        <button class="btn btn-primary btnPrev" disabled>
                                            <i data-feather="chevron-left"></i>
                                        </button>
                                        <button class="btn btn-primary btnNext" disabled>
                                            <i data-feather="chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="col-12">
                                        <h5>Ingredientes Importados</h5>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar..." aria-describedby="resetSearch" id="search2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="resetSearch2">
                                                    <i data-feather="x"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive mb-4 mt-4">
                                        <table id="style-3" class="table style-3  table-hover" style="margin-top: 85px;">
                                            <thead>
                                                <tr>
                                                    <th> Nombre </th>
                                                    <th> ID </th>
                                                    <th> Unidad </th>
                                                    <th> Acción </th>
                                                </tr>
                                            </thead>
                                            <tbody id="lstIngredientes"></tbody>
                                        </table>
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
    <script src="assets/js/pages/ingredientes.js" type="text/javascript"></script>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script id="lista-productos-contifico-template" type="text/x-handlebars-template">
        {{#each results}}
            <tr class="results" data-filter="{{nombre}}">
                <td>
                    <button class="btn btn-link text-success btnImportar" data-ingredient='{"id": "{{id}}", "nombre": "{{nombre}}", "precio": "{{pvp1}}" }'>Importar</button>
                </td>
                <td>{{ nombre }}</td>
                <td>{{ id }}</td>
            </tr>
        {{/each}}
    </script>
    <script id="lista-ingredientes-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr class="results2" data-filter="{{ingrediente}}">
                <td>{{ ingrediente }}</td>
                <td>{{ id_contifico }}</td>
                <td>
                    <a href="javascript:;" data-ingredient='{"id": "{{id_contifico}}", "nombre": "{{ingrediente}}", "cod_unidad_medida": "{{cod_unidad_medida}}"}' class="text-primary btnEditUnidadMedida">{{cod_unidad_medida}}</a>
                </td>
                <td>
                    <a href="javascript:;" data-ingredient='{"id": "{{id_contifico}}", "nombre": "{{ingrediente}}"}' class="btnEliminar">
                        <i data-feather="trash-2" class="text-danger"></i>
                    </a>
                </td>
            </tr>
        {{/each}}
    </script>
</body>

</html>