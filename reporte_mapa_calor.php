<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}

require_once "clases/cl_sucursales.php";
$clsucursales = new cl_sucursales(NULL);


$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';
$alias = $session['alias'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="assets/css/widgets/modules-widgets.css">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .imground {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .dropify-wrapper {
            display: block;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            width: 100%;
            max-width: 100%;
            height: 110px !important;
            padding: 5px 10px;
            font-size: 14px;
            line-height: 22px;
            color: #777;
            background-color: #fff;
            background-image: none;
            text-align: center;
            border: 0 !important;
            -webkit-transition: border-color .15s linear;
            transition: border-color .15s linear;
        }

        .respGalery>div {
            margin-top: 15px;
        }

        .croppie-container .cr-boundary {
            background-image: url(assets/img/transparent.jpg);
            background-position: center;
            background-size: cover;
        }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
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
            <div class="layout-px-spacing bg-white">
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Reporte Mapa de calor</h3>
                </div>

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12 ">
                        <div class="widget-content widget-content-area br-6 py-2">
                                <div class="x_content">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <label>Sucursales <span class="asterisco">*</span></label>
                                                <select class="form-control  basic" id="sucursalSelect">
                                                    <option value="0" selected="selected">Todas las sucursales</option>
                                                    <?php
                                                    $resp = $clsucursales->all();
                                                    foreach ($resp as $sucursales) {
                                                        $estado = $sucursales["estado"] == "D" ? " - <span class='text-danger'>(Eliminada)</span>" : "";
                                                        echo '<option value="' . $sucursales['cod_sucursal'] . '">' . $sucursales['nombre'] . ''.$estado.'</option>';
                                                    }

                                                    ?>
                                                </select>
                                            </div>


                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group" >
                                                <label>Fecha inicio</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_inicio" id="fecha_inicio">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-3 col-xs-12 input-group">
                                                <label>Fecha fin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_fin" id="fecha_fin">
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-3 col-sm-3 col-12" style="text-align: right;">
                                                <button class="btn btn-primary btnReporte" style="margin-top: 30px;" data-empresa="<?= $cod_empresa ?>" data-alias="<?= $alias ?>">Generar reporte</button>
                                            </div>
                                        </div>
                                    </div>




                                </div>
                        </div>
                        <hr>
                    </div>




                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  underline-content">
                        <div id="map" style="width:100%;height:500px;"></div>
                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
     <script>
    let map;
    let heatCircles = [];

    async function initMap() {
        const { Map } = await google.maps.importLibrary("maps");
        map = new Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: -2.1753280, lng: -79.90624 },
            mapTypeId: 'roadmap'
        });
    }

    function refreshHeatmap(officeId, dateStart, dateEnd) {
        officeId = officeId || 0;
        if (!map) { messageDone('El mapa aún no está listo, intenta de nuevo.', 'warning'); return; }
        $.ajax({
            url: 'controllers/controlador_reporte_mapa_calor.php?metodo=getLocationsOrders',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ office_id: officeId, dateStart: dateStart, dateEnd: dateEnd }),
            success: function(response) {
                heatCircles.forEach(function(c) { c.setMap(null); });
                heatCircles = [];

                if (!Array.isArray(response) || response.length === 0) {
                    messageDone('No hay datos para el rango seleccionado.', 'warning');
                    return;
                }

                response.forEach(function(p) {
                    var lat = parseFloat(p.latitud);
                    var lng = parseFloat(p.longitud);
                    if (isNaN(lat) || isNaN(lng)) return;
                    heatCircles.push(new google.maps.Circle({
                        strokeColor: 'transparent',
                        fillColor: '#FF4400',
                        fillOpacity: 0.08,
                        map: map,
                        center: { lat: lat, lng: lng },
                        radius: 300
                    }));
                });

                map.setCenter({ lat: parseFloat(response[0].latitud), lng: parseFloat(response[0].longitud) });
            }
        });
    }

    $(".btnReporte").on('click', function(){
        const officeId = parseInt($('#sucursalSelect').val());
        const dateStart = $("#fecha_inicio").val();
        const dateEnd = $("#fecha_fin").val();
        refreshHeatmap(officeId, dateStart, dateEnd);
    });

    // $('#periodoSelect, #sucursalSelect').on('change', function() {
        
    // });

    /*Combos fecha*/
    var f4 = flatpickr(document.getElementById('fecha_inicio'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    var f4 = flatpickr(document.getElementById('fecha_fin'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });
    </script>
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&loading=async&callback=initMap"></script>

</body>

</html>