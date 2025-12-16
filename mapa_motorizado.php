<?php
require_once "funciones.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_ordenes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clusuarios = new cl_usuarios(NULL);
$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <!-- END PAGE LEVEL CUSTOM STYLES -->
</head>
<body>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR CUPON</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Tipo <span class="asterisco">*</span></label>
                              <select name="cmbTipo" id="cmbTipo" class="form-control" required="required" autocomplete="off">
                                <option value="0">Porcentaje</option>
                                <option value="1">Dinero</option>
                              </select>
                          </div>
                         <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Monto (No decimales)<span class="asterisco">*</span></label>
                              <input type="text" placeholder="" name="txt_monto" id="txt_monto" class="form-control maxlength" required="required" autocomplete="off" maxlength="3"/>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Cantidad de Usos <span class="asterisco">*</span></label>
                              <input type="text" placeholder="" name="txt_cantidad" id="txt_cantidad" class="form-control maxlength" required="required" autocomplete="off" maxlength="2"/>
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Fecha de Expiraci&oacute;n <span class="asterisco">*</span></label>
                              <input name="fecha_expiracion" id="fecha_expiracion" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Seleccione fecha" value="">
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
                        <div class="widget-content widget-content-area br-6" style="height: 980px;">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Seguimiento Motorizados</h4>
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                                    <select id="cmbMotorizado" class="form-control">
                                        <option value="">Escoja un motorizado</option>
                                        <?php
                                          $lista = $Clusuarios->lista_motorizados();
                                          foreach ($lista as $motorizado) {
                                            $id = $motorizado['cod_usuario'];
                                            $nombre = $motorizado['nombre']." ".$motorizado['apellido'];
                                            echo '<option value="'.$id.'" data-id="'.$motorizado['cod_usuario'].'">'.$nombre.'</option>';
                                          }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                                
                            </div> 
                            
                            <!---->
                            <div class="mb-4 mt-4">
                            
                                <div class="col-xl-7 col-md-7 col-sm-7 col-7">
                                   <div id="map" style="width: 100%; height: 500px;"></div>
                                </div>
                                <div class="col-xl-5 col-md-5 col-sm-5 col-5">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="infoMotorizado">
                                     
                                    </div>
                                    <br>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                         <br>
                                       	<h3 style="border-bottom: 1px solid #dee2e6;">Lista de Ordenes</h3> 
                                        <div class="table-responsive mb-4 mt-4" >
                                            <table id="style-3" class="table style-3  table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>N.</th>
                                                        <th>Cliente</th>
                                                        <th class="text-center">Estado</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="OrdenesMotorizado">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                   
                                   
                                </div>
                              
                            </div><!---->
                            
                            <!--
                            <div class="mb-4 mt-4">
                               <div id="map" style="width: 100%; height: 500px;"></div>
                            </div>-->
                           
                            
                            
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
     <script type="text/javascript">
        var markers = [];
        var map, marker;
        var pos = {lat: -2.1638691840653013, lng: -79.91144648977968};

        function reloadMotorizados(){
            $.ajax({
                  beforeSend: function(){
                      //OpenLoad("Buscando informacion, por favor espere...");
                   },
                  url: 'controllers/controlador_usuario.php?metodo=lista_motorizados',
                  type: 'GET',
                  success: function(response){
                      console.log(response);
                      if( response['success'] == 1)
                      {
                        for (var i = 0; i < markers.length; i++) {
                           markers[i].setMap(null);
                        }

                        $("#cmbMotorizado").html('<option value="">Escoja un motorizado</option>');
                        var data = response['data'];
                        for (var i = 0; i < data.length; i++) {
                          var moto = data[i];
                          var lat = parseFloat(moto['latitud']);
                          var lon = parseFloat(moto['longitud']);
                          if(lat!="" && lon!=""){
                              var id = moto['cod_usuario'];
                              var nombre = moto['nombre'] + " " + moto['apellido'];
                              addMarker(id, nombre, lat, lon);
                              console.log(nombre);
                              $("#cmbMotorizado").append('<option value="'+lat+','+lon+'" data-id="'+id+'">'+nombre+'</option>');
                          }
                          
                        }

                        console.log(data);
                      } 
                      else
                      {
                        messageDone(response['mensaje'],'error');
                      }
                                               
                  },
                  error: function(data){
                    console.log(data);
                  },
                  complete: function(resp)
                  {
                    //CloseLoad();
                  }
              });
        }

        function addMarker(id, nombre, lat, lon){
            var posMarker = {lat: lat, lng: lon};
            var image = 'https://digitalmindtec.com/shipping.png';
            marker = new google.maps.Marker({
              position: posMarker,
              map: map,
              icon: image,
              label: nombre,
              id: id
            });
            marker.addListener('click', function() {
              getInfo(this);
            });
            markers.push(marker);
        }

      function initMap() {
        console.log(pos);
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 14,
          center: pos
        });

        //marker.setPosition(pos);
        //map.setCenter(marker.getPosition());

        reloadMotorizados();
          setInterval(function(){
            reloadMotorizados();
          }, 5000);
      }

      function getInfo(pMarker){
        console.log(pMarker);
        console.log(pMarker.id);
        alert(pMarker.label);
      }

      $("#cmbMotorizado").on("change",function(){
        var optionSelected = $(this).find("option:selected");
        var valueSelected  = optionSelected.val();
        var res = valueSelected.split(",");
        var codigo=optionSelected.attr("data-id");
       
        if(res.length<2)
          return;

        lats = res[0];
        longs = res[1];
        map.setCenter(new google.maps.LatLng(lats,longs));
        var parametros = {
        "codigo": codigo
        }
        
         $.ajax({
                  beforeSend: function(){
                      //OpenLoad("Buscando informacion, por favor espere...");
                   },
                  url: 'controllers/controlador_usuario.php?metodo=info_motorizado',
                  type: 'POST',
                  data: parametros,
                  success: function(response){
                      console.log(response);
                      if( response['success'] == 1)
                      {
                        $('#style-3').DataTable().destroy();
                        $("#infoMotorizado").html(response['html']);
                        $("#OrdenesMotorizado").html(response['tabla']);
                        //$('#style-3').destroy();
                        
                        //$('#style-3').DataTable();
                        cargarComponentes();
        
                      } 
                      else
                      {
                        messageDone(response['mensaje'],'error');
                      }
                                               
                  },
                  error: function(data){
                    console.log(data);
                  },
                  complete: function(resp)
                  {
                    //CloseLoad();
                  }
              });

      });
        
         
        
        function cargarComponentes(){
            $('#style-3').DataTable( {
                dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                buttons: {
                    buttons: [
                        { extend: 'copy', className: 'btn' },
                        { extend: 'excel', className: 'btn' },
                        { extend: 'pdf', className: 'btn' },
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
                        "excel": "Excel",
                        "pdf": "PDF",
                        "create": "Crear",
                        "edit": "Editar",
                        "remove": "Remover",
                        "upload": "Subir"
                    }
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 5,
                "order": [[ 0, "desc" ]]
            } );    
        }
        
        cargarComponentes();
      
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&callback=initMap"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>