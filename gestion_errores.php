<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

$session = getSession();
if (!isLogin()) {
    header("location:login.php");
    exit;
}

$Clempresas = new cl_empresas(NULL);
$empresa = $Clempresas->get($session['cod_empresa']);
$apikey = $empresa ? $empresa['api_key'] : "";
$casher_id = $session['cod_usuario'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="assets/css/apps/mailbox.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="plugins/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="bootstrap/css/custom-sidebar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components/timeline/timeline-pasos.css?v=1" rel="stylesheet" type="text/css" />
    <link href="assets/css/elements/tooltip.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        .indicator-box {
            padding: 5px 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 1px 3px 4px 1px;
        }

        .loader-sm {
            width: 22px;
            height: 22px;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
        }

        .clientInfo svg {
            width: 18px;
            height: 18px;
        }

        .clientInfo .name {
            font-size: 20px;
            font-weight: bold;
        }

        .bg-info-light {
            background-color: #e7f7ff !important;
        }

        .tagsOrderList {
            background-color: #d7e4ff;
            font-size: 12px;
            line-height: 13px;
            color: white;
            font-weight: normal;
            padding: 3px 10px;
            border-radius: 5px;
            margin-right: 5px;
        }

        .tagsOrderList svg {
            width: 16px;
            height: 16px;
        }

        .stickyAgregarCarrito {
            position: sticky;
            bottom: 0px;
            height: 10px;
            z-index: 999;
            background-color: white;
        }

        .row.colums-table-head {
            height: 30px;
            border-bottom: 1px solid #e3e3e3;
            color: #1b55e2;
            font-weight: bolder;
        }

        .item-products .title {
            font-size: 16px;
            font-weight: bold;
            color: #565656;
        }

        .row.item-products {
            padding: 15px 0px;
            border-bottom: 1px solid #e3e3e3;
        }

        .changeTipo.active {
            background-color: #2196f3 !important;
            z-index: 0 !important;
        }

        .table-products-details tbody .product-name {
            font-size: 15px !important;
            font-weight: bold;
            color: black !important;
        }

        .feather-16 {
            width: 16px;
            height: 16px;
        }

        .feather-18 {
            width: 18px;
            height: 18px;
        }

        /* Pantalla de solo lectura: el contenedor del mailbox ocupa todo el ancho */
        .mail-box-container .tab-title {
            padding-top: 15px;
        }

        @media (max-width:1025px) {

            .table-sm td,
            .table-sm th {
                font-size: 11px !important;
            }
        }
    </style>
    <!--  END CUSTOM STYLE FILE  -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>

