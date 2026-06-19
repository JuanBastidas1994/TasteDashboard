<?php
require_once "funciones.php";
require_once "clases/cl_promociones_nueva.php";
require_once "clases/cl_productos.php";

if (!isLogin()) {
    header("location:login.php");
}

$session = getSession();

$tiposValidos = [
    'promo'          => ['titulo' => 'Promoción',       'volver' => 'promociones.php', 'campoId' => 'cod_promocion'],
    'producto_nuevo' => ['titulo' => 'Producto nuevo',  'volver' => 'productos.php',   'campoId' => 'cod_producto'],
    'evento'         => ['titulo' => 'Evento',          'volver' => 'index.php',       'campoId' => null],
];

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (!isset($tiposValidos[$tipo])) {
    header("location: ./index.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$campoId = $tiposValidos[$tipo]['campoId'];
$volver = $tiposValidos[$tipo]['volver'];

$tituloSugerido = '';
$descripcionSugerida = '';

if ($id > 0) {
    if ($tipo === 'promo') {
        $Clpromociones = new cl_promociones_nueva(NULL);
        $promocion = $Clpromociones->obtener($id);
        if ($promocion) {
            $tituloSugerido = $promocion['descripcion'];
            $descripcionSugerida = "Aprovecha nuestra promoción: " . $promocion['descripcion'];
        }
    } elseif ($tipo === 'producto_nuevo') {
        $Clproductos = new cl_productos(NULL);
        $producto = $Clproductos->get($id);
        if ($producto) {
            $tituloSugerido = "¡Nuevo producto disponible!";
            $descripcionSugerida = "Ya puedes pedir " . $producto['nombre'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
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
                    <div><span id="btnBack" data-module-back="<?php echo $volver; ?>" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Volver</span></span>
                    </div>
                    <h3 id="titulo">Notificar: <?php echo $tiposValidos[$tipo]['titulo']; ?></h3>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-6 col-lg-8 col-sm-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>Enviar push a clientes</h4></div>
                            <p style="color:#888ea8;">Se enviará a todos los clientes de tu empresa con notificaciones push activas.</p>
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" id="txt_tipo" value="<?php echo $tipo; ?>">
                                <?php if ($campoId && $id > 0) { ?>
                                    <input type="hidden" name="<?php echo $campoId; ?>" value="<?php echo $id; ?>">
                                <?php } ?>
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <label>Título <span class="asterisco">*</span></label>
                                        <input type="text" placeholder="Título" name="titulo" id="txt_titulo" class="form-control" value="<?php echo htmlspecialchars($tituloSugerido); ?>" required="required" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <label>Descripción <span class="asterisco">*</span></label>
                                        <textarea placeholder="Descripción" name="descripcion" id="txt_descripcion" class="form-control" required="required" autocomplete="off"><?php echo htmlspecialchars($descripcionSugerida); ?></textarea>
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary btnSendNotification">Enviar Notificación</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/crear_notificacion_expo.js" type="text/javascript"></script>
</body>

</html>
