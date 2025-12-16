<?php
require_once "funciones.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_clickup.php";

if(!isLogin()){
    header("location:login.php");
}

$clScrum = new cl_clickup(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$info = $clScrum->getList();
//var_dump($info);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    
    <?php css_mandatory(); ?>

    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="assets/css/apps/scrumboard.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/forms/theme-checkbox-radio.css" rel="stylesheet" type="text/css">
    <link href="assets/css/elements/avatar.css" rel="stylesheet" type="text/css" />
    <!--<link href="assets/css/components/custom-media_object.css" rel="stylesheet" type="text/css" />-->
    <!-- FANCYBOX -->
    <link rel="stylesheet" type="text/css" href="plugins/fancybox/jquery.fancybox.css"/>
    <!-- END PAGE LEVEL STYLES -->
    
    <style>
    .task-list-section {
        cursor: move;
    }

    .task-list-container {
        overflow-y: scroll;
        max-height: 470px;
    }
    
    .select2-container--open {
        z-index: 99999;
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
      
      .card-img-top{
            object-fit: cover !important;
            height: 100px !important;
      }
    </style>
</head>
<body>
    
    <!-- Modal Standar-->
    <div class="modal fade modal-notification" id="standardModal" tabindex="-1" role="dialog" aria-labelledby="standardModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document" id="standardModalLabel">
        <div class="modal-content" id="areaCanvas">
          <div class="modal-body text-center">
              <div class="icon-content">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
              </div>
              <!--<img src="" id="imgSoporte" style="width: 100%;"/>-->
              <div id="make_tarea"  style="width: 100%;"></div>
           </div>
          <div class="modal-footer justify-content-between">
            <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
            <button type="button" class="btn btn-primary">Usar Imagen</button>
          </div>
        </div>
      </div>
    </div>
                                    
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="ModalDetalleTarea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloTareaModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-7">
                            <div id="contentTareaModal">
                                
                            </div>
                            <div id="uploadAtachment">
                                <form id="frmSubirAdjunto" method="POST" action="#">  
                                    <input type="hidden" id="idTask" name="idTask" value=""/>
                                    <p><b>Subir Adjuntos</b></p>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="file" name="attachment" id="dropifyAdjunto" class="dropify" data-default-file="assets/img/200x200.jpg" data-max-file-size="3M" />
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <button type="button" class="btn btn-primary form-control" id="btnSubirAdjunto"><i data-feather="upload"></i> Subir Adjunto</button>
                                    </div>
                                </form>        
                            </div>
                        
                        </div>
                        <div class="col-5">
                            <div id="contentComentarios">
                                
                            </div>
                            <div id="setComentario" style="text-align: right;">
                                <textarea class="form-control" placeholder="Escriba algun comentario " id="txtCommentTask" style="display:none"></textarea>
                                <div id="containerEmoji"></div>
                                <button class="btn btn-primary btnAddCommentTask" style="margin-top:15px;">Comentar</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="ModalCrearTarea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" data-translate="button-crear-ticket">CREAR TICKET</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmCrearTarea" name="frmCrearTarea" class="form-horizontal form-label-left">    
                    <div class="x_content">
                      <div class="form-group">
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>T&iacute;tulo <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Nombre" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off"/>
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Descripci&oacute;n <span class="asterisco">*</span></label>
                              <textarea placeholder="Ingresa una descripcion" name="txt_descripcion" id="txt_descripcion" class="form-control" required="required" autocomplete="off"/></textarea>
                          </div>
                      </div>
                      
                      <div class="form-group">
                              <div class="col-md-5 col-sm-5 col-xs-12 input-group" style="margin-bottom:10px;">
                                  <label>Tags <span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="El rol define que puede hacer el usuario dentro del sistema"></span>
                                    </label>
                                    <select name="cmbTags[]" id="cmbTags" multiple="multiple" class="form-control tagging" required="required">
                                        <option value="dashboard">Dashboard</option>
                                        <option value="app">Aplicaci&oacute;n M&oacute;vil</option> 
                                        <option value="front">Front Page</option>
                                        <option value="app delivery">App delivery</option>
                                        <option value="base de datos">Base de datos</option>
                                        <option value="cliente">Cliente</option>
                                        <option value="giftcards">Giftcards</option>
                                        <option value="android">Android</option>
                                        <option value="ios">Ios</option>
                                        <option value="punto venta">Punto de Venta</option>
                                    </select>
                              </div>
                              <div class="col-md-4 col-sm-4 col-xs-12 input-group" style="margin-bottom:10px;">
                                  <label>Prioridad <span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este n&uacute;mero servir&aacute; para cualquier tipo de comunicacion con el usuario"></span>
                                    </label>
    
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i data-feather="flag"></i></span>
                                    </div>
                                    <select name="cmbPrioridad" id="cmbPrioridad" class="form-control" required="required" placeholder="Notification" aria-label="notification" aria-describedby="basic-addon1">
                                        <option value="4">Baja</option>
                                        <option value="3">Normal</option> 
                                        <option value="2">Alta</option>
                                        <option value="1">Urgente</option>
                                    </select>
                                </div>
                              </div>
                            <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                <label>Estado</label>
                                <div><span id="crearTicketEstado" style="padding: 5px 10px; text-transform: uppercase; margin-right: 25px;">OPEN</span></div>
                                <input type="hidden" value="" id="txtEstadoTicket" name="txt_estado" />
                            </div>
                      </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnCrearTarea">Guardar</button>
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
                <div class="action-btn layout-top-spacing mb-5">
                    <p><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> Soporte</p>
                    <!--<button id="add-list" class="btn btn-primary">Add List</button>-->
                    <input type="hidden" id="dragTask" value="STOP"/>
                    <a class="btn btn-primary addTask" data-status="OPEN" data-color="#81B1FF"><i data-feather="plus-circle"></i> Crear Ticket</a>
                    <button id="" class="btn btn-danger btnSoporte" data-translate="button-capturas" style="display:none;">Captura para soporte</button>
                </div>
                <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="compose-box">
                                    <div class="compose-content" id="addTaskModalTitle">
                                        <h5 class="add-task-title">Add Task</h5>
                                        <h5 class="edit-task-title">Edit Task</h5>

                                        <div class="addTaskAccordion" id="add_task_accordion">
                                            <div class="card task-simple">
                                                <div class="card-header" id="headingOne">
                                                    <div class="mb-0" data-toggle="collapse" role="navigation" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"> Option 1 </div>
                                                </div>

                                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#add_task_accordion">
                                                    <div class="card-body">
                                                        <form action="javascript:void(0);">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-title mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                                        <input id="s-simple-task" type="text" placeholder="Task" class="form-control" name="task">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card task-text-progress">
                                                <div class="card-header" id="headingTwo">
                                                    <div class="mb-0" data-toggle="collapse" role="navigation" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> Option 2 </div>
                                                </div>
                                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#add_task_accordion">
                                                    <div class="card-body">
                                                        <form action="javascript:void(0);">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-title mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                                        <input id="s-task" type="text" placeholder="Task" class="form-control" name="task">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-badge mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                                        <textarea id="s-text" placeholder="Task Text" class="form-control" name="taskText"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-badge mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>

                                                                        <div class="" style="width: 100%">
                                                                            <input type="range" min="0" max="100" class="custom-range" value="0" id="progress-range-counter">

                                                                            <div class="range-count"><span class="range-count-number" data-rangeCountNumber="0">0</span> <span class="range-count-unit">%</span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card task-image">
                                                <div class="card-header" id="headingThree">
                                                    <div class="mb-0" data-toggle="collapse" role="navigation" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"> Option 3
                                                    </div>
                                                </div>
                                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#add_task_accordion">
                                                    <div class="card-body">
                                                        <form action="javascript:void(0);">

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-title mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                                        <input id="s-image-task" type="text" placeholder="Task" class="form-control" name="task">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="task-badge mb-4">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star" style="align-self: self-start;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                                        <div class="input-group mb-3">
                                                                            <div class="custom-file">
                                                                                <input type="file" class="custom-file-input" id="inputGroupFile02">
                                                                                <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02">Choose file</label>
                                                                            </div>
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text" id="inputGroupFileAddon02">Upload</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="img-preview mb-4">
                                                                        <img src="assets/img/400x168.jpg" class="img-fluid" alt="scrumboard">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn" data-dismiss="modal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Discard</button>
                                <button data-btnfn="addTask" class="btn add-tsk">Add Task</button>
                                <button data-btnfn="editTask" class="btn edit-tsk" style="display: none;">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="addListModal" tabindex="-1" role="dialog" aria-labelledby="addListModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="compose-box">
                                    <div class="compose-content" id="addListModalTitle">
                                        <h5 class="add-list-title">Add List</h5>
                                        <h5 class="edit-list-title">Edit List</h5>
                                        <form action="javascript:void(0);">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="list-title">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3" y2="6"></line><line x1="3" y1="12" x2="3" y2="12"></line><line x1="3" y1="18" x2="3" y2="18"></line></svg>
                                                        <input id="s-list-name" type="text" placeholder="List Name" class="form-control" name="task">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn" data-dismiss="modal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Discard</button>
                                <button class="btn add-list">Add List</button>
                                <button class="btn edit-list" style="display: none;">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="deleteConformation" tabindex="-1" role="dialog" aria-labelledby="deleteConformationLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" id="deleteConformationLabel">
                            <div class="modal-header">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </div>
                                <h5 class="modal-title" id="exampleModalLabel">Delete the task?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="">If you delete the task it will be gone forever. Are you sure you want to proceed?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" data-remove="task">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row scrumboard" id="cancel-row">
                    <div class="col-lg-12 layout-spacing">

                        <div class="task-list-section">
                            <?php
                            //var_dump($info['statuses']);
                            
                            if(isset($info['statuses'])){
                                $status = $info['statuses'];
                                
                                for($x=0;$x<=count($status)-1;$x++){
                                    $nombre = $status[$x]['status'];
                                    $id = $status[$x]['id'];
                                    $color = $status[$x]['color'];
                                    $index = $status[$x]['orderindex'];
                                    
                                    echo '
                                    <div data-section="s-new" class="task-list-container" data-connect="sorting">
                                    <div class="connect-sorting" style="border-top: 1px solid '.$color.';">
                                        <div class="task-container-header">
                                            <h6 class="s-heading" data-listTitle="'.$nombre.'">'.$nombre.'</h6>
                                            <div class="dropdown">
                                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-1">
                                                    <a class="dropdown-item list-edit" href="javascript:void(0);">Edit</a>
                                                    <a class="dropdown-item list-delete" href="javascript:void(0);">Delete</a>
                                                    <a class="dropdown-item list-clear-all" href="javascript:void(0);">Clear All</a>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="connect-sorting-content columnOrder'.$index.' columnId'.$id.' columnTask" data-sortable="true" data-index="'.$index.'" data-name="'.$nombre.'">
    
    
                                        </div>
    
                                        <div class="add-s-task">
                                            <a class="addTask" data-status="'.$nombre.'" data-color="'.$color.'"><i data-feather="plus-circle"></i> Add Task</a>
                                        </div>
    
                                    </div>
                                </div>
                                    ';
                                }    
                            }
                            
                            ?>
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
    <!-- FANCYBOX -->
    <script type="text/javascript" src="plugins/fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="assets/js/pages/scrumboard.js"></script>
    

    <script src="assets/js/custom.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="assets/js/ie11fix/fn.fix-padStart.js"></script>
    <script src="assets/js/apps/scrumboard.js"></script>
    <script src="assets/js/components/html2canvas.js"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- CANVAS -->
    <script type="text/javascript" src="https://unpkg.com/konva@4.1.4/konva.min.js"></script>
    <script src="plugins/canvas/ClpantallaAnalisis.js"></script>
    <!-- EMOJIS -->
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <script>
        $(document).ready(function(){
            $("#txtCommentTask").emojioneArea({
              container: "#containerEmoji",
              hideSource: false,
              pickerPosition: "bottom"
            });
        });
    </script>
    <script>
        var width = $("#standardModal").innerWidth();
        var height = window.innerHeight - 50;
        //var height = window.innerHeight;
        var lienzo = new Lienzo("make_tarea", width, height);
        
        $(".btnSoporte").on("click", function(){
            html2canvas($("body"), {
                onrendered: function(canvas) {
                    theCanvas = canvas;
                    var dataURL = canvas.toDataURL('image/jpeg');
                    
                    $("#imgSoporte").attr("src", dataURL);
                      
                    $("#standardModal").modal();
                    
                    var image = new Image();
                    image.src = dataURL;
                    image.onload = function() {
                        //var width = $("#areaCanvas").innerWidth() - 50;
                        //var height = window.innerHeight - 50;
                        //lienzo.CanvasResize(width, height);
                        lienzo.updateFondoSize(image);
                    };  
                    
                    
                }
            });
        });
        
        function GetlienzoActivo(container) {
            lienzoActivo = lienzo;
        }
    </script>
</body>
</html>