<body>
    <!-- Modal Orden Detalle (solo lectura) -->
    <div class="modal fade bs-example-modal-xl" id="OrdenDetailModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center" id="orden-detalle-header">

                </div>
                <div class="modal-body" style="padding-bottom: 0px !important;">
                    <?php
                    include('templates/gestion-errores/orden-detalle-modal.html')
                    ?>
                    <div class="x_content" id="orden-detalle-body"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tracking (solo lectura) -->
    <div class="modal left fade" id="trackingModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title text-center" style="width: 100%;">Tracking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    include('templates/gestion-ordenes-tracking.html')
                    ?>
                    <div class="x_content" id="orden-tracking">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <div class="sub-header-container">
        <header class="header navbar navbar-expand-sm expand-header">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                <i data-feather="menu"></i>
            </a>
            <ul class="navbar-nav flex-row align-items-center">
                <li>
                    <div style="text-transform: uppercase; font-size: 20px; font-weight: bold; color: #727272;">
                        Gestión de errores
                    </div>
                    <div style="font-size: 12px;">Todas las empresas y sucursales · solo hoy</div>
                </li>
            </ul>
        </header>
    </div>

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container sidebar-closed" id="container">

        <div class="overlay show"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <?php echo sidebar(); ?>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="row">
                            <div class="col-xl-12  col-md-12">
                                <div>
                                    <input id="apikey_empresa" type="hidden" value="<?= $apikey ?>">
                                    <input id="casher_id" type="hidden" value="<?= $casher_id ?>">
                                </div>
                            </div>

                            <div class="col-xl-12  col-md-12">
                                <div class="d-flex flex-wrap mb-3 mt-2">
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="preordenesErrors">-</div>
                                        <div>Órdenes <br /> con error</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesNoFacturadas">-</div>
                                        <div>Órdenes <br /> no facturadas</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesErrorFacturacion">-</div>
                                        <div>Facturas <br /> con error</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesErrorAsignacion">-</div>
                                        <div>Courier <br /> error asignación</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesCanceledByCourier">-</div>
                                        <div>Pedidos cancelados <br /> por el courier</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 mb-2 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesCanceledByUser">-</div>
                                        <div>Pedidos cancelados <br /> por el Cajero</div>
                                    </div>
                                </div>
                                <div class="mail-box-container">
                                    <div class="mail-overlay"></div>
                                    <div class="tab-title">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-12 mail-categories-container">
                                                <div class="mail-sidebar-scroll">
                                                    <script id="status-list" type="text/x-handlebars-template">
                                                        {{#each this}}
                                                            <li class="nav-item">
                                                                <a class="nav-link list-actions envio-{{is_envio}}" id="{{id}}"><i data-feather="{{icono}}"></i><span class="nav-names">{{nombre}}</span> <span class="mail-badge badge badge-{{id}}">{{cantidad}}</span></a>
                                                            </li>
                                                        {{/each}}
                                                    </script>
                                                    <ul class="nav nav-pills d-block" id="pills-tab" role="tablist">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="mailbox-inbox" class="accordion mailbox-inbox">
                                        <div class="action-center justify-content-start">
                                            <div class="">
                                                <div class="btn-group" role="group" aria-label="Tipo de orden">
                                                    <button type="button" class="changeTipo btn btn-outline-info active" data-value="todas">Todas</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="delivery">Envíos</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="pickup">Pickup</button>
                                                </div>
                                                <input type="hidden" id="is_envio" value="todas">
                                            </div>
                                        </div>

                                        <?php
                                        include('templates/gestion-errores/orden-lista.html')
                                        ?>
                                        <div class="message-box">
                                            <script id="lottieAnimation" type="text/x-handlebars-template">
                                                <h3 class="text-center mt-3">{{text}}</h3>
                                                <lottie-player src="{{animation}}" background="transparent" speed="1" loop autoplay></lottie-player>
                                            </script>
                                            <div class="message-box-scroll" id="lista_ordenes">
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

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script>
        Handlebars.registerHelper('eq', function(arg1, arg2, options) {
            return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('diferent', function(arg1, arg2, options) {
            return (arg1 !== arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('mayor', function(arg1, arg2, options) {
            return (arg1 > arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('decimal', function(number) {
            return parseFloat(number).toFixed(2);
        });
        Handlebars.registerHelper('colorStatus', function(status) {
            if (status == "ENTRANTE")
                return "primary";
            else if (status == "ASIGNADA")
                return "warning";
            else if (status == "ENVIANDO")
                return "secondary";
            else if (status == "ENTREGADA")
                return "success";
            else if (status == "ANULADA")
                return "danger";
            else if (status == "NO_ENTREGADA")
                return "danger";
            else if (status == "PUNTO_RECOGIDA")
                return "info";
            else if (status == "PUNTO_ENTREGA")
                return "dark";
            else
                return "info";
        });
        Handlebars.registerHelper('ifIn', function(elem, list, options) {
            if (list.indexOf(elem) > -1) {
                return options.fn(this);
            }
            return options.inverse(this);
        });
        Handlebars.registerHelper('array', function() {
            return Array.prototype.slice.call(arguments, 0, -1);
        });
    </script>
    <?php js_mandatory(); ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    <script src="plugins/notification/snackbar/snackbar.min.js"></script>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="bootstrap/js/popper.min.js"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/gestion-errores/index.js?v=6" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/tracking.js?v=2" type="text/javascript"></script>
</body>

</html>
