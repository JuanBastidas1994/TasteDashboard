/**
 * Modal "Facturas" del día: reemplaza a la vieja pestaña "Ordenes no Facturadas" del Cierre
 * Diario (que usaba la ruta legacy /facturas/electronica, solo Contifico). Sin filtros a
 * propósito — siempre hoy y la sucursal actual — y sin opción de anular (eso vive únicamente en
 * facturas_pendientes.php, con la regla de "mismo día").
 */
let facturasHoyActuales = [];

function today_iso() {
    let d = new Date();
    let day = d.getDate().toString().padStart(2, "0");
    let month = (d.getMonth() + 1).toString().padStart(2, "0");
    return `${d.getFullYear()}-${month}-${day}`;
}

function abrirFacturasHoy(){
    $("#facturasHoyModal").modal();
    cargarFacturasHoy();
}

function cargarFacturasHoy(){
    let hoy = today_iso();
    let target = $("#facturasHoyBody");
    target.html('<tr><td colspan="5" class="text-center">Cargando...</td></tr>');

    fetch(`controllers/controlador_facturas.php?metodo=getFacturasUnificadas&fecha_inicio=${hoy}&fecha_fin=${hoy}&sucursal=${sucursal_id}`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            facturasHoyActuales = response.data;
            let template = Handlebars.compile($("#facturas-hoy-template").html());
            target.html(template(response.data));
            feather.replace();
        }
        else{
            facturasHoyActuales = [];
            target.html(`<tr><td colspan="5" class="text-center">${response.mensaje}</td></tr>`);
        }
    })
    .catch(error => {
        console.log(error);
        target.html('<tr><td colspan="5" class="text-center">Error al cargar las facturas</td></tr>');
    });
}

$("body").on("click", ".btnVerErrorHoy", function(){
    let cod_orden = $(this).data("id");
    let orden = facturasHoyActuales.find(o => o.cod_orden == cod_orden);
    let motivo = (orden && orden.ultimo_error) ? orden.ultimo_error : "No se registró un motivo de error para esta orden.";
    $("#errorFacturaHoyTexto").text(motivo);
    $("#errorFacturaHoyModal").modal("show");
});

async function reenviarFacturasHoy(){
    let pendientes = facturasHoyActuales.filter(o => o.estado_envio === "NO_ENVIADA");
    if(pendientes.length === 0){
        notify("No hay facturas pendientes por reenviar", "warning", 2);
        return;
    }

    let confirm = await swal.fire({
        title: `Se reenviarán ${pendientes.length} factura(s)`,
        text: '¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    });
    if(!confirm.value) return;

    let $btn = $("#btnReenviarFacturasHoy");
    let htmlOriginal = $btn.html();
    $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Reenviando...');

    let exitosas = 0;
    let fallidas = 0;

    for(let orden of pendientes){
        let resultado = await reenviarFacturaHoy(orden.cod_orden);
        if(resultado.success === 1) exitosas++; else fallidas++;
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    $btn.prop("disabled", false).html(htmlOriginal);
    feather.replace();
    notify(`Reenvío terminado: ${exitosas} enviada(s), ${fallidas} fallida(s)`, fallidas > 0 ? 'warning' : 'success', 5);
    cargarFacturasHoy();
}

/*ENDPOINT UNICO: el api de gestion de ordenes resuelve internamente si es Contifico o Runfood*/
function reenviarFacturaHoy(cod_orden){
    return fetch(`${ApiUrl}/facturacion/electronica`, {
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .catch(error => {
        console.log(error);
        return { success: 0 };
    });
}
