$(function() {
});

function getOrdenesProgramadas(){
    let fecha = "2023-09-26";

    OpenLoad("Buscando datos...");
    fetch(`${ApiUrl}/ordenes/programadas/${sucursal_id}/${fecha}`,{
        method: 'GET',
        headers: {
            'Api-Key': ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        console.log(response);
        if(response.success == 1){
        }
        else{
        }
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}