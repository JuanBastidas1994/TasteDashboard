<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clsucursales = new cl_sucursales(NULL);
$session = getSession();
if(!userGrant()){
    header("location:index.php");
}

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
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
     
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR SUCURSAL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="cod_sucursal" id="cod_sucursal" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="upload mt-1 pr-md-1">
                                    <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                    <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                </div>
                            </div>
                        
                          <div class="col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                              <label>Nombre <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control maxlength" required="required" autocomplete="off" maxlength="50"/>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                              <label>Hora de Inicio <span class="asterisco">*</span></label>
                              <input name="hora_ini" id="hora_ini" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Seleccione hora" value="08:30">
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                              <label>Hora Fin <span class="asterisco">*</span></label>
                              <input name="hora_fin" id="hora_fin" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Seleccione hora" value="17:30">
                          </div>
                      </div>

                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-6" style="margin-bottom:10px;">
                              <label>Direcci&oacute;n <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Direccion del establecimiento" name="txt_direccion" id="txt_direccion" class="form-control" required="required" autocomplete="off"/>
                          </div>
                           <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                              <label>Cobertura:</label>
                              <input type="text" placeholder="Ej 10" name="txt_cobertura" id="txt_cobertura" class="gllpRadius form-control maxlength" required="required" autocomplete="off" maxlength="2" value="10"/>
                           </div>
                           <div class="col-md-2 col-sm-2 col-xs-12" style="margin-bottom:10px;">
                              <label>Emisor <span class="asterisco">*</span></label>
                              <input type="text" placeholder="Ej. 001" name="txt_emisor" id="txt_emisor" class="form-control maxlength" required="required" autocomplete="off" maxlength="3"/>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                              <label>Estado:</label>
                              <select class="form-control" name="cmbEstado" id="cmbEstado">
                                  <option value="A">Activo</option>
                                  <option value="I">Inactivo</option>
                              </select>
                           </div>
                      </div>

                      
                      <div class="form-group">
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                              <label>Ubicaci&oacute;n <span class="asterisco">*</span></label>
                          </div>
                          <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                            <fieldset class="gllpLatlonPicker" >
                              <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                  <input type="text" class="gllpSearchField form-control" placeholder="Direcci&oacute;n de busqueda">
                              </div> 
                              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                  <button type="button" class="gllpSearchButton btn btn-primary form-control"><i data-feather="search"></i> Buscar</button>
                              </div>
                              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px; margin-bottom: 15px;">
                                <div class="gllpMap" style="margin-left: 0; width: 100%;">Google Maps</div> 
                              </div>  

                              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                  <label>Latitud:</label>
                                  <input type="text" class="gllpLatitude form-control" id="txt_latitud" name="txt_latitud"  value=""/>
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                  <label>Longitud:</label>
                                  <input type="text" class="gllpLongitude form-control" id="txt_longitud" name="txt_longitud" value=""/>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                  <label>Zoom:</label>
                                  <input type="number" class="gllpZoom form-control" value="15"/>
                               </div> 
                               <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                  <label>&nbsp;</label>
                                  <input type="button" class="gllpUpdateButton btn btn-primary"  value="Actualizar">
                               </div>
                            </fieldset>
                          </div>
                      </div>

                      <div class="form-group">
                          
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
                    
                    <div class="alert alert-arrow-right alert-icon-right alert-light-danger mb-4" role="alert" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" data-dismiss="alert" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                        <strong>Advertencia!</strong> El permiso de ubicación esta apagado, si quieres una mejor experiencia de usuario habilita esta funcion para que el sistema detecte tu ubicacion
                    </div>

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Sucursales</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <a href="crear_sucursales.php" class="btn btn-primary" id="">Nueva Sucursal</a>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Direcci&oacute;n</th>
                                                <th>Cobertura</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $resp = $Clsucursales->lista();
                                            foreach ($resp as $sucursal) {
                                                $badge='primary';
                                                $imagen = $files.$sucursal['image'];
                                                if($sucursal['estado'] == 'I')
                                                    $badge='danger';
                                                echo '<tr data-value="'.$sucursal['cod_sucursal'].'">
                                                    <td class="text-center"><span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span></td>
                                                    <td>'.$sucursal['nombre'].'</td>
                                                    <td>'.$sucursal['direccion'].'</td>
                                                    <td>'.$sucursal['distancia_km'].'km</td>
                                                    <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($sucursal['estado']).'</span></td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="crear_sucursales.php?id='.$sucursal['cod_sucursal'].'" data-value="'.$sucursal['cod_sucursal'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                            <li><a href="javascript:void(0);" data-value="'.$sucursal['cod_sucursal'].'"  class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                                        </ul>
                                                    </td>
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
    
    <?php js_mandatory(); ?>
    <!-- Mapas -->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAHa67r_2hPqR_URtU8zsibmJx9Ahq7yGQ&libraries=places"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/sucursales.js" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script>
        // $("document").ready(function(){
        //     if ("geolocation" in navigator){
        //       navigator.geolocation.getCurrentPosition(show_location, show_error, {timeout:1000, enableHighAccuracy: true}); //position request
        //     }else{
        //       console.log("Browser doesn't support geolocation!");
        //     }
        // });

        //Success Callback
        function show_location(position){
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
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
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