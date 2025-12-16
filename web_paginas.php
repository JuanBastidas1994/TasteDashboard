<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_web_pagina.php";
require_once "clases/cl_web_anuncios.php";
require_once "clases/cl_categorias_noticias.php";
require_once "clases/cl_web_modulos.php";
if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clempresas = new cl_empresas(NULL);
$ClWebPaginas = new cl_web_pagina(NULL);
$ClWebAnuncios = new cl_web_anuncios(NULL);
$ClCatNoticias = new cl_categorias_noticias(NULL);
$ClWebModulos = new cl_web_modulos(NULL);
$session = getSession();
$nombre = "";
if(isset($_GET['id']) && isset($_GET['emp'])){
    $alias = $_GET['id'];
    $aliasEmpresa = $_GET['emp'];

    $empresa = $Clempresas->getByAlias($aliasEmpresa);
    if(!$empresa){
        header("location: ./empresas.php");
    }
    $cod_empresa = $empresa['cod_empresa'];
    $api_key = $empresa['api_key'];
    $nombre = $empresa['nombre'];

    $webPagina = $ClWebPaginas->getByAlias($alias, $cod_empresa);
    $aliasPagina = $webPagina['alias'];
}
else{
    header("location: ./empresas.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="plugins/carousel/owl.carousel.css"/>
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
        }

        .product-tab4 {
            margin-bottom: 20px;
            padding: 15px;
        }

        .tab-header4{
            position: relative;
            z-index: 99;
        }
    </style>
</head>
<body id="pagina-secciones">
    <!-- Modal -->
    <div class="modal fade bs-example-modal-xl" id="crearSeccionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva Sección</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <form name="frmSave" id="frmSave" autocomplete="off">    
                    <input type="hidden" id="idEsquema" name="idEsquema" value="">
                    <div id="" class="table-responsive mb-4 mt-4" style="max-height: 500px;">

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label>T&iacute;tulo <span class="asterisco">*</span></label>
                            <input type="text" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off" value="" placeholder="">
                        </div>

                        <div class="form-group col-md-8 col-sm-8 col-xs-12">
                            <label for="">Tipo de la sección</label>
                            <select class="form-control cmbTipoSeccion" id="cmbTipo" name="cmbTipo">
                                <option value="">Escoja un tipo</option>
                                <?php
                                //OBTENER TIPOS 
                                $tipos = $ClWebPaginas->getTipos();
                                foreach ($tipos as $tipo)
                                    echo '<option value="'.$tipo['code'].'">'.$tipo['nombre'].'</option>';
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-12">
                            <label for="txt_classname">Clase Adicional</label>
                            <input type="text" name="txt_classname" id="txt_classname" class="form-control" autocomplete="off" value="" placeholder="Ej .bg-white .color-primary">
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12 div-formas" style="display:none;">
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label>Forma </label>
                                <select class="form-control" id="cmbForma" name="cmbForma">
                                    <option value="slide_4">Slider</option>
                                    <option value="lista_4">Lista de 4 Columnas</option>
                                    <option id="optionBanner" value="banner" style="display: none;">Banner</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label># columnas lg </label>
                                <select class="form-control" id="cmbNumColumnas" name="cmbNumColumnas">
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label># columnas md </label>
                                <select class="form-control" id="cmbMD" name="cmbMD">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label># columnas sm </label>
                                <select class="form-control" id="cmbSM" name="cmbSM">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label for="">Items</label>
                                <select class="form-control" id="cmbModulos" name="cmbModulos" style="display:none">
                                <?php
                                $modulos = $ClWebModulos->listabyempresa($cod_empresa);
                                foreach ($modulos as $modulo) {
                                    echo '<option value="'.$modulo['cod_web_modulos_producto'].'">'.$modulo['nombre'].'</option>';
                                }    
                                ?>
                                </select>
                                
                                <select class="form-control" id="cmbAnuncios" name="cmbAnuncios"  style="display:none">
                                <?php
                                $modulos = $ClWebAnuncios->listabyempresa($cod_empresa);
                                foreach ($modulos as $modulo) {
                                    echo '<option value="'.$modulo['cod_anuncio_cabecera'].'">'.$modulo['nombre'].'</option>';
                                }
                                ?>
                                </select>

                                <select class="form-control" id="cmbBlog" name="cmbBlog"  style="display:none">
                                <?php
                                $blog = $ClCatNoticias->listabyempresa($cod_empresa);
                                foreach ($blog as $bg) {
                                    echo '<option value="'.$bg['cod_categorias_noticias'].'">'.$bg['nombre'].'</option>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12 div-html"  style="display:none;">
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 textareaInput">
                                <label>Detalle </label>
                                <textarea name="txt_detalle" id="txt_detalle" rows="8" class="form-control" placeholder="Ingrese aquí el código"></textarea>
                            </div>

                            <div class="form-group col-md-12 col-sm-12 col-xs-12 htmlInput">
                                <label>HTML </label>
                                <textarea class="form-control" name="htmlEditor" id="htmlEditor" placeholder="HTML"></textarea>
                            </div>
                        </div>    

                    </div>
                </form>   
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>

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
                
                <div class="layout-top-spacing" style="display: block;">
                    <div><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Empresas</span></span>
                    </div>
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-7">
                            <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : ""; ?></h3>
                            <h4><?php echo $webPagina['titulo']; echo $webPagina['home']; ?> </h4>
                            <input type="hidden" id="cod_pagina" value="<?php echo $webPagina['cod_front_pagina']; ?>" />
                            <input type="hidden" id="cod_empresa" value="<?php echo $cod_empresa; ?>" />
                            <input type="hidden" id="api_key" value="<?php echo $api_key; ?>" />
                            <input type="hidden" id="aliasPagina" value="<?php echo $aliasPagina; ?>" />


                            <div class="btnAcciones" style="margin-bottom: 15px;">
                                <span id="btnHome" style="cursor: pointer;margin-right: 15px;">
                                <?php
                                $colorStar = 'style="color: #888ea8;"'; //DEFAULT
                                if($webPagina['home'] == 1){
                                    $colorStar = 'style="color: #e1e13f;fill: #eded4f;"'; //Amarillo
                                }
                                ?>
                                    <i class="feather-16" data-feather="star" <?= $colorStar ?>></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Home</span>
                                </span>

                                <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                                    <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <button class="btn btn-primary btn-nuevo">Nueva Sección</button>
                                <button class="btn btn-outline-primary btn-refresh" style="margin-left: 15px;">Refrescar</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-9 col-lg-9 col-sm-12  layout-spacing">
                        <div class="">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="appSection"></div>
                            </div>
                        </div>
                    </div>

                    <!-- AGOTADOS O PENDIENTES -->
                    <div class="col-xl-3 col-lg-3 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Posición</h4>
                                </div>
                            </div>
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Titulo</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstEsquema" class="connectedSortable">
                                            
                                        </tbody>
                                    </table>
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
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    <script type="text/javascript" src="plugins/carousel/owl.carousel.min.js" defer></script>
    <?php js_mandatory(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="assets/js/pages/web_pagina.js?id=4" type="text/javascript"></script>

    <!--CKEDITOR -->
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>