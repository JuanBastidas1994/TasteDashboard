let runfoodApi = "http://localhost:51042/runfood";
let isFacturacion = false;
let await_status = "";
let sistemaFacturacion = "";
$(function(){
    getInfoFacturacion();
});

function getInfoFacturacion(){
    let config = getConfigGestionOrdenes();
    let facturacionConfig = config.facturacion;
    if(facturacionConfig !== undefined && facturacionConfig !== null){
        if(facturacionConfig.length > 0){
            isFacturacion = true;
            facturacion = facturacionConfig[0];
            await_status = facturacion.await_status;
            if(facturacion.cod_sistema_facturacion == 1)
                sistemaFacturacion = "CONTIFICO";
            else if(facturacion.cod_sistema_facturacion == 3)
                sistemaFacturacion = "RUNFOOD";
        }
    }
}

function facturar_inventario(cod_orden, estado){
    getInfoFacturacion();
    if(isFacturacion){
        
        if(await_status == estado){
            
            if(sistemaFacturacion == "CONTIFICO"){
                facturaElectronica(cod_orden, true);
                setInventario(cod_orden, "EGR");
            }
            
            if(sistemaFacturacion == "RUNFOOD"){
                facturarFromRunfood(cod_orden);
            }
        }else{
            console.log("AUN NO ES HORA DE FACTURAR");
            notify("AUN NO ES HORA DE FACTURAR",'warning',2);
        }
    }
}

function anular_facturar_inventario(cod_orden) {
    getInfoFacturacion();
    if(isFacturacion){
        
        if(await_status == "ASIGNADA"){
            
            if(sistemaFacturacion == "CONTIFICO"){
                facturaElectronica(cod_orden, false);
                setInventario(cod_orden, "ING");
            }
            
        }
    }
}

/*CONTIFICO*/
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


/*RUNFOOD*/
function facturarFromRunfood(cod_orden){
    let ruta = `${ApiUrl}/runfood/electronica`;
    fetch(ruta,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({
            id: cod_orden
        })
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success === 1){
            console.log(response.factura);

            sendInvoiceToRunfoodB4j(response.factura)
                .then(data => {
                    console.log(data, cod_orden);
                    setRunfoodId(cod_orden, data); //Asignar orden con id Runfood
                })
                .catch(error=>{
                    messageDone(error,'error');
                });
        }
        else if(response.success === 0){
            notify(response.mensaje,'error',5);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

//SEND INVOICE BY JC SERVER
function sendInvoiceToRunfoodB4j(facturaArray){
    var promesa = new Promise(function(resolve, reject){
        fetch(`${runfoodApi}/invoice`,{
            method: 'POST',
            body: JSON.stringify(facturaArray)
        })
        .then(res => res.json())
        .then(response => {
            if(response.success == 1){
                console.log("B4J", response);
                resolve(response.idRunfood);
            }else{
                reject(response.mensaje); 
            }
        })
        .catch(error=>{
            reject(error);
        });
    });
    return promesa;
}

//Set RunfoodId To Orden
function setRunfoodId(cod_orden, RunfoodId) {
    fetch(`${ApiUrl}/runfood/asignar`,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({
            "id": cod_orden,
            "idRunfood": RunfoodId
        })
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            notify(response.mensaje,'success',2);
        }
        else{
            notify(response.mensaje,'error',5);
        }
    })
    .catch(error=>{
        console.log(error);
        notify("Error - No se pudo ligar la factura con runfood Id",'error',2);
    });
}