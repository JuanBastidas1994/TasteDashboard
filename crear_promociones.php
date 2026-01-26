<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_promociones_nueva.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);
$Clpromociones = new cl_promociones_nueva(NULL);
$session = getSession();

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$cod_promocion = 0;
$promocion = [
    'descripcion'   => '',
    'is_porcentaje' => 1,
    'valor'         => '',
    'texto'         => '',
    'fecha_inicio'  => '',
    'fecha_fin'     => '',
    'is_recurrente' => 0,
    'sucursales'    => [],
    'productos'     => [],
    'recurrencia'   => []
];
if (isset($_GET['id'])) {
    $cod_promocion = $_GET['id'];
    $promocion = $Clpromociones->obtener($_GET['id']);
    if(!$promocion){
        header("location: ./promociones_v2.php");
    }
}
extract($promocion, EXTR_SKIP);

$empresa = $Clempresas->get($session['cod_empresa']);

$dias = [
    1 => 'lunes',
    2 => 'martes',
    3 => 'miércoles',
    4 => 'jueves',
    5 => 'viernes',
    6 => 'sábado',
    7 => 'domingo'
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php css_mandatory(); ?>
    <style type="text/css">
        .form-group label,
        label {
            font-size: 13px;
            color: #acb0c3;
            letter-spacing: 1px;
        }

        .croppie-container .cr-boundary {
            background-image: url(assets/img/transparent.jpg);
            background-position: center;
            background-size: cover;
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

        .itemSucursal {
            border-bottom: 1px solid #e0e6ed;
            padding: 8px 15px;
            margin-bottom: 2px;
        }

        .itemSucursal .title {
            font-size: 16px;
            font-weight: bold;
        }

        .switch.s-icons {
            height: auto;
        }

        .feather-16 {
            width: 16px;
            height: 16px;
        }

        .contificoCrear {
            display: initial;
        }

        .contificoExiste {
            display: none;
        }

        .select2-dropdown {
            z-index: 9999 !important;
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
    <?php echo navbar(true, "productos.php"); ?>
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
                    <div><span id="btnBack" data-module-back="promociones_v2.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Promociones</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($descripcion != "") ? $descripcion : "Agregar Promoción"; ?></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_promocion != 0) ? "" : "display: none;";  ?>">
                        <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Promoción</span>
                        </span>

                        <span style="cursor: pointer;margin-right: 15px;display: none;">
                            <i class="feather-16" data-feather="copy"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Duplicar</span>
                        </span>

                        <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                        </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Informacion basica -->
                        <div class="widget-content widget-content-area br-6">
                            <input type="hidden" name="id" id="id" value="<?php echo $cod_promocion; ?>">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">

                                    <div class="form-row">
                                        <div class="form-group col-md-10 col-sm-10 col-xs-12">
                                            <label>Nombre de la promo <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Motivo del descuento" name="descripcion" id="descripcion" class="form-control" required="required" autocomplete="off" value="<?php echo $descripcion; ?>">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-2 col-xs-12" style="margin-bottom:10px;">
                                            <label>Estado <span class="asterisco">*</span></label>
                                            <div>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                    <input type="checkbox" name="chk_estado" id="chk_estado" checked />
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                            <form id="frmProductos" method="POST" action="#">
                                <div class="">
                                    <h4>Productos</h4>
                                    <p>Escoje los productos a los que le vayas a aplicar el descuento</p>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <?php
                                            $resp = $Clproductos->GetProductosbyEmpresaOrder();
                                            $aux = 0;
                                            $auxCategoria = 0;
                                            $open = 0;
                                        ?>
                                        <?php foreach($resp as $clave => $itemCat): ?>
                                            <?php if($itemCat['cod_categoria'] != $auxCategoria):?>
                                                <div class="row  justify-content-start mt-2">
                                                    <div class="col-12 my-1 bt-primary ">
                                                        <div class="n-chk">
                                                            <label class="new-control new-checkbox checkbox-outline-default">
                                                            <input type="checkbox" class="new-control-input input-category" data-category="<?=$itemCat['cod_categoria']?>">
                                                            <span class="new-control-indicator"></span> <h5 ><?= $itemCat['categoria']?> </h5>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php foreach($resp as $comidaPadre): ?>
                                                    <?php if($comidaPadre['cod_categoria'] == $itemCat['cod_categoria'] && $comidaPadre['cod_producto_padre'] == 0): ?>
                                                        <div class="col-4  ">
                                                            <div class="n-chk">
                                                                <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                                                                <!-- <input type="checkbox" name="cmb_productos[]" value="<?=$comidaPadre['cod_producto']?>" class="new-control-input input-padre cat-<?=$itemCat['cod_categoria']?> input-padre<?=$comidaPadre['cod_producto'] ?>" data-padre="<?=$comidaPadre['cod_producto'] ?>"> -->

                                                                <input
                                                                    type="checkbox"
                                                                    name="cmb_productos[]"
                                                                    value="<?=$comidaPadre['cod_producto']?>"
                                                                    class="new-control-input input-padre cat-<?=$itemCat['cod_categoria']?> input-padre<?=$comidaPadre['cod_producto'] ?>"
                                                                    data-padre="<?=$comidaPadre['cod_producto'] ?>"
                                                                    <?= in_array($comidaPadre['cod_producto'], $productos) ? 'checked' : '' ?>
                                                                >
                                                                <span class="new-control-indicator"></span> <b class="text-primary"><?= $comidaPadre['nombre']?></b>
                                                                </label>
                                                            </div>
                                                        </div>
        
                                                    <?php endif ?>
                                                <?php endforeach; ?>
                                                </div>
                                                <?php if($clave != count($resp)-1  ):?>
                                                    <hr  size="8px" color="black">
                                                <?php endif?>
                                            <?php endif ?>
                                            
        
                                            <?php $auxCategoria = $itemCat['cod_categoria'];?> 
        
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">

                        <!-- Precio por disponibilidad -->
                        <div class="widget-content widget-content-area br-6">
                            
                            <form id="frmDisponibilidad" method="POST" action="#">
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Inicio <span class="asterisco">*</span></label>
                                        <input name="fecha_inicio" id="hora_ini" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="<?php echo $fecha_inicio ?>" required>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Fin <span class="asterisco">*</span></label>
                                        <input name="fecha_fin" id="hora_fin" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="<?php echo $fecha_fin ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Descuento <span class="asterisco">*</span></label>
                                        <select class="form-control" name="cmb_tipo_descuento" id="cmb_tipo_descuento">
                                                <option value="0" <?php if($is_porcentaje == 1) echo 'checked'; ?> >Porcentaje</option>
                                                <option value="2" <?php if($is_porcentaje == 0 && $texto == '2x1') echo 'selected'; ?>>2X1</option>
                                                <option value="3" <?php if($is_porcentaje == 0 && $texto == '3x2') echo 'selected'; ?>>3x2</option>
                                                <option value="4" <?php if($is_porcentaje == 0 && $texto == '4x3') echo 'selected'; ?>>4x3</option>
                                                <option value="5" <?php if($is_porcentaje == 0 && $texto == '5x4') echo 'selected'; ?>>5x4</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12 inputPorcentaje" style="<?php if($is_porcentaje == 0) echo 'display:none;'; ?>">
                                        <label>Porcentaje descuento <span class="asterisco">*</span></label>
                                        <input type="number" name="porcentaje_descuento" id="porcentaje_descuento" class="form-control" type="text" placeholder="Porcentaje de descuento" value="<?php echo $valor; ?>">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h4>Disponibilidad</h4>
                                </div>
                                <div class="row">
                                    <?php
                                    $sucursalesAll = $Clsucursales->lista();
                                    foreach ($sucursalesAll as $suc) {
                                        $cod_sucursal = $suc['cod_sucursal'];
                                        $nombre = $suc['nombre'];
                                        $checked = 'checked';
                                        if($sucursales){
                                            $checked = in_array($cod_sucursal, $sucursales) ? 'checked' : '';
                                        }

                                        echo '
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 itemSucursal">
                                            <div class="col-md-9 col-sm-9 col-xs-9"><label for="chksucursal' . $cod_sucursal . '"><span class="title">' . $nombre . '</span></label></div>
                                            <div class="col-md-3 col-sm-3 col-xs-3">
                                                <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                <input type="checkbox" class="chkDisponibilidad" name="chkSucursal[]" '.$checked.' id="chksucursal' . $cod_sucursal . '" value="' . $cod_sucursal . '">
                                                <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>';
                                    }
                                    ?>

                                </div>
                            </form>
                        </div>

                        <!-- DAYS AVAILABLE -->
                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                            <div>
                                <h4>Días</h4>
                            </div>
                            <form id="frmDias" method="POST" action="#">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input type="hidden" id="dias_disponibles" value="<?php echo $days; ?>" />
                                        <p>Escoge los días que quieras que aparezcan tus productos</p>
                                        <div>
                                            <label class="new-control new-radio radio-classic-primary">
                                                <input type="radio" class="new-control-input rbDisponibleDias" id="todosDias" value="0" name="is_recurrencia"
                                                    <?php if(!$recurrencia) echo 'checked'; ?>
                                                >
                                                <span class="new-control-indicator"></span>Todos los días
                                            </label>
                                        </div>
                                        <div>
                                            <label class="new-control new-radio radio-classic-primary">
                                                <input type="radio" class="new-control-input rbDisponibleDias" id="ciertosDias" value="1" name="is_recurrencia"
                                                    <?php if($recurrencia) echo 'checked'; ?>
                                                >
                                                <span class="new-control-indicator"></span>Sólo algunos días
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="chooseDias form-group col-md-12 col-sm-12 col-xs-12" style="<?php if(!$recurrencia) echo 'display:none;'; ?>">
                                        <?php foreach ($dias as $num => $nombre): ?>
                                            <?php
                                                $activo = isset($recurrencia[$nombre]);
                                                $inicio = $activo ? substr($recurrencia[$nombre]['inicio'], 0, 5) : '';
                                                $fin    = $activo ? substr($recurrencia[$nombre]['fin'], 0, 5) : '';
                                            ?>

                                            <div class="day-item row align-items-center mb-2">
                                                <div class="col-md-3">
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox"
                                                            class="chk-day"
                                                            name="dias[<?= $num ?>][dia_semana]"
                                                            value="<?= $num ?>"
                                                            <?= $activo ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <span class="ml-2"><?= ucfirst($nombre) ?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <input type="time"
                                                        name="dias[<?= $num ?>][inicio]"
                                                        class="form-control time-start"
                                                        value="<?= $inicio ?>"
                                                        <?= $activo ? '' : 'disabled' ?>>
                                                </div>

                                                <div class="col-md-4">
                                                    <input type="time"
                                                        name="dias[<?= $num ?>][fin]"
                                                        class="form-control time-end"
                                                        value="<?= $fin ?>"
                                                        <?= $activo ? '' : 'disabled' ?>>
                                                </div>
                                            </div>

                                        <?php endforeach; ?>

                                        <div class="mb-3">
                                            <button type="button" class="btn btn-sm btn-secondary" id="btnReplicarHorario">
                                                Replicar horario en todos los días activos
                                            </button>
                                        </div>
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
    <script src="assets/js/pages/crear_promociones.js?v=124" type="text/javascript"></script>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</body>

</html>