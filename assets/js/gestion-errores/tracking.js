var mapTrack = null;
var markerMoto = null;
var intervalTrackingId = 0;

$(function() {
});

function getTrackingByOrden(order_id, e){
    e.stopPropagation();
    $("#trackingModal").modal();
    getOrden(order_id)
        .then(order => {
            var template = Handlebars.compile($("#order-tracking-mapa-template").html());
            $("#orden-tracking").html(template(order));
            
            if(order.is_envio == 1){
                if(order.guia !== undefined){   //GUIA
                    
                }else{  //MAPA
                    loadMapTracking(order);
                }
            }
            feather.replace();
            console.log(order);
        })
        .catch(error=>{
            CloseLoad();
            messageDone(error,'error');
        });
}

function loadMapTracking(order){
    //INICIALIZAR MAPA
    var mapa = document.getElementById('mapaTracker');
    mapTrack = new google.maps.Map(mapa, {
        zoom: 15,
        center: {
            lat: parseFloat(order.sucursal.latitud),
            lng: parseFloat(order.sucursal.longitud)
        }
    });

    //MARKET SUCURSAL
    new google.maps.Marker({
        position: {
            lat: parseFloat(order.sucursal.latitud),
            lng: parseFloat(order.sucursal.longitud),
        },
        icon: 'assets/img/marker-office.png',
        map: mapTrack,
        id: 25
    });

    //MARKER CLIENT
    new google.maps.Marker({
        position: {
            lat: parseFloat(order.latitud),
            lng: parseFloat(order.longitud),
        },
        map: mapTrack,
        id: 30
    });
    
    markerMoto = new google.maps.Marker({
        map: mapTrack,
        icon: 'assets/img/marker-moto.png'
        });

    // Solo lectura y sin consultar otros APIs: se muestra la ruta (local -> cliente) y el timeline.
    // El seguimiento en vivo del motorizado depende de la Api-Key de la empresa dueña de la orden.
}

$('#trackingModal').on('hidden.bs.modal', function () {
    clearInterval(intervalTrackingId);
})