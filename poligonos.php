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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="bootstrap/css/custom-sidebar.css" rel="stylesheet" type="text/css" />
    <style>
        .alert-box {
            display: none;
            z-index: 9999;
            height: 105px;
        }
        .sticky {
            cursor: pointer;
            position: sticky;
            bottom: 0px;
            background-color: #0e1726;
            color: white !important;
            padding: 8px;
            font-size: 15px;
            text-align: right;
            font-weight: 900;
        }
        #map{
            width: 100%; 
            height: calc(100vh - 50px);
        }
        #bloqueZoom{
            background: #f1f2f3;
        }
    </style>
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
            <div id="bloqueZoom">
                
                <!-- Area de notificación -->
                <div class="fixed-top bg-info alert-box alert-box-poligono">
                    <div class="d-flex align-items-center justify-content-center" style="height: 100px;">
                        <div>&nbsp;</div>
                        <div class="desc" style="font-size: 2vw;">
                            De click en un área del mapa para crear un polígono
                        </div>
                        <div onclick="disableNewPolygon()" class="text-center"
                            style="position: absolute; right: 0; font-size: 3vw; width: 50px; cursor: pointer;">
                            X
                        </div>
                    </div>
                </div>
                
                <!-- Modal Pölygono Detalle-->
                <div class="modal right fade" id="polygonDetailModal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="padding:0px; margin:0px;">
                        <i data-feather="x"></i>
                    </button>
                    <h5 class="modal-title text-center" style="width: 100%;">Polígono Detalle</h5>
                </div>
                <div class="modal-body" style="padding: 0px 15px;">
                    <div class="x_content" style="height: 100%;">
                        <h5 class="mb-3 mt-3 title"><b>Entre ríos</b></h5>
                        <div>
                            <div>Vértices</div>
                            <ul class="vertices"></ul>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-danger" onclick="onDeletePolygon()"><i data-feather="trash"></i> Eliminar Polígono</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button class="btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
                
                
                
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-sm-12">
                        <div class="">
                            
                            <div class="">
                                <!-- Mapa -->
                                <div class="col-xl-9 col-lg-8 col-md-8 col-8">
                                    <div id="map" style=""></div>
                                </div>

                                <!-- Sucursales -->
                                <div class="col-xl-3 col-lg-4 col-md-4 col-4">
                                    <div id="lstSucursales" class="mt-3"></div>

                                    <script id="sucursales-item" type="text/x-handlebars-template">
                                        {{#each this}}
                                        <div class="widget-content widget-content-area br-6 mb-2 p-2">
                                            <div class="d-flex align-items-center">
                                                <div style="background: {{color}}; width: 10px; height: 10px;"></div>
                                                <div class="ml-2" style="cursor: pointer;"
                                                    onclick="moveToOfficePosition({{cod_sucursal}})">{{nombre}}</div>
                                                <div class="ml-auto mr-3"> {{vertices_count}} </div>
                                                <div>
                                                    <button class="btn btn-primary" style="background: {{color}} !important;"
                                                        onclick="activateNewPolygon({{cod_sucursal}})">+</button>    
                                                </div>
                                            </div>
                                        </div>
                                        {{/each}}
                                    </script>
                                   
                                </div>
                              
                            </div>
                            
                        </div>
                    </div>

                </div>
                
                <!-- Sticky -->
                <div class="sticky">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary" id="btnZoom"
                                                onclick="changeModeZoom()"><i data-feather="maximize"></i> Maximixar</button>
                        <div class="ml-auto mr-3">
                            <input type="checkbox" onchange="activateDrag(this)" /> Permitir arrastrar
                        </div>
                        <button class="btn btn-secondary" 
                                                onclick="savePolygons()"><i data-feather="save"></i> Guardar cambios</button>
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
    <script src="./assets/js/poligonos/index.js"></script>
    <script src="./assets/js/poligonos/poligonos.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- Mapas -->
     <script type="text/javascript">
        var marker;
        /*TEMPLATES EN EL MISMO ARCHIVO*/
        var templateSucursales = Handlebars.compile($("#sucursales-item").html());
        
    let Offices = null;
    function getSucursales(){
	    $.ajax({
	        url:'./controllers/controlador_sucursal.php?metodo=getPolygons',
	        type: "GET",
	        success: function(response){
	            if(response['success']==1){
	                var sucursales = response['data'];
                	if(sucursales.length > 0){
                        Offices = sucursales
                        sucursales.map((sucursal) => {
                            console.log(sucursal);
                            //let color = randomColor();
                            //Recorrer los poligonos de las sucursales y crearlos
                            let polygons = sucursal.vertices;
                            polygons.map((polygon) => {
                                let coordenadas = [];
                                polygon.map((vertice) => {
                                    coordenadas.push({ 
                                        lat: vertice[0], 
                                        lng: vertice[1]
                                    });
                                });
                                addPolygon(coordenadas, sucursal);                                
                            });
                            addMarker(sucursal.latitud, sucursal.longitud, sucursal.nombre, sucursal.cod_sucursal);
                        });
                        $("#lstSucursales").html(templateSucursales(sucursales));
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

    function savePolygons(){
        let info = {
            poligonos: getCoordenadas()
        }
	    $.ajax({
	        url:'./controllers/controlador_sucursal.php?metodo=savePolygons',
	        type: "POST",
            data: info,
	        success: function(response){
	            if(response['success']==1){
	                notify("Polígonos actualizados correctamente",'success',2);
	            }else{
                    notify("No se pudo actualizar los polígonos",'error',2);
                }
	        },
	        error: function(data){
	            notify("No hay sucursales creadas",'success',2);
	        },
	        complete: function()
	        {
	          
	        }
	    });
	}

    function getOfficeById(id){
        let resp = false;
        Offices.map((office) => {            
            if(parseInt(office.cod_sucursal) === id)
                resp = office;
        });
        return resp;
    }

    function addMarker(lat, lng, office_name, office_id){
        var posMarker = {lat: parseFloat(lat), lng: parseFloat(lng)};
        var image = 'https://dashboard.mie-commerce.com/assets/img/tienda.png';
        marker = new google.maps.Marker({
            position: posMarker,
            map: map,
            icon: image,
            label: office_name,
            id: office_id
        });
    }

    function moveToOfficePosition(office_id){
        let office = getOfficeById(office_id);

        var latlng = new google.maps.LatLng(parseFloat(office.latitud), parseFloat(office.longitud));
        map.setCenter(latlng);
    }
      
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&libraries=places,geometry,drawing"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>