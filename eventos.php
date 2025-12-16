<?php
require_once "funciones.php";
require_once "clases/cl_eventos.php";
require_once "clases/cl_usuarios.php";

if(!isLogin()){
    header("location:login.php");
}

$ClEventos = new cl_eventos(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_rol = $session['cod_rol'];
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
    <link href="plugins/fullcalendar/custom-fullcalendar.advance.css" rel="stylesheet" type="text/css" />
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
    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR EVENTO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                          <div class="col-md-3 col-sm-3 col-xs-12">
                              <div class="upload mt-1 pr-md-1">
                                  <input type="file" name="img_profile" id="input-file-max-fs" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="1M" />
                                  <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                              </div>
                          </div>
                          <div class="col-md-9 col-sm-9 col-xs-12" style="margin-bottom:10px;">
                              <label>Título <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Nombre" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off"/>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-9 col-sm-9 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Categoría <span class="asterisco">*</span> 
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="El rol define que puede hacer el usuario dentro del sistema"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="layers"></i></span>
                                </div>
                                <select name="cmbCategoria" id="cmbCategoria" class="form-control" required="required" placeholder="Notification" aria-label="notification" aria-describedby="basic-addon1">
                                  <?php
                                  $resp = $ClEventos->getCategorias();
                                  foreach ($resp as $rol) {
                                    echo '<option value="'.$rol['cod_agenda_categoria'].'">'.$rol['nombre'].'</option>';
                                  }
                                  ?>
                              </select>
                            </div>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Fecha del evento
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Especificar fecha del evento"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="calendar"></i></span>
                                </div>
                                <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="fecha_evento" id="fecha_evento">
                            </div>
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Hora de inicio
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Hora en que inicia el evento"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="clock"></i></span>
                                </div>
                                <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="hora_inicio" id="hora_inicio">
                            </div>
                          </div>

                          <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                              <label>Hora de Fin
                                    <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Hora en que finaliza el evento"></span>
                                </label>

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="clock"></i></span>
                                </div>
                                <input type="date" class="form-control" aria-label="notification" aria-describedby="basic-addon1" name="hora_fin" id="hora_fin">
                            </div>
                          </div>
                      </div>
                      
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <label>PDF</label>
                                <input type="file" class="form-control" name="fileEvent" id="fileEvent">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <label>Descripción <span class="asterisco">*</span></label>
                                <textarea class="form-control" name="txt_descripcion" id="txt_descripcion"></textarea>
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
    
    <div class="modal fade bs-example-modal-lg" id="modalInfoEvento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">EVENTO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                            <img src="" class="imgEvento" style="width: 100%;"/>
                          </div>
                          
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                            <h3 class="tituloEvento"></h3>
                            <p class="descEvento"></p>
                            <p><i data-feather="calendar"></i> <span class="fechaEvento"></span></p>
                            <p><i data-feather="clock"></i> <span class="inicioEvento"></span> - <span class="finEvento"></span></p>
                            <input type="hidden" value="0" class="idEvento"/>
                          </div>
                         
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-danger" id="btnEliminarEvento">Eliminar</button>
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
                
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Eventos</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <div id="calendar"></div>
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
    <script src="plugins/fullcalendar/moment.min.js"></script>
    <script src="plugins/fullcalendar/fullcalendar.min.js"></script>
    <script src='plugins/fullcalendar/es.js'></script>
    
    <script src="assets/js/pages/eventos.js" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>