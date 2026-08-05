<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if (!isLogin()) {
    header("location:login.php");
    exit;
}

$Clempresas = new cl_empresas(NULL);
$session = getSession();
$empresa = $Clempresas->get($session['cod_empresa']);
$apikey = $empresa ? $empresa['api_key'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <?php css_mandatory(); ?>
    <style type="text/css">

    .badgeCustom{
        padding: 3px 5px;
        border-radius: 25px;
        font-size: 11px;
        font-weight: bold;
    }

    .flota-board {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 12px;
        align-items: flex-start;
    }

    .flota-column {
        background: #f4f4f4;
        border-radius: 10px;
        flex: 0 0 85vw;
        max-width: 340px;
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        max-height: 78vh;
    }

    @media (min-width: 900px) {
        .flota-column { flex: 1 1 0; max-width: none; }
    }

    .flota-column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px 10px;
    }

    .flota-column-header h5 { margin: 0; font-weight: 600; }

    .flota-column-body {
        overflow-y: auto;
        padding: 0 12px 12px;
        flex: 1;
        min-height: 80px;
    }

    .flota-column-empty {
        color: #999;
        font-size: 13px;
        text-align: center;
        padding: 20px 0;
    }

    .flota-card {
        background: white;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        transition: transform .15s, box-shadow .15s;
    }

    .flota-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,.12);
    }

    .flota-card .flota-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .flota-card .flota-card-empresa {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .flota-card .flota-card-empresa img {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        object-fit: cover;
    }

    .flota-card .flota-card-cliente { font-size: 12px; color: #666; }

    .flota-modal-dialog {
        max-width: 1300px;
        width: 95%;
    }

    @media (min-width: 1600px) {
        .flota-modal-dialog { max-width: 1500px; }
    }

    .flota-moto-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: white;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }

    .flota-moto-card-main { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .flota-moto-card-info { min-width: 0; }
    .flota-moto-card-info .info-primary { white-space: nowrap; text-overflow: ellipsis; overflow: hidden; font-weight: 600; }
    .flota-moto-card-info .info-secondary { font-size: 11px; color: #777; }
    .flota-moto-card-actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }

    .btn-icon-sutil {
        border: none;
        background: transparent;
        color: #888;
        padding: 4px;
        border-radius: 6px;
        line-height: 0;
        cursor: pointer;
    }

    .btn-icon-sutil:hover { background: #f0f0f0; color: #444; }
    .btn-icon-sutil:disabled { opacity: .35; cursor: not-allowed; }
    .btn-icon-sutil svg { width: 16px; height: 16px; }

    .flota-timeline { padding: 8px 4px; }
    .flota-timeline-step { display: flex; gap: 14px; }
    .flota-timeline-step .flota-timeline-line-wrap { display: flex; flex-direction: column; align-items: center; width: 20px; }
    .flota-timeline-dot { width: 16px; height: 16px; border-radius: 50%; background: #e0e0e0; flex-shrink: 0; }
    .flota-timeline-step.complete .flota-timeline-dot { background: #6777ef; }
    .flota-timeline-step.current .flota-timeline-dot { background: #6777ef; box-shadow: 0 0 0 4px rgba(103,119,239,.2); }
    .flota-timeline-line { width: 2px; flex: 1; background: #e0e0e0; min-height: 28px; }
    .flota-timeline-step.complete .flota-timeline-line { background: #6777ef; }
    .flota-timeline-content { padding-bottom: 24px; }
    .flota-timeline-title { font-weight: 600; }
    .flota-timeline-step:not(.complete) .flota-timeline-title { color: #999; font-weight: 400; }
    .flota-timeline-fecha { font-size: 12px; color: #999; }

    .flota-marker-label {
        background: #fff;
        padding: 1px 5px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,.3);
        white-space: nowrap;
    }

    </style>
</head>
<body>

    <!-- MODAL DETALLE DE PEDIDO -->
    <div class="modal fade bs-example-modal-lg" id="pedidoDetailModal" tabindex="99" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl flota-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" id="pedidoDetailTitle" style="width: 100%;">Pedido</h5>
                </div>

                <div class="modal-body" style="padding: 0px 15px;">
                    <ul class="nav nav-tabs mt-2" id="pedidoTabsNav">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-info">Información</a></li>
                        <li class="nav-item" id="navAsignacionItem"><a class="nav-link" data-toggle="tab" href="#tab-asignacion">Asignación</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-historial">Historial</a></li>
                    </ul>

                    <div class="tab-content pt-3">
                        <!-- INFORMACION -->
                        <div class="tab-pane fade show active" id="tab-info">
                            <div id="pedido-info"></div>
                            <script id="pedido-info-template" type="text/x-handlebars-template">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <h5>Lugar de Retiro</h5>
                                        <div class="d-flex align-items-center mb-2">
                                            {{#if empresa_logo}}<img src="{{empresa_logo}}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:8px;">{{/if}}
                                            <strong>{{empresa_nombre}}</strong>
                                        </div>
                                        <div>Sucursal: {{sucursal_nombre}}</div>
                                        <div>Dirección: {{sucursal_direccion}}</div>
                                        <div><a href="tel:{{sucursal_telefono}}">Teléfono: {{sucursal_telefono}}</a></div>
                                        <div class="mt-1">
                                            <a href="https://www.google.com/maps?q={{sucursal_lat}},{{sucursal_lon}}" target="_blank"><i data-feather="map-pin"></i> Ver en mapa</a>
                                        </div>

                                        <h5 class="mt-4">Cliente</h5>
                                        <div>{{cliente_nombre}} {{cliente_apellido}}</div>
                                        <div><a href="tel:{{telefono}}">Teléfono: {{telefono}}</a></div>
                                        {{#if referencia}}<div>Referencia: {{referencia}}</div>{{/if}}
                                        <div class="mt-1">
                                            <a href="https://www.google.com/maps?q={{cliente_lat}},{{cliente_lon}}" target="_blank"><i data-feather="map-pin"></i> Ver en mapa</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <h5>Forma de Pago</h5>
                                        <div>{{pago_label}} — ${{pago.monto}}</div>

                                        <h5 class="mt-4">Ítems</h5>
                                        <table style="width: 100%;">
                                            <tbody>
                                                {{#each items}}
                                                <tr>
                                                    <td>{{cantidad}}x {{nombre}}</td>
                                                    <td class="text-right">${{precio_final}}</td>
                                                </tr>
                                                {{/each}}
                                            </tbody>
                                        </table>

                                        {{#if incidencia_pendiente}}
                                        <div class="mt-4" style="background:#FEF3C7; border-radius:8px; padding:12px;">
                                            <h5 style="color:#B45309; margin-top:0;"><i data-feather="alert-triangle" style="width:16px;height:16px;"></i> Problema reportado</h5>
                                            <div><b>Motivo:</b> {{motivoLabel incidencia_pendiente.motivo}}</div>
                                            {{#if incidencia_pendiente.comentario}}<div><b>Comentario del motorizado:</b> {{incidencia_pendiente.comentario}}</div>{{/if}}
                                            <div class="text-muted" style="font-size:12px;">Reportado: {{fechaFormateada incidencia_pendiente.fecha_reporte}}</div>

                                            <div class="mt-2">
                                                <button class="btn btn-success btn-sm" id="btnResolverIncidencia" type="button">Ya se resolvió, continúa</button>
                                                <button class="btn btn-danger btn-sm" id="btnMostrarCancelar" type="button">Cancelar pedido (no entregado)</button>
                                            </div>

                                            <div id="panelCancelarPedido" style="display:none; margin-top:12px; border-top:1px solid #FDE68A; padding-top:12px;">
                                                <div class="form-group">
                                                    <label class="d-block"><input type="radio" name="motivoCancelar" value="NO_CONTESTA"> El cliente no contesta</label>
                                                    <label class="d-block"><input type="radio" name="motivoCancelar" value="DIRECCION_INCORRECTA"> La dirección es incorrecta</label>
                                                    <label class="d-block"><input type="radio" name="motivoCancelar" value="CLIENTE_RECHAZO"> El cliente no quiso recibir el pedido</label>
                                                    {{#if esEfectivo}}<label class="d-block"><input type="radio" name="motivoCancelar" value="NO_QUISO_PAGAR"> El cliente no quiso pagar</label>{{/if}}
                                                </div>
                                                <textarea id="comentarioCancelar" class="form-control" rows="2" placeholder="Comentario (opcional)"></textarea>
                                                <button class="btn btn-danger btn-sm mt-2" id="btnConfirmarCancelar" type="button">Confirmar cancelación</button>
                                            </div>
                                        </div>
                                        {{/if}}

                                        <h5 class="mt-4">Motorizado</h5>
                                        {{#if cod_motorizado}}
                                            <div class="d-flex align-items-center">
                                                {{#if motorizado_imagen}}
                                                    <img src="{{motorizado_imagen}}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;margin-right:10px;">
                                                {{else}}
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:#e5e7eb;margin-right:10px;"><i data-feather="user"></i></div>
                                                {{/if}}
                                                <div>
                                                    <div>{{motorizado_nombre}} {{motorizado_apellido}}</div>
                                                    <div><a href="tel:{{motorizado_telefono}}">{{motorizado_telefono}}</a> · Placa {{motorizado_placa}}</div>
                                                </div>
                                            </div>
                                            {{#unless entregada}}
                                                <div class="mt-2">
                                                    <button class="btn btn-warning" id="btnQuitarAsignacion">Quitar asignación</button>
                                                </div>
                                            {{/unless}}
                                        {{else}}
                                            <div>Sin motorizado asignado todavía.</div>
                                            {{#unless entregada}}
                                                <div class="mt-2">
                                                    <button class="btn btn-primary" type="button" onclick="irATabAsignacion()">Asignar</button>
                                                </div>
                                            {{/unless}}
                                        {{/if}}
                                    </div>
                                </div>
                            </script>
                        </div>

                        <!-- ASIGNACION: solo tiene sentido si el pedido aún no tiene motorizado -->
                        <div class="tab-pane fade" id="tab-asignacion">
                            <div class="row">
                                <div class="col-lg-9 col-12">
                                    <div id="mapaPedido" style="width:100%; height:60vh; min-height:420px; border-radius:8px;"></div>
                                </div>
                                <div class="col-lg-3 col-12 mt-3 mt-lg-0">
                                    <h5>Motorizados de tu flota</h5>
                                    <input type="text" id="filtroMotorizado" class="form-control form-control-sm mb-2" placeholder="Buscar motorizado...">
                                    <div id="lista-motorizados-cercanos" style="max-height:calc(60vh - 20px); min-height:200px; overflow-y:auto;"></div>
                                    <script id="motorizados-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                        <div class="flota-moto-card">
                                            <div class="flota-moto-card-main">
                                                {{#if imagen}}
                                                    <img src="{{imagen}}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;">
                                                {{else}}
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#e5e7eb;"><i data-feather="user"></i></div>
                                                {{/if}}
                                                <div class="flota-moto-card-info">
                                                    <div class="info-primary">{{nombres}}</div>
                                                    <div class="info-secondary"><a href="tel:{{telefono}}"><i data-feather="phone"></i> {{telefono}}</a></div>
                                                    <div class="info-secondary mt-1">
                                                        <span class="badgeCustom outline-badge-{{badgeEstado estado_trabajo}}">{{labelEstado estado_trabajo}}</span>
                                                        {{#if es_invitado}}<span class="badgeCustom" style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">Invitado</span>{{/if}}
                                                    </div>
                                                    <div class="info-secondary mt-1"><i data-feather="clock"></i> {{fechaRelativa fecha_ubicacion}}</div>
                                                </div>
                                            </div>
                                            <div class="flota-moto-card-actions">
                                                <button type="button" class="btn-icon-sutil" title="{{#if_eq estado_trabajo "no_disponible"}}Sin ubicación en el mapa{{else}}Ver en el mapa{{/if_eq}}" {{#if_eq estado_trabajo "no_disponible"}}disabled{{/if_eq}} onclick="centrarMotoEnMapa({{id}}, event)"><i data-feather="map-pin"></i></button>
                                                <button type="button" class="btn-icon-sutil" title="Ver detalles" onclick="verDetalleMoto({{id}}, event)"><i data-feather="eye"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="asignarDesdeMapa({{id}}, event)">Asignar</button>
                                            </div>
                                        </div>
                                        {{else}}
                                        <div class="flota-column-empty">Sin motorizados</div>
                                        {{/each}}
                                    </script>
                                </div>
                            </div>
                        </div>

                        <!-- HISTORIAL -->
                        <div class="tab-pane fade" id="tab-historial">
                            <div id="pedido-historial"></div>
                            <script id="pedido-historial-template" type="text/x-handlebars-template">
                                <div class="flota-timeline">
                                    {{#each this}}
                                    <div class="flota-timeline-step {{#if complete}}complete{{/if}} {{#if current}}current{{/if}}">
                                        <div class="flota-timeline-line-wrap">
                                            <div class="flota-timeline-dot"></div>
                                            {{#unless @last}}<div class="flota-timeline-line"></div>{{/unless}}
                                        </div>
                                        <div class="flota-timeline-content">
                                            <div class="flota-timeline-title">{{titulo}}</div>
                                            <div class="flota-timeline-fecha">{{fecha}}</div>
                                        </div>
                                    </div>
                                    {{/each}}
                                </div>
                            </script>

                            <div id="pedido-historial-incidencias" class="mt-3"></div>
                            <script id="pedido-historial-incidencias-template" type="text/x-handlebars-template">
                                {{#if this.length}}
                                <h5>Problemas reportados</h5>
                                {{#each this}}
                                <div class="mb-3 pb-2" style="border-bottom:1px solid #eee;">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{motivoLabel motivo}}</strong>
                                        <span class="text-muted" style="font-size:12px;">{{fechaFormateada fecha_reporte}}</span>
                                    </div>
                                    <div style="font-size:13px;">Reportado por: {{motorizado_nombre}} {{motorizado_apellido}}</div>
                                    {{#if comentario}}<div style="font-size:13px;">"{{comentario}}"</div>{{/if}}
                                    {{#if_eq estado "PENDIENTE"}}
                                        <span class="badgeCustom outline-badge-warning">Pendiente de revisión</span>
                                    {{else}}
                                        <div style="font-size:13px; margin-top:4px;">
                                            <span class="badgeCustom outline-badge-{{#if_eq estado "CONFIRMADA"}}danger{{else}}success{{/if_eq}}">
                                                {{#if_eq estado "CONFIRMADA"}}Pedido cancelado{{else}}Resuelto, continuó{{/if_eq}}
                                            </span>
                                            por {{resueltoPorLabel resuelto_por}} · {{fechaFormateada fecha_resolucion}}
                                            {{#if comentario_resolucion}}— "{{comentario_resolucion}}"{{/if}}
                                        </div>
                                    {{/if_eq}}
                                </div>
                                {{/each}}
                                {{/if}}
                            </script>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Omitir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL UBICACIÓN DE MIS MOTORIZADOS: mismo mapa que la pestaña Asignación, pero de solo
         lectura y sin pedido de por medio — para ver dónde está cada quien en cualquier momento. -->
    <div class="modal fade" id="modalUbicacionMotorizados" tabindex="99" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl flota-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">Ubicación de mis motorizados</h5>
                </div>

                <div class="modal-body" style="padding: 15px;">
                    <div class="row">
                        <div class="col-lg-9 col-12">
                            <div id="mapaUbicacionMotorizados" style="width:100%; height:65vh; min-height:420px; border-radius:8px;"></div>
                        </div>
                        <div class="col-lg-3 col-12 mt-3 mt-lg-0">
                            <h5>Motorizados de tu flota</h5>
                            <input type="text" id="filtroMotorizadoUbicacion" class="form-control form-control-sm mb-2" placeholder="Buscar motorizado...">
                            <div id="lista-motorizados-ubicacion" style="max-height:calc(65vh - 20px); min-height:200px; overflow-y:auto;"></div>
                            <script id="motorizados-ubicacion-template" type="text/x-handlebars-template">
                                {{#each this}}
                                <div class="flota-moto-card">
                                    <div class="flota-moto-card-main">
                                        {{#if imagen}}
                                            <img src="{{imagen}}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;">
                                        {{else}}
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#e5e7eb;"><i data-feather="user"></i></div>
                                        {{/if}}
                                        <div class="flota-moto-card-info">
                                            <div class="info-primary">{{nombres}}</div>
                                            <div class="info-secondary"><a href="tel:{{telefono}}"><i data-feather="phone"></i> {{telefono}}</a></div>
                                            <div class="info-secondary mt-1">
                                                <span class="badgeCustom outline-badge-{{badgeEstado estado_trabajo}}">{{labelEstado estado_trabajo}}</span>
                                                {{#if es_invitado}}<span class="badgeCustom" style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">Invitado</span>{{/if}}
                                            </div>
                                            <div class="info-secondary mt-1"><i data-feather="clock"></i> {{fechaRelativa fecha_ubicacion}}</div>
                                        </div>
                                    </div>
                                    <div class="flota-moto-card-actions">
                                        <button type="button" class="btn-icon-sutil" title="{{#if_eq estado_trabajo "no_disponible"}}Sin ubicación en el mapa{{else}}Ver en el mapa{{/if_eq}}" {{#if_eq estado_trabajo "no_disponible"}}disabled{{/if_eq}} onclick="centrarMotoEnMapaUbicacion({{id}}, event)"><i data-feather="map-pin"></i></button>
                                        <button type="button" class="btn-icon-sutil" title="Ver detalles" onclick="verDetalleMoto({{id}}, event)"><i data-feather="eye"></i></button>
                                    </div>
                                </div>
                                {{else}}
                                <div class="flota-column-empty">Sin motorizados</div>
                                {{/each}}
                            </script>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE DE MOTORIZADO: dónde está y qué pedido lleva ahora — no es la pantalla de
         estadísticas/histórico (eso queda para una pantalla aparte). Se abre desde el botón del ojo,
         tanto en la pestaña Asignación como en el modal de Ubicación de arriba. -->
    <div class="modal fade" id="modalDetalleMoto" tabindex="99" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" id="detalleMotoTitle" style="width: 100%;">Motorizado</h5>
                </div>

                <div class="modal-body">
                    <div id="detalleMotoContenido"></div>
                    <script id="detalle-moto-template" type="text/x-handlebars-template">
                        <div class="d-flex align-items-center mb-3">
                            {{#if imagen}}
                                <img src="{{imagen}}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;margin-right:12px;">
                            {{else}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#e5e7eb;margin-right:12px;"><i data-feather="user"></i></div>
                            {{/if}}
                            <div>
                                <div style="font-weight:600;">{{nombres}}</div>
                                <div><a href="tel:{{telefono}}">{{telefono}}</a> · Placa {{placa}}</div>
                                <span class="badgeCustom outline-badge-{{badgeEstado estado_trabajo}}">{{labelEstado estado_trabajo}}</span>
                            </div>
                        </div>

                        <div id="mapaDetalleMoto" style="width:100%; height:220px; border-radius:8px; margin-bottom:12px; background:#f4f4f4;"></div>
                        <div class="mb-3" style="font-size:13px; color:#777;"><i data-feather="clock"></i> Última ubicación: {{fechaRelativa fecha_ubicacion}}</div>

                        <h5>Pedido(s) activo(s)</h5>
                        {{#if pedidos_actuales.length}}
                            {{#each pedidos_actuales}}
                            <div class="flota-card" onclick="cerrarDetalleYAbrirPedido({{cod_orden}})">
                                <div class="flota-card-top">
                                    <strong>#{{cod_orden}}</strong>
                                    <span class="badgeCustom outline-badge-info">{{sub_estado}}</span>
                                </div>
                                <div class="flota-card-empresa">{{empresa_nombre}} — {{sucursal_nombre}}</div>
                                <div class="flota-card-cliente">{{cliente_nombre}} · ${{total}}</div>
                            </div>
                            {{/each}}
                        {{else}}
                            <div class="flota-column-empty">Sin pedido activo</div>
                        {{/if}}
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <?php echo navbar(); ?>
    <!--  END NAVBAR  -->

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php echo sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">
                    <input id="apikey_flota" type="hidden" value="<?= htmlspecialchars($apikey) ?>">
                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 0px; align-items: flex-end;">
                                <div style="flex: 1; min-width: 220px;">
                                    <label for="selectComercio">Filtrar por comercio:</label>
                                    <select id="selectComercio" style="width:100%">
                                        <option value=""></option>
                                        <?php
                                        $comercios = $Clempresas->getComercios($session['cod_empresa']);
                                        foreach ($comercios as $c) {
                                            $img = url_sistema . 'assets/empresas/' . $c['alias'] . '/' . $c['logo'];
                                            echo "<option value='{$c['cod_empresa']}' data-image_path='{$img}'>{$c['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="flex: 1; min-width: 220px;">
                                    <label for="selectMotorizado">Filtrar por motorizado:</label>
                                    <select id="selectMotorizado" style="width:100%">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary" id="btnUbicacionMotorizados">
                                        <i data-feather="map-pin"></i> Ubicación de mis motorizados
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flota-board">
                            <div class="flota-column" style="background:#FEF2F2;">
                                <div class="flota-column-header">
                                    <h5>⚠ Con problema</h5>
                                    <span class="badge badge-danger" id="count-incidencia">0</span>
                                </div>
                                <div class="flota-column-body" id="col-incidencia"></div>
                            </div>
                            <div class="flota-column">
                                <div class="flota-column-header">
                                    <h5>Nuevo / Pendiente</h5>
                                    <span class="badge badge-primary" id="count-pendiente">0</span>
                                </div>
                                <div class="flota-column-body" id="col-pendiente"></div>
                            </div>
                            <div class="flota-column">
                                <div class="flota-column-header">
                                    <h5>En curso</h5>
                                    <span class="badge badge-warning" id="count-en_curso">0</span>
                                </div>
                                <div class="flota-column-body" id="col-en_curso"></div>
                            </div>
                            <div class="flota-column">
                                <div class="flota-column-header">
                                    <h5>Enviando</h5>
                                    <span class="badge badge-info" id="count-enviando">0</span>
                                </div>
                                <div class="flota-column-body" id="col-enviando"></div>
                            </div>
                            <div class="flota-column">
                                <div class="flota-column-header">
                                    <h5>Entregada (hoy)</h5>
                                    <span class="badge badge-success" id="count-entregada">0</span>
                                </div>
                                <div class="flota-column-body" id="col-entregada"></div>
                            </div>
                        </div>

                        <script id="flota-card-template" type="text/x-handlebars-template">
                            {{#each items}}
                            <div class="flota-card" data-cod_orden="{{cod_orden}}" onclick="openPedido({{cod_orden}})">
                                <div class="flota-card-top">
                                    <strong>#{{cod_orden}}</strong>
                                    <span class="badgeCustom outline-badge-{{../colorColumna}}">{{sub_estado}}</span>
                                </div>
                                <div class="flota-card-empresa">
                                    {{#if empresa_logo}}<img src="{{empresa_logo}}">{{/if}}
                                    {{empresa_nombre}} — {{sucursal_nombre}}
                                </div>
                                <div class="flota-card-cliente">{{cliente_nombre}} · ${{total}}</div>
                                {{#if motorizado_nombre}}
                                    <div class="flota-card-cliente mt-1 d-flex align-items-center" style="gap:6px;">
                                        {{#if motorizado_imagen}}
                                            <img src="{{motorizado_imagen}}" class="rounded-circle" style="width:18px;height:18px;object-fit:cover;">
                                        {{else}}
                                            <i data-feather="user" style="width:14px;height:14px;"></i>
                                        {{/if}}
                                        {{motorizado_nombre}} {{motorizado_apellido}}
                                    </div>
                                {{/if}}
                            </div>
                            {{else}}
                            <div class="flota-column-empty">Sin pedidos</div>
                            {{/each}}
                        </script>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="assets/js/pages/flota_gestion_pro.js?v=1" type="text/javascript"></script>

    <script>
        $(function () {
            $('#selectComercio').select2({ placeholder: 'Seleccione un comercio', allowClear: true });
            $('#selectComercio').on('change', cargarPedidos);

            $('#selectMotorizado').select2({ placeholder: 'Seleccione un motorizado', allowClear: true });
            $('#selectMotorizado').on('change', cargarPedidos);
        });
    </script>

</body>
</html>
