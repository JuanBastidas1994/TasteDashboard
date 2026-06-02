<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_promocion_producto_gratis.php";

if (!isLogin()) {
    header("location:login.php");
}

$session      = getSession();
$Clsucursales = new cl_sucursales(NULL);
$Clproductos  = new cl_productos(NULL);
$ClPG         = new cl_promocion_producto_gratis();

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

// Cargar configuraciones existentes indexadas por sucursal
$registros_existentes = [];
foreach ($ClPG->getByCodEmpresa() as $r) {
    $registros_existentes[$r['cod_sucursal']] = $r;
}

// Tomar el primer registro activo como referencia para pre-poblar el formulario
$config_ref = null;
foreach ($registros_existentes as $r) {
    if ($r['estado'] === 'A') {
        $config_ref = $r;
        break;
    }
}

// Productos agrupados por categoría
$resp       = $Clproductos->GetProductosbyEmpresaOrder();
$categorias = [];
foreach ($resp as $p) {
    if ($p['cod_producto_padre'] == 0) {
        $categorias[$p['cod_categoria']]['nombre']      = $p['categoria'];
        $categorias[$p['cod_categoria']]['productos'][] = $p;
    }
}

$sucursalesAll = $Clsucursales->lista();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
    <link href="plugins/dropify/dropify.min.css" rel="stylesheet">
    <style>
        .itemSucursal {
            border-bottom: 1px solid #e0e6ed;
            padding: 10px 15px;
            margin: 0 -20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .itemSucursal:last-child { border-bottom: none; }
        .itemSucursal .title { font-size: 15px; font-weight: 600; }
        .switch.s-icons { height:auto; }
        .pg-img-preview-wrap {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 500px;
        }
        .pg-img-preview-wrap img {
            width: 100%;
            border-radius: 8px;
            display: block;
            border: 1px solid #ddd;
            object-fit: cover;
        }
        .pg-img-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.58);
            color: #fff;
            text-align: center;
            padding: 12px 16px;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            line-height: 1.4;
        }
        .select2-dropdown { z-index: 9999 !important; }
    </style>
