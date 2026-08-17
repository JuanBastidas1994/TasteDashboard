/**
 * Red de seguridad para la facturación automática: aunque el aviso en tiempo real (Firebase)
 * falle por lo que sea (caída de red del courier, timeout, etc.), este worker revisa cada
 * cierto tiempo si quedaron órdenes ENTREGADA de HOY sin factura en la sucursal actual, y las
 * reintenta solo. Corre en un hilo aparte (Web Worker) para no trabar la pantalla.
 */
let ApiUrl = "";     // base de api_gestion_ordenes, para reenviar la factura
let ApiKey = "";
let SiteUrl = "";    // controllers/controlador_facturas.php de taste (usa la sesión del cajero)
let Sucursal = 0;
let interval = 1000;

onmessage = function(event) {
    let data = event.data;
    ApiUrl = data.ApiUrl;
    ApiKey = data.ApiKey;
    SiteUrl = data.SiteUrl;
    Sucursal = data.Sucursal;
    interval = parseInt((data.tiempo * 1000) * 60);
    console.log("WEB WORKER FIND FACTURAS PENDIENTES", data);
    setTimeout(buscarPendientes, interval);
}

function today() {
    let d = new Date();
    let day = d.getDate().toString().padStart(2, "0");
    let month = (d.getMonth() + 1).toString().padStart(2, "0");
    return `${d.getFullYear()}-${month}-${day}`;
}

function buscarPendientes(){
    let hoy = today();
    let params = `?metodo=getFacturasUnificadas&fecha_inicio=${hoy}&fecha_fin=${hoy}&sucursal=${Sucursal}&estado=NO_ENVIADA`;

    fetch(`${SiteUrl}${params}`, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(response => {
        setTimeout(buscarPendientes, interval);
        if(response.success == 1 && response.data && response.data.length > 0){
            reenviarTodas(response.data);
        }
    })
    .catch(error => {
        setTimeout(buscarPendientes, interval);
        console.log("Error buscando facturas pendientes", error);
    });
}

async function reenviarTodas(pendientes){
    let exitosas = 0;
    let fallidas = 0;

    for(let orden of pendientes){
        try {
            let res = await fetch(`${ApiUrl}/facturacion/electronica`, {
                method: 'POST',
                headers: { 'Api-Key': ApiKey },
                body: JSON.stringify({ id: orden.cod_orden })
            });
            let data = await res.json();
            if(data.success === 1) exitosas++; else fallidas++;
        } catch(error) {
            fallidas++;
        }
        await new Promise(resolve => setTimeout(resolve, 800));
    }

    if(exitosas > 0 || fallidas > 0){
        postMessage({
            exitosas,
            fallidas,
            mensaje: `Facturación automática: ${exitosas} enviada(s)` + (fallidas > 0 ? `, ${fallidas} con error` : '')
        });
    }
}
