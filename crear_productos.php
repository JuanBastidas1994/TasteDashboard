<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_categorias.php";

if (!isLogin()) {
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clcategorias = new cl_categorias(NULL);
$Clproductos = new cl_productos(NULL);
$session = getSession();

$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

$cod_producto = 0;
$cod_producto_padre = 0;
// $imagen = url_sistema . '/assets/img/200x200.jpg';
$imagen ="";
$nombre = "";
$desc_corta = "";
$desc_larga = "";
$estado = "checked";
$open_detalle = "";
$base = "checked";
$combo = "";
$displayCombo = "display:none";
$txt_peso = 0;
$txt_volumen = 0;
$txt_sku = "";
$tiempo_preparacion = 0;
$fecha_create = "";
$fSinStock = "";
$precio = "";
$precio_anterior = "";
$peso = "";
$precio_no_tax = "";
$iva_valor = "";
$costo = "";
$days = "";
$etiquetas = [];

$showTimeline = "none";
if ($session['alias'] == "meet-galapagos")
    $showTimeline = "initial";

$varianteVisualizacion = array("LISTA", "SELECCIONAR");
$optionVarianteVisualizacion = "";
for ($i = 0; $i < count($varianteVisualizacion); $i++) {
    $optionVarianteVisualizacion .= '<option value="' . $varianteVisualizacion[$i] . '">' . ucfirst(strtolower($varianteVisualizacion[$i])) . '</option>';
}

$impuesto = 0;
$empresa = $Clempresas->get($session['cod_empresa']);
$impuesto = ($empresa['impuesto']/100)+1;
$tipo_empresa = $empresa['cod_tipo_empresa'];
$displayRetail = "display:none";
if ($tipo_empresa == 2) {
    $displayRetail = "";
}

// PERMISOS EMPRESA
$permisos = $Clempresas->getIdPermisionByBusiness($session['cod_empresa']);
$classNoShow = "
    .no-show-ingredients {
        display: none !important;
    }";
$classVariantesNoShow = "
    .contentVariantes {
        display: none;
    }";    
$classEmpaquesNoShow = "
    .contentEmpaques {
        display: none;
    }";    
if(in_array("PRODUCTO_INGREDIENTES", $permisos))
    $classNoShow = "";
    
if(in_array("PRODUCTO_VARIANTE", $permisos))
    $classVariantesNoShow = "";   
    
$empaque = [
    'unidades' => 0,
    'alto' => 0
];



$sizeMinWidth = 150;
$sizeMinHeight = 150;
$sizeMaxWidth = 400;
$sizeMaxHeight = 400;
$quality = 0.8;
$alias = "";

$sizeCrop = $Clempresas->getSizeCrop($session['cod_empresa']);
if ($sizeCrop) {
    $sizeMinWidth = $sizeCrop['size_min_width'];
    $sizeMinHeight = $sizeCrop['size_min_height'];
    $sizeMaxWidth = $sizeCrop['size_max_width'];
    $sizeMaxHeight = $sizeCrop['size_max_height'];
    $quality = $sizeCrop['quality'];
}

if (isset($_GET['id'])) {
    $alias = $_GET['id'];
    $producto = NULL;
    if ($Clproductos->getArrayByAlias($alias, $producto)) {
        $cod_producto = $producto['cod_producto'];
        $cod_producto_padre = $producto['cod_producto_padre'];
        $imagen = $files . $producto['image_min'];
        $nombre = $producto['nombre'];
        $desc_corta = $producto['desc_corta'];
        $desc_larga = editor_decode($producto['desc_larga']);
        $categorias = $Clproductos->get_categorias($cod_producto);
        $etiquetas = $Clproductos->getEtiquetas($cod_producto);
        $precio = $producto['precio'];
        $precio_anterior = $producto['precio_anterior'];
        $peso = $producto['peso'];
        $precio_no_tax = $producto['precio_no_tax'];
        $iva_valor = $producto['iva_valor'];
        $costo = $producto['costo'];

        if ($producto['estado'] == 'I')
            $estado = "";
        if ($producto['open_detalle'] == "0")
            $open_detalle = "checked";

        if ($producto['is_combo'] == 1) {
            $combo = "checked";
            $displayCombo = "";
        }

        $fecha_create = $producto['fecha_create'];

        
        if ($producto['noStock'] == 1)
            $fSinStock = "checked";

        if ($producto['cobra_iva'] == 0)
            $base = "";

        $txt_peso = $producto['peso'];
        $txt_volumen = $producto['volumen'];
        $txt_sku = $producto['sku'];
        $tiempo_preparacion = $producto['tiempo_preparacion'];

        $optionVarianteVisualizacion = "";
        for ($i = 0; $i < count($varianteVisualizacion); $i++) {
            $chkVarianteVisualizacion = "";
            if ($producto['variante_visualizacion'] == $varianteVisualizacion[$i])
                $chkVarianteVisualizacion .= "selected";
            $optionVarianteVisualizacion .= '<option value="' . $varianteVisualizacion[$i] . '" ' . $chkVarianteVisualizacion . '>' . ucfirst(strtolower($varianteVisualizacion[$i])) . '</option>';
        }


        $days = $Clproductos->getDays($cod_producto);
        $days = ($days !== false) ? implode(",", $days) : "";
        
        if(in_array("PRODUCTO_EMPAQUE", $permisos)){
            $classEmpaquesNoShow = "";   
            $resp = $Clproductos->getEmpaque($cod_producto);
            if($resp){
                $empaque = $resp;
            }
        }
    } else {
        header("location: ./index.php");
    }
}else{
    $categorias = [];
}

$empresa = $Clempresas->get($session['cod_empresa']);
$tipoRecorte = $empresa['tipo_recorte'];

$cod_variaciones = [];
$variaciones = [];
function recursive($array, $posicion, &$data, &$codigos)
{
    global $variaciones, $cod_variaciones;
    $opcion = $array[$posicion]['detalle'];
    foreach ($opcion as $key => $value) {
        if (isset($array[$posicion + 1])) {
            $data[0][$posicion] = $value['detalle'];
            $codigos[0][$posicion] = intval($value['cod_producto_caracteristica_detalle']);
            recursive($array, $posicion + 1, $data, $codigos);
        } else {
            $data[0][$posicion] = $value['detalle'];
            $codigos[0][$posicion] = intval($value['cod_producto_caracteristica_detalle']);
            $variaciones[] = $data[0];
            $cod_variaciones[] = $codigos[0];
        }
    }
}
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
            border-radius: 6px;
            border: 1px solid #e0e6ed;
            padding: 14px 26px;
            margin-bottom: 10px;
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
        <?=$classNoShow?>
        <?=$classVariantesNoShow?>
        <?=$classEmpaquesNoShow?>
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>

<body>
    <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RECORTADOR</h5>
                    <input type="hidden" id="txtalias" value="<?= $session['alias'] ?>">

                    <input type="hidden" id="minWidth" value="<?= $sizeMinWidth ?>">
                    <input type="hidden" id="minHeight" value="<?= $sizeMinHeight ?>">
                    <input type="hidden" id="maxWidth" value="<?= $sizeMaxWidth ?>">
                    <input type="hidden" id="maxHeight" value="<?= $sizeMaxHeight ?>">
                    <input type="hidden" id="quality" value="<?= $quality ?>">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="x_content">
                        <div class="form-group">

                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <input type="hidden" id="txt_crop" name="txt_crop" value="" />
                                <input type="hidden" id="txt_crop_min" name="txt_crop_min" value="" />
                                <img id="my-image" src="#" style="width: 100%; max-height: 400px;" />
                            </div>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
                    <button class="btn btn-dark crop-rotate" data-deg="-90">Rotate Left</button>
                    <button class="btn btn-dark crop-rotate" data-deg="90">Rotate Right</button>
                    <button type="button" class="btn btn-primary" id="crop-get">Recortar</button>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL ITEMS -->
    <div class="modal fade bs-example-modal-lg" id="modalItems" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Opciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="x_content">
                        <form id="frmOpciones" method="POST" action="#">
                            <div class="row">
                                <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                    <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="¿Que bebida deseas?" name="txt_opcion_titulo" id="txt_opcion_titulo" class="form-control" required="required" autocomplete="off" value="">
                                </div>
                                <div class="form-group col-md-2 col-sm-2 col-xs-12">
                                    <label>Min <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Esto campo indica al usuario, cuantos productos maximo podra escoger por opcion, ejemplo: 2 Bebidas"></span>
                                    </label>
                                    <input type="number" value="0" name="txt_opciones_cantidad" id="txt_opciones_cantidad" class="form-control" autocomplete="off" value="" required="required">
                                </div>
                                <div class="form-group col-md-2 col-sm-2 col-xs-12">
                                    <label>Max <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Esto campo indica al usuario, cuantos productos maximo podra escoger por opcion, ejemplo: 2 Bebidas"></span>
                                    </label>
                                    <input type="number" name="txt_opciones_cantidad_max" id="txt_opciones_cantidad_max" class="form-control" autocomplete="off" value="" required="required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <label>Tipo <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Esto campo indica al usuario, cuantos productos maximo podra escoger por opcion, ejemplo: 2 Bebidas"></span>
                                    </label>
                                    <select class="form-control" name="cmb_tipo_opcion" id="cmb_tipo_opcion">
                                        <option value="0">A&ntilde;adir opci&oacute;n</option>
                                        <option value="1">A&ntilde;adir producto</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <label>Uno o multi <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Esto campo indica al usuario, cuantos productos maximo podra escoger por opcion, ejemplo: 2 Bebidas"></span>
                                    </label>
                                    <select class="form-control" name="cmb_isCheck" id="cmb_isCheck">
                                        <option value="0">Multi</option>
                                        <option value="1">Uno</option>
                                        <option value="2">Abierto (en desarrollo. NO USAR)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>Productos <span class="asterisco">*</span>
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoja los productos que el usuario tendra que decidir a escoger"></span></label>
                                    <select multiple="multiple" name="cmb_productos[]" id="cmb_productos" class="form-control basic selectOpc" required="required">

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div align="right" class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <button type="button" class="btn btn-outline-primary" id="addItem" data-tipo="G">A&ntilde;adir</button>
                                </div>
                            </div>

                            <div class="row" id="tituOpciones">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <h4>Items</h4>
                                    <input type="text" id="id_det_cab" name="id_det_cab" style="display:none">
                                </div>
                            </div>

                            <div class="row" id="divTablaOpciones">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nombre Detalle</th>
                                                <th>Agregar Precio</th>
                                                <th style="text-align: right;">Precio ($)</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbodyOPc connectedSortable">

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="modal-footer" id="divBotonesOpciones">
                    <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
                    <button class="btn btn-primary" id="btnAgregarOpcion">Guardar</button>
                    <button class="btn btn-primary" id="btnEditOpcion" style="display: none;">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL ITEMS -->

    <!--MODAL IMPORTAR -->
    <div class="modal fade bs-example-modal-lg" id="modalImporar" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Importar Opciones de otros productos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="x_content">
                        <form id="frmOpcionesImportar" method="POST" action="#">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>Productos</th>
                                                <th>Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="style-4" class="respOpcionesImportar">
                                            <?php
                                            $resp = $Clproductos->listaOld();
                                            foreach ($resp as $p) {
                                                $opciones = $Clproductos->opciones($p['cod_producto']);
                                                if (!$opciones) {
                                                    // echo '<tr><td colspan="4">No hay opciones</td></tr>';
                                                } else {
                                                    if ($p['cod_producto'] != $cod_producto)
                                                        echo '<tr data-id="' . $p['cod_producto'] . '" class="trImport product_' . $p['cod_producto'] . '">
                                                            <td></td>
                                                            <td><strong>' . strtoupper($p['nombre']) . '</strong></td>
                                                            <td>
                                                                <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye btnview"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                                <input type="hidden" class="viewImport" name="viewImport" id="viewImport" value="0"/>
                                                                <input type="hidden" class="codopcion" name="codopcion" id="codopcion" value="' . $p['cod_producto'] . '"/>
                                                            </td>
                                                            </tr>';
                                                    foreach ($opciones as $op) {
                                                        $htmlOpciones = "";
                                                        $detalles = $Clproductos->detalles($op['cod_producto_opcion'], $op['isDatabase']);
                                                        $htmlOpciones = implode(", ", $detalles);
                                                        echo '
                                                    <tr class="view_' . $op['cod_producto'] . ' trOpImport" style="display:none">
                                                    <input type="hidden" class="codopcion" name="codopcion" id="codopcion" value="' . $p['cod_producto'] . '"/>
                                                      <td>
                                                          <button data-id="' . $op['cod_producto_opcion'] . '" type="button" class="btn btn-secondary btnImportarOpcion">Importar</button>
                                                      </td>
                                                      <td class="nameIm">' . $op['titulo'] . '</td>
                                                      <td class="opIm">' . $htmlOpciones . '</td>
                                                      <td class="minIm" style="display:none">' . $op['cantidad_min'] . '</td>
                                                      <td class="maxIm" style="display:none">' . $op['cantidad'] . '</td>
                                                    </tr>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <!--MODAL IMPORTAR -->

    <!-- MODAL PRODUCTO INGREDIENTES -->
    <div class="modal" tabindex="-1" role="dialog" id="modalIngredientes">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nombre Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="lstIngredientes">
                        <div class="row align-items-center">
                            <div class="col-6 col-lg-3">
                                <select class="form-control" id="cmbIngredientes">
                                    
                                </select>                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <input type="number" class="form-control" placeholder="1" id="cantidadIngrediente">                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <p id="unidadMedida"></p>                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <button class="btn btn-primary" onclick="agregarIngredienteProducto()">Añadir</button>                         
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div>
                        <div>
                            <h5 class="mt-5">Lista de ingredientes añadidos</h5>
                            <div>
                                <hr>
                            </div>
                        </div>
                        <div class="lstProductosIngredientes"></div>
                    </div>
                    <!-- LISTA DE INGREDIENTES -->
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL PRODUCTO INGREDIENTES -->
    
    <!-- MODAL OPCIONES INGREDIENTES -->
    <div class="modal" tabindex="-1" role="dialog" id="modalOpcionesIngredientes">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nombre Producto</h5>
                    <input type="hidden" id="idOpcion">
                    <button type="button" class="close" id="btnCloseModalOpcionesIngredientes">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="lstIngredientes">
                        <div class="row align-items-center">
                            <div class="col-6 col-lg-3">
                                <select class="form-control" id="cmbOpcionesIngredientes">
                                    
                                </select>                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <input type="number" class="form-control" placeholder="1" id="cantidadOpcionesIngrediente">                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <p id="unidadMedidaOpciones"></p>                         
                            </div>
                            <div class="col-6 col-lg-3">
                                <button class="btn btn-primary" onclick="agregarIngredienteProductoOpciones()">Añadir</button>                         
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div>
                        <div>
                            <h5 class="mt-5">Lista de ingredientes añadidos</h5>
                            <div>
                                <hr>
                            </div>
                        </div>
                        <div class="lstProductosOpcionesIngredientes"></div>
                    </div>
                    <!-- LISTA DE INGREDIENTES -->
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL OPCIONES INGREDIENTES -->

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
                    <div><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Productos</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Producto"; ?></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_producto != 0) ? "" : "display: none;";  ?>">
                        <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nuevo Producto</span>
                        </span>

                        <span style="cursor: pointer;margin-right: 15px;display: none;">
                            <i class="feather-16" data-feather="copy"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Duplicar</span>
                        </span>

                        <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                        </span>

                        <!-- TIMELINE -->
                        <span style="cursor: pointer;margin-right: 15px; display:<?= $showTimeline ?>">
                            <a href="timelines.php?prd=<?= $cod_producto ?>">
                                <i class="feather-16" data-feather="activity"></i>
                                <span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Timeline</span>
                            </a>
                        </span>

                        <?php
                        echo '<span style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="calendar"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> ' . fechaLatinoShort($fecha_create) . '</span>
                      </span>';

                        ?>
                        <span style="cursor: pointer;margin-right: 15px;" class="no-show-ingredients">
                            <!-- <a href="javascript:;" id="showIngredients" onclick="getIngredientes()">
                                <i class="feather-16" data-feather="coffee"></i>
                                <span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Adm. Ingredientes</span>
                            </a> -->
                            <a href="producto_ingrediente.php?id=<?= $alias ?>" target="_blank">
                                <i class="feather-16" data-feather="coffee"></i>
                                <span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Adm. Ingredientes</span>
                            </a>
                        </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <!-- Informacion basica -->
                        <div class="widget-content widget-content-area br-6">
                            <input type="hidden" name="id" id="id" value="<?php echo $cod_producto; ?>">
                            <input type="hidden" name="tipoRecorte" id="tipoRecorte" value="<?php echo $tipoRecorte; ?>">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" name="cod_producto_padre" id="cod_producto_padre" value="<?php echo $cod_producto_padre; ?>">
                                <div class="x_content">
                                    <div class="col-md-3 col-sm-3 col-xs-12">
                                        <div class="upload mt-1 pr-md-1">
                                            <input type="file" name="img_product" id="dropifyPerfil" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="15M" data-allowed-file-extensions="jpeg jpg png" />
                                            <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Título" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off" value="<?php echo $nombre; ?>">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <label>Categor&iacute;as <span class="asterisco">*</span></label>
                                            <select multiple="multiple" name="cmb_categoria[]" id="cmb_categoria" class="form-control" required="required">
                                                <?php
                                                $resp = $Clcategorias->lista();
                                                foreach ($resp as $categoria) {
                                                    $selected = "";
                                                    if (in_array($categoria['cod_categoria'], $categorias))
                                                        $selected = 'selected="selected"';
                                                    echo '<option ' . $selected . ' value="' . $categoria['cod_categoria'] . '">' . $categoria['categoria'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                        $cod_tipo_empresa = $empresa['cod_tipo_empresa'];
                                        $display = "none";
                                        if ($cod_tipo_empresa <> 1)
                                            $display = "initial";
                                        ?>

                                        <div class="row">
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Peso<span class="asterisco">*</span><span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Peso en Kg"></span></label>
                                                <input type="number" name="txt_peso" id="txt_peso" class="form-control" value="<?php echo $txt_peso; ?>">
                                            </div>
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Volumen<span class="asterisco">*</span><span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Volumen"></span></label>
                                                <input type="number" name="txt_volumen" id="txt_volumen" class="form-control" value="<?php echo $txt_volumen; ?>">
                                            </div>

                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>SKU<span class="asterisco">*</span><span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="codigo de artículo"></span></label>
                                                <input type="text" name="txt_sku" id="txt_sku" class="form-control" value="<?php echo $txt_sku; ?>">
                                            </div>
                                            
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Tiempo Preparación<span class="asterisco">*</span><span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Tiempo de preparación del producto, debe ser multiplo de 5"></span></label>
                                                <input type="text" name="txt_preparacion" id="txt_preparacion" class="form-control" value="<?php echo $tiempo_preparacion; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                            <label>Estado <span class="asterisco">*</span></label>
                                            <div>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                    <input type="checkbox" name="chk_estado" id="chk_estado" <?php echo $estado; ?> />
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                            <label>Agregar directo al carrito <span class="asterisco">*</span>
                                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="No abrirá el detalle del producto, el item se agregará directamente al carrito (Si tiene opciones o variantes no se podra activar)"></span>
                                            </label>
                                            <div>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                    <input type="checkbox" name="chk_detalle" id="chk_detalle" <?php echo $open_detalle; ?> />
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;<?php echo $displayRetail ?>">
                                            <label>Es un combo?<span class="asterisco">*</span>
                                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Arma tu combo o kit agregando otros productos."></span>
                                            </label>
                                            <div>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                    <input type="checkbox" name="chk_combo" id="chk_combo" <?php echo $combo; ?> />
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;<?php echo $displayRetail ?>">
                                            <label>Facturar sin stock<span class="asterisco">*</span>
                                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Si está activo permite que el cliente pueda comprar este producto aunque no haya en stock."></span>
                                            </label>
                                            <div>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                    <input type="checkbox" name="chk_fSinStock" id="chk_fSinStock" <?php echo $fSinStock; ?> />
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                            <label>Descripción</label>
                                            <textarea name="txt_descripcion_corta" id="txt_descripcion_corta" class="form-control" autocomplete="off" style="resize: none;"><?php echo $desc_corta; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row d-none">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                            <label>Descripción Larga</label>
                                            <textarea name="txt_descripcion_larga" id="editor1" class="form-control" autocomplete="off" style="resize: none;"><?php echo $desc_larga; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Informacion Características -->
                        <?php
                        if (intval($cod_producto_padre) == 0) {
                        ?>
                            <div class="widget-content widget-content-area br-6 contentVariantes" style="margin-top: 15px;">
                                <div>
                                    <h4>Características</h4>
                                    <p>Este producto tiene múltiples variaciones, as&iacute; como diferentes tama&ntilde;os o colores</p>
                                </div>

                                <?php
                                if ($cod_producto == 0)
                                    $caracteristicas = false;
                                else
                                    $caracteristicas = $Clproductos->getCaracteristicas($cod_producto);
                                if (!$caracteristicas) {
                                ?>
                                    <form id="frmCaracteristicas" class="frmCaracteristicas" method="POST" action="#">
                                        <div class="VariantesSeleccion">
                                            <div class="row">
                                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                    <label>Caracter&iacute;stica <span class="asterisco">*</span></label>
                                                    <input type="text" placeholder="Ej. Talla" name="txt_opcion_titulo[]" class="form-control" required="required" autocomplete="off" value="">
                                                </div>
                                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                    <label>Atributos <span class="asterisco">*</span>
                                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoja los productos que el usuario tendra que decidir a escoger"></span><span><i>&nbsp;Separar las opciones con una coma</i></span></label>
                                                    <select multiple="multiple" name="cmb_variante_productos[0][]" class="form-control tagging" required="required">
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2 col-sm-2 col-xs-12">
                                                    <label>Tipo</label>
                                                    <select name="cmb_variante_tipo[]" class="form-control" required="required">
                                                        <option value="texto">Texto</option>
                                                        <option value="color">Color</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                <button type="button" class="btn btn-outline-primary" id="btnAgregarVariante">Agregar otra caracter&iacute;stica</button>
                                                <button type="button" class="btn btn-outline-primary" id="btnValidarVariante">Validar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-hover table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Característica</th>
                                                            <th>Color</th>
                                                            <th>&nbsp;</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="respAtributosValidar">

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                <button type="button" class="btn btn-outline-primary" id="btnGuardarCaracteristicas">Guardar Características</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                ?>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <?php
                                            foreach ($caracteristicas as $key => $caracteristica) {
                                                $items = "";
                                                $detalleCaracteristica = $caracteristica['detalle'];
                                                foreach ($detalleCaracteristica as $detalle) {
                                                    $style = '';
                                                    if ($caracteristica['tipo'] == "COLOR")
                                                        $style = 'style="background:' . $detalle['detalle2'] . '40; color:' . $detalle['detalle2'] . '; border: 0.1px solid #d1cfcf;"';
                                                    $items .= '<span class="shadow-none badge badge-primary" ' . $style . '>' . $detalle['detalle'] . '</span> &nbsp;';
                                                }

                                                echo '<div>
                                                <h5>' . $caracteristica['caracteristica'] . '</h5>
                                                ' . $items . '
                                          </div>';
                                            }

                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        } else {  //SOLO PARA VARIANTES
                        ?>
                            <div class="widget-content widget-content-area br-6 contentVariantes" style="margin-top: 15px;">
                                <div>
                                    <h4>Características</h4>
                                    <p>Características de esta variante</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <?php
                                        $caracteristicas = $Clproductos->get_atributos_variante($cod_producto);
                                        foreach ($caracteristicas as $key => $caracteristica) {
                                            echo '<span class="shadow-none badge badge-primary">' . $caracteristica['detalle'] . '</span> &nbsp;';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <form action="" id="frmVarianteEditarCaracteristicas">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <p>Características del producto padre</p>
                                            <?php
                                            $caracteristicas = $Clproductos->getCaracteristicas($cod_producto_padre);
                                            foreach ($caracteristicas as $key => $caracteristica) {
                                                $items = "";
                                                $detalleCaracteristica = $caracteristica['detalle'];
                                                foreach ($detalleCaracteristica as $detalle) {
                                                    $items .= '<option value="' . $detalle['cod_producto_caracteristica_detalle'] . '">' . $detalle['detalle'] . '</option>';
                                                }

                                                echo '<div>
                                                  <h5>' . $caracteristica['caracteristica'] . '</h5>
                                                  <select name="cmbAtributoVariante[]" class="form-control">
                                                    ' . $items . '
                                                  </select>
                                            </div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:right;">
                                            <button class="btn btn-primary btnActualizarAtributosVariante">Actualizar atributos</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- Informacion Variantes -->
                        <?php
                        if (intval($cod_producto_padre) == 0) {
                        ?>
                            <div class="widget-content widget-content-area br-6 contentVariantes" style="margin-top: 15px;">
                                <div>
                                    <h4>Variantes</h4>
                                    <p>Este producto tiene múltiples variaciones, as&iacute; como diferentes tama&ntilde;os o colores</p>

                                    <div class="row" style="margin: 20px 0;">
                                        <div class="col-md-offset-6 col-md-6 col-xs-12">
                                            <p>Mostrar las variantes en:</p>
                                            <select class="form-control" name="cmbVarianteVisualizacion" id="cmbVarianteVisualizacion">
                                                <?= $optionVarianteVisualizacion ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if ($cod_producto == 0)
                                    $variantes = false;
                                else
                                    $variantes = $Clproductos->lista_variantes($cod_producto);

                                if (!$variantes) {
                                    if (!$caracteristicas)
                                        echo 'Para crear variantes el producto debe tener creada características';
                                    else {
                                        $info = null;
                                        $cods = null;
                                        recursive($caracteristicas, 0, $info, $cods);
                                ?>
                                        <form id="frmVariantes" class="frmVariantes" method="POST" action="#">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <table class="table table-hover table-bordered style-3">
                                                        <thead>
                                                            <tr>
                                                                <th>Variante</th>
                                                                <th>Precio</th>
                                                                <th>SKU</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="">
                                                            <?php
                                                            foreach ($variaciones as $key => $items) {
                                                                $attrTexto = base64_encode(json_encode($items));
                                                                $attrCodigos = base64_encode(json_encode($cod_variaciones[$key]));
                                                                $texto = implode("/", $items);
                                                                echo '<tr>
                                                        <td>' . $texto . '</td>
                                                        <td>
                                                            <input type="text" value="0.00" name="txt_precio_variante[]" class="form-control"/>
                                                            <input type="hidden" value="' . $attrTexto . '" name="txt_atributos_texto[]" class="form-control"/>
                                                            <input type="hidden" value="' . $attrCodigos . '" name="txt_atributos_codigo[]" class="form-control"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="txt_variante_sku[]" class="form-control" placeholder="SKU" value="' . $txt_sku . '"/>
                                                        </td>
                                                    </tr>';
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                    <button type="button" class="btn btn-outline-primary" id="btnGuardarVariante">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-hover table-bordered style-3">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>Variaciones</th>
                                                        <th>Precio</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="respVariantes">
                                                    <?php
                                                    foreach ($variantes as $variante) {
                                                        $nombreVariante = $variante['nombre'];
                                                        $imagenVariante = $files . $variante['image_min'];
                                                        // $htmlOpciones = implode("/", $variante['atributos']);
                                                        $htmlOpciones = ($variante['atributos']) ? implode("/", $variante['atributos']) : "";
                                                        echo '
                                            <tr>
                                              <td class="text-center">
                                                  <span><img src="' . $imagenVariante . '" class="profile-img" alt="' . $nombreVariante . '"></span>
                                              </td>
                                              <td>' . $htmlOpciones . '</td>
                                              <td>$' . $variante['precio'] . '</td>
                                              <td>
                                                <a target="_blank" href="crear_productos.php?id=' . $variante['alias'] . '" data-value="' . $variante['alias'] . '"  class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar Variante"><i data-feather="edit-2"></i></a>
                                              </td>
                                            </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <form id="frmVariantes" method="POST" action="#">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <p><b>Nueva Variante</b></p>
                                                <div class="form-group col-md-6 col-sm-8 col-xs-12">
                                                    <label>Atributos <span class="asterisco">*</span>
                                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escriba separado por comas ',' los atributos de este producto"></span></label>
                                                    <select multiple="multiple" id="cmb_new_variante_atributos" name="cmb_new_variante_atributos[]" class="form-control tagging" required="required">
                                                    </select>
                                                    <input type="hidden" id="txt_new_variante_atributos" value="" name="txt_atributos_variante[]" class="form-control" />
                                                </div>
                                                <div class="form-group col-md-3 col-sm-4 col-xs-12">
                                                    <label>SKU <span class="asterisco">*</span></label>
                                                    <input type="number" placeholder="0.00" name="txt_variante_sku[]" class="form-control" required="required" autocomplete="off" value="">
                                                </div>
                                                <div class="form-group col-md-3 col-sm-4 col-xs-12">
                                                    <label>Precio <span class="asterisco">*</span></label>
                                                    <input type="number" placeholder="0.00" name="txt_precio_variante[]" class="form-control" required="required" autocomplete="off" value="">
                                                </div>
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                    <button type="button" class="btn btn-outline-primary" id="btnAddNewVariante">Agregar</button>
                                                    <button type="button" class="btn btn-outline-primary" id="btnGuardarVariante" style="display:none;">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- Informacion Opciones -->
                        <?php
                        if ($tipo_empresa != 2) {
                        ?>
                            <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                                <div>
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-10 col-sm-10 col-xs-12">
                                            <h4>Opciones</h4>
                                        </div>
                                        <div class="form-group col-md-2 col-sm-2 col-xs-12">
                                            <button type="button" style="text-align: right;" class="btn btn-success" id="btnImportar">Importar</button>
                                        </div>
                                        <span class="offset-md-8 offset-sm-8 col-md-6 col-sm-6 col-xs-12" id="detailImport" style="color:red;display:none">Importar opciones de otros productos</span>
                                    </div>
                                    <p>Este apartado sirve para cuando un producto le da opciones a escoger a un usuario, como por ejemplo <b>Encebollado con bebida</b>, donde las bedidas serían las opciones a escoger.</p>
                                </div>
                                <form id="frmOpciones2" method="POST" action="#">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Titulo</th>
                                                        <th>Productos</th>
                                                        <th>Cant min.</th>
                                                        <th>Cant max.</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="style-33" class="connectedSortable respOpciones">
                                                    <?php
                                                    $opciones = $Clproductos->opciones($cod_producto);
                                                    if (!$opciones) {
                                                        echo '<tr><td colspan="4">No hay opciones</td></tr>';
                                                    }
                                                    foreach ($opciones as $op) {
                                                        //$htmlOpciones = implode(", ", $op['items']);
                                                        $htmlOpciones = "";
                                                        $detalles = $Clproductos->detalles($op['cod_producto_opcion'], $op['isDatabase']);
                                                        $htmlOpciones = implode(", ", $detalles);
                                                        //<td>'.$htmlOpciones.'</td>
                                                        echo '
                                            <tr data-id="' . $op['cod_producto_opcion'] . '" >
                                              <td>' . $op['titulo'] . '</td>
                                              <td>' . $htmlOpciones . '</td>
                                              <td style="text-align: center;">' . $op['cantidad_min'] . '</td>
                                              <td style="text-align: center;">' . $op['cantidad'] . '</td>
                                              <td>
                                                <a href="javascript:void(0);" data-value="' . $op['cod_producto_opcion'] . '"  class="bs-tooltip btnEditarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a>
                                                <a href="javascript:void(0);" data-value="' . $op['cod_producto_opcion'] . '"  class="bs-tooltip btnEliminarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                                              </td>
                                            </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                            <button type="button" class="btn btn-outline-primary" id="btnAbrirModal">A&ntilde;adir</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">

                        <!--ARMA TU COMBO O KIT -->

                        <div class="widget-content widget-content-area br-6 dataCombo" style="margin-bottom: 15px;<?php echo $displayCombo ?>">
                            <div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-10 col-sm-10 col-xs-12">
                                        <h4>Arma tu Kit o Combo</h4>
                                    </div>
                                </div>
                            </div>

                            <form id="frmOpcionesCombo" method="POST" action="#">
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <label>Productos <span class="asterisco">*</span></label>
                                        <select multiple="multiple" name="cmb_productosCombo[]" id="cmb_productosCombo" class="form-control basic selectOpc" required="required">
                                            <?php
                                            // $listaProductos = $Clproductos->get_Combo($cod_producto);
                                            // $listaP = $Clproductos->lista();
                                            // foreach ($listaP as $c) {
                                            //     $selected = "";
                                            //     if (in_array($c['cod_producto'], $listaProductos))
                                            //         $selected = 'selected="selected"';
                                            //     echo '<option ' . $selected . ' value="' . $c['cod_producto'] . '" data-peso="' . $c['peso'] . '">' . $c['nombre'] . '</option>';
                                            // }
                                            ?>
                                        </select>

                                        <div class="row">
                                            <div align="right" class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <button type="button" class="btn btn-primary" id="addItemCombo">A&ntilde;adir</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody id="style-3" class="tbodyCombo connectedSortable respOpcionesCombo">
                                                <?php
                                                $peso = 0;
                                                $lista = $Clproductos->lista_Combo($cod_producto);
                                                if ($lista) {
                                                    foreach ($lista as $c) {
                                                        $total = $c['peso'] * $c['cantidad'];
                                                        $peso = $total + $peso;
                                                        echo '
                                                    <tr class="trItem">
                                                          <td>
                                                                <input class="form-control txt_cod_producDetalle" name="txt_cod_producDetalle[]" value="' . $c['cod_producto_detalle'] . '" type="hidden">
                                                                <input class="form-control txt_cod_hijo" name="txt_cod_hijo[]" value="' . $c['cod_producto_hijo'] . '" type="hidden">
                                                                <input class="form-control txt_peso_combo" name="txt_peso_combo[]" value="' . $c['peso'] . '" type="hidden">
                                                                <input class="form-control txt_nomCombo" name="txt_nomCombo[]" value="' . $c['nombre'] . '"  readonly>
                                                            </td>
                                                          <td><input type="number" class="form-control txt_cantidadCombo" name="txt_cantidadCombo[]" data-peso="' . $c['peso'] . '" placeholder="cantidad" min="1" value="' . $c['cantidad'] . '" style="text-align: right;"></td>    
                                                          <td>
                                                            <a href="javascript:void(0);" data-value="' . $c['cod_producto_detalle'] . '"  class="bs-tooltip btnEliminarCombo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                                                          </td>
                                                        </tr>
                                                    ';
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <?php
                                        echo '<tr>
                                                <td>
                                                    <label>Total peso Aprox:</label>
                                                </td>
                                                <td>
                                                    <label class="txt_pesoApr">' . $peso . ' Kg</label>
                                                </td>
                                                </tr>';
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary" id="btnGuardarCombo">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Precio -->

                        <div class="widget-content widget-content-area br-6">
                            <div>
                                <h4>Precio</h4>
                            </div>
                            <form id="frmPrecios" method="POST" action="#">
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Precio de Venta (PVP)</label>
                                            <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este campo sirve para que en la web se muestre el precio del producto con IVA"></span>
                                            <input type="number" placeholder="0.00" name="txt_precio" id="txt_precio" class="form-control" required="required" autocomplete="off" value="<?php echo $precio; ?>">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Precio comparaci&oacute;n
                                            <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este campo sirve para que en la web se muestre el producto con el precio rebajado, No obligatorio"></span>
                                        </label>
                                        <input type="number" placeholder="0.00" name="txt_precio_anterior" id="txt_precio_anterior" class="form-control" autocomplete="off" value="<?php echo $precio_anterior; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <input type="hidden" id="txt_impuesto" value="<?= $impuesto ?>" />
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label>Iva</label>
                                            <input type="number" placeholder="0.00" name="txt_iva" id="txt_iva" class="form-control" required="required" autocomplete="off" value="<?php echo $iva_valor; ?>" disabled>
                                            <input type="hidden" name="txt_ivaC" id="txt_ivaC" class="form-control" required="required" autocomplete="off" value="<?php echo $iva_valor; ?>">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Precio no tax
                                        </label>
                                        <input type="number" placeholder="0.00" name="txt_precio_no_tax" id="txt_precio_no_tax" class="form-control" autocomplete="off" value="<?php echo $precio_no_tax; ?>" disabled>
                                        <input type="hidden" name="txt_precio_no_taxC" id="txt_precio_no_taxC" class="form-control" required="required" autocomplete="off" value="<?php echo $precio_no_tax; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <input type="hidden" name="txt_baseC" id="txt_baseC" class="form-control" value="<?php echo $base; ?>">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="<?php $displayRetail ?>">
                                        <div class="row">
                                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                <input type="checkbox" name="chk_base" id="chk_base" <?php echo $base; ?> />
                                                <span class="slider round"></span>
                                            </label>
                                            <label>Cobra impuesto sobre la renta sobre este producto
                                                <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Se agrega el IVA al producto"></span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="row" style="display:none">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Costo del productos</label>
                                        <input type="number" placeholder="0.00" name="txt_costo" id="txt_costo" class="form-control" autocomplete="off" value="<?php echo $costo; ?>">
                                    </div>
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                        <label>Margen</label>
                                        <p class="form-control">33%</p>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                        <label>Ganancia </label>
                                        <p class="form-control">$3.00</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Precio por disponibilidad -->
                        <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                            <div>
                                <h4>Disponibilidad</h4>
                            </div>
                            <form id="frmDisponibilidad" method="POST" action="#">
                                <div class="row">
                                    <?php
                                    $sucursales = $Clsucursales->lista();
                                    foreach ($sucursales as $suc) {
                                        $cod_sucursal = $suc['cod_sucursal'];
                                        $nombre = $suc['nombre'];
                                        $precioDisponibilidad = floatval(0);
                                        $precioAnteriorDisponibilidad = floatval(0);
                                        $estado = '';
                                        $chkprecio = "";
                                        $style = "none";
                                        $replace = 0;
                                        $selecreplace = 0;
                                        if ($cod_producto > 0) {
                                            $precioDisponibilidad = floatval($producto['precio']);
                                            $precioAnteriorDisponibilidad = floatval($producto['precio_anterior']);


                                            $disponibilidad = $Clproductos->getdisponibilidad($cod_producto, $cod_sucursal);
                                            if ($disponibilidad) {
                                                $precioDisponibilidad = floatval($disponibilidad['precio']);
                                                $precioAnteriorDisponibilidad = floatval($disponibilidad['precio_anterior']);
                                                $estado = $disponibilidad['estado'];
                                                $replace = $disponibilidad['replacePrice'];
                                            }
                                        }


                                        $checked = "checked";
                                        $seleccionado = 1;

                                        if ($estado == 'A') {
                                            $checked = 'checked';
                                            $seleccionado = 1;
                                        } else if ($estado == 'I') {
                                            $checked = "";
                                            $seleccionado = 0;
                                        }

                                        if ($replace == 1) {
                                            $selecreplace = 1;
                                            $chkprecio = "checked";
                                            $style = "block";
                                        }

                                        echo '
                              <div class="form-group col-md-12 col-sm-12 col-xs-12 itemSucursal">
                                <div class="col-md-6 col-sm-6 col-xs-6"><label for="chksucursal' . $cod_sucursal . '"><span class="title">' . $nombre . '</span></label></div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                <h6>Activar</h6>
                                  <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                      <input type="checkbox" class="chkDisponibilidad" name="chkSucursal[]" id="chksucursal' . $cod_sucursal . '" ' . $checked . ' value="' . $cod_sucursal . '">
                                      <span class="slider round"></span>
                                  </label>
                                  </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">  
                                  <h6>Precio</h6>
                                  <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                      <input type="checkbox" class="chkPrecio" name="chkPrecio[]" id="chkprecio' . $cod_sucursal . '" ' . $chkprecio . ' value="' . $cod_sucursal . '">
                                      <span class="slider round"></span>
                                  </label>                                      
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12 contentPrecio" style="display:' . $style . '">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Precio de Venta (PVP) <span class="asterisco">*</span></label>
                                        <input type="number" placeholder="0.00" name="txt_precio_sucursal[]" class="form-control" required="required" autocomplete="off" value="' . $precioDisponibilidad . '">

                                        <input type="hidden" name="id[]" value="' . $cod_sucursal . '"/>
                                        <input type="hidden" name="select[]" class="sucSelect" value="' . $seleccionado . '"/>
                                        <input type="hidden" name="precioR[]" class="sucPrecio" value="' . $selecreplace . '"/>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Precio comparaci&oacute;n 
                                          <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este campo sirve para que en la web se muestre el producto con el precio rebajado, No obligatorio"></span>
                                        </label>
                                        <input type="number" placeholder="0.00" name="txt_precio_anterior_sucursal[]" class="form-control" autocomplete="off" value="' . $precioAnteriorDisponibilidad . '">
                                    </div>
                                </div>
                              </div>';
                                    }
                                    ?>

                                </div>
                                <div class="row" style="display:none">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary" id="btnGuardarDisponibilidad">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Galeria -->
                        <?php if (intval($cod_producto_padre) == 0) { ?>
                            <div class="widget-content widget-content-area br-6 d-none" style="margin-top: 15px;">
                                <div>
                                    <h4>Galer&iacute;a</h4>
                                </div>
                                <form id="frmUploadImg" method="POST" action="#">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="file" name="img_galery" id="dropifyGaleria" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="1M" />
                                            <input type="hidden" name="txt_crop_galeria" id="txt_crop_galeria" value="">
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button type="button" class="btn btn-primary form-control" id="btnUploadImg"><i data-feather="upload"></i> Subir Imagen</button>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12 respGalery" style="margin-top: 20px;">

                                            <?php
                                            if ($cod_producto != 0) {
                                                $respGaleria = $Clproductos->lista_imagenes($cod_producto);
                                                if (count($respGaleria) > 0) {
                                                    foreach ($respGaleria as $galeria) {
                                                        $imgGalery = $files . $galeria['nombre_img'];
                                                        echo '<div class="col-md-4 col-sm-4 col-xs-12">
                                        <img src="' . $imgGalery . '" style="width: 100%;height: 120px;object-fit: cover;"/>
                                        <span data-value="' . $galeria['cod_imagen'] . '" class="deleteImg custom-file-container__image-multi-preview__single-image-clear">
                                            <span class="custom-file-container__image-multi-preview__single-image-clear__icon" data-upload-token="fbjn5kugte6vr2cegadi4t">×</span>
                                        </span>
                                      </div>';
                                                    }
                                                } else
                                                    echo '<p>No hay elementos</p>';
                                            } else
                                                echo '<p>No hay elementos</p>';

                                            ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>

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
                                                <input type="radio" class="new-control-input rbDisponibleDias" id="todosDias" checked="checked" value="0" name="rb_dias">
                                                <span class="new-control-indicator"></span>Todos los días
                                            </label>
                                        </div>
                                        <div>
                                            <label class="new-control new-radio radio-classic-primary">
                                                <input type="radio" class="new-control-input rbDisponibleDias" id="ciertosDias" value="1" name="rb_dias">
                                                <span class="new-control-indicator"></span>Sólo algunos días
                                            </label>
                                        </div>
                                    </div>
                                    <div class="chooseDias form-group col-md-12 col-sm-12 col-xs-12" style="display:none;">
                                        <select multiple="multiple" name="cmbDias[]" id="cmbDias" class="form-control" required="required">
                                            <option value="1">Lunes</option>
                                            <option value="2">Martes</option>
                                            <option value="3">Miércoles</option>
                                            <option value="4">Jueves</option>
                                            <option value="5">Viernes</option>
                                            <option value="6">Sábado</option>
                                            <option value="7">Domingo</option>
                                        </select>
                                    </div>

                                </div>
                            </form>
                        </div>
                        
                        <!-- EMPAQUE -->
                        <div class="widget-content widget-content-area br-6 contentEmpaques" style="margin-top: 15px;">
                            <div>
                                <h4>Empaque</h4>
                            </div>
                            <form id="frmEmpaque" method="POST" action="#">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <p>Ingresa información del empaque del producto</p>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label>Unidades</label>
                                        <input type="text" name="txt_unidades" id="txt_unidades" value="<?php echo $empaque['unidades']; ?>" placeholder="Unidades que trae el empaque" class="form-control" required="required"/>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label>Alto</label>
                                        <input type="text" name="txt_alto" id="txt_alto" value="<?php echo $empaque['alto']; ?>" placeholder="Alto del empaque" class="form-control" required="required"/>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <!-- Tags -->
                        <div class="widget-content widget-content-area br-6 d-none" style="margin-top: 15px;">
                            <div>
                                <h4>Etiquetas</h4>
                            </div>
                            <p>Las etiquetas ayudan a tus clientes a encontrar los productos en la tienda Online</p>
                            <form id="frmEtiquetas" method="POST" action="#">
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <label><span><i>&nbsp;Separar las etiquetas con una coma</i></span></label>
                                        <select multiple="multiple" name="cmbEtiquetas[]" class="form-control tagging" required="required">
                                            <?php
                                            if (count($etiquetas) > 0) {
                                                foreach ($etiquetas as $eti) {
                                                    echo '<option selected value="' . $eti['tag'] . '">' . $eti['tag'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Contifico -->
                        <?php
                        $factElectronica = $Clempresas->getProveedorFact($empresa['cod_empresa']);
                        if ($factElectronica) {
                            $nameFact = $factElectronica['nombre'];
                            $identificadorFact = $factElectronica['identificador'];
                            $cod_sistema = $factElectronica['cod_sistema_facturacion'];

                            echo '
                          <div class="widget-content widget-content-area br-6" style="margin-top: 15px;">
                            <div><h4>' . $nameFact . '</h4></div>';

                            $productFact = $Clproductos->getIdFactElect($cod_producto, $cod_sistema);
                            if (!$productFact) {
                                echo '
                                <form id="frmContifico" method="POST" action="#">
                                    <input type="hidden" id="cod_sistema_facturacion" name="" value="' . $cod_sistema . '"/>
                                    <input type="hidden" id="identificador_fact" name="" value="' . $identificadorFact . '"/>
                                  <div class="row">
                                      <div class="col-md-12 col-sm-12 col-xs-12">
                                        <p>Este apartado sirve para ligar los productos del Ecommerce con los productos de ' . $nameFact . ' y poder facturar</p>
                                        <div>
                                          <label class="new-control new-radio radio-classic-primary">
                                            <input type="radio" class="new-control-input rbProductoContifico" value="0" name="rb_proceso_contifico">
                                            <span class="new-control-indicator"></span>El producto ya existe en ' . $nameFact . '
                                          </label>
                                        </div>
                                        <div>
                                          <label class="new-control new-radio radio-classic-primary">
                                            <input type="radio" class="new-control-input rbProductoContifico" checked="checked" value="1" name="rb_proceso_contifico">
                                            <span class="new-control-indicator"></span>Crear el producto
                                          </label>
                                        </div>
                                      </div>
        
                                      <div class="contificoExiste form-group col-md-12 col-sm-12 col-xs-12">
                                          <label>Proporcione el Id del producto que se encuentra en sus sistema contable <span class="asterisco">*</span></label>
                                          <input type="text" placeholder="Ej. pKBe1wBvZXiNWbXy" class="form-control" autocomplete="off" id="txt_id_contifico">
                                      </div>
        
                                  </div>
                                  <div class="contificoExiste" style="text-align: right;">
                                      <button type="button" class="btn btn-outline-primary" id="btnActualzarIdSistemaContable">Actualizar ID</button>
                                  </div>
                                  <div class="contificoCrear" style="text-align: right;">
                                      <button type="button" class="btn btn-outline-primary" id="btnCrearSistemaContable">Crear Producto en Sistema Contable</button>
                                  </div>
                                  </form>';
                            } else {
                                echo '<span><b>Id ' . $nameFact . ': </b></span>';
                                echo '<span>' . $productFact['id'] . '</span>';
                            }

                            echo '</div>';
                        }
                        ?>

                    </div>

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/crear_productos2.js?v=124" type="text/javascript"></script>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js?v=2"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- TEMPLATES -->
    <script id="lista-ingredientes-template" type="text/x-handlebars-template">
        {{#each this}}
            <option value="{{cod_ingrediente}}" data-ingrediente='{ "cod_ingrediente": {{cod_ingrediente}}, "cod_unidad_medida": "{{cod_unidad_medida}}" }'>{{ingrediente}}</option>
        {{/each}}
    </script>
    <script id="lista-producto-ingredientes-template" type="text/x-handlebars-template">
        {{#each this}}
            <div class="row align-items-center mt-3" data-ingrediente="{{cod_ingrediente}}">
                <div class="col-6 col-lg-3">a
                    <p>{{ingrediente}}</p>                         
                </div>
                <div class="col-6 col-lg-3">
                    <input type="number" class="form-control" placeholder="1" value="{{valor}}" id="cantidadIngrediente-{{cod_producto_ingrediente}}">                         
                </div>
                <div class="col-6 col-lg-3">
                    <p>{{cod_unidad_medida}}</p>                         
                </div>
                <div class="col-6 col-lg-3">
                    <button class="btn btn-success btnEditProductoIngrediente" data-id="{{cod_producto_ingrediente}}">
                        <i data-feather="save"></i>
                    </button>                         
                    <button class="btn btn-danger btnDeleteProductoIngrediente" data-id="{{cod_producto_ingrediente}}">
                        <i data-feather="trash-2"></i>
                    </button>                         
                </div>
            </div>
        {{/each}}
    </script>
    <script id="lista-producto-opciones-ingredientes-template" type="text/x-handlebars-template">
        {{#each this}}
            <div class="row align-items-center mt-3" data-ingrediente="{{cod_ingrediente}}">
                <div class="col-6 col-lg-3">
                    <p>{{ingrediente}}</p>                         
                </div>
                <div class="col-6 col-lg-3">
                    <input type="number" class="form-control" placeholder="1" value="{{valor}}" id="cantidadIngredienteOpcion-{{cod_producto_opcion_ingrediente}}">                         
                </div>
                <div class="col-6 col-lg-3">
                    <p>{{cod_unidad_medida}}</p>                         
                </div>
                <div class="col-6 col-lg-3">
                    <button class="btn btn-success btnEditProductoOpcionIngrediente" data-id="{{cod_producto_opcion_ingrediente}}">
                        <i data-feather="save"></i>
                    </button>                         
                    <button class="btn btn-danger btnDeleteProductoOpcionIngrediente" data-id="{{cod_producto_opcion_ingrediente}}">
                        <i data-feather="trash-2"></i>
                    </button>                         
                </div>
            </div>
        {{/each}}
    </script>
</body>

</html>