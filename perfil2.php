<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_paymentez.php";

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
$Clusuarios = new cl_usuarios($session['cod_usuario']);
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = $files.$Clusuarios->imagen;
$nombres = $Clusuarios->nombre.' '.$Clusuarios->apellido;
$cod_empresa = $session['cod_empresa'];
$Clempresa = new cl_empresas();
$empresa = $Clempresa->get($cod_empresa);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/users/user-profile.css" rel="stylesheet" type="text/css" />
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.css" rel="stylesheet" type="text/css" />
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
      
      #my-card input {
        height: 50px !important;
    }
    </style>
</head>
<body>
    
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
        
        <!-- Modal -->
        <div class="modal fade" id="modalAddTarjetas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar tarjeta</h5>
                        <input type="email" style="display: none;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form autocomplete="asdasdasdas" method="post" action="">
                                <input autocomplete="off" name="hidden" type="text" style="display:none;">
                                <input autocomplete="off" name="hidden" type="password" style="display:none;">
                                <div class="payment-form" id="my-card" data-capture-name="true"></div>
                            </form>
                        </div>
                        <div class="row">
                            <span id="messages"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarTarjeta"> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade bd-example-modal-lg" id="modalTarjetas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Formas de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <h4>Selecciona una tarjeta para realizar tu pago</h4><br><br>
                        <div class="container lstCards"></div>
                    </div>
                    <div class="modal-footer">
                        <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarTarjeta"> Guardar</button>-->
                    </div>
                </div>
            </div>
        </div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-spacing">

                    <!-- Content -->
                    <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12 layout-top-spacing">

                        <div class="user-profile layout-spacing">
                            <div class="widget-content widget-content-area">
                                <div class="d-flex justify-content-between">
                                    <h3 class="">Perfil</h3>
                                    <a href="#" class="mt-2 edit-profile"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg></a>
                                </div>

                                <div class="text-center user-info">
                                    <img src="<?php echo $imagen; ?>" alt="avatar" style="max-width: 150px;">
                                    <p class=""><?php echo $nombres; ?></p>
                                    <input type="hidden" id="userId" value="<?php echo $Clusuarios->cod_usuario; ?>" />
                                    <input type="hidden" id="empId" value="<?php echo $Clusuarios->cod_empresa; ?>" />
                                    <input type="hidden" id="correo" value="<?php echo $Clusuarios->correo; ?>" />
                                </div>
                                <div class="user-info-list">

                                    <div class="">
                                        <ul class="contacts-block list-unstyled">
                                            <li class="contacts-block__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-coffee"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg> <?php echo $Clusuarios->rol; ?>
                                            </li>
                                            <li class="contacts-block__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg><?php echo fechaLatinoShort($Clusuarios->fecha_nacimiento); ?>
                                            </li>
                                            <li class="contacts-block__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>Guayaquil, Ecuador
                                            </li>
                                            <li class="contacts-block__item">
                                                <a href="mailto:example@mail.com"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg><?php echo $Clusuarios->correo; ?></a>
                                            </li>
                                            <li class="contacts-block__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> <?php echo $Clusuarios->telefono; ?>
                                            </li>
                                        </ul>
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-8 col-lg-6 col-md-7 col-sm-12 layout-top-spacing">
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h3 class="">Facturaci&oacute;n</h3>
                                <div class="row">
                                    <div class="form-group col-md-7 col-sm-7 col-12">
                                        <p>Tu pr&oacute;xima fecha de facturaci&oacute;n es el <b><?php echo fechaLatinoShort($empresa['fecha_caducidad']); ?></b></p>
                                        <?php
                                            $cardActive = $Clempresa->getCardActive($cod_empresa);
                                            if($cardActive){
                                                echo '<p><img src="/assets/img/cards/'.$cardActive['type'].'.svg" style="width: 35px;"/> •••• •••• •••• '.$cardActive['number'].'</p>';
                                            }else{
                                                echo '<p>No tienes una tarjeta configurada, por favor registra.</p>';
                                            }
                                        ?>
                                    </div>
                                    
                                    <div class="form-group col-md-5 col-sm-5 col-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary btnTarjetas" id="">Administrar información de pago</button><br><br>
                                        <button type="button" class="btn btn-outline-primary btnAddTarjeta" id="">A&ntilde;adir información de pago</button>
                                    </div>
                            
                                </div>
                            </div>
                        </div>
                        
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h3 class="">Informaci&oacute;n del Plan</h3>
                                <div class="row">
                                    
                                    <div class="form-group col-md-7 col-sm-7 col-12">
                                        <p><b>Est&aacute;ndar</b></p>
                                    </div>
                                    
                                    <div class="form-group col-md-5 col-sm-5 col-12" style="text-align: right;">
                                        <a type="button" href="dashboard_planes.php" class="btn btn-outline-primary" id="">Cambiar de Plan</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h3 class="">Cambiar Contrase&ntilde;a</h3>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Contrase&ntilde;a Nueva <span class="asterisco">*</span></label>
                                        <input type="password" placeholder="" name="txt_pass" id="txt_pass" class="form-control" required="required" autocomplete="off" value="">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label>Repetir Contrase&ntilde;a Nueva <span class="asterisco">*</span> </label>
                                        <input type="password" placeholder="" name="txt_pass2" id="txt_pass2" class="form-control" autocomplete="off" value="">
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary" id="btnActualizarPassword">Actualizar contrase&ntilde;a</button>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                
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
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.js" charset="UTF-8"></script>
    <script src="assets/js/pages/perfil.js"></script>
</body>
</html>