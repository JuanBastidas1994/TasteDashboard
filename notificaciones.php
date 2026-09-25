<?php
require_once "funciones.php";
require_once "clases/cl_notificaciones_expo.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$ClNotificaciones = new cl_notificaciones_expo();
$Clempresas = new cl_empresas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$empresa = $Clempresas->get($session['cod_empresa']);
$nombreEmpresa = html_entity_decode($empresa['nombre']);
$logo = $files.$session['logo'];

$totalClientes = $ClNotificaciones->totalClientesAlcanzables();
$lista = $ClNotificaciones->lista();

$tiposHistorial = [
    'evento'         => ['texto' => 'General',        'clase' => 'badge-primary'],
    'promo'          => ['texto' => 'Promoción',      'clase' => 'badge-success'],
    'producto_nuevo' => ['texto' => 'Producto nuevo', 'clase' => 'badge-warning'],
    'mensaje'        => ['texto' => 'Mensaje directo','clase' => 'badge-info'],
];

/* Los registros viejos se guardaron con htmlentities, los nuevos en texto plano */
function textoSeguro($texto){
    return htmlspecialchars(html_entity_decode($texto, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <style type="text/css">
        .push-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            background: #f1f2f3;
            margin-bottom: 20px;
        }
        .push-stat .numero { font-size: 26px; font-weight: 700; color: #1b55e2; line-height: 1; }
        .push-stat .texto { color: #515365; font-size: 13px; }

        .push-preview {
            background: linear-gradient(160deg, #3b3f5c 0%, #1b2e4b 100%);
            border-radius: 18px;
            padding: 22px 14px 40px;
            min-height: 220px;
        }
        .push-preview .hora { color: #fff; text-align: center; font-size: 32px; font-weight: 300; margin-bottom: 18px; }
        .push-card {
            background: rgba(255,255,255,.92);
            border-radius: 14px;
            padding: 10px 12px;
            display: flex;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .push-card img { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; background: #fff; }
        .push-card .app { font-size: 11px; color: #888ea8; text-transform: uppercase; display: flex; justify-content: space-between; }
        .push-card .titulo { font-weight: 600; color: #0e1726; font-size: 13px; word-break: break-word; }
        .push-card .cuerpo { color: #3b3f5c; font-size: 13px; word-break: break-word; white-space: pre-line; }
        .push-card .contenido { flex: 1; min-width: 0; }

        .contador { font-size: 11px; color: #888ea8; float: right; }
        .push-ayuda { font-size: 12px; color: #888ea8; margin-top: 15px; }
        .push-ayuda a { color: #1b55e2; }

        .tabla-historial td { vertical-align: top; }
        .tabla-historial .mensaje { color: #888ea8; font-size: 12px; white-space: normal; max-width: 380px; }
        .tabla-historial .titulo { font-weight: 600; color: #3b3f5c; white-space: normal; }
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
                    <p style="color:#888ea8;">Envía un aviso al celular de los clientes que tienen tu app instalada.</p>
                </div>

                <div class="row layout-top-spacing">

                    <!-- Enviar a todos los clientes -->
                    <div class="col-xl-7 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>Enviar a todos los clientes</h4></div>

                            <div class="push-stat">
                                <div class="numero"><?php echo $totalClientes; ?></div>
                                <div class="texto"><?php echo $totalClientes == 1 ? 'cliente recibirá' : 'clientes recibirán'; ?> esta notificación<br><small>Solo cuentan quienes tienen la app instalada y aceptaron las notificaciones.</small></div>
                            </div>

                            <div class="row">
                                <div class="col-md-7">
                                    <form name="frmSave" id="frmSave" autocomplete="off">
                                        <div class="form-group">
                                            <label>T&iacute;tulo <span class="asterisco">*</span> <span class="contador" id="contTitulo">0/65</span></label>
                                            <input type="text" placeholder="Ej: ¡Hoy 2x1 en hamburguesas!" name="titulo" id="txt_titulo" class="form-control" maxlength="65" required="required" autocomplete="off">
                                        </div>

                                        <div class="form-group">
                                            <label>Mensaje <span class="asterisco">*</span></label>
                                            <textarea placeholder="Ej: Solo por hoy, pide desde la app y aprovecha." name="descripcion" id="txt_descripcion" class="form-control" rows="4" autocomplete="off"></textarea>
                                        </div>

                                        <div class="text-right">
                                            <button type="button" class="btn btn-primary btnSendNotification" data-total="<?php echo $totalClientes; ?>" <?php echo $totalClientes == 0 ? 'disabled' : ''; ?>>
                                                Enviar a <?php echo $totalClientes.($totalClientes == 1 ? ' cliente' : ' clientes'); ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-5">
                                    <label>Vista previa</label>
                                    <div class="push-preview">
                                        <div class="hora"><?php echo date('H:i'); ?></div>
                                        <div class="push-card">
                                            <img src="<?php echo $logo; ?>" alt="" onerror="this.style.visibility='hidden'">
                                            <div class="contenido">
                                                <div class="app"><span><?php echo htmlspecialchars($nombreEmpresa); ?></span><span>ahora</span></div>
                                                <div class="titulo" id="previewTitulo">Título de la notificación</div>
                                                <div class="cuerpo" id="previewMensaje">Aquí se verá tu mensaje.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="push-ayuda">
                                &iquest;Quieres anunciar una promo o un producto? Usa la campana en <a href="promociones.php">Promociones</a> o <a href="productos.php">Productos</a>.
                                Para escribirle a un cliente puntual, usa la campana en <a href="clientes.php">Clientes</a>.
                            </div>
                        </div>
                    </div>

                    <!-- Historial -->
                    <div class="col-xl-5 col-lg-12 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>&Uacute;ltimas notificaciones</h4></div>
                            <?php if (!$lista) { ?>
                                <p style="color:#888ea8; margin-top: 20px;">A&uacute;n no has enviado notificaciones.</p>
                            <?php } else { ?>
                            <div class="table-responsive mb-4 mt-4">
                                <table class="table table-hover tabla-historial">
                                    <thead>
                                        <tr>
                                            <th>Notificaci&oacute;n</th>
                                            <th>Enviada a</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($lista as $item) {
                                            $tipo = isset($tiposHistorial[$item['tipo']]) ? $tiposHistorial[$item['tipo']] : ['texto' => $item['tipo'], 'clase' => 'badge-secondary'];
                                            $data = json_decode($item['data'], true);

                                            if ($item['tipo'] == 'mensaje' && isset($data['destinatario'])) {
                                                $destino = textoSeguro($data['destinatario']);
                                            } else {
                                                $destino = $item['total_enviados'].($item['total_enviados'] == 1 ? ' cliente' : ' clientes');
                                            }
                                            $enviadoPor = $item['admin'] ? '<br><small style="color:#888ea8;">por '.textoSeguro($item['admin']).'</small>' : '';

                                            echo '<tr>
                                                <td>
                                                    <span class="badge '.$tipo['clase'].'">'.$tipo['texto'].'</span>
                                                    <div class="titulo">'.textoSeguro($item['titulo']).'</div>
                                                    <div class="mensaje">'.textoSeguro($item['mensaje']).'</div>
                                                </td>
                                                <td>'.$destino.$enviadoPor.'</td>
                                                <td style="white-space:nowrap;">'.fechaLatinoShort($item['fecha']).'<br><small style="color:#888ea8;">'.date('H:i', strtotime($item['fecha'])).'</small></td>
                                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>
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
    <script src="assets/js/pages/notificaciones_push.js?v=1" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>
