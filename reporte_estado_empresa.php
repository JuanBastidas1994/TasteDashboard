<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clempresa   = new cl_empresas(null);
$empresas    = $Clempresa->lista();
$session     = getSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php css_mandatory(); ?>
    <link href="plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
    <link href="assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
    <style>
        #reportContent { display: none; }

        .report-header-card {
            background: linear-gradient(135deg, #1b55e2 0%, #0a3abf 100%);
            color: #fff;
            border-radius: 20px;
            padding: 24px 28px;
        }
        .report-header-card h4 { color: #fff; margin-bottom: 4px; font-weight: 700; }
        .report-header-card p  { color: rgba(255,255,255,.8); margin-bottom: 0; font-size: 14px; }

        .filter-card {
            border-radius: 20px;
            padding: 24px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,.05);
        }

        @media print {
            .no-print, #sidebar, .navbar, .topbar { display: none !important; }
            #content { margin-left: 0 !important; padding: 0 !important; }
            #reportContent { display: block !important; }
            .filter-card { display: none !important; }
            .card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader"><div class="loader-content">
            <div class="spinner-grow align-self-center"></div>
        </div></div>
    </div>
    <!-- END LOADER -->

    <?php echo top(); ?>
    <?php echo navbar(); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php echo sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <!-- Breadcrumb -->
                <div class="row no-print">
                    <div class="col-12 mt-3 mb-1">
                        <span style="color:#888ea8; font-size:14px;">
                            <a href="index.php" style="color:#888ea8;">Dashboard</a>
                            &nbsp;/&nbsp; Reporte Estado Empresa
                        </span>
                    </div>
                </div>

                <!-- Título -->
                <div class="row no-print">
                    <div class="col-12 mb-3">
                        <h3 style="font-weight:700;">Reporte de Estado por Empresa</h3>
                        <p class="text-muted mb-0">Análisis de métricas clave con gráficas y exportación a PDF.</p>
                    </div>
                </div>

                <!-- Formulario de filtros -->
                <div class="row no-print mb-4">
                    <div class="col-12">
                        <div class="filter-card">
                            <div class="form-row align-items-end">

                                <!-- Empresa (obligatoria) -->
                                <div class="form-group col-xl-3 col-md-6 mb-3 mb-xl-0">
                                    <label class="font-weight-bold">
                                        Empresa <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="cmbEmpresa" required>
                                        <option value="">— Selecciona una empresa —</option>
                                        <?php foreach ($empresas as $emp): ?>
                                            <option value="<?= (int)$emp['cod_empresa'] ?>">
                                                <?= htmlspecialchars($emp['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Sucursal (opcional, default Todas) -->
                                <div class="form-group col-xl-3 col-md-6 mb-3 mb-xl-0">
                                    <label class="font-weight-bold">Sucursal</label>
                                    <select class="form-control" id="cmbSucursal">
                                        <option value="0">Todas las sucursales</option>
                                    </select>
                                </div>

                                <!-- Fecha inicio -->
                                <div class="form-group col-xl-2 col-md-4 mb-3 mb-xl-0">
                                    <label class="font-weight-bold">Fecha inicio</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i data-feather="calendar" style="width:16px;height:16px;"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" id="fecha_inicio">
                                    </div>
                                </div>

                                <!-- Fecha fin -->
                                <div class="form-group col-xl-2 col-md-4 mb-3 mb-xl-0">
                                    <label class="font-weight-bold">Fecha fin</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i data-feather="calendar" style="width:16px;height:16px;"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" id="fecha_fin">
                                    </div>
                                </div>

                                <!-- Botón Generar -->
                                <div class="form-group col-xl-2 col-md-4 mb-0">
                                    <label class="d-none d-xl-block">&nbsp;</label>
                                    <button class="btn btn-primary btn-block" id="btnGenerar">
                                        <i data-feather="bar-chart-2" style="width:16px;height:16px;"></i>
                                        &nbsp;Generar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- CONTENIDO DEL REPORTE (oculto hasta presionar Generar)       -->
                <!-- ============================================================ -->
                <div id="reportContent">

                    <!-- Cabecera del reporte -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="report-header-card d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h4 id="reportTitle">Reporte</h4>
                                    <p id="reportSubtitle"></p>
                                </div>
                                <div class="mt-2 mt-md-0 no-print">
                                    <button class="btn btn-light" id="btnGenerarPDF">
                                        <i data-feather="file-text" style="width:16px;height:16px;"></i>
                                        &nbsp;Descargar PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widgets (KPIs + gráficas) -->
                    <div class="row" id="widgetsInfo"></div>

                    <!-- Espaciado inferior -->
                    <div style="height: 40px;"></div>

                </div>
                <!-- FIN CONTENIDO DEL REPORTE -->

                <!-- Handlebars template para cada widget -->
                <script id="widget-template" type="text/x-handlebars-template">
                    <div class="col-lg-{{default column 4}} col-md-{{default column 4}} col-sm-6 col-12 mt-4">
                        <div class="card p-2" style="border-radius: 20px;">
                            <div class="widget-title d-flex mb-1">
                                <div class="flex-grow-1"><b>{{title}}</b></div>
                            </div>
                            <div class="widget-info" style="height: {{default height 120}}px;">
                                <h3 style="font-weight: bolder;">{{value}}</h3>
                                <div class="widget-chart"></div>
                            </div>
                        </div>
                    </div>
                </script>

            </div>
            <?php footer(); ?>
        </div>
        <!-- END CONTENT AREA -->

    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>

    <!-- Handlebars -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <!-- ApexCharts -->
    <script src="plugins/apex/apexcharts.min.js"></script>
    <!-- Dashboard helpers (addWidgetText, generateChart, createBarChartData…) -->
    <script src="assets/js/dashboard/dash_1.js"></script>
    <script src="assets/js/dashboard/charts.js?v=1"></script>
    <!-- html2canvas + jsPDF para exportar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        Handlebars.registerHelper('default', function (value, defaultValue) {
            return value != null ? value : defaultValue;
        });
    </script>

    <script src="assets/js/pages/reporte_estado_empresa.js?v=1"></script>
</body>
</html>
