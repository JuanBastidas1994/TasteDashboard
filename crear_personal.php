<?php
require_once "funciones.php";
require_once "clases/cl_personal.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";

if(!isLogin()){
    header("location:login.php");
}
$Clpersonal = new cl_personal(NULL);
$Clusuario = new cl_usuarios(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);

$session = getSession();
/*
if(!userGrant()){
    header("location:index.php");
}*/

$iddia = array("0","1","2","3","4","5","6");
$dias = array("Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo");
$disponibilidadDay = array("checked","checked","checked","checked","checked","checked","checked");
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$cod_usuario = "";
//$imagen = url_sistema.'/assets/img/200x200.jpg';
$imagen="";
$nombre = "";
$apellido="";
$correo = "";
$fecha_nac="";
$telefono = "";
$estado = "";
$chkSucursal = "checked ";
 
if(isset($_GET['id'])){
    $cod_usuario = $_GET['id'];
    $personal = $Clpersonal->getArray($cod_usuario);
    if($personal){
        //$disponibilidadDay = array();
        $imagen = $files.$personal['imagen'];
        $nombre = $personal['nombre'];
        $apellido = $personal['apellido'];
        $correo = $personal['correo'];
        $fecha_nac = $personal['fecha_nacimiento'];
        $telefono = $personal['telefono'];
        $estado = $personal['estado'];
        
        $chkSucursal = "";
        $estadoSucursal = "I";
        if($estado == "A")
        $chkSucursal = "checked";
        $estadoSucursal = "A";
    }
    else{
        header("location: ./index.php");
    } 
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

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/elements/alert.css">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
    
    <!-- CALENDAR -->
    <link href='calendar/fullcalendar.min.css' rel='stylesheet' />
    <link href='calendar/fullcalendar.print.min.css' rel='stylesheet' media='print' />
</head>
<body>
     <!-- Modal Recortador-->
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
     <!-- End Modal Recortador-->

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
                
                <div class="row layout-top-spacing">

                    <div class="col-md-12" style="margin-top:25px; ">
                        <div><span id="btnBack" data-module-back="personal.php" style="cursor: pointer;">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Personal</span></span>
                        </div>
                        <h3 id="titulo">Agregar Personal</h3>
                    </div>

                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <input type="text" placeholder="" name="cod_usuario" id="cod_usuario" class="form-control" required="required" autocomplete="off" value="<?php echo $cod_usuario; ?>"/>
                            <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                                <div class="x_content">    
                                    <div class="form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <div class="upload mt-1 pr-md-1">
                                                <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                                <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                            <label>Nombre <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control maxlength" required="required" autocomplete="off" maxlength="50" value="<?php echo $nombre; ?>"/>
                                        </div>
                                        <div class="col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                            <label>Apellido <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Apellido" name="txt_apellido" id="txt_apellido" class="form-control maxlength" required="required" autocomplete="off" maxlength="50" value="<?php echo $apellido; ?>"/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                            <label>Correo <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Correo" name="txt_correo" id="txt_correo" class="form-control maxlength" required="required" autocomplete="off" maxlength="50" value="<?php echo $correo; ?>"/>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Contrase&ntilde;a</label>
                                            <input type="text" placeholder="********" name="txt_password" id="txt_password" class="gllpRadius form-control" autocomplete="off"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Fecha de Nacimiento <span class="asterisco">*</span></label>
                                            <input type="date" name="txt_fecha_nac" id="txt_fecha_nac" class="form-control" required="required" autocomplete="off" value="<?php echo $fecha_nac; ?>"/>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Tel&eacute;fono</label>
                                            <input type="text" placeholder="Tel&eacute;fono" name="txt_telefono" id="txt_telefono" class="gllpRadius form-control" required="required" autocomplete="off" value="<?php echo $telefono; ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Estado:</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px; text-align:center;">
                                            <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                <input type="checkbox" class="chkEstadoSuc" name="chkEstadoSuc" id="chkEstadoSuc" value="" <?php echo $chkSucursal; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <input type="hidden" name="cmbEstado" id="cmbEstado" value="<?php echo $estadoSucursal; ?>">
                                        </div>
                                    </div>
                                </div>
                            </form>   
                            <input type="text" style="border:none;">                                       
                        </div>                    
                    </div>

                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                                <div style="margin-top: 15px;">
                                      <div><h4>Disponibilidad</h4></div>
                                      <form id="frmDisponibilidad" method="POST" action="#">
                                        <div>
                                            <select class="form-control" id="cmb_sucursales" name="cmb_sucursales">
                                                <option value="0">Escoja una sucursal</option>
                                                <?php
                                                    $sucursales = $Clsucursales->lista();
                                                    foreach ($sucursales as $suc) {
                                                        echo '<option value="'.$suc['cod_sucursal'].'">'.$suc['nombre'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div><br>
                                        <div id='calendar'></div>  
                                      </form>
                                      <input type="text" style="border:none">
                                </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div>
                                <h4>Servicios</h4>
                            </div>
                            <div>
                                <?php
                                    $servicios = $Clproductos->lista_servicios();
                                    foreach ($servicios as $serv) {
                                        echo'<p>'.$serv['nombre'].'</p>';
                                    }
                                ?>
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
    <script src="assets/js/pages/personal.js" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <!-- CALENDAR -->
    <script src='calendar/moment.min.js'></script>
    <script src='calendar/fullcalendar.min.js'></script>
    <script src='calendar/es.js'></script>
    <script>
        $(document).ready(function() {		
            var calendar = $('#calendar').fullCalendar({
                header: false,
                lang: 'es',
                defaultView: 'agendaWeek',
                columnFormat: 'dddd',
                selectable:true,
                allDaySlot: false,
                navLinks: true,
                editable: true,
                eventLimit: true,
                minTime: '07:00:00',
                maxTime: '21:00:00',
                slotDuration: "00:60:00",
                slotLabelFormat: 'H:mm',
                timeFormat: 'H(:mm)',
                select: function(start, end, jsEvent, view) {
                    var fechaClick = start.format("Y-MM-DD");
                    var eventHour = start.format("H");
                    var eventMinutes = start.format("m");
                    console.log(fechaClick + "Hora" + eventHour + "Minutos" + eventMinutes);
                    
                    if(eventHour != 0)
                    {
                        var value = $("#cmbHorarioConsultorio").val();
                        var obj = JSON.parse(value);
        
                        var parametros = {
                        dia: start.format("dddd"),
                        fecha_inicio: start.format("HH:mm"),
                        fecha_fin : end.format("HH:mm"),
                        cod_sucursal : obj.cod_consultorio
                        }
                        console.log(parametros);
                        ingresarDisponibilidad(parametros);
                    }
                },
                eventDrop: function(event, delta, revertFunc) {
                    idCita = event.id;
                    tipoCita = event.tipo;
                    revertFunc2 = revertFunc;
                    var fecha = event.start.format("Y-MM-DD");
                    var fecha_inicio = event.start.format("HH:mm");
                    var fecha_fin = event.end.format("HH:mm");

                    /*$("#titulo").html(event.title);
                    $("#reagenda_inicio").val(fecha_inicio);
                    $("#reagenda_fin ").val(fecha_fin);
                    $("#reagenda_id").val(idCita);
                    $("#reagenda_dia").val(event.start.format("dddd"));
                    $("#modalReagendar").modal();*/
                },
                eventResize: function(event, delta, revertFunc) {
                    idCita = event.id;
                    tipoCita = event.tipo;
                    revertFunc2 = revertFunc;
                    var fecha = event.start.format("Y-MM-DD");
                    var fecha_inicio = event.start.format("HH:mm");
                    var fecha_fin = event.end.format("HH:mm");

                    /*$("#titulo").html(event.title);
                    $("#reagenda_inicio").val(fecha_inicio);
                    $("#reagenda_fin ").val(fecha_fin);
                    $("#reagenda_id").val(idCita);
                    $("#reagenda_dia").val(event.start.format("dddd"));
                    $("#modalReagendar").modal();*/
                },
                loading: function(bool) {
                    if (bool){
                        console.log('Esta cargando...');
                    }
                    else{
                        //TERMINO DE CARGAR.
                    }
                }
            });
	    });

        function ReloadCalendar()
        {
            $('#calendar').fullCalendar('option', {
                /*slotDuration: "00:60:00",
	            slotLabelInterval: "00:60:00",*/
                minTime: "08:00:00",
                maxTime: "17:00:00"
            });
            
            /*$('#calendar').fullCalendar('removeEventSource', sources);
            $('#calendar').fullCalendar('addEventSource', sources);*/
            $('#calendar').fullCalendar('refetchEvents');
            $('#calendar').fullCalendar('render');
        }
        ReloadCalendar();
    </script>
</body>
</html>