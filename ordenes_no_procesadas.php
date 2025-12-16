<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales(NULL);
$Clempresas = new cl_empresas(NULL);
$session = getSession();
$cod_empresa = $session["cod_empresa"];
$apikey = $session["api_key"];

/* if(!userGrant()){
    header("location:index.php");
} */

if(isset($_GET["id"])) {
    $empresa = $Clempresas->getByAlias($_GET["id"]);
    if(!$empresa)
        header("location:index.php");
    $cod_empresa = $empresa["cod_empresa"];
    $apikey = $empresa["api_key"];
}

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

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Órdenes no procesadas</h4>
                                    <input type="hidden" id="cod_empresa" value="<?=$cod_empresa?>">
                                    <input type="hidden" id="apikey" value="<?=$apikey?>">
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 

                            <div class="row">
                                <div class="col-12 col-lg-4 mt-2">
                                    <label>Fecha inicio</label>
                                    <input type="text" id="fecha-inicio" class="pickr form-control">
                                </div>
                                <div class="col-12 col-lg-4 mt-2">
                                    <label>Fecha fin</label>
                                    <input type="text" id="fecha-fin" class="pickr form-control">
                                </div>
                                <div class="col-12 col-lg-4 d-flex align-items-end mt-2">
                                    <button class="btn btn-primary" onclick="getOrdenesNoProcesadas()">Buscar</button>
                                </div>
                            </div>

                            <div class="row my-4" id="all-users">
                                <button class="btn btn-success" onclick="procesarTodas()">Procesar todas las órdenes</button>
                            </div>
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Monto</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- TEMPLATE ID: orden-no-procesada-template -->
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
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>
    <script src="./assets/js/libs/moment/moment.min.js"></script>
    <script src="assets/js/pages/orden-no-procesada.js" type="text/javascript"></script>
    <script id="no-orden-no-procesada-template" type="text/x-handlebars-template">
        <tr>
            <td colspan="4">{{ text }}</td>
        </tr>
    </script>
    <script id="orden-no-procesada-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr class="tr-users" data-user-id="{{ cod_usuario }}">
                <td><a href="./cliente_detalle.php?id={{ cod_usuario }}" target="_blank">{{ nombre }}</a></td>
                <td class="text-right">${{ decimal total }}</td>
                <td class="text-center">
                    <ul class="list-unstyled">
                        <li>
                            <a href="javascript:void(0)" onclick="procesarOrden({{ cod_usuario }})">
                                <i data-feather="check"></i>
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        {{/each}}
    </script>
</body>
</html>