let ApiUrl = "https://api.mie-commerce.com/taste/v2";
let ApiKey = "";

$(function() {
    ApiKey = $("#apikey_empresa").val();
    getOrdenes();
});

function getOrdenes(){
    let info = {
        "business_id": $('#selectComercio').val() ?? "",
        "status": $('#selectEstado').val() ?? "",
    };
    fetch(`${ApiUrl}/gestion-flotas/`,{
        method: 'POST',
        headers: { 'Api-Key':ApiKey, },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            var template = Handlebars.compile($("#order-list-template").html());
            $("#order-list").html(template(response.data));
            feather.replace();
        }else{
             $("#order-list").html('No hay datos.');
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function getOrden(order_id){
    $("#orderDetailTitle").html(`Orden Detalle #${order_id}`);
    
    fetch(`${ApiUrl}/gestion-flotas/orden/${order_id}`,{
        method: 'GET',
        headers: { 'Api-Key':ApiKey, },
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            var template = Handlebars.compile($("#order-detail-template").html());
            $("#order-detail").html(template(response.data));
            feather.replace();
            
            initMap(response.data);
            $("#orderDetailModal").modal();
        }else{
            //  $("#order-list").html('No hay datos.');
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function asignarOrden(order_id, moto_id){
    fetch(`${ApiUrl}/gestion-flotas/asignar`,{
        method: 'POST',
        headers: { 'Api-Key':ApiKey, },
        body: JSON.stringify({
            order_id,
            moto_id
        })
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            getOrdenes();
            $("#orderDetailModal").modal('hide');
            // Abrir WhatsApp con el número y un mensaje
            let link = `https://pedidos.demo.mie-commerce.com/pedidos/?id=${response.token}`;
            let telefono = response.motorizado.telefono;
            let url = `https://api.whatsapp.com/send?phone=${telefono}&text=${link}`;
            window.open(url, '_blank');
        }else{
             alert(response.mensaje);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function removeAsignacion(order_id){
    fetch(`${ApiUrl}/gestion-flotas/deleteAsignacion`,{
        method: 'POST',
        headers: { 'Api-Key':ApiKey, },
        body: JSON.stringify({
            order_id
        })
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            getOrdenes();
            $("#orderDetailModal").modal('hide');
        }else{
             alert(response.mensaje);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function initMap(order){
    if($("#mapa").length){
        var mapa = document.getElementById("mapa");
        var latitud = parseFloat(mapa.getAttribute("data-latitud"));
        var longitud = parseFloat(mapa.getAttribute("data-longitud"));
        pos = {lat: latitud, lng: longitud};
        var map = new google.maps.Map(mapa, {
            zoom: 14,
            center: pos
        });

        //MARKER CLIENT
        marker = new google.maps.Marker({
            position: pos,
            map: map,
            id: 15
        });

        //MARKET SUCURSAL
        marker2 = new google.maps.Marker({
            position: {
                lat: parseFloat(order.sucursal.latitud ?? -2.17),
                lng: parseFloat(order.sucursal.longitud ?? -79.15),
            },
            icon: 'assets/img/marker-office.png',
            map: map,
            id: 16
        });
    }
}

$('#selectComercio, #selectEstado').on('change', function() {
    getOrdenes();
});

$("body").on('click', ".ordenItem",function(){
   getOrden($(this).data('value'));
});

$("body").on('click', "#btnAsignar",function(){
    let motoId = $("#motoId").val();
    if(motoId <= 0){
        alert('Debes escoger una moto');
        return;
    }
    
    let orderId = $("#orderId").val();
    asignarOrden(orderId, motoId);
   
});

$("body").on('click', "#btnDeleteAsignar",function(){
    Swal.fire({
       title: 'Desea remover al motorizado?',
       text: '¿Desea continuar?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
            let orderId = $("#orderId").val();
            removeAsignacion(orderId);
       }
    });
    
    
   
});