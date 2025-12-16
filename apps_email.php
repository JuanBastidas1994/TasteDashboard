<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_notificaciones.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clordenes = new cl_ordenes(NULL);
$Clusuarios = new cl_usuarios(NULL);
$Clnotificaciones = new cl_notificaciones(NULL);
$session = getSession();
$cod_rol = $session['cod_rol'];
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

/*NOTIFICACIONES*/
$htmlTiposNotificaciones = "";
$notificaciones = $Clnotificaciones->getTipoNotificacionUsuario();
if($notificaciones){
    foreach ($notificaciones as $tipo) {
        $htmlTiposNotificaciones.='<option value="'.strtolower($tipo['cod_notificacion_tipo']).'">'.ucwords(strtolower($tipo['tipo'])).'</option>';
    }
}

$mail = $Clusuarios->getEmailConfig($session['cod_usuario']);
$folders = json_decode($mail['folders'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="plugins/editors/quill/quill.snow.css">
    <link href="assets/css/apps/mailbox.css" rel="stylesheet" type="text/css" />

    <script src="plugins/sweetalerts/promise-polyfill.js"></script>
    <link href="plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="plugins/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
    <link href="plugins/fullcalendar/custom-fullcalendar.advance.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .content-box {
            background-color: #f9f9f9;
            position: absolute;
            top: 0;
            height: 100%;
            width: 0px;
            left: auto;
            right: 0px;
            overflow: hidden;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .detalleOrden{
            height: calc(100vh - 234px);
        }

        .changeTipo.active{
            background-color: #2196f3 !important;
            z-index: 0 !important;
        }

        .unread-mail {
            font-weight: bolder;
        }

        .unread-mail .user-email {
            color: #bd4c4c !important;
        }
        
        .popover {
            z-index: 99999999999999999 !important;
        }
    </style>
    <!--  END CUSTOM STYLE FILE  -->
</head>
<body>
     <!-- Modal -->
    <div class="modal fade bs-example-modal-xl" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pedidos Programados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="cod_sucursal" id="cod_sucursal" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <div class="labels">
                                    <p class="label label-primary">Envío a Domicilio</p>
                                    <p class="label label-danger">Retirar en Local</p>
                                    <!--<p class="label label-success">Personal</p>
                                    <p class="label label-danger">Important</p>-->
                                </div>
                              <div id="calendar"></div>
                          </div>
                      </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

      <!--MODAL NOTIFICAR -->
    <div class="modal fade bs-example-modal-lg" id="modalNotificar" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Enviar Notificaci&oacute;n a <span class="nameUser"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                        <form id="frmNotificar" method="POST" action="#">
                                <div class="row">
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label>Tipo <span class="asterisco">*</span></label>
                                            <select class="form-control" name="cmbTipoNotificacion" id="cmbTipoNotificacion">
                                                <?= $htmlTiposNotificaciones?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <label>Descripci&oacute;n <span class="asterisco">*</span></label>
                                            <textarea style="display:none" placeholder="Descripcion" name="descripcion" id="txt_descripcion" class="form-control textRecordatorio" required="required" autocomplete="off"></textarea>
                                            <div id="containerEmoji"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 btn-modal" style="text-align: right;">
                                        </div>
                                </div> 
                              </div>
                        </form>         
                    </div>
                
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>
    <!--MODAL NOTIFICAR -->

    <!--MODAL BUSQUEDA -->
    <div class="modal fade bs-example-modal-lg" id="modalBusqueda" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">B&uacute;squeda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                        <form id="frmBusqueda" method="POST" action="#">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label>Buscar por:</label>
                                            <select class="form-control" id="cmbBusqueda" name="cmbBusqueda">
                                                <option value="1">N&uacute;m. orden</option>
                                                <option value="2">N&uacute;m. c&eacute;dula</option>
                                                <option value="3">Nombre del cliente</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label>B&uacute;squeda</label>
                                            <input class="form-control" id="txtBuscar" name="txtBuscar" placeholder="Buscar por número de orden...">
                                        </div>
                                        
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <h3>Resultados</h3>
                                            <table class="table style-3 table-hover dataTable no-footer">
                                                <thead>
                                                    <tr>
                                                        <th>N&uacute;m. orden</th>
                                                        <th>Nombre</th>
                                                        <th>Fecha</th>
                                                        <th>Estado</th>
                                                        <th>Acci&oacute;n</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="busquedaResultados">
                                                    <tr>
                                                        <td colspan="5">
                                                            Sin resultados
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> 
                              </div>
                        </form>         
                    </div>
                
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>
    <!--MODAL BUSQUEDA -->
    
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
                    <div class="col-xl-12 col-lg-12 col-md-12">

                        <div class="row">
                            <div class="col-xl-12  col-md-12">
                                <div class="mail-box-container">
                                    <div class="mail-overlay"></div>

                                    <div class="tab-title">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-12 text-center mail-btn-container">
                                                <a id="btn-compose-mail" class="btn btn-block" href="javascript:void(0);"><i data-feather="volume-x"></i></a>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-12 mail-categories-container">

                                                <div class="mail-sidebar-scroll">

                                                    <ul class="nav nav-pills d-block" id="pills-tab" role="tablist">
                                                        <?php
                                                        foreach($folders as $folder){
                                                            $id = $folder['folder'];
                                                            $name = $folder['name'];
                                                            $icono = "mail";
                                                            
                                                            switch($id){
                                                                case 'INBOX': $icono="inbox"; break;
                                                                case 'INBOX.Sent': $icono="send"; break;
                                                                case 'INBOX.Archive': $icono="archive"; break;
                                                                case 'INBOX.spam': $icono="alert-triangle"; break;
                                                                case 'INBOX.Drafts': $icono="edit-2"; break;
                                                                case 'INBOX.Trash': $icono="trash"; break;
                                                            }
                                                            
                                                            echo '<li class="nav-item">
                                                                <a class="nav-link list-actions active" id="'.$id.'"><i data-feather="'.$icono.'"></i><span class="nav-names">'.$name.'</span> <span class="mail-badge badge badge-'.$id.'"></span></a>
                                                            </li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="mailbox-inbox" class="accordion mailbox-inbox">

                                        <div class="search">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu mail-menu d-lg-none"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                                            <input type="text" id="txtBusqueda" class="form-control input-search" placeholder="Buscar aqu&iacute;...">
                                        </div>

                                        <div class="action-center">
                                            <div class="">
                                                
                                            </div>

                                            <div class="dropdown">
                                                <i onclick="openCalendar();" data-feather="calendar"></i>
                                                <i onclick="$('#facturacionModal').modal();" data-feather="clipboard"></i>
                                                <i id="btn-pause-sound" data-feather="volume-x"></i>
                                                <i onclick="$('#sonidoModal').modal();" data-feather="music"></i>
                                                <i onclick="$('#modalPrinter').modal();" data-feather="printer"></i>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" data-toggle="tooltip" data-placement="top" data-original-title="Revive Mail" stroke-linejoin="round" class="feather feather-activity revive-mail"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-toggle="tooltip" data-placement="top" data-original-title="Delete Permanently" class="feather feather-trash permanent-delete"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                <div class="dropdown d-inline-block more-actions">
                                                    <a class="nav-link dropdown-toggle" id="more-actions-btns-dropdown" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="more-actions-btns-dropdown">
                                                        <a class="dropdown-item action-mark_as_read" href="javascript:void(0);">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg> Mark as Read
                                                        </a>
                                                        <a class="dropdown-item action-mark_as_unRead" href="javascript:void(0);">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg> Mark as Unread
                                                        </a>
                                                        <a class="dropdown-item action-delete" href="javascript:void(0);">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-toggle="tooltip" data-placement="top" data-original-title="Delete" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Trash
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="notificarSucursal" value="<?php echo $sucursalNotificar; ?>" />
                                            </div>
                                        </div>
                                
                                        <div class="message-box">
                                            
                                            <script id="email-item" type="text/x-handlebars-template">
                                                <div data-value="{{msgno}}" data-folder="{{folder}}" class="mail-item">
                                                    <div class="animated animatedFadeInUp fadeInUp" id="mailHeadingThree">
                                                        <div class="mb-0">
                                                            <div class="mail-item-heading"  role="navigation" data-target="#mailCollapse{{uid}}" aria-expanded="false">
                                                                <div class="mail-item-inner">
                                                                    <div class="d-flex">
                                                                        <div class="f-head">
                                                                            <span><b>JK</b></span>
                                                                        </div>
                                                                        <div class="f-body">
                                                                            <div class="meta-mail-time">
                                                                                <p class="user-email">{{to}} </p>
                                                                            </div>
                                                                            <div class="meta-title-tag">
                                                                                <p class="mail-content-excerpt"><span class="mail-title">{{subject}} -</span> {{body}}</p>
                                                                                <p class="meta-time align-self-center">{{date}}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </script>
                                            
                                            
                                            <div class="message-box-scroll" id="lista_ordenes">
                                            </div>
                                        </div>

                                        <div class="content-box">
                                            <div class="d-flex msg-close">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left close-message"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                                <h2 class="mail-title title-open-mail" data-selectedMailTitle="">Asunto</h2>
                                            </div>
                                            
                                            <script id="email-item-expanded" type="text/x-handlebars-template">
                                            <div class="detalleOrden ps ps--active-y">    
                                                <div class="mail-content-container sentmail" data-mailfrom="info@mail.com" data-mailto="alan@mail.com" data-mailcc="">

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <div class="d-flex user-info">
                                                            <div class="f-body">
                                                                <div class="meta-mail-time">
                                                                    <div class="">
                                                                        <p class="user-email" data-mailto="alan@mail.com"><span>Desde,</span> {{from}}</p>
                                                                    </div>
                                                                    <p class="mail-content-meta-date current-recent-mail">{{date}}</p>
                                                                    <p class="meta-time align-self-center">8:45 AM</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="action-btns">
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-original-title="Reply">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left reply"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>
                                                            </a>
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-original-title="Forward">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-right forward"><polyline points="15 14 20 9 15 4"></polyline><path d="M4 20v-7a4 4 0 0 1 4-4h12"></path></svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mail-content" data-mailtitle="Orden">
                                                        {{{body}}}
                                                    </div>

                                                    <div class="attachments">
                                                        <h6 class="attachments-section-title">Adjuntos</h6>
                                                        {{#each attachment}}
                                                            <div class="attachment file-pdf">
                                                                <div class="media">
                                                                    <i data-feather="{{iconAttachment tipo}}"></i>
                                                                    <div class="media-body">
                                                                        <p class="file-name">{{nombre}}</p>
                                                                        <p class="file-size">{{peso}}kb</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        {{/each}}
                                                    </div>
                                                </div>
                                            </div>    
                                            </script>
                                            
                                            
                                            
                                            <div id="orden"></div>
                                        </div>

                                    </div>
                                    
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="composeMailModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                <div class="compose-box">
                                                    <div class="compose-content">
                                                        <form>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="d-flex mb-4 mail-form">
                                                                        <p>From:</p>
                                                                        <select class="" id="m-form">
                                                                            <option value="info@mail.com">Info &lt;info@mail.com&gt;</option>
                                                                            <option value="shaun@mail.com">Shaun Park &lt;shaun@mail.com&gt;</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="d-flex mb-4 mail-to">
                                                                        <svg xmelns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                                        <div class="">
                                                                            <input type="email" id="m-to" placeholder="To" class="form-control">
                                                                            <span class="validation-text"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="d-flex mb-4 mail-cc">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3" y2="6"></line><line x1="3" y1="12" x2="3" y2="12"></line><line x1="3" y1="18" x2="3" y2="18"></line></svg>
                                                                        <div>
                                                                            <input type="text" id="m-cc" placeholder="Cc" class="form-control">
                                                                            <span class="validation-text"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex mb-4 mail-subject">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                                                <div class="w-100">
                                                                    <input type="text" id="m-subject" placeholder="Subject" class="form-control">
                                                                    <span class="validation-text"></span>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex">
                                                                <input type="file" class="form-control-file" id="mail_File_attachment" multiple="multiple">
                                                            </div>

                                                            <div id="editor-container">

                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button id="btn-save" class="btn float-left"> Save</button>
                                                <button id="btn-reply-save" class="btn float-left"> Save Reply</button>
                                                <button id="btn-fwd-save" class="btn float-left"> Save Fwd</button>

                                                <button class="btn" data-dismiss="modal"> <i class="flaticon-delete-1"></i> Discard</button>

                                                <button id="btn-reply" class="btn"> Reply</button>
                                                <button id="btn-fwd" class="btn"> Forward</button>
                                                <button id="btn-send" class="btn"> Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/ie11fix/fn.fix-padStart.js"></script>
    <script src="plugins/editors/quill/quill.js"></script>
    <script src="plugins/sweetalerts/sweetalert2.min.js"></script>
    <script src="plugins/notification/snackbar/snackbar.min.js"></script>
    <script src="plugins/ion.sound/ion.sound.js"></script>
    <!--<script src="assets/js/apps/custom-mailbox.js"></script>-->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDe9LjbQR0UAc8PMVJXc66flE7yqrJbD6o"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    
    <script src="bootstrap/js/popper.min.js"></script>
    
    <script src="plugins/fullcalendar/moment.min.js"></script>
    <script src="plugins/fullcalendar/fullcalendar.min.js"></script>
    <script src='plugins/fullcalendar/es.js'></script>
    <script src="assets/js/pages/apps_email.js" type="text/javascript"></script>
    <script src="assets/js/pages/gestion_ordenes_calendario.js?v=1" type="text/javascript"></script>
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <script src="assets/js/pages/notificaciones.js" type="text/javascript"></script>
    <script>
        $("#txt_descripcion").emojioneArea({
      container: "#containerEmoji",
      hideSource: false,
    });
    </script>
</body>
</html>