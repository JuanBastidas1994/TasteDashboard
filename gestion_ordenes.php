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
if($cod_rol == 2){
    $sucursales = $Clsucursales->lista();
    if($sucursales){
        $optionSucAbrir = "";
        foreach ($sucursales as $sucursal) {
            if($sucursal['estado'] == 'A'){
                $optionSucAbrir.= '<option value="'.$sucursal['cod_sucursal'].'">'.$sucursal['nombre'].'</option>';
            }
        }
    }
}
else{
    $sucursal =  $Clsucursales->getInfo($session['cod_sucursal']);
    $optionSucAbrir = "";
    if($sucursal){
        $optionSucAbrir = '<option value="'.$sucursal['cod_sucursal'].'">'.$sucursal['nombre'].'</option>';
    }
}

$permisoEncenderTienda = "";
if($cod_rol == 3){
    if($Clempresas->getPermisoTienda($session['cod_empresa']))
        $permisoEncenderTienda = "";
    else
        $permisoEncenderTienda = "display: none;";
}

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

/*NOTIFICACIONES*/
$htmlTiposNotificaciones = "";
$notificaciones = $Clnotificaciones->getTipoNotificacionUsuario();
if($notificaciones){
    foreach ($notificaciones as $tipo) {
        $htmlTiposNotificaciones.='<option value="'.strtolower($tipo['cod_notificacion_tipo']).'">'.ucwords(strtolower($tipo['tipo'])).'</option>';
    }
}

/*EMPRESA*/
$empresa = $Clempresas->get($session['cod_empresa']);
if($empresa){
    $apikey = $empresa['api_key'];
    $tipoEmpresa = $empresa['cod_tipo_empresa'];
    $permisoRecordarOrdenes = $empresa['recordar_ordenes'];

    //REDIRIGIR A GESTIÓN DE ÓRDENES 3
    $aliasEmpresa = $session["alias"];
    if($tipoEmpresa == 1 && $aliasEmpresa <> "morogrill"){
        header("location:gestion_ordenes_v5.php");
    }
}



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
            overflow: scroll;
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

        .text-comments p{
            font-size: 12px !important;
        }

        @media (max-width:1025px){
            .table-sm td, .table-sm th{
                font-size: 11px !important;
            }
        }
    </style>
    <!--  END CUSTOM STYLE FILE  -->
