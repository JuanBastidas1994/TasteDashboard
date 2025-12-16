<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="assets/css/elements/alert.css">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
</head>
<body>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true, ""); ?>
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
                                <div class="col-12">
                                    <h4>Reporte intento de pagos</h4>
                                </div>
                                <div class="col-12">
                                    <hr/>
                                </div>
                                <div class="col-12 my-5">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <label for="">Fecha inicio</label>
                                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control date" placeholder="Seleccione una fecha">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label for="">Fecha inicio</label>
                                            <input type="date" name="fechaFin" id="fechaFin" class="form-control date" placeholder="Seleccione una fecha">
                                        </div>
                                        <div class="col-12 col-md-4 align-self-end">
                                            <button class="btn btn-primary" id="buscar">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <script id="item-reporte-template" type="text/x-handlebars-template">
                                    {{#each this}}
                                        <tr>
                                            <td>{{fecha}}</td>
                                            <td>{{nombre}}</td>
                                            <td class="text-right">${{monto}}</td>
                                            <td class="text-center">{{fraude}}</td>
                                            <td>{{detalle}}</td>
                                            <td>
                                                <span class="badge badge-{{badge}} text-uppercase">{{tipo}}</span>
                                            </td>
                                        </tr>
                                    {{/each}}
                                </script>
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th>Monto</th>
                                                <th>Posible fraude</th>
                                                <th>Detalle</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="infoTabla">
                                            
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script src="assets/js/pages/reporte_intento_pagos.js" type="text/javascript"></script>
</body>
</html>