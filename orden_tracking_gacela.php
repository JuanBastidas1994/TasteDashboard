<?php
require_once "funciones.php";
require_once "clases/cl_ordenes.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";

if(!isLogin()){
    header("location:login.php");
}

$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clusuarios = new cl_usuarios(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = url_sistema.'/assets/img/200x200.jpg';
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $orden = $Clordenes->get_orden_array($id);
  if($orden){
    $order_token="";  
    if($orden['cod_courier']==1)
    {
        $order_token= $orden['order_token'];
    }
    if($order_token==""){
    header("location:login.php");
    }
    
    $cod_sucursal = $orden['cod_sucursal'];
    $infoSuc = $Clsucursales->isMySucursal($cod_sucursal);
    $numOrden = str_pad($orden['cod_orden'], 6, "0", STR_PAD_LEFT);
    $estado = $orden['estado'];
    $textoEstado = getEstado($estado);
    
    $badge='primary';
    if($orden['estado'] == 'I')
        $badge='danger';
    else if($orden['estado'] == "ENTREGADA")
        $badge='success';
    else if($orden['estado'] == "ASIGNADA")
        $badge='warning';
    else if($orden['estado'] == "CANCELADA" || $orden['estado'] == "ANULADA")
        $badge='danger';
	
    $cod_usuario = $orden['cod_usuario'];
    $nombre = $orden['nombre'].' '.$orden['apellido'];
    $telefono = $orden['telefono'];
    $direccion = $orden['referencia'];
    $correo = $orden['correo'];
    $fecha = fechaLatinoShort($orden['fecha']);
    $hora = explode(" ",$orden['fecha'])[1];
    /*--NUEVO-*/
    $is_envio = $orden['is_envio'];
    $styleLinea="display:block";
    if ($is_envio == 0)
    $styleLinea = "display:none";
    /*--NUEVO-*/
    /*UBICACION*/
    $latitud =  $orden['latitud'];
    $longitud =  $orden['longitud'];

    
    /*DATOS DE FACTURACION*/
    $fact = $orden['datos_facturacion'];
    if($fact != ""){
      $datos_facturacion = json_decode($fact,true);
    }else{
      $datos_facturacion['nombre'] = "";
      $datos_facturacion['apellido'] = "";
      $datos_facturacion['cedula'] = "";
      $datos_facturacion['correo'] = "";
      $datos_facturacion['empresa'] = "";
      $datos_facturacion['telefono'] = "";
      $datos_facturacion['direccion'] = "";
    }
    

    /*DINERO*/
    $subtotal = number_format($orden['subtotal'],2);
    $descuento = number_format($orden['descuento'],2);
    $envio = number_format($orden['envio'],2);
    $iva = number_format($orden['iva'],2);
    $total = number_format($orden['total'],2);

  }else{
    header("location: ./index.php");
  }
}else{
  header("location: ./index.php");
}

