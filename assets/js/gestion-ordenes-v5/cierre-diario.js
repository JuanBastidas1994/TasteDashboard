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
        }
        else{
            target.html("<p>No se pudo obtener la información, por favor intentalo nuevamente</p>");
        }
    })
    .catch(error=>{
        console.log(error);
    });
}
