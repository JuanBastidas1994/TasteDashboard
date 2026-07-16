let isFacturacion = false;
let await_status = "";
$(function(){
    getInfoFacturacion();
});

function getInfoFacturacion(){
    let config = getConfigGestionOrdenes();
    let facturacionConfig = config.facturacion;
    if(facturacionConfig !== undefined && facturacionConfig !== null){
        if(facturacionConfig.length > 0){
            isFacturacion = true;
            await_status = facturacionConfig[0].await_status;
        }
    }
}

function facturar_inventario(cod_orden, estado){
    getInfoFacturacion();
    if(isFacturacion){

        if(await_status == estado){
            facturarUnificada(cod_orden, true);
        }else{
            console.log("AUN NO ES HORA DE FACTURAR");
            notify("AUN NO ES HORA DE FACTURAR",'warning',2);
        }
    }
}

function anular_facturar_inventario(cod_orden) {
    getInfoFacturacion();
    console.log('ANULAR FACTURACION E INVENTARIO');
    console.log(await_status);
    if(isFacturacion){

        if(await_status == "ASIGNADA"){
            facturarUnificada(cod_orden, false);
        }
    }
}

/*ENDPOINT UNICO: el api de gestion de ordenes resuelve internamente si es Contifico o Runfood*/
function facturarUnificada(cod_orden, crear){
    let ruta = `${ApiUrl}/facturacion/anular`;
    if(crear)
        ruta = `${ApiUrl}/facturacion/electronica`;

    fetch(ruta,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success === 1){
            notify(response.mensaje,'success',2);
        }
        else if(response.success === 0){
            notify(response.mensaje,'error',5);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

/*CONTIFICO — usado únicamente por el cierre diario (reenvío masivo), no por el flujo de asignación/entrega*/
function facturaElectronica(cod_orden, crear){
    let ruta = `${ApiUrl}/facturas/anular`;
    if(crear)
        ruta = `${ApiUrl}/facturas/electronica`;
    
    let info = {
        id: cod_orden
    }
    
    fetch(ruta,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success === 1){
            notify(response.mensaje,'success',2);
        }
        else if(response.success === 0){
            notify(response.mensaje,'error',5);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}


function setInventario(cod_orden, tipo) {
    fetch(`${ApiUrl}/contifico/inventario/${tipo}`,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({cod_orden})
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}
