<?php
require_once "funciones.php";
require_once "clases/cl_modal_eventos.php";

if(!isLogin()){
    header("location:login.php");
}

$Cleventos = new cl_modal_eventos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$cod_modal_evento = 0;
$imagen = url_sistema.'/assets/img/200x200.jpg';

$titulo = "";
$fecha_ini = "";
$fecha_fin = "";
$accion_id = "";
$accion_desc = "";
$descripcion = "";
$ckEstado = "checked";

$optionAccion = "";
$opAcc = [];
$opAcc[] = "INFO";
$opAcc[] = "URL";
$opAcc[] = "PRODUCTO";
$opAcc[] = "NOTICIA";
$opAcc[] = "FILTER";

$opDesc = [];
$opDesc[] = "Informativo (No hace nada)";
$opDesc[] = "Ir a una URL";
$opDesc[] = "Ir a un Producto";
$opDesc[] = "Ir a una noticia";
$opDesc[] = "Filtrar Productos";

for($i=0; $i<count($opAcc); $i++){
    $optionAccion.='<option value="'.$opAcc[$i].'">'.$opDesc[$i].'</option>';
}

if(isset($_GET['id'])){
    $cod_modal_evento = $_GET['id'];
    $row = $Cleventos->get($cod_modal_evento);
    if($row){
        $titulo = $row['titulo'];
        $fecha_ini = $row['fecha_inicio'];
        $fecha_fin = $row['fecha_fin'];
        $accion_id = $row['accion_id'];
        $accion_desc = $row['accion_desc'];
        $descripcion = $row['descripcion'];
        $imagen = $files.$row['imagen'];
        if("A" <> $row['estado'])
            $ckEstado = "";

        for($i=0; $i<count($opAcc); $i++){
            $selectAcc = "";
            if($accion_id == $opAcc[$i])
                $selectAcc = "selected";
            $optionAccion.='<option value="'.$opAcc[$i].'" '.$selectAcc.'>'.$opDesc[$i].'</option>';
        }
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
                        <div><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Eventos</span></span>
                        </div>
                        <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Crear Evento"; ?></h3>
                    </div>

                    <div class="col-xl-10 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <input type="hidden" placeholder="" name="cod_modal_evento" id="cod_modal_evento" class="form-control" required="required" autocomplete="off" value="<?php echo $cod_modal_evento; ?>"/>
                            <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                                <div class="col-lg-12 col-ms-12 col-xs-12">    
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="upload mt-1 pr-md-1">
                                                <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                                <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="Nombre" name="txt_titulo" id="txt_titulo" class="form-control maxlength" required="required" autocomplete="off" maxlength="50" value="<?php echo $titulo; ?>"/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Accion ID:</label>
                                            <select class="form-control" name="cmb_accion" id="cmb_accion">
                                                <?= $optionAccion?>
                                            </select> 
                                        </div>
                                    </div>
            
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Fecha Inicio <span class="asterisco">*</span></label>
                                            <input type="text" name="txt_fecha_ini" id="txt_fecha_ini" class="form-control flatpickr-input" value="<?php echo $fecha_ini; ?>" required/>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Fecha Fin</label>
                                            <input type="text" name="txt_fecha_fin" id="txt_fecha_fin" class="form-control flatpickr-input" value="<?php echo $fecha_fin; ?>" required/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Descipci&oacute;n de la acci&oacute;n:</label>
                                            <textarea type="text" placeholder="Descipci&oacute;n de la acci&oacute;n" name="txt_desc_accion" id="txt_desc_accion" class="form-control" autocomplete="off"><?php echo $accion_desc; ?></textarea>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                            <label>Descipci&oacute;n:</label>
                                            <textarea type="text" placeholder="Descipci&oacute;n del Evento" name="txt_desc" id="txt_desc" class="form-control" autocomplete="off"><?php echo $descripcion; ?></textarea>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12" style="margin-bottom:10px;">
                                            <label>Estado:</label>
                                            <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                <input type="checkbox" name="ckEstado" id="ckEstado" value="" <?php echo $ckEstado; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>            
                                
                                </div>
                            </form>
                            <div>&nbsp;</div>    
                                        
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
    <!-- Mapas -->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDe9LjbQR0UAc8PMVJXc66flE7yqrJbD6o&libraries=places"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/modal_eventos.js" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script>
        $("document").ready(function(){
            if ("geolocation" in navigator){
              navigator.geolocation.getCurrentPosition(show_location, show_error, {timeout:1500, enableHighAccuracy: true}); //position request
            }else{
              console.log("Browser doesn't support geolocation!");
            }
        });

        //Success Callback
        function show_location(position){
            if($("#cod_sucursal").val() != 0){
                return;
            }
            
            var pos = {lat: position.coords.latitude, lng: position.coords.longitude};
            $("#txt_longitud").val(pos.lng);
            $("#txt_latitud").val(pos.lat);            
            console.log(pos);
            $(".gllpUpdateButton").trigger("click");
            /*infoWindow = new google.maps.InfoWindow({map: map});
            infoWindow.setPosition(pos);
            infoWindow.setContent('User Location found.');
            */
            //map.setCenter(pos);
        }

        //Error Callback 
        function show_error(error){
           switch(error.code) {
                case error.PERMISSION_DENIED:
                    $(".alert").show();
                    break;
                case error.POSITION_UNAVAILABLE:
                    $(".alert").show();
                    break;
                case error.TIMEOUT:
                    $(".alert").show();
                    break;
                case error.UNKNOWN_ERROR:
                    $(".alert").show();
                    break;
            }
        }


        var myTable = $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'pdf', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                "sInfoEmpty": "Mostrando pag. 1",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
               "sEmptyTable": "No se encontraron resultados",
               "sZeroRecords": "No se encontraron resultados",
               "buttons": {
                    "copy": "Copiar",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir",
                    "create": "Crear",
                    "edit": "Editar",
                    "remove": "Remover",
                    "upload": "Subir"
                }
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 10
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>