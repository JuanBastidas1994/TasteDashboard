<?php
require_once "funciones.php";
require_once "clases/cl_updates.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clupdates = new cl_updates();
$Clempresas = new cl_empresas();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_update = "";
$titulo = "";
$detalle = "";
$checked = "checked";
$selectImagen = "";
$selectVideo = "";
$selectLottie = "";

if(isset($_GET['id'])){
  $cod_update = $_GET['id'];
  $update = $Clupdates->get($cod_update);
    if($update){  
        $empresas = $Clupdates->getDetalle($cod_update);
        $titulo = $update['titulo'];
        $detalle = editor_decode($update['detalle']);
        $desc_corta = $update['desc_corta'];
        $estado = $update['desc_corta'];
        $tipoMultimedia = $update['tipo_multimedia'];
        $url = $update['url'];

        if($tipoMultimedia == 1)
            $selectImagen = "selected";
        else if($tipoMultimedia == 2)
            $selectVideo = "selected";
        else
            $selectLottie = "selected";

        if($estado <> 'A')
            $checked = "";
    }
    else{
        header("location: ./index.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="big5">
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
    <?php echo navbar(true,"dashboard_updates.php"); ?>
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
                    <div><span id="btnBack" data-module-back="categorias.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Actualizaciones</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Actualizaci&oacute;n"; ?></h3>
                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_producto != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Actualizaci&oacute;n</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                      </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" name="id" id="id" value="<?= $cod_update?>">
                              <div class="x_content">  
                                <div class="form-row">
                                  <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                          <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                          <input type="text" placeholder="T&iacute;tulo" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off" value="<?=  $titulo ?>">
                                      </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                          <label>Tipo de Empresas <span class="asterisco">*</span></label>
                                          <select name="cmb_tipos" id="cmb_tipos" class="form-control" required="required">
                                            <option value="0">Todos los tipos</option>
                                            <?php
                                                $resp = $Clempresas->get_tipoem();
                                                foreach ($resp as $tipos) {
                                                    echo '<option value="'.$tipos['cod_tipo_empresa'].'">'.$tipos['tipo'].'</option>';
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
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                        <label>Empresas <span class="asterisco">*</span></label>
                                            <select multiple="multiple" name="cmb_empresas[]" id="cmb_empresas" class="form-control" required="required">
                                            <?php
                                                $cod_empresas = [];
                                                if($empresas){
                                                    foreach ($empresas as $emp) {
                                                        $cod_empresas[] = $emp['cod_empresa'];
                                                    }
                                                }
                                                $resp = $Clempresas->lista();
                                                foreach ($resp as $empresas) {
                                                    $selected = "x";
                                                    if(in_array($empresas['cod_empresa'], $cod_empresas)){
                                                        $selected = "selected";
                                                    }
                                                    echo '<option value="'.$empresas['cod_empresa'].'" '.$selected.'>'.$empresas['nombre'].'</option>';
                                                }
                                            ?>
                                          </select>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                        <label>Roles <span class="asterisco">*</span></label>
                                            <select name="cmb_roles" id="cmb_roles" class="form-control" required="required">
                                            <?php
                                                $resp = $Clempresas->get_roles();
                                                foreach ($resp as $roles) {
                                                    if($roles['cod_rol'] < 4)
                                                        echo '<option value="'.$roles['cod_rol'].'">'.$roles['nombre'].'</option>';
                                                }
                                            ?>
                                          </select>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                        <label>Tipo Multimedia <span class="asterisco">*</span></label>
                                            <select name="cmb_multimedia" id="cmb_multimedia" class="form-control" required="required">
                                                <option value="1" <?= $selectImagen?>>Imagen</option>
                                                <option value="2" <?= $selectVideo?>>Video</option>
                                                <option value="3" <?= $selectLottie?>>Lottie</option>
                                            </select>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                        <label>URL <span class="asterisco">*</span></label>
                                            <input class="form-control" id="txt_url" name="txt_url" placeholder="URL" value="<?= $url?>"/>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <label>Descripci&oacute;n Corta</label>
                                        <textarea name="txt_descripcion_corta" id="txt_descripcion_corta" class="form-control" autocomplete="off" style="resize: none;"><?= $desc_corta; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                        <label>Detalle</label>
                                        <textarea name="txt_descripcion_larga" id="editor1" class="form-control" autocomplete="off" style="resize: none;" required="required"><?= $detalle; ?></textarea>
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
    <script src="assets/js/pages/crear_dashboard_updates.js?v=3" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>