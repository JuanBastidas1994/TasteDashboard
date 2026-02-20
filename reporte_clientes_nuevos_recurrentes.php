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
    <link href="plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
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
                    <h3 id="titulo">Reporte Clientes nuevos y recurrentes</h3>
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
                        
                        <div class="row widget-info">
                            <div class="col-4">
                                <div class="card p-2" style="border-radius: 20px;">
                                    <h4 class="widget-title"><b>Clientes nuevos</b></h4>
                                    <h3 class="widget-info"><b id="client_new">50</b></h3>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="card p-2" style="border-radius: 20px;">
                                    <h4 class="widget-title"><b>Clientes recurrentes</b></h4>
                                    <h3 class="widget-info"><b id="client_recurrent">50</b></h3>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4>Evolución de clientes</h4>
                            <div id="chartEvolucion"></div>
                        </div>

                    </div>
                    <div class="" id="widgetsInfo"></div>

                    <script id="widget-template" type="text/x-handlebars-template">
                        <div class="col-lg-{{default column 4}} col-md-{{default column 4}} col-sm-4 col-12 mt-4">
                            <div class="card p-2"  style="border-radius: 20px;">
                                <div class="widget-title d-flex">
                                    <div class="flex-grow-1"><b>{{title}}</b></div>
                                    <div class="btnShare" 
                                        data-type="{{type}}" 
                                        data-title="{{title}}"
                                        data-value="{{value}}"
                                        data-shared="{{sharedText}}"
                                        data-options="{{chartdata}}">
                                        <i data-feather="share-2"></i>
                                    </div>
                                </div>
                                <div class="widget-info" style="height: {{default height 120}}px;">
                                    <h3 style="font-weight: bolder; ">{{value}}</h3>
                                    <div class="widget-chart"></div>
                                </div>
                                <div class="widget-footer">
                                    
                                </div>
                            </div>
                        </div>
                    </script>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
     <script type="text/javascript" src="assets/js/handlebars-helpers.js"></script>
     <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>

    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>

    <script src="assets/js/dashboard/charts.js?v=1" type="text/javascript"></script>
    
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
     <script>
    Handlebars.registerHelper('default', function (value, defaultValue) {
        return value != null ? value : defaultValue;
    });
    var template = Handlebars.compile($("#widget-template").html());


    function refreshReport(officeId = 0, dateStart, dateEnd) {
        $.ajax({
            url: 'controllers/controlador_reporte_clientes_recurrentes.php?metodo=getInfoReport',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ office_id: officeId, dateStart, dateEnd }),
            success: function(response) {
                console.log(response);
                createLineChart(response.chart_news_recurrents);
                $("#client_recurrent").html(response.clients_recurrents);
                $("#client_new").html(response.clients_news);
            }
        });
    }

    $(".btnReporte").on('click', function(){
        const officeId = parseInt($('#sucursalSelect').val());
        const dateStart = $("#fecha_inicio").val();
        const dateEnd = $("#fecha_fin").val();
        refreshReport(officeId, dateStart, dateEnd);
    });


    /*Combos fecha*/
    var f4 = flatpickr(document.getElementById('fecha_inicio'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    var f4 = flatpickr(document.getElementById('fecha_fin'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    function createLineChart(data){
        //Grafico venta por sucursal
        $("#chartEvolucion").html('');
        sharedText = `Estas son mis ventas generales 📈`;
        chartData = createLineChartData(data, 'Ventas Totales', 300, 'number');
        generateChart($("#chartEvolucion")[0], chartData);

    }
    </script>

</body>

</html>