function datetimeShort($fecha){
  $separate = explode(" ",$fecha);
  $fecha = $separate[0];
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('y', strtotime($fecha));

  $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return "$nombreMes $numeroDia/$anio ".substr($separate[1], 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/timeline/custom-timeline.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
    <style type="text/css">
      .respGalery > div {
          margin-top: 15px;
      }

      .itemSucursal{
        border-radius: 6px;
        border: 1px solid #e0e6ed;
        padding: 14px 26px;
        margin-bottom: 10px;
      }

      .itemSucursal .title{
        font-size: 16px;
        font-weight: bold;
      }

      .switch.s-icons {
        height: auto;
      }

      .feather-16{
          width: 16px;
          height: 16px;
      }
    </style>
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><a id="btnBack" data-module-back="ordenes.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Ordenes</span></a>
                    </div>
                    <h3 id="titulo"><?php echo ($numOrden != "") ? "Orden #".$numOrden : "Orden"; ?> <span class="shadow-none badge badge-<?php echo $badge; ?>"><?php echo $textoEstado; ?></span></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px;">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;display:none;">
                        <i class="feather-16" data-feather="mail"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Enviar Correo</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;display:none;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Anular</span>
                      </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                          <?php
                          echo '<div class="map" data-latitud="'.$orden['latitud'].'" data-longitud="'.$orden['longitud'].'" data-orden="'.$orden['cod_orden'].'" data-is-gacela="'.$orden['cod_courier'].'" data-token="'.$orden['order_token'].'"  style="width: 100%; height: 500px;"></div>';
                          ?>
                    </div>

                    
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
    <script type="text/javascript">
	var map, marker;
    var pos = {lat: -25.363, lng: 131.044};
    $("document").ready(function(){
    	if($(".map").length>0){
    		//alert("hay algunos mapas");
    	}

        if ("geolocation" in navigator){
          navigator.geolocation.getCurrentPosition(show_location, show_error, {timeout:1000, enableHighAccuracy: true}); //position request
        }else{
          console.log("Browser doesn't support geolocation!");
        }

        $(".review").rating({
        	"value": 5,
        	"half":true,
        	"color":"#e4b405",
        	"emptyStar":"fa fa-star-o",
  			"halfStar":"fa fa-star-half-o",
  			"filledStar":"fa fa-star",
  			"click": function(e){
  				console.log(e);
  				$("#numero"+e.id).val(e.stars);
  			}
        });

    });

    //Success Callback
    function show_location(position){
        pos = {lat: position.coords.latitude, lng: position.coords.longitude};
        console.log(pos);
        marker.setPosition(pos);
        map.setCenter(marker.getPosition());
    }

    //Error Callback
    function show_error(error){
       switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Permission denied by user.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location position unavailable.");
                break;
            case error.TIMEOUT:
                alert("Request timeout.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Unknown error.");
                break;
        }
    }


        function initMap() {
	    console.log(pos);
	    var mapas = document.getElementsByClassName('map');
	    for(x=0; x<mapas.length; x++){
	    	var cod_orden = mapas[x].getAttribute("data-orden");
	    	var cod_motorizado = mapas[x].getAttribute("data-motorizado");
	    	var latitud = parseFloat(mapas[x].getAttribute("data-latitud"));
	    	var longitud = parseFloat(mapas[x].getAttribute("data-longitud"));
    		var isgacela = mapas[x].getAttribute("data-is-gacela");
	    	var metodo = "get_ubicacion";
	    	var token ="";
	    	if(isgacela == 1)
		    {
		        metodo = "get_ubicacionGacela";
		        //metodo = "get_ubicacionAleatorio"; // para pruebas
		        token = mapas[x].getAttribute("data-token");
		    }
		   // metodo = "get_ubicacionAleatorio";
	    	pos = {lat: latitud, lng: longitud};
	    	var position = [latitud, longitud];
	    //	console.log(pos);

	    	map = new google.maps.Map(mapas[x], {
		      zoom: 12,
		      center: pos
		    });

		    marker = new google.maps.Marker({
		      position: pos,
		      map: map,
		      id: 15
		    });
		    
		        var image = 'https://digitalmindtec.com/shipping.png';
		    markerMoto = new google.maps.Marker({
		      map: map,
		      icon: image
		    });
		    

            var numDeltas = 100;
            var delay = 10; //milliseconds
            var i = 0;
            var deltaLat;
            var deltaLng;
            function transition(result){
                i = 0;
                deltaLat = (result[0] - position[0])/numDeltas;
                deltaLng = (result[1] - position[1])/numDeltas;
                moveMarker();
            }
            
            function moveMarker(){
                position[0] += deltaLat;
                position[1] += deltaLng;
                var latlng = new google.maps.LatLng(position[0], position[1]);
                markerMoto.setPosition(latlng);
                if(i!=numDeltas){
                    i++;
                    setTimeout(moveMarker, delay);
                }
            }

		    setInterval(function(orden, mapa, motorizado, markerReload){ 
		    	var parametros = {
		    		cod_usuario: motorizado,
		    		cod_orden: cod_orden,
		    		cod_token: token
		    	}
		    //	console.log(motorizado);
		    	$.ajax({
			        url: 'controllers/controlador_usuario.php?metodo='+metodo,
			        type: 'GET',
			        data: parametros,
			        success: function(response){
			            console.log(response);
			            if( response['success'] == 1)
			            {
			                var data = response['data'];
			                var latitud = parseFloat(data['latitud']);
	    					var longitud = parseFloat(data['longitud']);
	    					var posMotorizado = {lat: latitud, lng: longitud};
			                console.log(posMotorizado);
			                //markerReload.setPosition(posMotorizado);
			                var result = [latitud,longitud];
                            transition(result);
			            } 
			        },
			        error: function(data){
			            console.log(data);
			        },
			    });
		    }, 3000, cod_orden, map, cod_motorizado, markerMoto);
	    }
	  }
</script>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&callback=initMap"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <!--<script src="assets/js/pages/orden_tracking.js" type="text/javascript"></script>-->
    <script src="assets/js/rating.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>