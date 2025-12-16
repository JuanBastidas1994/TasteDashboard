let ApiUrl = "https://api.mie-commerce.com/taste/v2";
let sucursal_id = 50;
let ApiKey = "";

let clusterer = null;
let markers = [];

var templateOrdenesParaAsignar = Handlebars.compile($("#ordenesparaasignar-template").html());
        
$(function() {
    ApiKey = $("#apikey_empresa").val();
    getListaOrdenes();
});

function getListaOrdenes(){
    let rango = $('.rbHoras:checked').val();
    clearMarkersAndClusters();
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
                let { coordenadas } = response;
                coordenadas.map((coordenada) => {
                    const { id, lat, lng, cliente } = coordenada;
                    if (!lat || !lng) return;
                    const m = addMarker(lat, lng, `#${id}`, id, coordenada)
                    markers.push(m);
                });

                console.log("Markers creados:", markers.length);
                if (markers.length) {

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
                             console.log("Órdenes dentro del cluster:", orderIds);
                             preAsignarOrdenes(orderIds, ordenesEntrante);
                         },
                     });
                     console.log("CLUSTERER creado:", !!clusterer);
                }
                
            }else{
                
            }
        })
        .catch(error=>{
            CloseLoad();
        });


}

function preAsignarOrdenes(orderIds, ordenesEntrante){
    window.selectedOrders = orderIds;

    console.log(ordenesEntrante);
    $("#orderIdAsignar").val(JSON.stringify(orderIds));
    $("#ordenesparaasignar").html(templateOrdenesParaAsignar(ordenesEntrante));

    $("#asignarModal").modal();

    clearMarkersAndClusters();
}


function clearMarkersAndClusters() {
  try {
    console.log('CLEAR CLUSTERS AND MARKERS');
    console.log(clusterer, markers);
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
        console.log(m);
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
  getListaOrdenes();
});


