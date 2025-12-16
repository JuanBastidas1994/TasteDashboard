<?php
require_once "funciones.php";
require_once "clases/cl_notificaciones.php";

if(!isLogin()){
    header("location:login.php");
}

$ClNotificaciones = new cl_notificaciones(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$lista = $ClNotificaciones->lista($session['cod_empresa']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
                
                    <div class="col-xl-5 col-lg-5 col-sm-12  layout-spacing">
                      <!-- Datos de Facturacion -->
                      <div class="widget-content widget-content-area br-6">
                            <div><h4>Crear nueva notificaci&oacute;n</h4></div>
                            <form name="frmSave" id="frmSave" autocomplete="off">
                            <div class="row">
                                <?php
                                if($lista){
                                ?>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>Objetivo <span class="asterisco">*</span></label>
                                    <select name="aplicacion" id="" class="form-control">
                                        <?php
                                        foreach ($lista as $items) {
                                           echo '<option value="'.$items['cod_empresa_notificacion'].'">'.$items['aplicacion'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" placeholder="Título" name="titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off">
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>Descripci&oacute;n <span class="asterisco">*</span></label>
                                    <textarea style="display:none" placeholder="Descripcion" name="descripcion" id="txt_descripcion" class="form-control" required="required" autocomplete="off"></textarea>
                                    <div id="containerEmoji"></div>
                                </div>
                                
                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                    <button type="button" class="btn btn-outline-primary btnSendNotification">Enviar Notificac&oacute;n</button>
                                </div>
                                <?php
                                }else{
                                    echo '<p>No tienes configurado el envio de notificaciones, por favor comunicate con soporte</p>';
                                }   
                                ?>
                            </div>
                            </form>
                      </div>
                    </div>

                    <div class="col-xl-7 col-lg-7 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>&Uacute;ltimas notificaciones</h4></div>
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>T&iacute;tulo</th>
                                                <th>Detalle</th>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $ClNotificaciones->listaGeneral($session['cod_empresa']);
                                            foreach ($resp as $items) {
                                                echo '<tr>
                                                    <td>'.$items['titulo'].'</td>
                                                    <td>'.$items['detalle'].'</td>
                                                    <td>'.$items['aplicacion'].'</td>
                                                    <td>'.fechaLatinoShort($items['fecha']).'</td>
                                                    <td>'.$items['cod_usuario'].'</td>
                                                </tr>';
                                            }
                                            ?>
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
    
    
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/notificaciones.js" type="text/javascript"></script>
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