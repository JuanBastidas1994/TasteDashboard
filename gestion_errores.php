<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";

$session = getSession();
if (!isLogin()) {
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$empresa = $Clempresas->get($session['cod_empresa']);
if ($empresa) {
    $apikey = $empresa['api_key'];
    $tipoEmpresa = $empresa['cod_tipo_empresa'];
    $permisoRecordarOrdenes = $empresa['recordar_ordenes'];
}
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$casher_id = $session['cod_usuario'];
$cod_rol = $session['cod_rol'];

$sucursal_id = ($cod_rol <= 2) ? 0 : $session['cod_sucursal'];
$Clsucursales = new cl_sucursales(NULL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="plugins/carousel/owl.carousel.css" />
    <link rel="stylesheet" type="text/css" href="plugins/carousel/owl.theme.css" />
    <link rel="stylesheet" type="text/css" href="plugins/editors/quill/quill.snow.css">
    <link href="assets/css/apps/mailbox.css" rel="stylesheet" type="text/css" />

    <script src="plugins/sweetalerts/promise-polyfill.js"></script>
    <!--<link href="plugins/sweetalerts/sweetalert2-v11.min.css" rel="stylesheet" type="text/css" />-->
    <link href="
        https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css
        " rel="stylesheet">
    <!--<link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />-->
    <link href="plugins/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="bootstrap/css/custom-sidebar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/elements/tooltip.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        .indicator-box {
            cursor: pointer;
            padding: 5px 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 1px 3px 4px 1px;
        }

        #client-tab-couriers img {
            max-width: 40px;
        }

        .owl-carousel {
            display: block !important;
        }

        .owl-buttons div {
            position: absolute;
            top: 50%;
            border-radius: 50%;
            padding: 10px !important;
        }

        .owl-buttons .owl-prev {
            left: 0;
        }

        .owl-buttons .owl-next {
            right: 0;
        }

        .loader-sm {
            width: 22px;
            height: 22px;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
        }

        .offices-items {
            border: 1px solid blue;
            border-radius: 15px;
        }

        .client-detail-options {
            font-size: 20px;
            padding: 10px;
            border-radius: 5px;
            color: #484848;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .client-detail-options.active {
            background-color: #e5f1ff;
        }

        .client-detail-options:hover {
            background-color: #f2f6fb;
        }

        .clientInfo svg {
            width: 18px;
            height: 18px;
        }

        .clientInfo .name {
            font-size: 20px;
            font-weight: bold;
        }

        .lst-horarios {
            font-size: 18px;
        }

        .swal2-popup .swal2-select {
            width: 100% !important;
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
            /* background-color: #cdeaf3; */
            padding: 3px 10px;
            border-radius: 5px;
            margin-right: 5px;
            /* height: 40px; */
        }

        .tagsOrderList svg {
            width: 16px;
            height: 16px;
        }

        .stickyAgregarCarrito {
            position: sticky;
            bottom: 0px;
            height: 60px;
            padding: 5px 0px;
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

        .bloqueCitaInfo {
            height: 45px;
            /*border-bottom: solid 0.1px #e9e6e6;*/
        }

        .bloqueCitaInfo span {
            font-size: 11px;
            margin-bottom: 0px;
        }

        .bloqueCitaInfo svg {
            width: 35px;
            text-align: left;
            color: #c058c7;
        }

        .content-box {
            background-color: #f9f9f9;
            position: absolute;
            top: 0;
            height: 100%;
            width: 0px;
            left: auto;
            right: 0px;
            overflow: hidden;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .detalleOrden {
            overflow: scroll;
            height: calc(100vh - 234px);
        }

        .changeTipo.active {
            background-color: #2196f3 !important;
            z-index: 0 !important;
        }

        .unread-mail {
            font-weight: bolder;
        }

        .unread-mail .user-email {
            color: #bd4c4c !important;
        }

        .popover {
            z-index: 99999999999999999 !important;
        }

        .text-comments p {
            font-size: 12px !important;
        }

        .table-products-details tbody .product-name {
            font-size: 15px !important;
            font-weight: bold;
            color: black !important;
        }

        .img-content {
            height: 50px;
            overflow: hidden;
            text-align: center;
        }

        .img-content img {
            left: -100%;
            right: -100%;
            top: -100%;
            bottom: -100%;
            margin: auto;
            max-height: 100%;
            max-width: 100%;
        }

        .feather-16 {
            width: 16px;
            height: 16px;
        }

        .feather-18 {
            width: 18px;
            height: 18px;
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
    <div class="fixed-top bg-danger alert-box alert-box-disconect"
        style="display: none; z-index: 9999;">
        <div class="d-flex align-items-center justify-content-center" style="height: 70px;">
            <div>&nbsp;</div>
            <div style="font-size: 2.5vw;">
                No hay conectividad
            </div>
            <div onclick="disabledReagenda()" class="text-center" style="position: absolute; right: 0; font-size: 2.5vw; width: 50px; cursor: pointer;">
                X
            </div>
        </div>
    </div>

    <?php include('templates/gestion-ordenes-toast.html') ?>
    <div id="toastJcContent">
        <div class="position-fixed p-3 toast-bottom-right" style="z-index: 1040; right: 0; bottom: 0;"></div>
        <div class="position-fixed p-3 toast-bottom-left" style="z-index: 1040; left: 0; bottom: 0;"></div>
        <div class="position-fixed p-3 toast-top-right" style="z-index: 1040; right: 0; top: 0;"></div>
        <div class="position-fixed p-3 toast-top-left" style="z-index: 1040; left: 0; top: 0;"></div>
    </div>

    <!-- Modal Orden Detalle-->
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
                    <div class="stickyAgregarCarrito" id="orden-detalle-footer"></div>
                </div>
                <div class="modal-footer" style="display: none;">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cliente-->
    <div class="modal right fade" id="clientModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">Cliente Detalle</h5>
                </div>
                <div class="modal-body" style="padding: 0px 15px;">
                    <div class="x_content row" id="client-detail" style="height: 100%;"></div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tracking-->
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
                <div class="modal-footer" style="display:none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sucursal-->
    <div class="modal left fade" id="sucursalModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">Sucursal Detalle</h5>
                </div>
                <div class="modal-body" style="padding: 0px 15px;">
                    <div class="x_content row" id="sucursal-detail" style="height: 100%;"></div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Configuracion-->
    <div class="modal right fade" id="configModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">CONFIGURACION</h5>
                </div>
                <div class="modal-body" style="padding: 0px 15px;">
                    <div class="x_content row" style="height: 100%;" id="config-detail">

                    </div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL ANULACION -->
    <div class="modal fade" id="anulacionOrdenModal" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Anulando Orden</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        X
                    </button>
                </div>
                <div class="modal-body">
                    <div class="x_content">
                        <div>
                            <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_ccxfskpm.json" background="transparent" speed="1" loop autoplay></lottie-player>
                        </div>
                        <div class="d-flex align-items-start mb-3 text-success">
                            <div class="mr-2">
                                <i data-feather="check"></i>
                            </div>
                            <span style="font-size: 16px;">Orden anulada correctamente</span>
                        </div>
                        <div></div>

                        <script id="anular-detalle-template" type="text/x-handlebars-template">
                            {{#eq success 1}}
                                <div class="d-flex align-items-start mb-3 text-success">
                                    <div class="mr-2"><i data-feather="check"></i></div>
                                    <span style="font-size: 16px;">{{mensaje}}</span>
                                </div>
                                {{else}}
                                    <div class="d-flex align-items-start mb-3 text-danger">
                                        <div class="mr-2"><i data-feather="frown"></i></div>
                                        <span style="font-size: 16px;">{{mensaje}}</span>
                                    </div>
                            {{/eq}}
                        </script>

                        <div class="anularDetalle">
                            <div class="d-flex align-items-start mb-3 text-secondary d-lg-none" id="anula-pago-orden">
                                <div class="spinner-border loader-sm mr-2">
                                </div>
                                <span style="font-size: 16px;">Revirtiendo pago</span>
                            </div>
                            <div class="d-flex align-items-start mb-3 text-secondary d-lg-none" id="anula-factura-orden">
                                <div class="spinner-border loader-sm mr-2">
                                </div>
                                <span style="font-size: 16px;">Revirtiendo factura</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal HISTORIAL-->
    <div class="modal right fade" id="historialModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">HISTORIAL</h5>
                </div>
                <div class="modal-body" style="padding: 0px 15px; background-color: #f1f2f3;">
                    <script id="history-template" type="text/x-handlebars-template">
                        {{#each this}}
                            <div class="col-12 mt-2 history-order" style="cursor: pointer;" data-order="{{cod_orden}}">
                                <div class="card p-3">
                                    <div class="row">
                                        <div class="col-9">
                                            <h5>#{{cod_orden}} {{nombre}}</h5>
                                        </div>
                                        <div class="col-3 text-right">
                                            <span class="badge badge-{{colorStatus estado}}">{{estado}}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="img-content">
                                                        <img src="{{motorizado_foto}}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col-6 align-self-center">
                                                    <p class="m-0" style="font-size: 12px;">{{motorizado}}</p>
                                                    <p class="m-0" style="font-size: 10px;">{{estado_mensaje}}</p>
                                                </div>
                                                <div class="col-4 align-self-center text-right">
                                                    <a href="tel:{{motorizado_telefono}}"><i data-feather="phone"></i> {{motorizado_telefono}}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{/each}}
                    </script>
                    <div class="x_content row" id="history-detail"></div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Asignación Mis Motorizados-->
    <div class="modal left fade" id="MisMotorizadosModal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title text-center" style="width: 100%;">Mis Motorizados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <?php
                    include('templates/gestion-ordenes-mis-motorizados.html')
                    ?>
                    <div class="x_content" id="orden-mis-motorizados">
                    </div>
                </div>
                <div class="modal-footer" style="display:none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ordenes Anteriores -->
    <div class="modal fade" id="modalOrdenesAnteriores" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title text-center" style="width: 100%;">Órdenes antiguas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <?php
                    include('templates/gestion-errores/orden-lista.html')
                    ?>
                    <div class="container">
                        <div class="accordion mailbox-inbox">
                            <div class="message-box">
                                <div class="message-box-scroll" id="ordenes-antiguas"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnFinalizarOrdenes" onclick="prefinalizarOrdenesAntiguas()">Poner en entregadas</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Recipientes Orden-->
    <div class="modal left fade" id="recipientesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title text-center" style="width: 100%;">Recipientes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    include('templates/gestion-ordenes-recipientes.html')
                    ?>
                    <div class="x_content" id="orden-recipientes">
                    </div>
                </div>
                <div class="modal-footer" style="display:none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cierre Orden-->
    <div class="modal" id="cierreDiarioModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title text-center" style="width: 100%;">Cierre Diario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    include('templates/gestion-ordenes-cierre-diario.html')
                    ?>
                    <div class="x_content" id="cierre-diario">
                    </div>
                </div>
                <div class="modal-footer" style="display:none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ÓRDENES NO PROCESADAS -->
    <div class="modal fade" id="ordenesNoProcesadasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Órdenes no procesadas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                        include('templates/gestion-errores/orden-no-procesada.html');
                    ?>
                    <div id="ordenesNoProcesadas"></div>
                </div>
            </div>
        </div>
    </div>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <button style="display:none" class="linkCopied" data-clipboard-action="copy" data-clipboard-text="Hola">Copiedd</button>
    <div class="sub-header-container">
        <header class="header navbar navbar-expand-sm expand-header">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                <i data-feather="menu"></i>
            </a>

            <script id="office-selected" type="text/x-handlebars-template">
                <li onclick="getOfficeById()" style="cursor: pointer;">
                    <div style="text-transform: uppercase; font-size: 20px; font-weight: bold; color: #727272;">
                        {{nombre}}
                    </div>
                    <div class="text-right" style="font-size: 12px;">
                        {{#eq abierto true}}
                            <span class="text-success office-status">Abierto</span>
                        {{else}}
                            <span class="text-danger office-status">Cerrado</span>
                        {{/eq}}
                    </div>
                </li>
                <li class="ml-3" onclick="getOfficeById()">
                    <a href="javascript:void(0)"><i data-feather="power"></i></a>
                </li>
                {{#eq sucursal_input "0"}}
                    <li class="ml-3" onclick="openOfficesSelections();">
                        <a href="javascript:void(0)"><i data-feather="repeat"></i></a>
                    </li>
                {{/eq}}
            </script>
            <ul class="navbar-nav flex-row align-items-center office-selected-detail">
                <li style="cursor: pointer;">
                    <div style="text-transform: uppercase; font-size: 20px; font-weight: bold; color: #727272;">SUCURSAL NO ASIGNADA</div>
                </li>
                <li class="ml-3">
                    <a href="javascript:void(0)"><i data-feather="power"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav flex-row ml-auto ">
                <li class="bs-tooltip" data-placement="bottom" title="Usuarios Online" onclick="getAllUsersOnline()">
                    <i data-feather="user"></i>
                </li>
                <li class="bs-tooltip" data-placement="bottom" title="Órdenes programadas" onclick="getOrdenesProgramadas()">
                    <i data-feather="calendar"></i>
                </li>
                <li class="bs-tooltip" data-placement="bottom" title="Cierre Diario" onclick="getCierreDiario()">
                    <i data-feather="unlock"></i>
                </li>
                <li class="bs-tooltip" data-placement="bottom" title="Órdenes Antiguas" onclick="abrirOrdenesAntiguas()">
                    <i data-feather="rotate-ccw"></i>
                </li>
                <li class="bs-tooltip" data-placement="bottom" title="Notificaciones" onclick="abrirHistorial()">
                    <i data-feather="bell"></i>
                </li>
                <li class="bs-tooltip" data-placement="bottom" title="Configuraciones" onclick="abrirConfiguracion()">
                    <i data-feather="settings"></i>
                </li>
            </ul>
        </header>
    </div>


    <!--  END NAVBAR  -->
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
                                    <input id="alias_empresa" type="hidden" value="<?= $session['alias'] ?>">
                                    <input id="tipo_empresa" type="hidden" value="<?= $tipoEmpresa ?>">
                                    <input id="casher_id" type="hidden" value="<?= $casher_id ?>">
                                    <input type="hidden" placeholder="" name="cod_sucursal" id="cod_sucursal" value="<?php echo $sucursal_id; ?>" />
                                </div>
                            </div>

                            <div class="col-xl-12  col-md-12">
                                <div class="d-flex mb-3 mt-2">
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="preordenesErrors">-</div>
                                        <div>Ordenes <br /> con error</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="">-</div>
                                        <div>Ordenes <br /> no facturadas</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesErrorFacturacion">-</div>
                                        <div>Facturas <br /> con error</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesErrorAsignacion">-</div>
                                        <div>Courier <br /> error asignación</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesCanceledByCourier">-</div>
                                        <div>Pedidos cancelados <br /> por el courier</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesCanceledByUser">-</div>
                                        <div>Pedidos cancelados <br /> por el Cajero</div>
                                    </div>
                                    <div class="d-flex align-items-center mr-3 indicator-box" onclick="getOrdenesNoProcesadas()">
                                        <div class="mr-2" style="font-size: 45px;" id="ordenesNoProcesadasValue">0</div>
                                        <div>Órdenes no <br /> procesadas</div>
                                    </div>
                                </div>
                                <div class="mail-box-container">
                                    <div class="mail-overlay"></div>
                                    <div class="tab-title">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-12 text-center mail-btn-container">
                                                <a id="btn-compose-mail" class="btn btn-block" href="javascript:void(0);" onclick="apagarSirena()"><i data-feather="volume-x"></i></a>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-12 mail-categories-container">

                                                <div class="mail-sidebar-scroll">
                                                    <script id="status-list" type="text/x-handlebars-template">
                                                        {{#each this}}
                                                            <li class="nav-item">
                                                                <a class="nav-link list-actions envio-{{is_envio}}" id="{{id}}"><i data-feather="{{icono}}"></i><span class="nav-names">{{nombre}}</span> <span class="mail-badge badge badge-{{id}}"></span></a>
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
                                            <div class="d-lg-none">
                                                <i data-feather="menu" class="mail-menu"></i>
                                            </div>
                                            <div class="">
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="button" class="changeTipo btn btn-outline-info active" data-value="todas">Todas</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="delivery">Envíos</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="pickup">Pickup</button>
                                                </div>
                                                <input type="hidden" name="" id="is_envio" value="todas">
                                            </div>

                                            <div class="dropdown ml-auto">
                                            </div>
                                        </div>

                                        <?php
                                        include('templates/gestion-ordenes-orden-lista-v3.html')
                                        ?>
                                        <div class="message-box">
                                            <script id="lottieAnimation" type="text/x-handlebars-template">
                                                <h3 class="text-center mt-3">{{text}}</h3>
                                                <lottie-player src="{{animation}}" background="transparent" speed="1" loop autoplay></lottie-player>
                                            </script>
                                            <div class="message-box-scroll" id="lista_ordenes">
                                            </div>
                                        </div>

                                        <div class="content-box">
                                            <div class="d-flex msg-close">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left close-message">
                                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                                    <polyline points="12 19 5 12 12 5"></polyline>
                                                </svg>
                                                <h2 class="mail-title" data-selectedMailTitle="">Orden</h2>
                                            </div>
                                            <?php
                                            include('templates/gestion-ordenes-orden-detalle.html')
                                            ?>
                                            <div id="orden-detalle"></div>
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
        Handlebars.registerHelper('select', function(value, options) {
            var $el = $('<select />').html(options.fn(this));
            $el.find('[value="' + value + '"]').attr({
                'selected': 'selected'
            });
            return $el.html();
        });
        Handlebars.registerHelper('times', function(block) {
            var accum = '';
            for (var i = 1; i <= 15; ++i)
                accum += block.fn(i);
            return accum;
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
        Handlebars.registerHelper('reverse', function(arreglo) {
            return arreglo.reverse();
        });
        Handlebars.registerHelper('count', function(arrayElement) {
            return arrayElement.length;
        });
        Handlebars.registerHelper('in_array', function(arg1, arg2, options) {
            for (var x = 0; x < arg1.length; x++) {
                if (arg1[x] === arg2) {
                    return options.fn(this);
                }
            }
            return options.inverse(this);
        });
    </script>
    <script type="text/javascript" src="plugins/carousel/owl.carousel.min.js" defer></script>
    <?php js_mandatory(); ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/ie11fix/fn.fix-padStart.js"></script>
    <script src="plugins/editors/quill/quill.js"></script>
    <!--<script src="plugins/sweetalerts/sweetalert2-v11.min.js"></script>-->
    <script src="
        https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js
        "></script>
    <script src="plugins/notification/snackbar/snackbar.min.js"></script>
    <script src="plugins/ion.sound/ion.sound.js"></script>
    <!--<script src="assets/js/apps/custom-mailbox.js"></script>-->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>

    <script src="bootstrap/js/popper.min.js"></script>

    <script src='plugins/toastr/toastr.min.js'></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/gestion-errores/sounds.js" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/index.js?v=4" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/facturacion.js?v=1" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/tracking.js?v=1" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/recipientes.js?v=1" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/mis-motorizados.js" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/client.js" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/office.js" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/printers.js?v=0" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/firebase.js?v=3" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/cierre-diario.js?v=1" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/ordenes-programadas.js" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/toastJc.js?v=1" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/autoasignar.js?v=2" type="text/javascript"></script>
    <script src="assets/js/gestion-errores/ordenes-no-procesadas.js" type="text/javascript"></script>
    <script src="assets/js/pages/gestion_ordenes_fidelizacion.js?v=1" type="text/javascript"></script>
    <!-- <script src="assets/js/pages/gestion_ordenes_impresion.js?v=1" type="text/javascript"></script> -->
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <script src="assets/js/pages/notificaciones.js" type="text/javascript"></script>
    <script>
        $("#txt_descripcion").emojioneArea({
            container: "#containerEmoji",
            hideSource: false,
        });
        /*
        let d = new Date;
        let idGO = d.getTime();

        const bc = new BroadcastChannel("gestion-errores");
        bc.onmessage = (event) => {
            console.log("BroadcastChannel Messages",event);
            if (event.data !== idGO) {
                messageDone('Ya se está ejecutando una instancia de este sitio','error');
                window.location = "index.php"; //Enviar a una pagina de error
            }
        };
        bc.postMessage(idGO);*/
    </script>
</body>

</html>