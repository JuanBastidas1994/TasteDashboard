<?php
require_once "funciones.php";

if(!isLogin()){
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gb18030">
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
                                    <h4>Cotización de Precio</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        <label>Sucursal</label>
                                        <select id="cmbSucursal" class="form-control">
                                            <option value="">Todas</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-1 col-12 align-items-end d-flex">
                                        <button class="btn btn-primary" onclick="getCotizaciones();">Filtrar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table id="style-3" class="table style-3  table-hover" style="margin-top: 0px !important;">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Courier</th>
                                                <th>Latitud</th>
                                                <th>Longitud</th>
                                                <th>Distancia</th>
                                                <th>Precio</th>
                                                <th>Cliente</th>
                                                <th>Dispositivo</th>
                                                <th>Creado</th>
                                                <th>Vigente</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- LISTA DE COTIZACIONES -->
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

    <script src="./assets/js/pages/cotizacion_precio.js"></script>

    <!-- TEMPLATES -->
    <script id="sucursales-template" type="text/x-handlebars-template">
        {{#each this}}
            <option value="{{cod_sucursal}}">{{nombre}}</option>
        {{/each}}
    </script>
    <script id="cotizaciones-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr>
                <td>{{sucursal}}</td>
                <td>{{courier_nombre}}</td>
                <td>
                    {{latitud}}
                    <a href="javascript:void(0);" class="btnCopiar bs-tooltip" data-copy="{{latitud}}" data-toggle="tooltip" data-placement="top" data-original-title="Copiar latitud">
                        <i data-feather="copy"></i>
                    </a>
                </td>
                <td>
                    {{longitud}}
                    <a href="javascript:void(0);" class="btnCopiar bs-tooltip" data-copy="{{longitud}}" data-toggle="tooltip" data-placement="top" data-original-title="Copiar longitud">
                        <i data-feather="copy"></i>
                    </a>
                </td>
                <td>{{#if distancia_km}}{{decimal distancia_km}} km{{else}}-{{/if}}</td>
                <td>${{decimal precio}}</td>
                <td>{{#if cliente}}{{cliente}}{{else}}<span class="text-danger font-weight-bold">No Auth</span>{{/if}}</td>
                <td>{{#if device_type}}{{device_type}}{{else}}-{{/if}}</td>
                <td>{{creado_en}}</td>
                <td><span class="badge badge-{{colorStatus vigente_estado}}">{{vigente_estado}}</span></td>
                <td class="text-center">
                    <a href="https://www.google.com/maps/dir/?api=1&origin={{sucursal_latitud}},{{sucursal_longitud}}&destination={{latitud}},{{longitud}}" target="_blank" class="btn btn-sm btn-primary bs-tooltip" data-toggle="tooltip" data-placement="top" data-original-title="Ver ruta en Google Maps">
                        <i data-feather="map-pin"></i>
                    </a>
                </td>
            </tr>
        {{/each}}
    </script>
</body>
</html>
