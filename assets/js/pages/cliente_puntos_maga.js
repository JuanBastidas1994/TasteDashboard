let cod_usuario = 0;

$(function() {
    var urlParams = new URLSearchParams(window.location.search); //get all parameters
    var cod_usuario = urlParams.get('id'); 
    if(!cod_usuario){
        window.location.href = "./";
        return;
    }

    getClient(cod_usuario);
});

$("body").on("change", "#cmbTipo", function(){
    let value = $(this).val();
    if(value == "puntos") {
        $("#table-expired-points").removeClass("d-none");
        $("#table-expired-money").addClass("d-none");
        
    }
    else {
        $("#table-expired-points").addClass("d-none");
        $("#table-expired-money").removeClass("d-none");
    }
});

function getClient(cod_usuario) {
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_clientes.php?metodo=fidelizacionMaga&cod_usuario=${cod_usuario}`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        console.log(response);
        if(response.success == 1){
            let client = response.data.cliente;
            $("#historic-total-orders").html("$" + client.total_ordenes);
            $("#historic-points").html(client.puntos);
            $("#historic-used-credit").html("$" + client.total_credito_utilizado);
            
            $("#current-level").html(client.nivel.nombre);
            $("#current-points").html(client.puntos_actuales);
            $("#current-money").html("$" + parseFloat(client.dinero_actual).toFixed(2));

            var target = $("#table-current-credit");
            var template = Handlebars.compile($("#current-credit-template").html());
            target.html(template(response.data.puntos_activos));
            
            var target = $("#table-expired-points");
            var template = Handlebars.compile($("#expired-points-template").html());
            target.html(template(response.data.puntos_caducados));
            
            var target = $("#table-expired-money");
            var template = Handlebars.compile($("#expired-money-template").html());
            target.html(template(response.data.dinero_caducado));

            $("#historic-expired-money").html("$" + client.total_dinero_caducado);
            $("#historic-expired-points").html(client.total_puntos_caducados);

            feather.replace();
            $("#cmbTipo").trigger("change");

            $('.popover').popover();
        }
        else{

        }
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}