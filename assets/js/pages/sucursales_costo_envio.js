$(function () {
    getSucursalesCostoEnvio();
});

function getSucursalesCostoEnvio() {
    OpenLoad("Cargando...");

    fetch(`controllers/controlador_sucursal.php?metodo=getCostosEnvio`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            let target = $("#style-3 tbody");
            let template = Handlebars.compile($("#results-template").html());
            target.html(template(response.data));
        }
        else{
            messageDone(response.mensaje, 'error');
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        messageDone('Ocurrió un error', 'error');
        console.log(error);
    });
}

function guardarCostoEnvio() {
    let sucursales = $(".cod_sucursal");
    let data = [];
    sucursales.each(function (index, element) {
        let obj = {
            cod_sucursal: element.value,
            cod_sucursal_costo_envio: document.getElementsByClassName('cod_sucursal_costo_envio')[index].value,
            base_dinero: document.getElementsByClassName('base_dinero')[index].value,
            base_km: document.getElementsByClassName('base_km')[index].value,
            adicional_km: document.getElementsByClassName('adicional_km')[index].value,
        }
        data.push(obj);      
    });
    
    
    info = {
        costos: data
    };
    
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_sucursal.php?metodo=saveCostosEnvio`,{
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        console.log(response);
        if(response.success == 1) {
            getSucursalesCostoEnvio();
            messageDone(response.mensaje, 'success');
        }
        else{
            messageDone(response.mensaje, 'error');
        }
    })
    .catch(error=>{
        CloseLoad();
        messageDone('Ocurrió un error', 'error');
        console.log(error);
    });
}