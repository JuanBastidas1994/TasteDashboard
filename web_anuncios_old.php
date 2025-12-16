<?php
require_once "funciones.php";
require_once "clases/cl_web_modulos.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_web_anuncios.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$ClWebModulos = new cl_web_modulos(NULL);
$Clproductos = new cl_productos(NULL);
$ClWebAnuncios = new cl_web_anuncios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$imagen = url_sistema.'/assets/img/200x200.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
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

      .respGalery > div {
          margin-top: 15px;
      }

      .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
      }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>
<body>
   <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RECORTADOR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                            <input type="hidden" id="txt_crop" name="txt_crop" value="" />
                            <input type="hidden" id="txt_crop_min" name="txt_crop_min" value="" />
                             <img id="my-image" src="#" style="width: 100%; max-height: 400px;"/>
                             <input type="hidden" id="img-ori" name="img-ori" value="<?php echo $imagen;?>" />
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
                
                <div class="col-md-8" >
                    <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px;vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </a>
                    <h3 id="titulo">Anuncios Web</h3>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="cmbModulos">
                    <?php
                    $modulos = $ClWebAnuncios->lista();
                    foreach ($modulos as $modulo) {
                        echo '<option value="'.$modulo['cod_anuncio_cabecera'].'">'.$modulo['nombre'].'</option>';
                    }    
                    ?>
                    </select>
                </div>

                <div class="row layout-top-spacing" style="display: block;">
                    
                    <!-- CREAR PROMOCION -->
                   <div class="col-xl-6 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                          
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" name="id" id="id" value="">
                                <!-- <input type="hidden" name="nombre_img" id="nombre_img" value=""> -->
                                <input type="hidden" name="widthAnun" id="widthAnun" value="">
                                <input type="hidden" name="heightAnun" id="heightAnun" value="">
                                
                              <div class="x_content">   
                                    <div class="col-md-3 col-sm-3 col-xs-12">
                                        <div class="upload mt-1 pr-md-1">
                                            <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                            <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                        </div>
                                    </div>

                                <div class="form-row">
                                  <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                          <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                          <input type="text" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off" value="">
                                      </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                         <label>Subt&iacute;tulo </label>
                                          <input type="text" name="txt_subtitulo" id="txt_subtitulo" class="form-control" autocomplete="off" value="">
                                      </div>
                                      
                                      <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                          <label>Estado </label>
                                          <div>
                                              <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                  <input type="checkbox" name="chk_estado" id="chk_estado" checked>
                                                  <span class="slider round"></span>
                                              </label>
                                          </div>
                                      </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                         <label>Texto boton</label>
                                          <input type="text" placeholder="" name="txt_texto_boton" id="txt_texto_boton" class="form-control" autocomplete="off" value="">
                                      </div>
                                      
                                      <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                          <label>URL boton</label>
                                          <div>
                                           <input type="text" placeholder="" name="txt_url_boton" id="txt_url_boton" class="form-control" autocomplete="off" value="">
                                          </div>
                                      </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        
                                        <label>Categorías <a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Este campo se utiliza para agrupar los anuncios por categorías. Se agregan separando con ','"><i data-feather="help-circle"></i></a></label>
                                        <select class="form-control" multiple="multiple" name="cmbCat[]" id="cmbCat">
                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <label>Descripci&oacute;n Corta</label>
                                        <textarea name="txt_descripcion_corta" id="txt_descripcion_corta" class="form-control" autocomplete="off" style="resize: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align:right;">
                                        <button type="button" class="btn btn-danger" id="btnLimpiar" wfd-id="137">Limpiar y Nuevo</button>
                                       <button type="button" class="btn btn-primary" id="btnGuardar" wfd-id="137">Guardar</button>
                                    </div>
                                </div>
                                </div>  
                              </form>
                        </div>
                    </div>

                    <!-- LISTA PROMOCIONES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Orden de Promociones</h4>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Titulo</th>
                                                <th class="text-center">Subtitulo</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstAgotados" class="connectedSortable">
                                            
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
    
    <?php js_mandatory(); ?>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="assets/js/pages/web_anuncios.js?v=3" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>