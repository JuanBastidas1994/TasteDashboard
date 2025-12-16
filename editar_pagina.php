<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_web_pagina.php";
require_once "clases/cl_productos.php";
if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$ClWebPaginas = new cl_web_pagina(NULL);
$ClProductos = new cl_productos();
$session = getSession();
$permitEditSections = false;

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
if(isset($_GET['emp'])){
    $permitEditSections = true;
    $empresa = $Clempresas->getByAlias($_GET['emp']);
}else{
    $empresa = $Clempresas->get($session['cod_empresa']);
}

if(!$empresa){
    header("location: ./empresas.php");
}

$cod_empresa = $empresa['cod_empresa'];
$api_key = $empresa['api_key'];
$nombre = $empresa['nombre'];

$webPagina = $ClWebPaginas->getHomeByBusiness($cod_empresa);
$aliasPagina = $webPagina['alias'];

$productos = $ClProductos->listaActivos();
$imagen = url_sistema.'/assets/img/200x200.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link rel="stylesheet" type="text/css" href="plugins/carousel/owl.carousel.css"/>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
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
        
        .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
      }
      
      .inputAccion{
          display:none;
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

                        <div class="form-group col-md-12 col-sm-12 col-xs-12 div-formas" style="display:none;">
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <label>Forma </label>
                                <select class="form-control" id="cmbForma" name="cmbForma">
                                    <option value="slide_4">Slider</option>
                                    <option id="optionBanner" value="banner" style="display: none;">Banner</option>
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
    
    <!-- Modal Agregar Ordenar Items-->
    <div class="modal fade bs-example-modal-xl" id="ordenarProductsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ordenar Productos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            
                            <h3>Todos Productos</h3>
                            <div id="" class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th>Precio</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstDisponibles" class="connectedSortable">
                                        <?php
                                        $htmlDisponibles = "";
                                        if(!$productos)
                                            $htmlDisponibles = '<tr><td colspan="4">No hay registros</td></tr>';
                                        foreach ($productos as $producto) {
                                            $imagen_product = $files.$producto['image_min'];
                                            $badge='primary';
                                            if($producto['estado'] == 'I')
                                                $badge='danger';
                                            $htmlDisponibles .= '<tr data-id="'.$producto['cod_producto'].'">
                                                <td class="text-center">
                                                    <span><img src="'.$imagen_product.'" class="profile-img" alt="Imagen"></span>
                                                </td>
                                                <td>'.$producto['nombre'].'</td>
                                                <td>$'.number_format($producto['precio'],2).'</td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($producto['estado']).'</span></td>
                                            </tr>';
                                        }  
                                        echo $htmlDisponibles;  
                                        ?>
                                        </tbody>
                                    </table>
                            </div>
                            
                        </div>
                        <div class="col-6">
                            <h3>Productos de la sección</h3>
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Precio</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstAgotados" class="connectedSortable">
                                            
                                        </tbody>
                                    </table>
                            </div>
                            <script id="product-table-ordenar" type="text/x-handlebars-template">
                                {{#each this}}
                                    <tr data-id="{{cod_producto}}">
                                        <td><img src="{{image_min}}" style="width: 50px" /></td>
                                        <td>{{nombre}}</td>
                                        <td>${{precio}}</td>
                                        <td>{{estado}}</td>
                                    </tr>
                                {{/each}}
                            </script>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Agregar Card Promo-->
    <div class="modal fade bs-example-modal-xl" id="addCardPromoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar item a <span id="carpromotitle">Seccion</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" name="id" id="frontDetallePromoId" value="">
                                <input type="hidden" name="widthAnun" id="widthAnun" value="">
                                <input type="hidden" name="heightAnun" id="heightAnun" value="">
                                
                              <div class="x_content">   
                                 <div class="col-12">
                                        <div class="upload mt-1 pr-md-1">
                                            <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                            <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                        </div>
                                    </div>

                                
                                    <div class="form-row">
                                        <div class="form-group col-12" style="margin-bottom:10px;">
                                            <label>Acci&oacute;n</label>
                                            <select name="cmbAccion" id="cmbAccion" class="form-control">
                                                <option value="INFO">Informativo (No hace nada)</option>
                                                <option value="URL">Ir a una URL</option>
                                                <option value="PRODUCTO">Ir a un Producto</option>
                                                <!--<option value="FILTER">Filtrar Productos</option>-->
                                                <!--<option value="NOTICIA">Ir a una noticia</option>-->
                                            </select>
                                        </div>
                                        <div class="form-group col-12" style="margin-bottom:10px;">
                                            <label>Detalle Acci&oacute;n</label>
                                            <input type="text" placeholder="" name="txt_accion_desc" id="txt_accion_desc" class="form-control inputAccion" autocomplete="off" value="">
                                            <select id="cmbProductos" name="cmbProductos" class="form-control inputAccion">
                                                <?php 
                                                    foreach ($productos as $producto) {
                                                        echo '<option value="'.$producto['alias'].'">'.$producto['nombre'].'</option>';
                                                    }    
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                </div>  
                              </form>
                    </div>
                        <div class="col-8">
                            
                            <div>
                                
                                <img id="my-image" src="#" style="width: 100%; max-height: 400px;"/>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-dark crop-rotate" data-deg="-90"><i data-feather="rotate-ccw"></i></button>
                                <button class="btn btn-dark crop-rotate" data-deg="90"><i data-feather="rotate-cw"></i></button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarContenidoDetallePromo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!--Modal reordenar posiciones-->
    <div class="modal fade bs-example-modal-xl" id="reordenarSeccionesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Arrastra y dale nueva posición</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                        <table id="style-3" class="table style-3">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sección</th>
                                    </tr>
                                </thead>
                                <tbody id="lstEsquema" class="connectedSortable">
                                    
                                </tbody>
                            </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
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
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Inicio</span></span>
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
                        <div class="col-5">
                            <div class="row justify-content-end">
                                <?php
                                if($permitEditSections){
                                    echo '
                                        <button class="btn btn-primary btn-nuevo"><i data-feather="plus"></i> Nueva Sección</button>
                                        <button class="btn btn-secondary ml-2 btn-reordenar"><i data-feather="arrow-up"></i> Ordenar Secciones</button>
                                    ';
                                }
                                ?>
                                <button class="btn btn-outline-primary ml-2 mr-5 btn-refresh"><i data-feather="refresh-cw"></i> Refrescar</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                        <div class="">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="appSection"></div>
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
    <script src="assets/js/pages/editar_pagina.js?id=1" type="text/javascript"></script>

    <!--CKEDITOR -->
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>