</head>
<body>
     <!-- Modal FullCalendar -->
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
    
        <!-- Modal Fidelizacion-->
    <div class="modal fade bs-example-modal-xl" id="fidelizacionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fidelización</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">  
                    <div class="x_content">
                        <div class="form-group" style="margin-bottom:10px;">
                            <ul class="nav nav-tabs  mb-3 mt-3" id="lineTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="home" aria-selected="true"><i data-feather="info"></i> Informaci&oacute;n</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link tabLista" id="lista-tab" data-toggle="tab" href="#lista" role="tab" aria-controls="home" aria-selected="true"><i data-feather="grid"></i> Lista</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="simpletabContent">
                                <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div>
                                            <input type="search" class="form-control cedulaFidelizacion" placeholder="Scanner"/>
                                        </div>
                                        <div>
                                            <div class="col-md-12 col-sm-12 col-xs-6">
                                                <span style="margin-top: 15px;"><b>Cliente</b></span>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label for="">Num. Documento</label>
                                                <input type="text" class="form-control fdCed" placeholder="" disabled/>
                                            </div>
                                            <div class="col-md-8 col-sm-8 col-xs-6">
                                                <label for="">Nombres</label>
                                                <input type="text" class="form-control fdNombres" placeholder="" disabled/>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="col-md-12 col-sm-12 col-xs-6">
                                                <span style="margin-top: 15px;"><b>Factura</b></span>
                                            </div>
                                            <div class="col-md-7 col-sm-7 col-xs-6">
                                                <label for="">N° Factura</label>
                                                <input type="text" class="form-control fdId" placeholder=""/>
                                            </div>
                                            <div class="col-md-5 col-sm-5 col-xs-6">
                                                <label for="">Total Factura</label>
                                                <input type="text" class="form-control fdTotal" placeholder=""/>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <span margin-top:15px;><b>Formas de pago</b></span>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <label for="">Efectivo</label>
                                                <input type="text" class="form-control fdEfectivo" placeholder=""/>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12"> 
                                                <label for="">Tarjeta</label>
                                                <input type="text" class="form-control fdTarjeta" placeholder=""/>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <label for="">Puntos</label>
                                                <input type="text" class="form-control fdPuntos" placeholder=""/>
                                            </div>
                                            <div style="text-align: right;">
                                                <button class="btn btn-primary fdBtnAgregar" style="margin-top:15px;" disabled >Acumular Orden</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <script id="no-data" type="text/x-handlebars-template">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
                                                <lottie-player src="https://assets9.lottiefiles.com/private_files/lf30_oqpbtola.json"  background="transparent"  speed="1"  style="height: 300px;" autoplay></lottie-player>
                                                <p style="color: #999;">{{mensaje}}</p>
                                            </div>
                                        </script>
                                        <script id="loading-data" type="text/x-handlebars-template">
                                            <div class="col-md-12" style="margin-top:30px;padding: 0; text-align:center;">
                                                <h4><div class="spinner-border text-success align-self-center loader-lg"></div> Cargando información...</h4>
                                            </div>
                                        </script>
                                        <script id="wait-for-data" type="text/x-handlebars-template">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
                                                <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_BKQVK4.json"  background="transparent"  speed="1"  style="height: 300px;" loop autoplay></lottie-player>
                                                <p style="color: #999;">Esperando lectura de Qr</p>
                                            </div>
                                        </script>
                                        <script id="cliente-info" type="text/x-handlebars-template">
                                            <div class="col-md-12" style="margin-top:30px;padding: 0; text-align:center;">
                                                <p>{{cliente.nombre}}</p>
                                                <p id="cliente_documento">{{cliente.num_documento}}</p>
                                                <h3>${{data.total_dinero}}</h3>
                                                <p>Nivel {{data.nivel}}</p>
                                                <p>{{data.total_puntos}} Puntos acumulados</p>
                                                <p>${{data.total_saldo}} Saldo</p>
                                                <button class="btn btn-danger fdBtnCancelar">Cambiar Usuario</button>
                                            </div>    
                                        </script>

                                        <div class="infoClienteFidelizacion"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="lista" role="tabpanel" aria-labelledby="lista-tab">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <th>N° Factura</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </thead>
                                        <tbody class="bodyOrdenesRunfood">

                                        </tbody>
                                    </table>
                                </div>
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

    <!-- Modal Impresión-->
    <div class="modal fade bs-example-modal-xl" id="ImpresionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Servicio de Impresión</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">  
                    <div class="x_content">
                        <div class="form-group" style="margin-bottom:10px;">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div>
                                    <div class="col-md-12 col-sm-12 col-xs-6">
                                        <h3>Configuración</h3>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <label for="txtPrintUrl">URL</label>
                                        <input id="txtPrintUrl" type="text" class="form-control" placeholder=""/>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6" style="display:none;">
                                        <label for="txtPrintPuerto">Puerto</label>
                                        <input id="txtPrintPuerto" type="text" class="form-control" placeholder=""/>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12">
                                        <button class="btn btn-primary">Actualizar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px;">
                                    <div class="col-md-12 col-sm-12 col-xs-6">
                                        <h3>Impresoras</h3>
                                        <label for="">Lista de impresoras</label>
                                        <select name="" id="cmbLstImpresoras" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <label for="">Ancho Papel</label>
                                        <select name="" id="" class="form-control printPapel">
                                            <option value="80">80mm</option>
                                            <option value="58">58mm</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <label for="">Tipo</label>
                                        <select name="" id="" class="form-control printTipo">
                                            <option value="CAJA">CAJA</option>
                                            <option value="COCINA">COCINA</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <label for="">Num Copias</label>
                                        <input type="number" class="form-control printPaginas" placeholder="" value="1" min="1"/>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-12 col-sm-12 col-xs-6">
                                        <label for="">Comunicación con la impresora (Puedes poner IP)</label>
                                        <input type="text" class="form-control printImpresora" placeholder="" value=""/>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-6" style="text-align: right; margin-top:10px;">
                                        <button class="btn btn-primary btnAddImpresora">Actualizar o Crear</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <script id="no-impresoras" type="text/x-handlebars-template">
                                    <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
                                        <lottie-player src="https://assets1.lottiefiles.com/private_files/lf30_w0qudqe6.json"  background="transparent"  speed="1"  style="height: 300px;" loop autoplay></lottie-player>
                                        <p style="color: #999;">{{mensaje}}</p>
                                    </div>
                                </script>
                                <script id="lista-impresoras" type="text/x-handlebars-template">
                                    {{#each this}}
                                    <div class="col-md-12" style="margin-top:30px;padding: 0; text-align:center;">
                                        <h3>{{nombre}}</h3>
                                        <p><b>{{tipo}}</b></p>
                                        <p>{{paginas}} Pág,  Size: {{size}}mm</p>
                                        <button class="btn btn-danger">Eliminar Impresora</button>
                                        <hr/>
                                    </div>
                                    {{/each}}
                                </script>

                                <div class="LstImpresorasSave"></div>
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
    
    <!-- Modal Sonido -->
    <div class="modal fade bs-example-modal-lg" id="sonidoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Configurar Sonido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="cod_sucursal" id="cod_sucursal" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">
                    <input type="hidden" name="permisoRecordarOrdenes" id="permisoRecordarOrdenes" value="<?= $permisoRecordarOrdenes?>">    
                    <div class="x_content">    
                      <div class="row">
                          <div class="col-12">
                              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                  <label>Sonido <span class="asterisco">*</span></label>
                                  <select class="form-control" id="cmbSonido">
                                      <!-- <option value="0">Seleccione un sonido</option> -->
                                  </select>
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                  <label>¿Repetir siempre?:</label>
                                  <select class="form-control" id="cmbRepeat">
                                      <option value="1">SI</option>
                                      <option value="0">NO</option>
                                  </select>
                               </div>
                          </div>
                      </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button class="btn btnTestSonido"><i class="flaticon-cancel-12"></i> Probar Sonido</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarSonido">Guardar</button>
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
    
    <!-- MODAL FACT START-->
    <div class="modal fade bs-example-modal-lg" id="facturacionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Facturaci&oacute;n Electr&oacute;nica</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                <form id="" name="" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                          <?php
                            $cod_sistema_facturacion = 0;
                            $msgHtml = "<p>No tienes configurado Facturaci&oacute;n Electr&oacute;nica</p>";
                            $fact = $Clempresas->getProveedorFact($session['cod_empresa']);
                            if($fact){
                                $cod_sistema_facturacion = $fact['cod_sistema_facturacion'];
                                $name_facturacion = $fact['nombre'];
                                $id_facturacion = $fact['identificador'];
                                $msgHtml = "<p style='font-size: 18px;'>Facturaci&oacute;n Electr&oacute;nica Configurada.</p>
                                            <p>la factura se emitir&aacute; automaticamente al asignar la orden</p><br/>
                                            <p><b>Proveedor: </b>$name_facturacion</p>
                                            <p><b>Alias: </b>$id_facturacion</p>
                                            <p><b>Identificador: </b>$cod_sistema_facturacion</p>";
                            }
                            ?>
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <input type="hidden" id="fact_electronica" name="fact_electronica" value="<?php echo $cod_sistema_facturacion; ?>"/>
                              <?php echo $msgHtml; ?>
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
    <!-- MODAL FACT END-->

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

     <!--MODAL ABRIR TIENDA -->
     <div class="modal fade bs-example-modal-lg" id="modalAbrirTienda" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Abrir / Cerrar Tienda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                        <div class="row">
                            <div class="col-xl-6 col-md-6">
                                <label for="">Escoger la sucursal</label>
                                <select class="form-control" name="cmbSucFestivo" id="cmbSucFestivo">
                                    <?= $optionSucAbrir ?>
                                </select>
                            </div>
                            
                            <div class="col-xl-6 col-md-6" id="bloqueEstadoAbierto">
                                
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-xl-6 col-md-6">
                                <label style="margin-top: 20px; color: #000;">¿Desea una hora específica para cerrar, abrir o ver los cierres de la(s) sucursal(es)? <a href="sucursal_festivos.php">Click Aqu&iacute;</a></label>
                            </div>
                        </div>
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL BUSQUEDA -->

    <!--MODAL RECORDATORIOS Y AUTOASIGNACION -->
    <div class="modal fade bs-example-modal-lg" id="modalRecordatorios" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Recordatorios (sólo Delivery)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="x_content">    
                        <div class="row">
                            <div class="col-md-6 divAlertas">
                                <div class="col-md-12" style="display: flex; justify-content: space-between;">
                                    <label>¿Desea recordar pedidos entrantes?</label>
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input type="checkbox" name="chkRecordatorios" id="chkRecordatorios">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 row divAsignacion">
                                <div class="col-md-12" style="display: flex; justify-content: space-between;">
                                    <label>¿Desea asignar los pedidos autom&aacute;ticamente?</label>
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input type="checkbox" name="chkAsignar" id="chkAsignar" <?=$disabledCheck?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 div-recordar" style="display: flex;">
                                <div class="col-md-6">
                                    <label>Realizar cada:</label>
                                    <select class="form-control" id="cmbRecordar">
                                        <option value="1">1 minuto</option>
                                        <option value="3">3 minutos</option>
                                        <option value="5">5 minutos</option>
                                        <option value="10">10 minutos</option>
                                        <option value="15">15 minutos</option>
                                    </select>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary btnGuardarRecodatorios" >Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL NOTIFICAR -->
    
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar2(); ?>
    <!--  END NAVBAR  -->
    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container sidebar-closed" id="container">

        <div class="overlay show"></div>
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
                                <div class="col-xl-6 col-md-6">
                                    <div class="" style="margin-bottom:15px;text-align:left;">
                                        <input id="apikey_empresa" type="hidden" value="<?= $apikey?>">
                                        <input id="alias_empresa" type="hidden" value="<?= $session['alias']?>">
                                        <input id="tipo_empresa" type="hidden" value="<?= $tipoEmpresa?>">
                                        
                                        <button class="btn btn-info btnAbrirTienda" style="<?= $permisoEncenderTienda?>">Abrir / Cerrar Tienda</button>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="contenedorActivarSound" style="margin-bottom:15px;text-align:right;">
                                        <button class="btn btn-primary activarSound">Activar Sonido</button>
                                    </div>
                                </div>
                            </div>
                            
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
                                                        $query = "SELECT * FROM tb_estado_ordenes ORDER BY posicion ASC";
                                                        $resp = Conexion::buscarVariosRegistro($query);
                                                        foreach ($resp as $estados) {
                                                            $nombre = $estados['nombre'];
                                                            $id = $estados['cod_estado'];
                                                            $icono = $estados['icono'];
                                                            echo '<li class="nav-item">
                                                                <a class="nav-link list-actions active" id="'.$id.'"><i data-feather="'.$icono.'"></i><span class="nav-names">'.$nombre.'</span> <span class="mail-badge badge badge-'.$id.'"></span></a>
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
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="button" class="changeTipo btn btn-outline-info active" data-value="E">Env&iacute;os</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="P">Pickup</button>
                                                    <button type="button" class="changeTipo btn btn-outline-info" data-value="T">Todas</button>
                                                </div>
                                                <input type="hidden" name="" id="is_envio" value="1">
                                            </div>

                                            <div class="dropdown">
                                                <i onclick="openCalendar();" data-feather="calendar"></i>
                                                <i onclick="$('#facturacionModal').modal();" data-feather="clipboard"></i>
                                                <i onclick="$('#fidelizacionModal').modal();" data-feather="star"></i>
                                                <i id="btn-pause-sound" data-feather="volume-x"></i>
                                                <i onclick="$('#sonidoModal').modal();" data-feather="music"></i>
                                                <i onclick="openImpresion()" data-feather="printer"></i>
                                                <i onclick="$('#modalRecordatorios').modal();" data-feather="clock"></i>

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

                                                <select class="form-control" id="cmbSucursal" style="max-width: 200px; display: initial;">
                                                    <?php
                                                    if($session['cod_rol']==2){
                                                        $sucursalNotificar = 0;
                                                        echo '<option value="0">Todas las sucursales</option>';
                                                        $sucursales = $Clsucursales->lista();
                                                        foreach ($sucursales as $suc) {
                                                            echo '<option value="'.$suc['cod_sucursal'].'">'.$suc['nombre'].'</option>';
                                                        }
                                                    }else{
                                                        $sucursalNotificar = $session['cod_sucursal'];
                                                        $sucursales = $Clsucursales->getInfo($session['cod_sucursal']);
                                                        if($sucursales){
                                                            echo '<option value="'.$sucursales['cod_sucursal'].'">'.$sucursales['nombre'].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" id="notificarSucursal" value="<?php echo $sucursalNotificar; ?>" />
                                            </div>
                                        </div>
                                
                                        <div class="message-box">
                                            <div class="message-box-scroll" id="lista_ordenes">
                                            </div>
                                        </div>

                                        <div class="content-box">
                                            <div class="d-flex msg-close">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left close-message"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                                <h2 class="mail-title" data-selectedMailTitle="">Orden</h2>
                                            </div>
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
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <?php js_mandatory(); ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/ie11fix/fn.fix-padStart.js"></script>
    <script src="plugins/editors/quill/quill.js"></script>
    <script src="plugins/sweetalerts/sweetalert2.min.js"></script>
    <script src="plugins/notification/snackbar/snackbar.min.js"></script>
    <script src="plugins/ion.sound/ion.sound.js"></script>
    <!--<script src="assets/js/apps/custom-mailbox.js"></script>-->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    
    <script src="bootstrap/js/popper.min.js"></script>
    
    <script src="plugins/fullcalendar/moment.min.js"></script>
    <script src="plugins/fullcalendar/fullcalendar.min.js"></script>
    <script src='plugins/fullcalendar/es.js'></script>
    <script src="assets/js/pages/gestion_ordenes_complete.js" type="text/javascript"></script>
    <script src="assets/js/pages/gestion_ordenes_calendario.js?v=1" type="text/javascript"></script>
    <script src="assets/js/pages/gestion_ordenes_fidelizacion.js?v=1" type="text/javascript"></script>
    <script src="assets/js/pages/gestion_ordenes_impresion.js?v=1" type="text/javascript"></script>
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