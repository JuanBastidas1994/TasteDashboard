<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
$session = getSession();

$ClEmpresas = new cl_empresas();
$empresa = $ClEmpresas->get($session["cod_empresa"]);
if($empresa) {
    $apikey = $empresa["api_key"];
}

$permisoAnularFacturas = $ClEmpresas->tienePermiso($session["cod_empresa"], 'ANULAR_FACTURAS') ? 1 : 0;

if(!isLogin()){
    header("location:login.php");
}

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gb18030">
    <?php css_mandatory(); ?>
    <style type="text/css">
        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 0;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
            z-index: 50;
        }
        #style-3_wrapper .dataTables_info,
        #style-3_wrapper .dataTables_paginate {
            margin-top: 10px;
        }
    </style>
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
                                    <h4>Listado de Facturas</h4>
                                    <input type="hidden" id="apiEmpresa" value="<?=$apikey?>">
                                    <input type="hidden" id="permisoAnularFacturas" value="<?=$permisoAnularFacturas?>">
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-lg-2 col-12">
                                        <label>Sucursal</label>
                                        <select id="cmbSucursal" class="form-control">
                                            <option value="">Todas</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12">
                                        <label>Fecha inicio</label>
                                        <input type="date" id="fecha_inicio" class="form-control picker">
                                    </div>
                                    <div class="col-lg-2 col-12">
                                        <label>Fecha fin</label>
                                        <input type="date" id="fecha_fin" class="form-control picker">
                                    </div>
                                    <div class="col-lg-2 col-12">
                                        <label>Cliente</label>
                                        <input type="text" id="txtCliente" class="form-control" placeholder="Nombre del cliente">
                                    </div>
                                    <div class="col-lg-1 col-12">
                                        <label>Estado</label>
                                        <select id="cmbEstado" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="ENVIADA">Enviadas</option>
                                            <option value="NO_ENVIADA">No enviadas</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12">
                                        <label>Buscar en tabla</label>
                                        <input type="text" id="txtBuscarTabla" class="form-control" placeholder="Buscar en lo cargado...">
                                    </div>
                                    <div class="col-lg-1 col-12 align-items-end d-flex">
                                        <button class="btn btn-primary" onclick="getFacturasUnificadas();">Filtrar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 my-3">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="badge badge-success" style="font-size:14px;">
                                            Enviadas: <span id="statEnviadas">0</span>
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge badge-danger" style="font-size:14px;">
                                            No enviadas: <span id="statNoEnviadas">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table id="style-3" class="table style-3  table-hover" style="margin-top: 0px !important;">
                                        <thead>
                                            <tr>
                                                <th>Orden</th>
                                                <th>Sucursal</th>
                                                <th>Cliente</th>
                                                <th>Documento</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Forma de pago</th>
                                                <th class="text-center">Electrónica</th>
                                                <th class="text-center">Inventario</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- LISTA DE FACTURAS -->
                                        </tbody>
                                    </table>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <div class="sticky-actions">
                <button class="btn btn-danger" onclick="reenviarPendientes();">
                    <i data-feather="send"></i>
                    Reenviar pendientes del rango
                </button>
                <button class="btn btn-primary" onclick="descargarExcel();">
                    <i data-feather="download"></i>
                    Descargar Excel
                </button>
            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <div class="modal fade" id="errorFacturaModal" tabindex="-1" role="dialog" aria-labelledby="errorFacturaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorFacturaModalLabel">Motivo del error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="errorFacturaTexto"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="inventarioModal" tabindex="-1" role="dialog" aria-labelledby="inventarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inventarioModalLabel">Detalle de inventario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="inventarioOrigenTexto" class="text-muted mb-3"></p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody id="inventarioTablaBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCopiarJsonInventario">Copiar JSON</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php js_mandatory(); ?>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <script src="./assets/js/pages/facturas_pendientes.js"></script>

    <!-- TEMPLATES -->
    <script id="sucursales-template" type="text/x-handlebars-template">
        {{#each this}}
            <option value="{{cod_sucursal}}">{{nombre}}</option>
        {{/each}}
    </script>
    <script id="facturas-unificadas-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr>
                <td>{{cod_orden}}</td>
                <td>{{sucursal}}</td>
                <td>{{cliente}}</td>
                <td>{{#if num_factura}}{{num_factura}}{{else}}-{{/if}}</td>
                <td>{{fecha}}</td>
                <td>${{decimal total}}</td>
                <td>{{#if formas_pago}}{{formas_pago}}{{else}}-{{/if}}</td>
                <td class="text-center">
                    <span class="bs-tooltip" data-toggle="tooltip" data-placement="top" data-original-title="{{electronica.title}}">
                        <i data-feather="{{electronica.icono}}" class="text-{{electronica.clase}}"></i>
                    </span>
                </td>
                <td class="text-center">
                    {{#if inventario}}
                    <span class="bs-tooltip" data-toggle="tooltip" data-placement="top" data-original-title="{{inventario.title}}">
                        <i data-feather="{{inventario.icono}}" class="text-{{inventario.clase}}"></i>
                    </span>
                    {{else}}
                    <span class="text-muted bs-tooltip" data-toggle="tooltip" data-placement="top" data-original-title="Sin movimiento de inventario asociado">-</span>
                    {{/if}}
                    {{#if estado_inventario}}
                    <a href="javascript:void(0);" class="bs-tooltip btnVerInventario" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Ver detalle de inventario">
                        <i data-feather="list"></i>
                    </a>
                    {{/if}}
                </td>
                <td class="text-center">
                    <ul class="table-controls">
                        <li>
                            <a href="./orden_detalle.php?id={{cod_orden}}" target="_blank">
                                <i data-feather="eye"></i>
                            </a>
                            {{#if permisoAnularFacturas}}
                            {{#eq estado_envio "NO_ENVIADA"}}
                            <a href="javascript:void(0);" class="bs-tooltip btnReenviar" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Reenviar factura">
                                <i data-feather="refresh-cw"></i>
                            </a>
                            {{/eq}}
                            {{#if ultimo_error}}
                            <a href="javascript:void(0);" class="bs-tooltip btnVerError text-danger" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Ver motivo del error">
                                <i data-feather="alert-triangle"></i>
                            </a>
                            {{/if}}
                            {{#if puede_anular}}
                            <a href="javascript:void(0);" class="bs-tooltip btnAnular" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Anular factura">
                                <i data-feather="x-circle"></i>
                            </a>
                            {{/if}}
                            {{#eq estado_envio "ENVIADA"}}
                            {{#ifIn estado_inventario (array "NO_DEBITADO" "NO_APLICA")}}
                            <a href="javascript:void(0);" class="bs-tooltip btnReintentarInventario" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Verificar / reintentar inventario">
                                <i data-feather="package"></i>
                            </a>
                            {{/ifIn}}
                            {{/eq}}
                            {{#eq estado_inventario "NO_REVERTIDO"}}
                            <a href="javascript:void(0);" class="bs-tooltip btnReintentarReversionInventario" data-id="{{cod_orden}}" data-toggle="tooltip" data-placement="top" data-original-title="Reintentar reversión de inventario">
                                <i data-feather="rotate-ccw"></i>
                            </a>
                            {{/eq}}
                            {{/if}}
                        </li>
                    </ul>
                </td>
            </tr>
        {{/each}}
    </script>
</body>
</html>
