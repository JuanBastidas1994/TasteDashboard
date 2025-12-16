<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_ordenes.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clordenes = new cl_ordenes(NULL);

if(isset($_GET['alias'])){  //NO ES POR SESSION
    $empresa = $Clempresas->getByAlias($_GET['alias']);
}else{                      //ES POR SESSION
    $session = getSession();
    $empresa = $Clempresas->get($session['cod_empresa']);
}

if($empresa){
    $cod_empresa = $empresa['cod_empresa'];
    $api = $empresa['api_key'];
}else{
    header("location:index.php");
}

$query = "SELECT * FROM tb_empresa_costo_envio WHERE cod_empresa = $cod_empresa";
$row = Conexion::buscarRegistro($query, NULL);
if($row){
    $base_km = $row['base_km'];
    $base_dinero = $row['base_dinero'];
    $adicional_km = $row['adicional_km'];
}
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
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Simulador Asignación Ordenes</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                                
                            </div> 
                            
                            <!---->
                            <div class="mb-4 mt-4">
                                <div class="col-xl-7 col-md-7 col-sm-7 col-7">
                                    <div class="row">
                                        <input type="hidden" id="apikey" value="<?php echo $api; ?>" />
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label>Rango Km de 0 a n? <span class="asterisco">*</span> </label>
                                            <input type="number" placeholder="" name="base_km" id="base_km" class="form-control" autocomplete="off" value="<?php echo $base_km; ?>">
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label>Tarifa por rango km <span class="asterisco">*</span></label>
                                            <input type="text" placeholder="" name="base_dinero" id="base_dinero" class="form-control" required="required" autocomplete="off" value="<?php echo $base_dinero; ?>">
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label>Tarifa por km adicional? <span class="asterisco">*</span> </label>
                                            <input type="number" placeholder="" name="adicional_km" id="adicional_km" class="form-control" autocomplete="off" value="<?php echo $adicional_km; ?>">
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row" style="margin-top:25px;">
                                        <div class="col-md-12 col-sm-12 col-xs-12"><h4>Ubicaci&oacute;n</h4></div>
                                    </div>
                                    
                                   <fieldset class="gllpLatlonPicker" >
                                      <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                          <input type="text" class="gllpSearchField form-control" placeholder="Direcci&oacute;n de busqueda">
                                      </div> 
                                      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                          <button type="button" class="gllpSearchButton btn btn-primary form-control"><i data-feather="search"></i> Buscar</button>
                                      </div>
                                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px; margin-bottom: 15px;">
                                        <div class="gllpMap" style="margin-left: 0; width: 100%; height: 400px;">Google Maps</div> 
                                      </div>
        
                                      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                          <label>Latitud:</label>
                                          <input type="text" class="gllpLatitude form-control" id="txt_latitud" name="txt_latitud" value="-2.152763678438842"/>
                                      </div>
                                      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                          <label>Longitud:</label>
                                          <input type="text" class="gllpLongitude form-control" id="txt_longitud" name="txt_longitud" value="-79.88652217812259"/>
                                      </div>
                                      <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                          <label>Zoom:</label>
                                          <input type="number" class="gllpZoom form-control" value="15"/>
                                       </div> 
                                       <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                          <label>&nbsp;</label>
                                          <input type="button" class="gllpUpdateButton btn btn-primary"  value="Actualizar">
                                       </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="display:none;">
                                              <label>Latitud Origen:</label>
                                              <input type="text" class="gllpLatitudeStart form-control" id="" value=""/>
                                          </div>
                                          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="display:none;">
                                              <label>Longitud Destino:</label>
                                              <input type="text" class="gllpLongitudeStart form-control" id=""  value=""/>
                                          </div>
                                    </fieldset>
                                </div>
                                <div class="col-xl-5 col-md-5 col-sm-5 col-5">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="infoMotorizado">
                                        <h3 style="border-bottom: 1px solid #dee2e6;">Resultados</h3> 
                                    </div>
                                    <script id="no-data" type="text/x-handlebars-template">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
                                            <lottie-player src="https://assets9.lottiefiles.com/private_files/lf30_oqpbtola.json"  background="transparent"  speed="1"  style="height: 300px;" autoplay></lottie-player>
                                            <p style="color: #999;">No hay cobertura</p>
                                    	</div>
                                    </script>
                                    <script id="loading-data" type="text/x-handlebars-template">
                                        <div class="col-md-12" style="margin-top:30px;padding: 0; text-align:center;">
                                			<h4><i class="fa fa-spinner fa-spin"></i> Cargando información...</h4>
                                		</div>
                                    </script>
                                    <script id="sucursales-item" type="text/x-handlebars-template">
                                        <div class="row">
                                            {{#each this}}
                                            <div class="col-xl-3 col-md-3 col-sm-3 col-3">
                                                <img src="{{image}}" style="width:100%;"/>
                                                <p><b>Cobertura:</b> {{distancia_km}}km</p>
                                                <p><button class="btn btn-primary btnArmarRuta" data-latitud="{{latitud}}" data-longitud="{{longitud}}">Armar Ruta</button></p>
                                            </div>
                                            <div class="col-xl-9 col-md-9 col-sm-9 col-9">
                                                <h4>{{nombre}} - ${{precio}}</h4>
                                                <p><b>Abierto:</b> {{abierto}}</p>
                                                <div class="col-xl-4 col-md-4 col-sm-4 col-12">
                                                    <p><b>LINEAL</b></p>
                                                    <p><b>Distancia:</b> {{LINEA_RECTA.distance}}km</p>
                                                    <p><b>Precio:</b> ${{LINEA_RECTA.price}}</p>
                                                </div>
                                                <div class="col-xl-4 col-md-4 col-sm-4 col-12">
                                                    <p><b>GOOGLE MAPS</b></p>
                                                    <p><b>Distancia:</b> {{GOOGLE_MAPS.distance}}km</p>
                                                    <p><b>Precio:</b> ${{GOOGLE_MAPS.price}}</p>
                                                    <p><b>Tiempo:</b> {{GOOGLE_MAPS.tiempo}}</p>
                                                </div>
                                                <div class="col-xl-4 col-md-4 col-sm-4 col-12">
                                                    <p><b>GACELA</b></p>
                                                    <p><b>Distancia:</b> {{GACELA.distance}}km</p>
                                                    <p><b>Precio:</b> ${{GACELA.price}}</p>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <hr/>
                                            </div>
                                            {{/each}}
                                        </div>
                                    </script>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 lstResultado">
                                        <div class="row">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <p style="text-align:center;">Esperando consulta...</p>
                                            </div>
                                            <hr/>
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
    <?php js_mandatory(); ?>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- Mapas -->
     <script type="text/javascript">
        var marker;
        /*TEMPLATES EN EL MISMO ARCHIVO*/
        var templateloading = Handlebars.compile($("#loading-data").html()); //DEBEN SER CREADOS CON LA ETIQUETA SCRIPT
        var templatenodata = Handlebars.compile($("#no-data").html());
        var templateSucursales = Handlebars.compile($("#sucursales-item").html());
        

        //https://dashboard.mie-commerce.com/assets/img/shipping.png
        function addMarker(id, nombre, lat, lon, pmap, radius){
            var posMarker = {lat: lat, lng: lon};
            var image = 'https://dashboard.mie-commerce.com/assets/img/tienda.png';
            marker = new google.maps.Marker({
              position: posMarker,
              map: pmap,
              icon: image,
              label: nombre,
              id: id
            });
            
            //ADHERIR COBERTURA
            let randomColor = "#"+Math.floor(Math.random()*16777215).toString(16);
            console.log(randomColor);
            const cityCircle = new google.maps.Circle({
              strokeColor: randomColor,
              strokeOpacity: 0.8,
              strokeWeight: 2,
              fillColor: randomColor,
              fillOpacity: 0.35,
              map: pmap,
              center: posMarker,
              radius: radius*1000,
            });
            /*
            marker.addListener('click', function() {
              getInfo(this);
            });*/
        }
        
    $(document).ready(function(){
            getSucursales();
    });
      
    
    $("body").on("change","#txt_latitud", function(){
        getSucursalesCobertura();
    });
    
    $("body").on("click",".btnArmarRuta", function(){
        $(".gllpLatitudeStart").val($(this).data("latitud"));
        $(".gllpLongitudeStart").val($(this).data("longitud"));
        
        let picker = window.mapPicker;
        picker.create_route();
    });
    
    function getSucursales(){
        let apiKey = $("#apikey").val();
	    $.ajax({
	        url:'https://api.mie-commerce.com/v8/sucursales',
	        type: "GET",
	        headers: {
	            'Api-Key':apiKey
	        },
	        success: function(response){
	            if(response['success']==1){
	                var sucursales = response['data'];

                	if(sucursales.length > 0){
                	    for(var x=0; x<sucursales.length; x++){
                            var item = sucursales[x];
                            console.log(item);
                            addMarker(item.cod_sucursal, item.nombre, parseFloat(item.latitud), parseFloat(item.longitud), window.mapGeneral, parseFloat(item.distancia_km));
                        }
                	}else{
                	    alert("Esta empresa no tiene sucursales");
                	}
	            }
	        },
	        error: function(data){
	            alert("Esta empresa no tiene sucursales error");
	        },
	        complete: function()
	        {
	          
	        }
	    });
	}
	
	function getSucursalesCobertura(){
        if($("#base_km").val().trim() == "" || $("#base_dinero").val().trim() == "" || $("#adicional_km").val().trim() == ""){
            alert("Debes ingresar el costo de envío");
            return;
        }

        let apiKey = $("#apikey").val();
	    let info = {
	        latitud:$("#txt_latitud").val(),
	        longitud:$("#txt_longitud").val(),
	        base_km:$("#base_km").val(),
	        base_dinero:$("#base_dinero").val(),
	        adicional: $("#adicional_km").val()
	    }
	    console.log(JSON.stringify(info));
	    $.ajax({
	        url:`https://api.mie-commerce.com/v8/sucursales/cobertura-test`,
	        type: "POST",
	        headers: {
	            'Api-Key':apiKey
	        },
	        data: JSON.stringify(info),
	        beforeSend: function(){
                $(".lstResultado").html(templateloading());
    		},
	        success: function(response){
	            console.log(response);
	            if(response['success']==1){
	                var sucursales = response['data'];
                    
                	if(sucursales.length > 0){
                	    $('.lstResultado').html(templateSucursales(sucursales));
                	}else{
                	    $(".lstResultado").html(templatenodata());
                	}
	            }else{
	                $(".lstResultado").html(templatenodata());
	            }
	        },
	        error: function(data){
	            console.log(data);
	            $(".lstResultado").html(templatenodata());
	        },
	        complete: function()
	        {
	          
	        }
	    });
	}
      
      
      
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&libraries=places,geometry,drawing"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>