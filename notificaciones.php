<?php
require_once "funciones.php";
require_once "clases/cl_notificaciones_expo.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_promociones_nueva.php";
require_once "clases/cl_productos.php";

if(!isLogin()){
    header("location:login.php");
}

$ClNotificaciones = new cl_notificaciones_expo();
$Clempresas = new cl_empresas(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clpromociones = new cl_promociones_nueva();
$Clproductos = new cl_productos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$empresa = $Clempresas->get($session['cod_empresa']);
$nombreEmpresa = html_entity_decode($empresa['nombre']);
$logo = $files.$session['logo'];

$verTodas = isset($_GET['todas']);
$filtro = isset($_GET['filtro']) && in_array($_GET['filtro'], ['programadas', 'automaticas']) ? $_GET['filtro'] : 'todas';
$lista = $ClNotificaciones->lista($verTodas ? 500 : 15, $filtro);
$programadasPendientes = $ClNotificaciones->totalProgramadasPendientes();

/* Conserva filtro y "ver todas" al cambiar uno de los dos */
function urlHistorial($filtro, $verTodas){
    $params = [];
    if ($filtro !== 'todas') $params['filtro'] = $filtro;
    if ($verTodas) $params['todas'] = 1;
    return 'notificaciones.php'.($params ? '?'.http_build_query($params) : '').'#historial';
}
$resumen = $ClNotificaciones->resumenMes();
$meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$totalClientes = $ClNotificaciones->contarAudiencia(['tipo' => 'todos']);
$configAuto = $ClNotificaciones->configAutomaticas();

$sucursales = $Clsucursales->lista() ?: [];
$promociones = $Clpromociones->lista() ?: [];
$productos = $Clproductos->lista() ?: [];

/* Regalo de cumpleaños: se configura en configuraciones.php > Cumpleaños */
$regaloCumple = Conexion::buscarRegistro(
    "SELECT e.fidelizacion, f.valor_regalo_cumple, f.compra_minimo_regalo_cumple
        FROM tb_empresas e
        LEFT JOIN tb_empresa_fidelizacion_puntos f ON f.cod_empresa = e.cod_empresa
        WHERE e.cod_empresa = :cod_empresa",
    [':cod_empresa' => $session['cod_empresa']]
);
$tieneRegaloCumple = $regaloCumple && $regaloCumple['fidelizacion'] == 1 && $regaloCumple['valor_regalo_cumple'] > 0;

/* Llegada desde la campana de Promociones / Productos: tipo y texto precargados */
$preTipo = isset($_GET['tipo']) && in_array($_GET['tipo'], ['promo', 'producto_nuevo']) ? $_GET['tipo'] : 'evento';
$preId = isset($_GET['id']) ? $_GET['id'] : '';
$preTitulo = '';
$preMensaje = '';
if ($preTipo === 'promo' && $preId !== '') {
    $promo = $Clpromociones->obtener((int)$preId);
    if ($promo) {
        $preTitulo = html_entity_decode($promo['descripcion']);
        $preMensaje = 'Aprovecha nuestra promoción: '.html_entity_decode($promo['descripcion']);
    }
} elseif ($preTipo === 'producto_nuevo' && $preId !== '') {
    $producto = [];
    if ($Clproductos->getArrayByAlias(addslashes($preId), $producto)) {
        $preTitulo = '¡Nuevo producto disponible! 🎉';
        $preMensaje = 'Ya puedes pedir '.html_entity_decode($producto['nombre']);
    }
}

$tipos = [
    'evento'            => ['texto' => 'Información',    'icono' => '📢', 'clase' => 'tag-info'],
    'promo'             => ['texto' => 'Promoción',      'icono' => '🏷️', 'clase' => 'tag-promo'],
    'producto_nuevo'    => ['texto' => 'Novedad',        'icono' => '🆕', 'clase' => 'tag-novedad'],
    'mensaje'           => ['texto' => 'Mensaje directo','icono' => '💬', 'clase' => 'tag-info'],
    'calificacion'      => ['texto' => 'Calificación',   'icono' => '⭐', 'clase' => 'tag-auto'],
    'cumpleanos'        => ['texto' => 'Cumpleaños',     'icono' => '🎂', 'clase' => 'tag-auto'],
    'cumpleanos_previo' => ['texto' => 'Cumpleaños',     'icono' => '🎈', 'clase' => 'tag-auto'],
    'recompra'          => ['texto' => 'Recompra',       'icono' => '🛒', 'clase' => 'tag-auto'],
];
$estados = [
    'ENVIADA'    => 'est-ok',
    'PROGRAMADA' => 'est-programada',
    'ENVIANDO'   => 'est-programada',
    'CANCELADA'  => 'est-off',
    'ERROR'      => 'est-error',
];

/* Los registros viejos se guardaron con htmlentities, los nuevos en texto plano */
function textoSeguro($texto){
    return htmlspecialchars(html_entity_decode($texto, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
}

function numeroCorto($n){
    if ($n >= 1000000) return round($n / 1000000, 1).'M';
    if ($n >= 1000) return round($n / 1000, 1).'K';
    return (string)$n;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <style type="text/css">
        :root {
            --nt-primary: #5b47e0;
            --nt-primary-soft: #efedfd;
            --nt-text: #1f2340;
            --nt-muted: #8a8fa8;
            --nt-border: #e6e8f0;
        }
        .nt-card { background: #fff; border-radius: 10px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(20, 24, 60, .06); height: 100%; }
        .nt-card h4 { color: var(--nt-text); font-weight: 600; margin-bottom: 2px; }
        .nt-sub { color: var(--nt-muted); font-size: 13px; margin-bottom: 18px; }
        .nt-label { color: var(--nt-text); font-weight: 600; font-size: 13px; margin-bottom: 6px; display: flex; justify-content: space-between; }
        .nt-label .contador { color: var(--nt-muted); font-weight: 400; font-size: 12px; }
        .nt-card .form-control { border-color: var(--nt-border); color: var(--nt-text); }
        .nt-card .form-control:focus { border-color: var(--nt-primary); box-shadow: none; }
        .nt-card .emojionearea { border-color: var(--nt-border); box-shadow: none; }
        .nt-card .emojionearea .emojionearea-editor { min-height: 90px; color: var(--nt-text); }

        .nt-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .nt-chip { border: 1px solid var(--nt-border); background: #fff; color: var(--nt-text); border-radius: 8px; padding: 8px 14px; font-size: 13px; cursor: pointer; user-select: none; }
        .nt-chip.activo { border-color: var(--nt-primary); background: var(--nt-primary-soft); color: var(--nt-primary); font-weight: 600; }
        .nt-extra { margin-top: 10px; display: none; }

        .nt-alcance { font-size: 12px; color: var(--nt-muted); margin-top: 6px; }
        .nt-alcance b { color: var(--nt-primary); }

        .nt-phone { border-radius: 26px; padding: 8px; background: #1f2340; max-width: 290px; margin: 0 auto; }
        .nt-phone-screen { border-radius: 20px; min-height: 300px; padding: 14px 12px; background: linear-gradient(170deg, #5a6f96 0%, #9fb0c9 55%, #dfe5ee 100%); }
        .nt-phone-hora { color: #fff; font-size: 12px; font-weight: 600; margin-bottom: 26px; }
        .nt-push { background: rgba(255, 255, 255, .93); border-radius: 14px; padding: 10px 12px; display: flex; gap: 10px; box-shadow: 0 6px 16px rgba(0, 0, 0, .15); }
        .nt-push img { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; background: #fff; }
        .nt-push .contenido { flex: 1; min-width: 0; }
        .nt-push .app { font-size: 11px; color: var(--nt-muted); display: flex; justify-content: space-between; gap: 6px; }
        .nt-push .app span:first-child { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .nt-push .app span:last-child { white-space: nowrap; }
        .nt-push .titulo { font-weight: 600; color: var(--nt-text); font-size: 13px; word-break: break-word; }
        .nt-push .cuerpo { color: #464b66; font-size: 13px; word-break: break-word; white-space: pre-line; }

        .nt-footer { border-top: 1px solid var(--nt-border); margin: 22px -24px -22px; padding: 14px 24px; display: flex; justify-content: flex-end; }
        .nt-btn { background: var(--nt-primary); border-color: var(--nt-primary); color: #fff; }
        .nt-btn:hover, .nt-btn:focus { background: #4a37cc; color: #fff; }
        .nt-btn[disabled] { opacity: .5; }

        .nt-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .nt-stat { border: 1px solid var(--nt-border); border-radius: 10px; padding: 12px; display: flex; gap: 10px; align-items: center; }
        .nt-stat .icono { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .nt-stat .icono svg { width: 20px; height: 20px; }
        .nt-stat .valor { font-size: 18px; font-weight: 700; color: var(--nt-text); line-height: 1.1; }
        .nt-stat .texto { font-size: 11px; color: var(--nt-muted); }
        .nt-nota { font-size: 11px; color: var(--nt-muted); margin-top: 10px; }

        .nt-tabla { width: 100%; }
        .nt-tabla th { font-size: 11px; color: var(--nt-muted); text-transform: uppercase; font-weight: 600; padding: 8px 6px; border-bottom: 1px solid var(--nt-border); }
        .nt-tabla td { padding: 12px 6px; border-bottom: 1px solid var(--nt-border); vertical-align: middle; font-size: 13px; color: var(--nt-text); }
        .nt-noti { display: flex; gap: 10px; align-items: center; }
        .nt-noti .ico { width: 38px; height: 38px; border-radius: 9px; background: var(--nt-primary-soft); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .nt-noti .t { font-weight: 600; }
        .nt-noti .m { color: var(--nt-muted); font-size: 12px; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .nt-small { color: var(--nt-muted); font-size: 12px; }
        .nt-tag { font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: 600; white-space: nowrap; }
        .tag-promo { background: #efedfd; color: #5b47e0; }
        .tag-novedad { background: #e6f7ee; color: #1a9a55; }
        .tag-info { background: #e8f1fd; color: #1b6fd8; }
        .tag-auto { background: #fff4e0; color: #c47a00; }
        .est-ok { background: #e6f7ee; color: #1a9a55; }
        .est-programada { background: #e8f1fd; color: #1b6fd8; }
        .est-off { background: #f0f1f5; color: #8a8fa8; }
        .est-error { background: #fdecec; color: #d64545; }
        .nt-acciones { color: var(--nt-muted); cursor: pointer; padding: 0 6px; }
        .nt-filtros { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
        .nt-filtros .nt-chip { text-decoration: none; padding: 6px 12px; }
        .nt-contador { background: var(--nt-primary); color: #fff; border-radius: 10px; font-size: 11px; padding: 1px 7px; margin-left: 4px; }
        .flatpickr-input[readonly] { background: #fff; }

        .nt-tip { background: var(--nt-primary-soft); border-radius: 10px; padding: 14px 16px; display: flex; gap: 12px; align-items: center; margin-top: 16px; }
        .nt-tip .ico { width: 36px; height: 36px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .nt-tip b { color: var(--nt-text); font-size: 13px; }
        .nt-tip p { margin: 0; font-size: 12px; color: #5b5f7a; }

        .nt-auto { border: 1px solid var(--nt-border); border-radius: 10px; padding: 14px 16px; height: 100%; }
        .nt-auto .cab { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .nt-auto .cab b { color: var(--nt-text); }
        .nt-auto p { font-size: 12px; color: var(--nt-muted); margin-bottom: 8px; }
        .nt-siempre { font-size: 11px; background: #e6f7ee; color: #1a9a55; border-radius: 6px; padding: 2px 8px; font-weight: 600; }

        @media (max-width: 767px) { .nt-stats { grid-template-columns: repeat(2, 1fr); } }
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                        <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Notificaciones push</h3>
                </div>

                <div class="row layout-top-spacing">

                    <!-- ============ CREAR ============ -->
                    <div class="col-12 layout-spacing">
                        <div class="nt-card">
                            <h4>Crear nueva notificación</h4>
                            <div class="nt-sub">Personaliza tu mensaje y elige a quién deseas enviarlo</div>

                            <form id="frmNotificacion" autocomplete="off">
                                <input type="hidden" name="tipo" id="txtTipo" value="<?php echo $preTipo; ?>">
                                <input type="hidden" name="cuando" id="txtCuando" value="ahora">

                                <div class="row">
                                    <!-- Contenido -->
                                    <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="nt-label">Título * <span class="contador" id="contTitulo">0/50</span></label>
                                    <input type="text" class="form-control" name="titulo" id="txtTitulo" maxlength="50" placeholder="Ej: ¡Disfruta tu combo favorito! 🎉" value="<?php echo htmlspecialchars($preTitulo); ?>">
                                </div>

                                <div class="form-group">
                                    <label class="nt-label">Mensaje * <span class="contador" id="contMensaje">0/200</span></label>
                                    <textarea id="txtMensaje" maxlength="200" placeholder="Escribe tu mensaje aquí..."><?php echo htmlspecialchars($preMensaje); ?></textarea>
                                </div>
                                    </div>

                                    <!-- Tipo, audiencia y cuándo -->
                                    <div class="col-lg-4 col-md-7">
                                        <div class="form-group">
                                            <label class="nt-label">Tipo de mensaje</label>
                                            <div class="nt-chips" data-input="#txtTipo">
                                                <span class="nt-chip" data-value="evento">📢 Información</span>
                                                <span class="nt-chip" data-value="promo">🏷️ Promoción</span>
                                                <span class="nt-chip" data-value="producto_nuevo">🆕 Novedad</span>
                                            </div>
                                            <div class="nt-extra" data-tipo="promo">
                                                <select class="form-control" name="cod_promocion" id="cmbPromocion">
                                                    <option value="">Selecciona la promoción</option>
                                                    <?php foreach ($promociones as $p) {
                                                        $sel = ($preTipo === 'promo' && $preId == $p['cod_promocion']) ? 'selected' : '';
                                                        echo '<option value="'.$p['cod_promocion'].'" '.$sel.'>'.textoSeguro($p['descripcion']).'</option>';
                                                    } ?>
                                                </select>
                                                <div class="nt-small" style="margin-top:4px;">Al tocarla, el cliente abre el menú de la app.</div>
                                            </div>
                                            <div class="nt-extra" data-tipo="producto_nuevo">
                                                <select class="form-control" name="alias" id="cmbProducto">
                                                    <option value="">Selecciona el producto</option>
                                                    <?php foreach ($productos as $p) {
                                                        $sel = ($preTipo === 'producto_nuevo' && $preId === $p['alias']) ? 'selected' : '';
                                                        echo '<option value="'.htmlspecialchars($p['alias']).'" '.$sel.'>'.textoSeguro($p['nombre']).'</option>';
                                                    } ?>
                                                </select>
                                                <div class="nt-small" style="margin-top:4px;">Al tocarla, el cliente abre el producto.</div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="nt-label">Audiencia</label>
                                            <select class="form-control" name="audiencia" id="cmbAudiencia">
                                                <option value="todos">Todos los clientes</option>
                                                <option value="inactivos">Clientes que no piden hace...</option>
                                                <?php if (count($sucursales) > 1) { ?>
                                                <option value="sucursal">Clientes de una sucursal</option>
                                                <?php } ?>
                                            </select>
                                            <div class="nt-extra" data-audiencia="inactivos">
                                                <select class="form-control" name="dias_inactivo" id="cmbDiasInactivo">
                                                    <option value="15">15 días</option>
                                                    <option value="30" selected>30 días</option>
                                                    <option value="60">60 días</option>
                                                    <option value="90">90 días</option>
                                                </select>
                                            </div>
                                            <div class="nt-extra" data-audiencia="sucursal">
                                                <select class="form-control" name="cod_sucursal" id="cmbSucursal">
                                                    <?php foreach ($sucursales as $s) {
                                                        echo '<option value="'.$s['cod_sucursal'].'">'.textoSeguro($s['nombre']).'</option>';
                                                    } ?>
                                                </select>
                                                <div class="nt-small" style="margin-top:4px;">Clientes que han pedido en esa sucursal.</div>
                                            </div>
                                            <div class="nt-alcance">Llegará a <b id="lblAlcance"><?php echo $totalClientes; ?></b> clientes con la app y notificaciones activas</div>
                                        </div>

                                        <div class="form-group">
                                            <label class="nt-label">¿Cuándo?</label>
                                            <div class="nt-chips" data-input="#txtCuando">
                                                <span class="nt-chip activo" data-value="ahora">⚡ Enviar ahora</span>
                                                <span class="nt-chip" data-value="programar">⏰ Programar</span>
                                            </div>
                                            <div class="nt-extra" data-cuando="programar">
                                                <input type="text" class="form-control flatpickr" name="fecha_programada" id="txtFechaProgramada" placeholder="Selecciona fecha y hora">
                                                <div class="nt-small" style="margin-top:4px;">Se envía en un margen de hasta 15 minutos después de la hora elegida.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vista previa -->
                                    <div class="col-lg-3 col-md-5">
                                        <label class="nt-label" style="justify-content:center;">Vista previa</label>
                                        <div class="nt-phone">
                                            <div class="nt-phone-screen">
                                                <div class="nt-phone-hora"><?php echo date('H:i'); ?></div>
                                                <div class="nt-push">
                                                    <img src="<?php echo $logo; ?>" alt="" onerror="this.style.visibility='hidden'">
                                                    <div class="contenido">
                                                        <div class="app"><span><?php echo htmlspecialchars($nombreEmpresa); ?></span><span>ahora mismo</span></div>
                                                        <div class="titulo" id="previewTitulo">Título de la notificación</div>
                                                        <div class="cuerpo" id="previewMensaje">Aquí se verá tu mensaje.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="nt-footer">
                                    <button type="button" class="btn nt-btn" id="btnEnviar">Enviar ahora</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============ RESUMEN + HISTORIAL ============ -->
                    <div class="col-12 layout-spacing">
                        <div class="nt-card" style="height:auto; margin-bottom: 20px;">
                            <h4 style="font-size: 15px; margin-bottom: 14px;">Resumen de <?php echo $meses[(int)date('n')]; ?></h4>
                            <div class="nt-stats">
                                <div class="nt-stat">
                                    <div class="icono" style="background:#efedfd; color:#5b47e0;"><i data-feather="send"></i></div>
                                    <div><div class="valor"><?php echo numeroCorto($resumen['enviadas']); ?></div><div class="texto">Enviadas este mes</div></div>
                                </div>
                                <div class="nt-stat">
                                    <div class="icono" style="background:#e8f1fd; color:#1b6fd8;"><i data-feather="smartphone"></i></div>
                                    <div><div class="valor"><?php echo numeroCorto($resumen['entregadas']); ?></div><div class="texto">Entregadas</div></div>
                                </div>
                                <div class="nt-stat">
                                    <div class="icono" style="background:#e6f7ee; color:#1a9a55;"><i data-feather="mouse-pointer"></i></div>
                                    <div><div class="valor"><?php echo numeroCorto($resumen['abiertas']); ?></div><div class="texto">Abiertas</div></div>
                                </div>
                                <div class="nt-stat">
                                    <div class="icono" style="background:#fdecf4; color:#d6457f;"><i data-feather="percent"></i></div>
                                    <div><div class="valor"><?php echo $resumen['tasa']; ?>%</div><div class="texto">Tasa de apertura</div></div>
                                </div>
                            </div>
                            <div class="nt-nota">Entregadas = celulares que aceptaron la notificación. Abiertas = clientes que la tocaron (requiere la última versión de la app).</div>
                        </div>

                        <div class="nt-card" style="height:auto;" id="historial">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
                                <h4 style="font-size: 15px;"><?php echo $verTodas ? 'Todas las notificaciones' : 'Últimas notificaciones'; ?></h4>
                                <?php if ($verTodas) { ?>
                                    <a href="<?php echo urlHistorial($filtro, false); ?>" style="color: var(--nt-primary); font-size: 13px;">Ver menos</a>
                                <?php } else { ?>
                                    <a href="<?php echo urlHistorial($filtro, true); ?>" style="color: var(--nt-primary); font-size: 13px;">Ver todas</a>
                                <?php } ?>
                            </div>
                            <div class="nt-filtros">
                                <a href="<?php echo urlHistorial('todas', $verTodas); ?>" class="nt-chip <?php echo $filtro === 'todas' ? 'activo' : ''; ?>">Todas</a>
                                <a href="<?php echo urlHistorial('programadas', $verTodas); ?>" class="nt-chip <?php echo $filtro === 'programadas' ? 'activo' : ''; ?>">
                                    ⏰ Programadas<?php if ($programadasPendientes > 0) { ?> <span class="nt-contador"><?php echo $programadasPendientes; ?> pendiente<?php echo $programadasPendientes == 1 ? '' : 's'; ?></span><?php } ?>
                                </a>
                                <a href="<?php echo urlHistorial('automaticas', $verTodas); ?>" class="nt-chip <?php echo $filtro === 'automaticas' ? 'activo' : ''; ?>">🤖 Automáticas</a>
                            </div>

                            <?php if (!$lista) { ?>
                                <p class="nt-small" style="margin: 20px 0;">
                                    <?php echo $filtro === 'programadas' ? 'No tienes notificaciones programadas.' : ($filtro === 'automaticas' ? 'Aún no se han enviado notificaciones automáticas.' : 'Aún no has enviado notificaciones.'); ?>
                                </p>
                            <?php } else { ?>
                            <div class="table-responsive">
                                <table class="nt-tabla">
                                    <thead>
                                        <tr>
                                            <th>Notificación</th>
                                            <th>Tipo</th>
                                            <th>Audiencia</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lista as $item) {
                                            $tipo = isset($tipos[$item['tipo']]) ? $tipos[$item['tipo']] : ['texto' => $item['tipo'], 'icono' => '🔔', 'clase' => 'tag-info'];
                                            $estadoClase = isset($estados[$item['estado']]) ? $estados[$item['estado']] : 'est-off';
                                            $fecha = $item['estado'] === 'PROGRAMADA' ? $item['fecha_programada'] : ($item['fecha_envio'] ?: $item['fecha']);

                                            if ($item['estado'] === 'PROGRAMADA') {
                                                $alcance = 'Pendiente';
                                            } else {
                                                $alcance = number_format($item['total_ok']).' entregadas';
                                                if ($item['aperturas'] > 0) $alcance .= ' · '.$item['aperturas'].' abiertas';
                                            }
                                            $estadoTexto = ucfirst(strtolower($item['estado']));
                                            if ($item['audiencia'] && strpos($item['audiencia'], 'automatica') !== false && $item['estado'] === 'ENVIADA') {
                                                $estadoTexto = 'Automática';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="nt-noti">
                                                        <div class="ico"><?php echo $tipo['icono']; ?></div>
                                                        <div style="min-width:0;">
                                                            <div class="t"><?php echo textoSeguro($item['titulo']); ?></div>
                                                            <div class="m" title="<?php echo textoSeguro($item['mensaje']); ?>"><?php echo textoSeguro($item['mensaje']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="nt-tag <?php echo $tipo['clase']; ?>"><?php echo $tipo['texto']; ?></span></td>
                                                <td>
                                                    <?php echo htmlspecialchars($ClNotificaciones->describirAudiencia($item['audiencia'])); ?>
                                                    <div class="nt-small"><?php echo $alcance; ?></div>
                                                </td>
                                                <td style="white-space:nowrap;">
                                                    <?php echo fechaLatinoShort($fecha); ?>
                                                    <div class="nt-small"><?php echo date('H:i', strtotime($fecha)); ?></div>
                                                    <?php if ($item['fecha_programada'] && $item['estado'] !== 'PROGRAMADA') { ?>
                                                        <div class="nt-small" title="Programada para el <?php echo date('d/m/Y H:i', strtotime($item['fecha_programada'])); ?>">⏰ Programada</div>
                                                    <?php } ?>
                                                </td>
                                                <td><span class="nt-tag <?php echo $estadoClase; ?>"><?php echo $estadoTexto; ?></span></td>
                                                <td>
                                                    <?php if ($item['estado'] === 'PROGRAMADA') { ?>
                                                        <div class="dropdown">
                                                            <span class="nt-acciones" data-toggle="dropdown"><i data-feather="more-horizontal"></i></span>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item btnCancelarProgramada" href="javascript:void(0);" data-id="<?php echo $item['id']; ?>">Cancelar envío</a>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>

                            <div class="nt-tip">
                                <div class="ico">💡</div>
                                <div>
                                    <b>Consejo</b>
                                    <p>Un título corto con un emoji y un beneficio claro ("2x1 hoy", "envío gratis") suele llamar más la atención. Revisa la tasa de apertura para comparar qué funciona mejor.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ AUTOMÁTICAS ============ -->
                    <div class="col-12 layout-spacing">
                        <div class="nt-card" style="height:auto;">
                            <h4>Notificaciones automáticas</h4>
                            <div class="nt-sub">Se envían solas a tus clientes con la app. Aparecen en el historial agrupadas por día.</div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6" style="margin-bottom: 12px;">
                                    <div class="nt-auto">
                                        <div class="cab"><b>⭐ Calificación del pedido</b><span class="nt-siempre">Siempre activa</span></div>
                                        <p>Entre 30 y 45 minutos después de entregado el pedido, invitamos al cliente a calificarlo. Al tocarla abre el pedido.</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6" style="margin-bottom: 12px;">
                                    <div class="nt-auto">
                                        <div class="cab"><b>🎂 Cumpleaños</b><span class="nt-siempre">Siempre activa</span></div>
                                        <p>Un aviso 3 días antes y un saludo el día del cumpleaños (10:00 a. m.).
                                        <?php if ($tieneRegaloCumple) { ?>
                                            El día del cumpleaños se acredita el regalo de <b>$<?php echo number_format($regaloCumple['valor_regalo_cumple'], 2); ?></b><?php echo $regaloCumple['compra_minimo_regalo_cumple'] > 0 ? ' a quienes han comprado al menos $'.$regaloCumple['compra_minimo_regalo_cumple'] : ''; ?> y el mensaje lo menciona.
                                        <?php } else { ?>
                                            No tienes un regalo de cumpleaños configurado, así que solo se envía el saludo.
                                        <?php } ?>
                                        </p>
                                        <a href="configuraciones.php" style="color: var(--nt-primary); font-size: 12px;">Configurar regalo de cumpleaños</a>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12" style="margin-bottom: 12px;">
                                    <div class="nt-auto">
                                        <form id="frmRecompra" autocomplete="off">
                                            <div class="cab">
                                                <b>🛒 Recordatorio de recompra</b>
                                                <label class="switch s-icons s-outline s-outline-primary mb-0">
                                                    <input type="checkbox" name="recompra_activo" value="1" <?php echo $configAuto['recompra_activo'] ? 'checked' : ''; ?>>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                            <p>Si un cliente lleva estos días sin pedir, le llega este recordatorio una sola vez (10:00 a. m.). Si vuelve a pedir, el contador empieza de nuevo. Usa <b>{nombre}</b> para personalizar.</p>
                                            <div class="form-group mb-2">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="recompra_dias" min="3" max="180" value="<?php echo (int)$configAuto['recompra_dias']; ?>">
                                                    <div class="input-group-append"><span class="input-group-text">días sin pedir</span></div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-2">
                                                <input type="text" class="form-control" name="recompra_titulo" maxlength="50" placeholder="Título" value="<?php echo htmlspecialchars($configAuto['recompra_titulo']); ?>">
                                            </div>
                                            <div class="form-group mb-2">
                                                <textarea class="form-control" name="recompra_mensaje" maxlength="200" rows="2" placeholder="Mensaje"><?php echo htmlspecialchars($configAuto['recompra_mensaje']); ?></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnGuardarRecompra">Guardar</button>
                                            </div>
                                        </form>
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
    <?php js_mandatory(); ?>
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <script src="assets/js/pages/notificaciones_push.js?v=3" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>
