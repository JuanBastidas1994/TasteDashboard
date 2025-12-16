<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales();
$session = getSession();

$cod_empresa = $session['cod_empresa']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <!--<link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />

</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div class="col-md-8" >
                        <h3 id="titulo">Costos de envío</h3>
                    </div>
                </div>
                <div class="row layout-top-spacing">
                    <div class="col-12  layout-spacing">
                        
                        <div class="widget-content widget-content-area br-6">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table id="style-3" class="table table-striped style-3">
                                                <thead>
                                                    <tr>
                                                        <th>Sucursal</th>
                                                        <th>Rango Km de 0 a n? *</th>
                                                        <th>Tarifa por rango km *</th>
                                                        <th>Tarifa por km adicional? *</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- TEMPLATE ID: results-template -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-row mt-3">
                                            <div class="col-12 text-right">
                                                <button class="btn btn-primary" onclick="guardarCostoEnvio()">Guardar</button>
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
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    <?php js_mandatory(); ?>
    <script src="./assets/js/libs/jquery.validate.js"></script>
    <script src="./assets/js/pages/sucursales_costo_envio.js"></script>

    <script id="results-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr>
                <td>
                    {{nombre}}
                    <input type="hidden" class="form-control cod_sucursal" value="{{cod_sucursal}}">
                    <input type="hidden" class="form-control cod_sucursal_costo_envio" value="{{cod_sucursal_costo_envio}}">
                </td>
                <td>
                    <input type="text" class="form-control base_km" value="{{base_km}}">
                </td>
                <td>
                    <input type="text" class="form-control base_dinero" value="{{decimal base_dinero}}">
                </td>
                <td>
                    <input type="text" class="form-control adicional_km" value="{{decimal adicional_km}}">
                </td>
            </tr>
        {{/each}}
    </script>
</body>
</html>