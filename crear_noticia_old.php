<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_noticias.php";
require_once "clases/cl_categorias_noticias.php";

if(!isLogin()){
    header("location:login.php");
}


$ClcategoriasNoticias= new cl_categorias_noticias(NULL);


$Clnoticias = new cl_noticias(NULL);
$session = getSession();

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_noticia = 0;
$imagen = url_sistema.'/assets/img/200x200.jpg';
$nombre = "";
$desc_corta = "";
$desc_larga = "";
$estado = "checked";
if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $noticia = NULL;
  if($Clnoticias->getArrayByAlias($alias, $noticia)){
    $cod_noticia= $noticia['cod_noticia'];
    //echo $cod_noticia;
    $imagen = $files.$noticia['image_min'];
    $nombre = $noticia['titulo'];
    $desc_corta = $noticia['desc_corta'];
    $desc_larga = editor_decode($noticia['desc_larga']);
    $categorias = $Clnoticias->get_categorias($cod_noticia);
    if($noticia['estado']=='I')
      $estado = "";
  }
  /*else{
    header("location: ./noticias.php");
  }*/
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <style type="text/css">
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
    <?php echo navbar(true,"categorias.php"); ?>
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
                    <div><span id="btnBack" data-module-back="noticias.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Noticias</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Noticia"; ?></h3>
                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_noticia != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Noticia</span>
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
                
                    <div class="col-xl-10 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                       
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" name="id_noti" id="id_noti" value="<?php echo $cod_noticia; ?>">
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
                                          <input type="text" placeholder="Título" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off" value="<?php echo $nombre; ?>">
                                      </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                          <label>Categor&iacute;as <span class="asterisco">*</span></label>
                                          <select multiple="multiple" name="cmb_categoria[]" id="cmb_categoria" class="form-control" required="required">
                                            <?php
                                            $resp = $ClcategoriasNoticias->lista();
                                            foreach ($resp as $categoria) {
                                                $selected = "";
                                                if(in_array($categoria['cod_categorias_noticias'], $categorias))
                                                  $selected = 'selected="selected"';
                                              echo '<option '.$selected.' value="'.$categoria['cod_categorias_noticias'].'">'.$categoria['nombre'].'</option>';
                                            }
                                            ?>
                                          </select>
                                      </div>
                                      
                                      <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                          <label>Estado <span class="asterisco">*</span></label>
                                          <div>
                                              <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                  <input type="checkbox" name="chk_estado" id="chk_estado" checked>
                                                  <span class="slider round"></span>
                                              </label>
                                          </div>
                                      </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <label>Descripci&oacute;n Corta</label>
                                        <textarea name="txt_descripcion_corta" id="txt_descripcion_corta" class="form-control" autocomplete="off" style="resize: none;"><?php echo $desc_corta; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <label>Descripci&oacute;n Larga</label>
                                        <textarea name="txt_descripcion_larga" id="editor1" class="form-control" autocomplete="off" style="resize: none;" required="required"><?php echo $desc_larga; ?></textarea>
                                    </div>
                                </div>

                                
                                </div>  
                              </form>
                        </div>
                    </div>
                  <!-- 
                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">                     
                      <div class="widget-content widget-content-area br-6">
                          <div><h4>Productos en esta categor&iacute;a</h4></div>
                            <div class="row"> 
                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px;">
                                  <table class="table style-3  table-hover">
                                    <thead>
                                      <tr>
                                        <th>&nbsp;</th>
                                        <th>Producto</th>
                                        <th>Quitar</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                        if($cod_producto != 0){
                                          $productos = new cl_productos(NULL);
                                          $resp = $productos->listaByCategoria($cod_producto);
                                          if(count($resp)>0){
                                            foreach ($resp as $p){
                                              $imagen = $files.$p['image_min'];
                                               echo '
                                                  <tr>
                                                    <td class="text-center">
                                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                    </td>
                                                    <td>'.$p['nombre'].'</td>
                                                    <td>
                                                      <a href="javascript:void(0);" data-value="'.$p['cod_producto'].'"  class="bs-tooltip btnEliminarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Quitar de la categor&iacute;a"><i data-feather="x"></i></a>
                                                    </td>
                                                  </tr>';
                                            }
                                          }else
                                            echo '<p>No hay elementos</p>';
                                        }else
                                          echo '<p>No hay elementos</p>';
                                        ?>
                                    </tbody>
                                  </table>  
                                  
                                </div>
                            </div>
                      </div>
                    </div>
                   -->
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/crear_noticias.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>