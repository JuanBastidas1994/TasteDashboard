var mapMotorizados = null;
//var markerMoto = null;
var markerMotos = [];
var intervalMotosId = 0;

$(function() {
});

function openMisMotorizados(order_id){
    $("#MisMotorizadosModal").modal();
    OpenLoad("Buscando motorizados");

    getMotorizadosByOrden(order_id)
        .then(data=>{
            CloseLoad();
            console.log("MOTORIZADOS", data);

            var template = Handlebars.compile($("#order-mis-motorizados-template").html());
            $("#orden-mis-motorizados").html(template(data));
            feather.replace();

            //INICIALIZAR MAPA
            let order = data.orden;
            var mapa = document.getElementById('mapaMisMotorizados');
            mapMotorizados = new google.maps.Map(mapa, {
                zoom: 14,
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
                map: mapMotorizados,
                id: 25
            });

            //MARKER CLIENT
            new google.maps.Marker({
                position: {
                    lat: parseFloat(order.latitud),
                    lng: parseFloat(order.longitud),
                },
                map: mapMotorizados,
                id: 30
            });

            //MARKER MOTOS
            drawMotos(data.motorizados, order_id);

            //TIMER
            intervalMotosId = setInterval(function () {
                reloadMotorizadosOrder(order_id);
            }, 8000);
        })
        .catch(error=>{
            CloseLoad();
            messageDone(error,'error');
        });
}

function drawMotos(motorizados, order_id){
    motorizados.forEach(moto => {
        //markerMotos
        let marker = new google.maps.Marker({
            map: mapMotorizados,
            icon: 'assets/img/marker-moto.png',
            id: moto.id,
            label: moto.nombres,
            position: {
                lat: parseFloat(moto.latitud),
                lng: parseFloat(moto.longitud)
            },
            info_aditional: moto,
            order_id: order_id
        });
        marker.addListener('click', function() {
            let moto = this.info_aditional;
            let order_id = this.order_id;
            console.log(moto);
            //alert(moto.nombres);
            setMotorizadoOrder(order_id, moto.id);
        });
        markerMotos.push(marker);
    });
}

function reloadMotorizadosOrder(order_id){
    getMotorizadosByOrden(order_id)
    .then(data=>{
        markerMotos.forEach((marker, key) => {
            markerMotos[key].setMap(null);
        });
        markerMotos = [];

        //MARKER MOTOS
        drawMotos(data.motorizados, order_id);
    })
    .catch(error=>{
        CloseLoad();
        messageDone(error,'error');
    });
}

$('#MisMotorizadosModal').on('hidden.bs.modal', function (e) {
    clearInterval(intervalMotosId);
});

function getMotorizadosByOrden(order_id){
    var promesa = new Promise(function(resolve, reject){
        fetch(`${ApiUrl}/ordenes/motorizados/${order_id}`,{
            method: 'GET',
            headers: {
            'Api-Key':ApiKey
            }
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                resolve(response);
            }else{
                reject(response.mensaje); 
            }
        })
        .catch(error=>{
            reject('Ocurrió un error al obtener información de la configuracion');
        });
    });
    return promesa;
}

function setMotorizadoOrder(order_id, motorizado_id){
    messageConfirm("¿Estás seguro de asignar la orden?", "", "question")
        .then(function(result) {
            if (result) {
                asignarOrden(order_id, 99, motorizado_id);
            }
        });
}