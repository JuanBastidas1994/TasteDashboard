<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_ordenes.php";

if(!isLogin()){
    header("location:login.php");
}
$Clempresas = new cl_empresas(NULL);
$Clordenes = new cl_ordenes(NULL);
$session = getSession();
$empresa = $Clempresas->get($session['cod_empresa']);

if($empresa){
    $cod_empresa = $empresa['cod_empresa'];
    $api = $empresa['api_key'];
}else{
    header("location:index.php");
}

if(isset($_GET['id'])){
  $id = $_GET['id'];
  $orden = $Clordenes->get_orden_array($id);
  if($orden){
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
  }else{
    header("location: ./index.php");
  }
}else{
  header("location: ./index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <title>Orden #<?= $numOrden; ?></title>
    <?php css_mandatory(); ?>
    <link rel="stylesheet" href="plugins/timeline/style.css">
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
                    <div><a href="orden_detalle.php?id=<?php echo $id; ?>" id="btnBack" data-module-back="ordenes.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Orden Detalle</span></a>
                    </div>
                    <h3 id="titulo"><?php echo ($numOrden != "") ? "Orden #".$numOrden : "Orden"; ?> <span class="shadow-none badge badge-<?php echo $badge; ?>"><?php echo $textoEstado; ?></span></h3>
                    <input type="hidden" id="idOrden" value="<?php echo $id; ?>">
                    <input type="hidden" id="apikey" value="<?php echo $api; ?>">
                </div>

                <div class="row layout-top-spacing">
                    <script id="steps" type="text/x-handlebars-template">
                        <section id="cd-timeline" class="cd-container">
                            {{#each timeline}}
                                <div class="cd-timeline-block">
                                    {{#eq complete true}}
                                    <div class="cd-timeline-img cd-picture">
                                    {{else}}    
                                    <div class="cd-timeline-img cd-movie">
                                    {{/eq}}    
                                        <img src="{{image}}" alt="{{titulo}}">
                                    </div>
                        
                                    <div class="cd-timeline-content step0">
                                        <h2>{{titulo}}</h2>
                                        <p>{{texto}}</p>
                                        <span class="cd-date">{{fecha}}</span>
                                    </div>
                                </div>
                            {{/each}}    
                        </section>
                    </script>
                    <script id="infoGuia" type="text/x-handlebars-template">
                        <h3>Datos de la guía</h3>
                        <p><b>Número de guía:</b> {{noGuia}}</p>
                        <p><b>Servicio:</b> {{producto}}</p>
                        <p><b># Artículos:</b> {{numeroEnvios}}</p>
                        <p><b>Peso:</b> {{pesoKilos}}kg</p>
                        <hr/>
                        <p><b>De:</b> {{nombreCliente}}</p>
                        <p><b>Ciudad Origen:</b> {{ciudadOrigen}}</p>
                        <p><b>Dirección Origen:</b> {{direccionOrigen}}</p>
                        <p><b>Teléfono Origen:</b> {{telefonoOrigen1}}</p>
                        <hr/>
                        <p><b>Para:</b> {{para}}</p>
                        <p><b>Ciudad Destino:</b> {{ciudadDestino}}</p>
                        <p><b>Dirección Destino:</b> {{direccionDestino}}</p>
                        <p><b>Teléfono Destino:</b> {{telefonoDestino2}}</p>
                        <hr/>
                    </script>
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="full-steps"></div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="infoDelivery" style="display: none;">
                            <div class="infoMotorizado">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px; margin-bottom:15px;">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <img src="" class="imgMotorizado" style="width: 90px; height: 90px; border-radius: 50%;"/>
                                        <span class="nameMotorizado"></span>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <a class="btn bg-color btn-primary telefonoMotorizado" href="" style="border: 0; border-radius: 15px;margin-top: 25px;"><i class="fa fa-phone" aria-hidden="true"></i> Llamar a <span class="nameMotorizado"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="map" id="mapaTracker" style="width: 100%; height: 500px;"></div>
                        </div>
                        <div class="buttonPrintGuia">
                            <button class="btn btn-primary printGuia" style="display:none;"><i data-feather="printer"></i> Imprimir</button>
                        </div>
                        <div class="infoPickup" style="display:none; text-align: center;">
                            <img src="assets/img/pickupcar.png" style="max-width: 300px;" />
                            <h3>El cliente debe retirar su pedido</h3>
                            <p>en el local <span class="local_retiro"></span> el d&iacute;a <span class="fecha_retiro"></span></p>
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
    <script  src="plugins/timeline/script.js"></script>
<script type="text/javascript">
Handlebars.registerHelper('eq', function(arg1, arg2, options) {
    return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
});

var map;
var markerOrden = null, markerSucursal = null, markerMoto = null;
var pos = {lat: -2.1693477, lng: -79.8985397};

$("document").ready(function(){
    loadMap();
    tracking();
});

function loadMap(){
    var mapa = document.getElementById('mapaTracker');
    //INICIALIZAR MAPA
    map = new google.maps.Map(mapa, {
    zoom: 15,
    center: pos
    });

    /*INFO MARKER DESTINO*/
    if(markerOrden === null){
        markerOrden = new google.maps.Marker({
        map: map,
        icon: 'assets/img/marker-order.png'
        });
    }

    /*MARKER SUCURSAL*/
    if(markerSucursal === null){
        markerSucursal = new google.maps.Marker({
        map: map,
        icon: 'assets/img/marker-sucursal.png'
        });
    }

    /*MARKER MOTO*/
    if(markerMoto === null){
        markerMoto = new google.maps.Marker({
        map: map,
        icon: 'assets/img/marker-moto.png'
        });
    }
    
    
    setInterval(function () {
        tracking();
    }, 10000);
}

function tracking(){
    var id = $("#idOrden").val();
    if(id == "" || id == null){
        window.location.href = './mis_ordenes.php';
    }
    fetch(`https://api.mie-commerce.com/v10/tracking/${id}`,{
            method: 'GET',
            headers: {
              'Api-Key':$("#apikey").val()
            },
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                var data = response.data;

                if(data.is_envio == 1){
                    if(data.guia !== undefined){
                        var template = Handlebars.compile($("#infoGuia").html());
                        $(".infoDelivery").html(template(data.guia));
                        $(".printGuia").show();
                    }

                    $(".badge-pick").html('Envío a domicilio');
                    $(".tipo-orden").html('Delivery');
                    $(".infoDelivery").show();
                }
                else{
                    $(".badge-pick").html('Recoje tu pedido');
                    $(".tipo-orden").html('Retirar en Local');
                    $(".local_retiro").html(data.sucursal);
                    $(".fecha_retiro").html(data.fecha_text_retiro+" a las "+data.hora_retiro);
                    $(".infoPickup").show();
                }

                
                /*ACTUALIZAR PROGRESS BAR*/
                var template = Handlebars.compile($("#steps").html());
                $(".full-steps").html(template(data));
                initializeTimeline();
                
                /*MARKERS SUCURSAL Y ORDEN*/
                var latlng = new google.maps.LatLng(data.latitud, data.longitud);
                markerOrden.setPosition(latlng);
                var latlng = new google.maps.LatLng(data.latitud_sucursal, data.longitud_sucursal);
                markerSucursal.setPosition(latlng);
                
                var track = data.tracking;
                if(track !== null){
                    $(".infoMotorizado").show();
                    $(".imgMotorizado").attr('src', track.imagen);
                    $(".nameMotorizado").html(track.nombre + " " + track.apellido);
                    $(".telefonoMotorizado").attr("href","tel:"+track.telefono);
                    
                    /*ACTUALIZAR MARKER MOTORIZADO*/
                    if( (track.latitud != null && track.latitud != "") && (track.longitud != null && track.longitud != "")  ){
                        var latlng = new google.maps.LatLng(track.latitud, track.longitud);
                        markerMoto.setPosition(latlng);

                    }else
                        markerMoto.setMap(null);
                }else{
                    $(".infoMotorizado").hide();
                }
            }
        })
        .catch(error=>{
            console.log(error);
        });
}

$(".printGuia").on("click", function(){
        $(".infoDelivery").print({
            //Use Global styles
            globalStyles : true,
            //Add link with attrbute media=print
            mediaPrint : true,
            //Custom stylesheet
            stylesheet : "assets/css/printCustom.css",
            //Print in a hidden iframe
            iframe : false,
            //Don't print this
            noPrintSelector : ".modal-footer",
            //Add this at top
            prepend : "",
            //Add this on bottom
            append : "",
            //Etiqueta HTML5
            doctype: '<!doctype html>',
            //Log to console when printing is done via a deffered callback
            deferred: $.Deferred().done(function() { console.log('Printing done', arguments); })
        }); 
});

</script>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <!--<script src="assets/js/pages/orden_tracking.js" type="text/javascript"></script>-->
    <script src="assets/js/rating.js" type="text/javascript"></script>
    <script src="assets/js/libs/jQuery.print.js" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->


</body>
</html>