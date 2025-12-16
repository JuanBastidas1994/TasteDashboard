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

    if(order.estado == "ASIGNADA" || order.estado == "ENVIANDO"){
        console.log("LLAMANDO A TRACKING", order);
        //EMPEZAR TRACKING
        tracking(order);
        intervalTrackingId = setInterval(function () {
            tracking(order);
        }, 8000);
    }
}

function tracking(order){
    fetch(`https://api.mie-commerce.com/v8/tracking/${order.id}`,{
            method: 'GET',
            headers: {
            'Api-Key':ApiKey
            }
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                let orden = response.data;
                console.log("TRACKINGGGGGG", orden);
                if(orden.is_envio == 1){
                    if(orden.guia === undefined){
                        var track = orden.tracking;
                        console.log("TRACKINGGGGGG", track);
                        if(track !== null){
                            /*ACTUALIZAR MARKER MOTORIZADO*/
                            if( (track.latitud != null && track.latitud != "") && (track.longitud != null && track.longitud != "")  ){
                                
                                var latlng = new google.maps.LatLng(track.latitud, track.longitud);
                                console.log("DEBE DIBUJAR MARKER", latlng);
                                markerMoto.setPosition(latlng);
                                mapTrack.setCenter(latlng);
                            }else
                                markerMoto.setMap(null);
                        }
                    }
                }
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            console.log(error);
        });
}

$('#trackingModal').on('hidden.bs.modal', function (e) {
    // do something...
    console.log("SE CERRO MODAL TRACKING, DEBE DETENER EL TIMEOUT");
    clearInterval(intervalTrackingId);
})