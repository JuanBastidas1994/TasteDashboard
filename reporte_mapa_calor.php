<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_reporte_ventas.php";

require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}
$clsucursales = new cl_sucursales(NULL);

$Clcategorias = new cl_categorias(NULL);

$Clreportes = new cl_reporte_ventas(NULL);
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&libraries=visualization"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
     <script>
    let map;
    let heatmap;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: -2.1753280, lng: -79.90624 },
            mapTypeId: 'roadmap'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: [],
            radius: 30,
            opacity: 0.7
        });

        heatmap.setMap(map);
    }

    // 🔄 Función para refrescar puntos con AJAX
    function refreshHeatmap(officeId = 0, dateStart, dateEnd) {
        $.ajax({
            url: 'controllers/controlador_reporte_mapa_calor.php?metodo=getLocationsOrders',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ office_id: officeId, dateStart, dateEnd }),
            success: function(response) {
                const points = response.map(p => new google.maps.LatLng(parseFloat(p.latitud), parseFloat(p.longitud)));
                heatmap.setData(points);

                if(points.length > 0){
                    map.setCenter(points[0]); // centra en el primer punto
                }
            }
        });
    }

    // Inicia mapa
    initMap();

    // Ejemplo: refrescar al cambiar filtro
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

</body>

</html>