const { API_TASTE, API_MOTORIZADOS } = window.__CONFIG__;
let ApiUrl = API_TASTE;
let ApiKey = "";
let facturasActuales = [];

$(document).ready(function() {
    ApiKey = $("#apiEmpresa").val();
    $("#fecha_inicio").val(today());
    $("#fecha_fin").val(today());
    flatpickr(document.getElementsByClassName('picker'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    getSucursales();
    getFacturasUnificadas();
});

function today() {
    let date = new Date();
    let d = date.getDate();
    let day = d.toString().padStart(2, "0");
    let m = date.getMonth() + 1;
    let month = m.toString().padStart(2, "0");
    let year = date.getFullYear();
    return `${year}-${month}-${day}`;
}

function getSucursales() {
    fetch(`controllers/controlador_facturas.php?metodo=getSucursales`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            let template = Handlebars.compile($("#sucursales-template").html());
            $("#cmbSucursal").append(template(response.data));
        }
    })
    .catch(error => {
        console.log(error);
    });
}

function buildFiltros() {
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();

    if(fecha_inicio == "") {
        notify("Ingrese fecha inicio", "error", 2);
        return null;
    }
    if(fecha_fin == "") {
        notify("Ingrese fecha fin", "error", 2);
        return null;
    }

    let fI = new Date(fecha_inicio);
    let fF = new Date(fecha_fin);
    if(fI > fF) {
        notify("La fecha inicio no puede ser mayor a la fecha fin", "error", 2);
        return null;
    }

    return {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        sucursal: $("#cmbSucursal").val(),
        cliente: $("#txtCliente").val(),
        documento: $("#cmbDocumento").val(),
        estado: $("#cmbEstado").val()
    };
}

function getFacturasUnificadas() {
    let filtros = buildFiltros();
    if(!filtros) return;

    datatableReInit();
    let target = $("#style-3 tbody");

    let params = `?metodo=getFacturasUnificadas&fecha_inicio=${filtros.fecha_inicio}&fecha_fin=${filtros.fecha_fin}`
        + `&sucursal=${filtros.sucursal}&cliente=${encodeURIComponent(filtros.cliente)}`
        + `&documento=${filtros.documento}&estado=${filtros.estado}`;

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_facturas.php${params}`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            // PDO/MySQL devuelve 0/1 como string: normalizamos a boolean real para que
            // {{#if puede_anular}} en el template no trate "0" como verdadero.
            response.data.forEach(function(orden){
                orden.puede_anular = (orden.puede_anular == 1);
            });
            facturasActuales = response.data;

            let template = Handlebars.compile($("#facturas-unificadas-template").html());
            target.html(template(response.data));
            feather.replace();
            $('[data-toggle="tooltip"]').tooltip();
        }
        else{
            facturasActuales = [];
            target.html("");
            notify(response.mensaje, "error", 2);
            datatableReInit();
        }
        CloseLoad();
    })
    .catch(error => {
        target.html("");
        notify("Error al realizar la petición", "error", 2);
        CloseLoad();
        console.log(error);
        datatableReInit();
    });
}

function datatableReInit() {
    $('#style-3').DataTable().clear().destroy();
    $('#style-3').DataTable({
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdfHtml5', className: 'btn' },
            ]
        },
        stripeClasses: [],
        lengthMenu: [7, 10, 20, 50],
        pageLength: 20,
        order: [[4, "desc"]]
    });
}

$("body").on("click", ".btnReenviar", function(){
    let cod_orden = $(this).data("id");
    swal.fire({
       title: 'Se reenviará la factura',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          reenviarFactura(cod_orden).then(() => getFacturasUnificadas());
       }
    });
});

$("body").on("click", ".btnVerError", function(){
    let cod_orden = $(this).data("id");
    let orden = facturasActuales.find(o => o.cod_orden == cod_orden);
    let motivo = (orden && orden.ultimo_error) ? orden.ultimo_error : "No se registró un motivo de error para esta orden.";
    $("#errorFacturaTexto").text(motivo);
    $("#errorFacturaModal").modal("show");
});

$("body").on("click", ".btnAnular", function(){
    let cod_orden = $(this).data("id");
    swal.fire({
       title: 'Se anulará la factura',
       text: 'La orden podrá volver a facturarse después. ¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          anularFactura(cod_orden).then(() => getFacturasUnificadas());
       }
    });
});

/*ENDPOINT UNICO: el api de gestion de ordenes resuelve internamente si es Contifico o Runfood*/
function anularFactura(cod_orden) {
    let ruta = `${ApiUrl}/facturacion/anular`;

    return fetch(ruta, {
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .then(response => {
        if(response.success === 1){
            notify(response.mensaje, 'success', 2);
        }
        else{
            notify(response.mensaje, 'error', 5);
        }
        return response;
    })
    .catch(error => {
        console.log(error);
        notify("Error de red al anular la factura", "error", 2);
        return { success: 0 };
    });
}

/*ENDPOINT UNICO: el api de gestion de ordenes resuelve internamente si es Contifico o Runfood*/
function reenviarFactura(cod_orden) {
    let ruta = `${ApiUrl}/facturacion/electronica`;

    return fetch(ruta, {
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .then(response => {
        if(response.success === 1){
            notify(response.mensaje, 'success', 2);
        }
        else{
            notify(response.mensaje, 'error', 5);
        }
        return response;
    })
    .catch(error => {
        console.log(error);
        notify("Error de red al reenviar la factura", "error", 2);
        return { success: 0 };
    });
}

async function reenviarPendientes() {
    let filtros = buildFiltros();
    if(!filtros) return;

    OpenLoad("Buscando pendientes...");
    let params = `?metodo=getFacturasUnificadas&fecha_inicio=${filtros.fecha_inicio}&fecha_fin=${filtros.fecha_fin}`
        + `&sucursal=${filtros.sucursal}&cliente=${encodeURIComponent(filtros.cliente)}`
        + `&documento=${filtros.documento}&estado=NO_ENVIADA`;

    let response;
    try {
        let res = await fetch(`controllers/controlador_facturas.php${params}`, { method: 'GET' });
        response = await res.json();
    } catch(error) {
        CloseLoad();
        notify("Error al buscar las órdenes pendientes", "error", 2);
        return;
    }

    if(response.success != 1 || !response.data || response.data.length == 0){
        CloseLoad();
        notify("No hay órdenes pendientes en el rango seleccionado", "warning", 2);
        return;
    }

    let pendientes = response.data;
    CloseLoad();

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

    let exitosas = 0;
    let fallidas = 0;

    for(let orden of pendientes){
        OpenLoad(`Reenviando ${exitosas + fallidas + 1} de ${pendientes.length}...`);
        let resultado = await reenviarFactura(orden.cod_orden);
        if(resultado.success === 1) exitosas++; else fallidas++;
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    CloseLoad();
    notify(`Reenvío masivo terminado: ${exitosas} enviadas, ${fallidas} fallidas`, fallidas > 0 ? 'warning' : 'success', 5);
    getFacturasUnificadas();
}
