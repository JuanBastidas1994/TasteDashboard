<?php
require_once "funciones.php";
require_once "clases/cl_demo.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_telegram.php";
require_once "clases/cl_fidelizacion.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_botonPagos.php";

if(!isLogin()){
    header("location:login.php");
}

$Cldemo = new cl_demo(NULL);
$Clempresas = new cl_empresas(NULL);
$ClTelegram = new cl_telegram(NULL);
$Clfidelizacion = new cl_fidelizacion(NULL);
$Clusuarios = new cl_usuarios();
$ClPagos = new cl_botonpagos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_demo = 0;
$cod_empresa = 0;
$alias = "";
$imagen = url_sistema.'/assets/img/200x200.jpg';
$nombre = "";
$password = passRandom();
$telefono = "";
$usuario = "";
$rnombre = "";
$rcorreo = "";
$urlWeb ="";
$color = "";
$botname = "";
$token = "";

$contacto_nombre = "";
$contacto_correo = "";
$api = "";
if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $empresa = $Cldemo->getByAlias($alias);
  if($empresa){
    $cod_demo = $empresa['cod_demo'];
    $cod_empresa = $empresa['cod_empresa'];
    $imagen = url_sistema.'assets/demos/'.$empresa['logo'];
    $nombre = $empresa['nombre'];
    $correo = $empresa['correo'];
    $telefono = $empresa['telefono'];
    $direccion = $empresa['direccion'];
    $alias = $empresa['alias'];
    $color= $empresa['color'];

  }else{
    header("location: ./index.php");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
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

      .table-hover:not(.table-dark) tbody tr:hover .new-control.new-checkbox .new-control-indicator {
          border: 0 !important;
      }

      .table td, .table th {
          padding: 8px;
      }
      .habilitado{
          background-color: #8dbf42;
      }
      .deshabilitado{
          background-color: #FFD83D;
      }
      .nopermitido{
          background-color: #FF8088;
      }
      
    </style>
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
    <?php echo navbar(true); ?>
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
                    <div><span id="btnBack" data-module-back="demos.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Demos</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Demo"; ?></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_demo != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nuevo Demo</span>
                      </span>

                      <span style="cursor: pointer;margin-right: 15px;display: none;">
                        <i class="feather-16" data-feather="copy"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Duplicar</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;" data-codigo="<?php echo $cod_demo;?>">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                      </span>
                      
                      <span style="cursor: pointer;margin-right: 15px;">
                       <a href="https://<?php echo $alias?>.prospecto.mie-commerce.com/" target="_blank">
                           <i class="feather-16" data-feather="airplay"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Ver Demo</span>
                       </a> 
                      </span>
                    </div>
                </div>
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                             <input type="hidden" name="id" id="id" value="<?php echo $cod_demo; ?>">
                             <input type="hidden" name="alias" id="alias" value="<?php echo $alias; ?>">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                               
                              <div class="x_content">   
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <div class="upload mt-1 pr-md-1">
                                        <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="1M" />
                                        <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Logo</p>
                                    </div>
                                </div>

                                <div class="form-row">
                                  <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                          <label>Nombre de la empresa<span class="asterisco">*</span></label>
                                          <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off" value="<?php echo $nombre; ?>">
                                      </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                          <label>Correo <span class="asterisco">*</span></label>
                                          <input type="email" placeholder="Correo" name="txt_correo" id="txt_correo" class="form-control" required="required" autocomplete="off" value="<?php echo $correo; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                          <label>Direcci&oacute;n <span class="asterisco">*</span></label>
                                          <input type="text" placeholder="Direcci&oacute;n " name="txt_direccion" id="txt_direccion" class="form-control" required="required" autocomplete="off" value="<?php echo $direccion; ?>">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                          <label>Tel&eacute;fono <span class="asterisco">*</span></label>
                                          <input type="text" placeholder="Tel&eacute;fono " name="txt_telefono" id="txt_telefono" class="form-control" required="required" autocomplete="off" value="<?php echo $telefono; ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                        <label>Empresa referencia <span class="asterisco">*</span></label>
                                        <select class="form-control" id="cmb_empresas" name="cmb_empresas" required>
                                            <?php
                                                $empresas = $Clempresas->lista();
                                                foreach($empresas as $emp){
                                                    $selected = "";
                                                        if($emp['cod_empresa'] == $cod_empresa)
                                                            $selected = "selected";
                                                    echo '<option value="'.$emp['cod_empresa'].'" '.$selected.'>'.$emp['nombre'].'</option>';
                                                }
                                            ?>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                          <label>Color<span class="asterisco">*</span></label>
                                          <input type="color" name="txtcolor" id="txtcolor" class="form-control" required="required" value="<?php echo $color; ?>">
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                        <label>Estado <span class="asterisco">*</span></label>
                                        <div>
                                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                <input type="checkbox" name="chk_estado" id="chk_estado" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        
                                    </div>

                                </div>
                                
                                </div>  
                              </form>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                          <!-- Informacion Automatica Start -->
                          	<div class="widget-content widget-content-area br-6">
    	                      	<div><h4>Informaci&oacute;n &Uacute;nica</h4></div>
    	                      	<div class="row" id="infoAlias">
    	                          <div class="col-md-12 col-sm-12 col-xs-12" >
    	                          	<label>Alias</label>
    	                          	<p><?php echo $alias; ?></p>
    	                          </div>
    	                          <div class="col-md-12 col-sm-12 col-xs-12">
    	                          	<label>Api Key</label>
    	                          	<p><?php echo $api; ?></p>
    	                          </div>	
    	                        </div>  
    	                    </div>
                          <!-- Informacion Automatica End -->
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
    <script src="assets/js/pages/crear_demo.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>