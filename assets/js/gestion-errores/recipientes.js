$(function() {
});

$("body").on("click", ".openRecipientes", function(e){
    e.stopPropagation();
    let data = $(this).data();
    openRecipientes(data.orden);
});

function openRecipientes(order_id){
    try {
        config = getConfigGestionOrdenes();
        let permisos = config.permisos;
        if(permisos.includes("GESTIONAR_RECIPIENTES")){
            getRecipientesByOrden(order_id);
        }
    }
    catch(err) {
        console.log("Error en Open Recipientes", err);
    }
}

$("body").on("change", ".cmbRecipiente", function(e){
    let data = $(this).data();
    setRecipienteToOrden(data.orden, data.recipiente, parseInt($(this).val()))
});

function getRecipientesByOrden(order_id){
    fetch(`${ApiUrl}/ordenes/recipientes/${order_id}`,{
            method: 'GET',
            headers: {
            'Api-Key':ApiKey
            }
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            console.log(response);
            if(response.success == 1){
                $("#recipientesModal").modal();
                var template = Handlebars.compile($("#order-recipientes-template").html());
                $("#orden-recipientes").html(template(response));
                feather.replace();
            }else{
            
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
        });
}


function setRecipienteToOrden(order_id, recipiente_id, cant){
    $("#process-loading-"+recipiente_id).html(`
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
    `);
    
    let info = {
        cod_orden: order_id,
        cod_recipiente: recipiente_id,
        cantidad: cant
    };
    fetch(`${ApiUrl}/ordenes/set-recipiente`,{
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
                iconLoading(recipiente_id, true);
                //notify(response.mensaje, "success", 2);
            }else{
                iconLoading(recipiente_id, false);
                //notify(response.mensaje, "error", 2);
            }
            feather.replace();
        })
        .catch(error=>{
            CloseLoad();
            iconLoading(recipiente_id, false);
            console.log(error);
        });
}

function iconLoading(recipiente_id, success){
    let text_class = "text-danger";
    let feather_icon = "x";
    if(success === true){
        feather_icon = "check";
        text_class = "text-success";
    }
        
    $("#process-loading-"+recipiente_id).html(`<span class="${text_class}" style="line-height: 40px;"><i data-feather="${feather_icon}"></i></span>`);
    setTimeout(() => {
      $("#process-loading-"+recipiente_id).html('');
    }, "2000");
}


$('#recipientesModal').on('hidden.bs.modal', function (e) {
    console.log("SE CERRO MODAL RECIPIENTES");
})