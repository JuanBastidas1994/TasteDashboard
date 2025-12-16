// let sucursal_id = 50;
let map;
let clusterer = null;
let markers = [];

var templateOrdenesParaAsignar = Handlebars.compile($("#ordenesparaasignar-template").html());
var templateItemOrdenListMap = Handlebars.compile($("#ordenesmaplist-item").html());

$(function() {
    initMap();
    getListaOrdenesMap();
});

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: { lat: -2.1753280, lng: -79.90624 },
        mapTypeControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        //disableDefaultUI: true,
        gestureHandling: "greedy", //Permite hacer zoom con el scroll
    });
}

function getListaOrdenesMap(){
    let rango = $('.rbHoras:checked').val();
    clearMarkersAndClusters();
    $("#ordenesMapList").html("");
    let info = {
        "cod_sucursal": sucursal_id,
        "estado": "ENTRANTE",
        "tipo": 'delivery',
        "rango": rango
    };
    OpenLoad("Buscando ordenes, por favor espere...");
    fetch(`${ApiUrl}/ordenes/lista-mapa`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            console.log(response);
            if(response.success == 1){
                let { coordenadas, data } = response;
                coordenadas.map((coordenada) => {
                    const { id, lat, lng, cliente } = coordenada;
                    if (!lat || !lng) return;
                    const m = addMarker(lat, lng, `#${id}`, id, coordenada)
                    markers.push(m);
                });

                console.log("Markers creados:", markers.length);
                if (markers.length) {
                    // const defaultRenderer = new markerClusterer.DefaultRenderer();
                    clusterer = new markerClusterer.MarkerClusterer({ 
                        map, 
                        markers,
                        gridSize: 80,
                        algorithmOptions: { maxZoom: 18 },
                        zoomOnClick: false,
                        onClusterClick: (event, cluster, map) => {
                             const markersInCluster = cluster.markers;
                             console.log(markersInCluster);
     
                             const ordenesEntrante = markersInCluster.map(m => m.orderData).filter(od => od.estado === "ENTRANTE");
                             const orderIds = ordenesEntrante.map(o => o.id);
                             if (orderIds.length === 0) {
                                const bounds = cluster.bounds;
                                if (bounds) {
                                    map.fitBounds(bounds);
                                }
                             }else{
                                 preAsignarOrdenesMapa(orderIds, ordenesEntrante);
                             }
                        },
                        renderer: {
                            render({ count, markers, position }) {
                                let color = "#00C853"; // 🟢 Verde por defecto
                                let hasExpress = false;
                                let hasEntrante = false;
                                let hasAsignada = false;
                                for (const marker of markers) {
                                    const data = marker.orderData;

                                    if (data?.is_express && data?.estado === "ENTRANTE") {
                                        hasExpress = true;
                                        break; // Prioridad máxima
                                    }
                                    if (data?.estado === "ENTRANTE") hasEntrante = true;
                                    else if (data?.estado === "ASIGNADA") hasAsignada = true;
                                }

                                if (hasExpress) {
                                    color = "#D50000"; // 🔴 Rojo
                                } else if (hasEntrante) {
                                    color = "#2962FF"; // 🔵 Azul
                                } else if (hasAsignada) {
                                    color = "#FFD600"; // 🟡 Amarillo
                                }

                                return new google.maps.Marker({
                                    position,
                                    icon: {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        scale: 30,
                                        fillColor: color,  // tu color aquí
                                        fillOpacity: 0.7,
                                        strokeWeight: 2,
                                        labelOrigin: new google.maps.Point(0, 0),
                                    },
                                        label: {
                                        text: String(count),
                                        color: '#000000',
                                        fontSize: '14px',
                                        fontWeight: 'bold',
                                    },
                                    zIndex: google.maps.Marker.MAX_ZINDEX + count,
                                });
                            }
                        }

                    });
                }

                $("#ordenesMapList").html(templateItemOrdenListMap({
                    orders: data,
                }));
                
            }else{
                $("#ordenesMapList").html('<div class="text-center"><b>No hay Ordenes</b></div>');
            }
        })
        .catch(error=>{
            CloseLoad();
        });


}

function addMarker(lat, lng, name, id, data){
    var image = data.icon;
    const marker = new google.maps.Marker({
        position: {lat: parseFloat(lat), lng: parseFloat(lng)},
        icon: image,
        label: name,
    });

    marker.orderData = Object.assign({}, data, { id });

    marker.addListener('click', function () {
        let orden = marker.orderData;
        if(orden){
            openOrden(orden.id);
        }
    });
    return marker;
}

function preAsignarOrdenesMapa(orderIds, ordenesEntrante){

    const config = getConfigGestionOrdenes();
    $("#orderIdAsignar").val(JSON.stringify(orderIds));
    $("#ordenesparaasignar").html(templateOrdenesParaAsignar({
        orders: ordenesEntrante,
        motorizados: config.motorizados
    }));

    $("#asignarModal").modal();
}


function clearMarkersAndClusters() {
  try {
    if (clusterer) {
      // clearMarkers quita los markers del cluster internamente
      clusterer.clearMarkers();
      // quitar overlay del mapa
      clusterer.setMap(null);
      clusterer = null;
    }

    // 2) quitar markers individuales del mapa (por si fueron añadidos)
    if (markers && markers.length) {
      markers.forEach(m => {
        // nos aseguramos de que sea un Marker válido antes de quitarlo
        if (m && typeof m.setMap === 'function') {
          m.setMap(null);
        }
      });
    }

    // 3) limpiar arreglo
    markers = [];
  } catch (err) {
    console.error("Error en clearMarkersAndClusters:", err);
  }
}

$('.rbHoras').on('change', function() {
  getListaOrdenesMap();
});

$('body').on('change', '.rbView', function() {
  let view = $('.rbView:checked').val();
  $(".viewItem").hide();
  console.log(view);
  if(view == 'map'){
    $('.viewMap').show();
  }else{
    $('.viewList').show();
  }
});

$('#btnAsignarOrdenesMasivas').on('click', function() {
    let ordersIds = $("#orderIdAsignar").val();
    let motorizado_id = $("#cmbMotorizadoMap").val();
        
    let info = {
        id_orders: JSON.parse(ordersIds),
        motorizado_id: motorizado_id
    }
    console.log(info);

    fetch(`${ApiUrl}/ordenes/asignar-masiva`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log(response);
            if(response.success == 1){
                $("#asignarModal").modal('hide');
                messageDone(response.mensaje,'success');
                notify(response.mensaje, "success", 2);
                // updateOrderFirebase(order_id, 'ASIGNADA');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
});


function changeModeZoom(){
    var elem = document.getElementById("bloqueZoom");
    
    if(document.fullscreenElement === null){ //ENTRAR A MODO ZOOM
        if (elem.requestFullscreen) {
          elem.requestFullscreen();
        }
    }else{                                  //SALIR DE MODO ZOOM
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

document.addEventListener("fullscreenchange", function() {
    let $btnZoom = $("#btnZoom");
    console.log(document.fullscreenElement);
    if(document.fullscreenElement !== null){
        $btnZoom.html(`<i data-feather="minimize"></i>`);
        feather.replace();
    }else{
        $btnZoom.html(`<i data-feather="maximize"></i>`);
        feather.replace();
    }
});