</head>
<body>

    <!-- Modal Croppie -->
    <div class="modal fade" id="modalCroppiePG" tabindex="99" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recortar imagen</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" style="margin-bottom:10px;">
                        <img id="pgImgCrop" src="#" style="width:100%;max-height:420px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark pg-crop-rotate" data-deg="-90">Rotate Left</button>
                    <button class="btn btn-dark pg-crop-rotate" data-deg="90">Rotate Right</button>
                    <button type="button" class="btn btn-primary" id="pgCropGet">Recortar</button>
                </div>
            </div>
        </div>
    </div>

    <?php top() ?>
    <?php navbar() ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php sidebar() ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="col-md-12" style="margin-top:25px;">
                    <h3>Producto Gratis - Primera Orden</h3>
                    <p class="text-muted">Configura el producto gratis que recibirá el cliente al alcanzar el monto mínimo en su primera orden.</p>
                </div>

                <div class="row layout-top-spacing">

                    <!-- Columna izquierda -->
                    <div class="col-xl-7 col-lg-12 layout-spacing">

                        <!-- Producto y configuración -->
                        <div class="widget-content widget-content-area br-6">
                            <h5 class="mb-3">Configuración</h5>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Producto gratis que llevará el cliente <span class="text-danger">*</span></label>
                                    <select class="form-control" id="cmb_producto" name="cod_producto">
                                        <option value="">— Seleccionar producto —</option>
                                        <?php foreach ($categorias as $dataCategoria): ?>
                                            <optgroup label="<?= htmlspecialchars($dataCategoria['nombre']) ?>">
                                                <?php foreach ($dataCategoria['productos'] as $producto): ?>
                                                    <option value="<?= $producto['cod_producto'] ?>"
                                                            data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                                        <?= ($config_ref && $config_ref['cod_producto'] == $producto['cod_producto']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($producto['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row" id="rowNombreProducto" style="<?= $config_ref ? '' : 'display:none;' ?>">
                                <div class="form-group col-md-12">
                                    <label>Nombre que verá el cliente <small class="text-muted">(editable)</small></label>
                                    <input type="text" class="form-control" id="txt_producto_nombre"
                                           placeholder="Ej: Café americano"
                                           value="<?= htmlspecialchars($config_ref['producto_nombre'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Monto mínimo de compra <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" min="0.01" class="form-control"
                                               id="txt_monto_minimo" placeholder="Ej: 15.00"
                                               value="<?= $config_ref ? $config_ref['monto_minimo'] : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Título de la promoción</label>
                                    <input type="text" class="form-control" id="txt_titulo"
                                           placeholder="Ej: ¡Producto gratis en tu primera orden!"
                                           value="<?= htmlspecialchars($config_ref['titulo'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio"
                                           value="<?= $config_ref ? substr($config_ref['fecha_inicio'], 0, 10) : date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha fin</label>
                                    <input type="date" class="form-control" id="fecha_fin"
                                           value="<?= $config_ref ? substr($config_ref['fecha_fin'], 0, 10) : '2099-12-31' ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="d-block">Disponible en Web</label>
                                    <label class="switch s-icons s-outline s-outline-success mb-0">
                                        <input type="checkbox" id="chk_is_web"
                                               <?= (!$config_ref || $config_ref['is_web']) ? 'checked' : '' ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="d-block">Disponible en App</label>
                                    <label class="switch s-icons s-outline s-outline-success mb-0">
                                        <input type="checkbox" id="chk_is_app"
                                               <?= (!$config_ref || $config_ref['is_app']) ? 'checked' : '' ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="widget-content widget-content-area br-6 mt-3">
                            <h5 class="mb-1">Imagen de la promoción</h5>
                            <p class="text-muted" style="font-size:12px;">PNG o JPG · 500×300 px recomendado</p>

                            <!-- Preview con overlay -->
                            <div id="pgImgContainer" <?= ($config_ref && $config_ref['imagen']) ? '' : 'style="display:none"' ?>>
                                <div class="pg-img-preview-wrap mb-2">
                                    <img id="pgImgPreview"
                                         src="<?= ($config_ref && $config_ref['imagen']) ? $files . $config_ref['imagen'] : '' ?>"
                                         alt="preview">
                                    <div class="pg-img-overlay">
                                        Estas a solo <strong id="ovMonto">
                                            <?= $config_ref ? '$' . number_format($config_ref['monto_minimo'], 2) : '$0.00' ?>
                                        </strong> para ganarte un
                                        <strong id="ovProducto">
                                            <?= htmlspecialchars($config_ref['producto_nombre'] ?? '—') ?>
                                        </strong> gratis
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnQuitarImgPG">
                                    <i data-feather="trash-2"></i> Quitar imagen
                                </button>
                            </div>

                            <!-- Uploader -->
                            <div id="pgImgUploader" <?= ($config_ref && $config_ref['imagen']) ? 'style="display:none"' : '' ?>>
                                <input type="file" id="inputImgPG" class="dropify-pg"
                                       accept=".jpg,.jpeg,.png"
                                       data-allowed-file-extensions="jpg jpeg png"
                                       data-max-file-size="5M">
                            </div>

                            <input type="hidden" id="txt_crop"         value="">
                            <input type="hidden" id="imagen_actual"    value="<?= htmlspecialchars($config_ref['imagen'] ?? '') ?>">
                        </div>

                        <div class="mt-3 text-right">
                            <button class="btn btn-primary px-4" id="btnGuardarPG">
                                <i data-feather="save"></i> Guardar configuración
                            </button>
                        </div>
                    </div>

                    <!-- Columna derecha: sucursales -->
                    <div class="col-xl-5 col-lg-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <h5 class="mb-3">Sucursales</h5>
                            <p class="text-muted" style="font-size:12px;">Activa las sucursales donde aplicará esta promoción.</p>
                            <?php foreach ($sucursalesAll as $suc):
                                $cod    = $suc['cod_sucursal'];
                                $activo = isset($registros_existentes[$cod]) && $registros_existentes[$cod]['estado'] === 'A';
                            ?>
                            <div class="itemSucursal">
                                <label for="chksuc<?= $cod ?>" class="mb-0">
                                    <span class="title"><?= htmlspecialchars($suc['nombre']) ?></span>
                                </label>
                                <label class="switch s-icons s-outline s-outline-success mb-0">
                                    <input type="checkbox" class="chkSucursal"
                                           id="chksuc<?= $cod ?>"
                                           value="<?= $cod ?>"
                                           <?= $activo ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
            <?php footer() ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script src="plugins/croppie/croppie.js"></script>
    <script src="plugins/dropify/dropify.min.js"></script>
    <script src="assets/js/pages/promocion_producto_gratis.js?v=1"></script>
</body>
</html>
