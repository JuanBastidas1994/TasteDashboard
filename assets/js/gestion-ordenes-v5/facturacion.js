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

/* Reenvío manual desde el detalle de la orden (cuando la tarjeta muestra "Sin comanda Runfood"). */
$("body").on("click", ".btnReenviarComanda", function(){
    let $btn = $(this);
    let cod_orden = $btn.data("id");
    $btn.prop("disabled", true);
    fetch(`${ApiUrl}/facturacion/comanda`,{
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .then(response => {
        if(response.success === 1){
            notify(response.mensaje,'success',2);
        }else{
            notify(response.mensaje,'error',5);
        }
        // Refresca el detalle y la lista para que el indicador muestre el estado real
        openOrden(cod_orden);
        getListaOrdenes();
    })
    .catch(error=>{
        $btn.prop("disabled", false);
        console.log(error);
    });
});

/* Red de seguridad: cada pocos minutos reenvía las comandas que quedaron sin crear en Runfood
   (red caída, Runfood sin responder...). Lo que siga fallando queda marcado en la tarjeta. */
const MINUTOS_REINTENTO_COMANDAS = 5;
let intervaloComandas = null;

function iniciarReintentoComandas(){
    if(intervaloComandas) return;
    let config = getConfigGestionOrdenes();
    let sistema = (config.facturacion && config.facturacion[0]) ? config.facturacion[0].cod_sistema_facturacion : null;
    if(sistema != 3) return;   // solo Runfood maneja comandas

    setTimeout(reintentarComandasPendientes, 30 * 1000);
    intervaloComandas = setInterval(reintentarComandasPendientes, MINUTOS_REINTENTO_COMANDAS * 60 * 1000);
}

function reintentarComandasPendientes(){
    fetch(`${ApiUrl}/facturacion/reintentar-comandas`,{
        method: 'POST',
        headers: { 'Api-Key': ApiKey },
        body: JSON.stringify({ sucursal_id: sucursal_id })
    })
    .then(res => res.json())
    .then(response => {
        if(response.success !== 1) return;
        if(response.enviadas.length > 0){
            notify(`Runfood: ${response.enviadas.length} comanda(s) reenviada(s)`,'success',3);
            getListaOrdenes();
        }
    })
    .catch(error=>{
        console.log("Error reintentando comandas", error);
    });
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
