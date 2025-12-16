let ordenesPorCerrar = null;
let ToastFacturas = null;
let isInitToast = false;
let proxFactura = 0;
let $toastFactObj = $("#facturasToast");

$(function() {
});

function getCierreDiario(){
    fetch(`${ApiUrl}/ordenes/cierre-diario/${sucursal_id}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        $("#cierreDiarioModal").modal();
        console.log("Cierre Diario",response);
        let target = $("#cierre-diario");
        if(response.success == 1){
            let template = Handlebars.compile($("#cierre-diario-template").html());
            target.html(template(response));
            feather.replace();
            ordenesPorCerrar = response.ordenes;
        }
        else{
            target.html("<p>No se pudo obtener la información, por favor intentalo nuevamente</p>");
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

$("body").on("click", ".btnReenviarFacturasMasivamente", function(){
    let num_ordenes = ordenesPorCerrar.length;
    
    if(num_ordenes > 0){
        $("#cierreDiarioModal").modal('hide');
        isInitToast = false;
        proxFactura = 0;
        reenviar_facturar_inventario();
    }else{
        messageDone('No tienes ordenes pendientes por facturar','error');
    }
    
});

function showProgressToast(num_factura, total_facturas){
    if(!isInitToast){
        $toastFactObj.toast('show');
        isInitToast = true;
    }
    
    let percent = (num_factura / total_facturas) * 100;
    $toastFactObj.find('.toast-desc').html(`Enviando ${num_factura}/${total_facturas} Facturas`);
    $toastFactObj.find('.progress-bar').css('width', percent + '%');
}


function reenviar_facturar_inventario() {
    if(ordenesPorCerrar.length > proxFactura){
        let cod_orden = ordenesPorCerrar[proxFactura]['cod_orden'];
        console.log(cod_orden);
        proxFactura = proxFactura + 1;
        showProgressToast(proxFactura, ordenesPorCerrar.length);
        reenviarFactura(cod_orden);
        setInventario(cod_orden, "EGR")
    }else{
        culminoProceso();
    }
}

function culminoProceso(){
    console.log("Culmino el proceso");
    isInitToast = false;
    setTimeout(function(){
        $toastFactObj.find('.progress-bar').css('width', '0%');
        $('#facturasToast').toast('hide');
    }, 1500);
}

function reenviarFactura(cod_orden){
    let info = {
        id: cod_orden
    }
    
    fetch(`${ApiUrl}/facturas/electronica`,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        reenviar_facturar_inventario();
        if(response.success === 1){
            notify(response.mensaje,'success',2);
        }else if(response.success === 0){
            notify(response.mensaje,'error',2);
        }
    })
    .catch(error=>{
        console.log(error);
        reenviar_facturar_inventario();
    });
}

function testToast(){
    $('#autoAsignacionToast').toast('show');
}

$('#facturasToast').on('hidden.bs.toast', function () {
  console.log("Toast cerrado");
});