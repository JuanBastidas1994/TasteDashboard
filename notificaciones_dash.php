<?php
require_once "funciones.php";
require_once "clases/cl_notificaciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$ClNotificaciones = new cl_notificaciones(NULL);
$ClEmpresas = new cl_empresas(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
</head>
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
                    <div><span id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Notificaciones</h3>
                </div>
                <div class="row layout-top-spacing">
                    
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                      <!-- Datos de Facturacion -->
                      <div class="widget-content widget-content-area br-6">
                            <div><h4>Crear nueva notificaci&oacute;n</h4></div>
                            <form name="frmSave" id="frmSave" autocomplete="off">
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label>Empresas <span class="asterisco">*</span></label>
                                    <select multiple="multiple" name="cmb_empresas[]" id="cmb_empresas" class="form-control">
                                        <option id="optEmpresa" value="all">Todas las empresas</option>
                                        <?php
                                        $empresas = $ClEmpresas->lista();
                                        foreach ($empresas as $emp) {
                                           echo '<option value="'.$emp['cod_empresa'].'">'.$emp['nombre'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label>Usuarios <span class="asterisco">*</span></label>
                                    <select multiple="multiple" name="cmb_usuarios[]" id="cmb_usuarios" class="form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Título" name="titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off">
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                     <label>Tipo de Notificación <span class="asterisco">*</span></label>
                                     <select id="cmb_tipo_noti" name="cmb_tipo_noti" class="form-control">
                                         <?php 
                                            $tipoNoti = $ClNotificaciones->getTipoNotificacion();
                                            foreach($tipoNoti as $tn){
                                                echo'<option value="'.$tn['icono'].'">'.$tn['nombre'].'</option>';
                                            }
                                         ?>
                                     </select>
                                 </div>
                            </div>
                            
                            <div class="row">  
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label>URL </label>
                                    <input type="text" id="txt_url" name="txt_url" class="form-control" placeholder="notificaciones.php">
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label>Descripci&oacute;n <span class="asterisco">*</span></label>
                                    <textarea style="display:none" placeholder="Descripcion" name="descripcion" id="txt_descripcion" class="form-control" required="required" autocomplete="off"></textarea>
                                    <div id="containerEmoji"></div>
                                </div>
                             
                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                    <button type="button" class="btn btn-outline-primary btnSendNotification">Enviar Notificaci&oacute;n</button>
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
    
    
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/notificaciones_dash.js" type="text/javascript"></script>
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    
    <script>
        $("#txt_descripcion").emojioneArea({
      container: "#containerEmoji",
      hideSource: false,
    });
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>