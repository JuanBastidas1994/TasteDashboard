/* La sucursal factura si está configurada en Contifico o Runfood (config.facturacion viene del api).
   Reglas fijas por sistema, resueltas en el api:
   - Contifico: factura al ENTREGAR.
   - Runfood: abre el pedido (comanda) al aceptar/asignar y factura al ENTREGAR. */
let isFacturacion = false;
$(function(){
    getInfoFacturacion();
});

function getInfoFacturacion(){
    let config = getConfigGestionOrdenes();
    let facturacionConfig = config.facturacion;
    isFacturacion = Array.isArray(facturacionConfig) && facturacionConfig.length > 0;
}

function facturar_inventario(cod_orden, estado){
    getInfoFacturacion();
    if(isFacturacion && estado == "ENTREGADA"){
        facturarUnificada(cod_orden, true);
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
    .then(apiJson)
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

/*COMANDA: para sistemas que además son POS/cocina (Runfood) la orden se abre allá apenas sale de
  ENTRANTE (imprime en cocina) y se factura al entregar. Para Contifico el api responde -1 y no hace nada.*/
function enviarComanda(cod_orden){
    getInfoFacturacion();
    if(!isFacturacion) return;
    llamarComanda(`${ApiUrl}/facturacion/comanda`, cod_orden);
}

function anularComanda(cod_orden){
    getInfoFacturacion();
    if(!isFacturacion) return;
    llamarComanda(`${ApiUrl}/facturacion/anular-comanda`, cod_orden);
}

function llamarComanda(ruta, cod_orden){
    fetch(ruta,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(apiJson)
    .then(response => {
        console.log(response);
        if(response.success === 1 && !response.skipped){
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
    .then(apiJson)
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
    .then(apiJson)
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
