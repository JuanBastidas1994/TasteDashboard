const { API_TASTE, API_MOTORIZADOS } = window.__CONFIG__;
let ApiUrl = API_TASTE;
let ApiKey = "";
let facturasActuales = [];
let facturasEnProceso = new Set();
let permisoAnularFacturas = false;
let inventarioJsonActual = null;

$(document).ready(function() {
    ApiKey = $("#apiEmpresa").val();
    permisoAnularFacturas = $("#permisoAnularFacturas").val() == "1";
    $("#fecha_inicio").val(primerDiaDelMes());
    $("#fecha_fin").val(today());
    flatpickr(document.getElementsByClassName('picker'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    getSucursales();
    getFacturasUnificadas();
});

function formatDate(date) {
    let d = date.getDate();
    let day = d.toString().padStart(2, "0");
    let m = date.getMonth() + 1;
    let month = m.toString().padStart(2, "0");
    let year = date.getFullYear();
    return `${year}-${month}-${day}`;
}

function today() {
    return formatDate(new Date());
}

function primerDiaDelMes() {
    let date = new Date();
    return formatDate(new Date(date.getFullYear(), date.getMonth(), 1));
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
        estado: $("#cmbEstado").val()
    };
}

// Una vez en Contifico (CREADA), la emisión al SRI la hace Contifico solo (puede demorar hasta
// 1 hora) — ya no depende de este sistema, por eso CREADA y EMITIDA_SRI se muestran igual (✓).
function calcularEstadoElectronica(orden) {
    if(orden.estado_factura === "ANULADA") {
        return { icono: "x-circle", clase: "danger", title: "Factura anulada" };
    }
    if(orden.estado_envio === "ENVIADA") {
        return { icono: "check-circle", clase: "success", title: "Enviada a Contifico" };
    }
    return { icono: "x-circle", clase: "danger", title: "No enviada a Contifico" };
}

// null cuando estado_inventario viene vacío (orden nunca facturada, sin fila en tb_orden_factura_electronica).
function calcularEstadoInventario(orden) {
    switch(orden.estado_inventario) {
        case "DEBITADO":
            return { icono: "check-circle", clase: "success", title: "Inventario debitado en Contifico" };
        case "REVERTIDO":
            return { icono: "check-circle", clase: "success", title: "Inventario revertido tras la anulación" };
        case "NO_DEBITADO":
            return { icono: "x-circle", clase: "danger", title: "Inventario no se pudo debitar" };
        case "NO_REVERTIDO":
            return { icono: "x-circle", clase: "danger", title: "Inventario no se pudo revertir tras la anulación" };
        default:
            return null;
    }
}

// silent=true evita el bloqueo de pantalla completa (OpenLoad/blockUI), se usa al refrescar
// después de un reenvío/anulación individual para no taparle la pantalla al usuario.
function getFacturasUnificadas(silent) {
    let filtros = buildFiltros();
    if(!filtros) return;

    let params = `?metodo=getFacturasUnificadas&fecha_inicio=${filtros.fecha_inicio}&fecha_fin=${filtros.fecha_fin}`
        + `&sucursal=${filtros.sucursal}&cliente=${encodeURIComponent(filtros.cliente)}`
        + `&estado=${filtros.estado}`;

    if(!silent) OpenLoad("Cargando...");
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
                orden.permisoAnularFacturas = permisoAnularFacturas;
                orden.electronica = calcularEstadoElectronica(orden);
                orden.inventario = calcularEstadoInventario(orden);
            });
            facturasActuales = response.data;

            let template = Handlebars.compile($("#facturas-unificadas-template").html());
            pintarFilas(template(response.data));
            actualizarIndicadores(response.data);
        }
        else{
            facturasActuales = [];
            pintarFilas("");
            notify(response.mensaje, "error", 2);
            actualizarIndicadores([]);
        }
        if(!silent) CloseLoad();
    })
    .catch(error => {
        pintarFilas("");
        notify("Error al realizar la petición", "error", 2);
        actualizarIndicadores([]);
        if(!silent) CloseLoad();
        console.log(error);
    });
}

function actualizarIndicadores(data) {
    let enviadas = data.filter(o => o.estado_envio === "ENVIADA").length;
    let noEnviadas = data.filter(o => o.estado_envio === "NO_ENVIADA").length;
    $("#statEnviadas").text(enviadas);
    $("#statNoEnviadas").text(noEnviadas);
}

// Devuelve la instancia de DataTables, inicializándola una sola vez. Pasar opciones de nuevo
// a una tabla ya inicializada tira error, por eso siempre se re-obtiene sin argumentos.
function getDataTable() {
    if($.fn.DataTable.isDataTable('#style-3')){
        return $('#style-3').DataTable();
    }
    let tabla = $('#style-3').DataTable({
        dom: 'rtip',
        stripeClasses: [],
        lengthMenu: [7, 10, 20, 50],
        pageLength: 20,
        order: [[4, "desc"]]
    });
    $("#txtBuscarTabla").on("keyup", function(){
        tabla.search(this.value).draw();
    });
    // draw.dt dispara en TODO redraw (filtro, paginación, orden), no solo el primero — así los
    // íconos feather y tooltips de filas que entran a la vista al cambiar de página también
    // se procesan (rows.add() usa los nodos reales del handlebars, no strings).
    tabla.on('draw.dt', function(){
        feather.replace();
        $('[data-toggle="tooltip"]').tooltip();
    });
    return tabla;
}

// Mezclar manipulación directa del DOM (target.html(...)) con la API de DataTables rompe la
// tabla: al llamar .destroy() DataTables restaura las filas que tenía cacheadas en su propio
// modelo de datos, pisando lo recién inyectado por jQuery — por eso el filtrado no se veía
// reflejado aunque el controlador sí devolvía los datos correctos. La forma correcta es dejar
// que DataTables maneje el DOM: clear() + rows.add() + draw().
function pintarFilas(filasHtml) {
    let tabla = getDataTable();
    tabla.clear();
    let $filas = $('<table>').html(filasHtml).find('tr');
    if($filas.length > 0){
        tabla.rows.add($filas);
    }
    tabla.draw();
}

// Evita que un doble clic (o clic mientras ya está en curso) dispare la misma acción dos veces,
// y muestra un spinner pequeño en el propio botón en vez de tapar toda la pantalla con OpenLoad.
function iniciarAccionBoton(cod_orden, claseBoton) {
    if(facturasEnProceso.has(cod_orden)) return false;
    facturasEnProceso.add(cod_orden);
    $(`.${claseBoton}[data-id="${cod_orden}"]`)
        .css("pointer-events", "none")
        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    return true;
}

function finalizarAccionBoton(cod_orden) {
    facturasEnProceso.delete(cod_orden);
}

$("body").on("click", ".btnReenviar", function(){
    let cod_orden = $(this).data("id");
    if(facturasEnProceso.has(cod_orden)) return;

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
          if(!iniciarAccionBoton(cod_orden, "btnReenviar")) return;
          reenviarFactura(cod_orden).then(() => {
              finalizarAccionBoton(cod_orden);
              getFacturasUnificadas(true);
          });
       }
    });
});

$("body").on("click", ".btnVerInventario", function(){
    let cod_orden = $(this).data("id");

    inventarioJsonActual = null;
    $("#inventarioOrigenTexto").text("Cargando...");
    $("#inventarioTablaBody").html("");
    $("#inventarioModal").modal("show");

    fetch(`${ApiUrl}/facturacion/ver-inventario`, {
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({ id: cod_orden })
    })
    .then(res => res.json())
    .then(response => {
        if(response.success !== 1){
            $("#inventarioOrigenTexto").text(response.mensaje || "No se pudo obtener el detalle de inventario");
            return;
        }

        let inv = response.inventario;
        inventarioJsonActual = inv;

        $("#inventarioOrigenTexto").text(response.origen === "HISTORICO"
            ? `Ya enviado a Contifico el ${response.fecha}. Esto es exactamente lo que se debitó.`
            : "Aún no se ha debitado: esto es lo que se enviaría si se procesa ahora.");

        let detalles = (inv && inv.detalles) ? inv.detalles : [];
        if(detalles.length === 0){
            $("#inventarioTablaBody").html('<tr><td colspan="4" class="text-center text-muted">Sin items</td></tr>');
            return;
        }
        let filas = detalles.map(function(d){
            return `<tr>
                <td>${escapeHtml(d.nombre || d.producto_id)}</td>
                <td>${escapeHtml(d.cantidad)}</td>
                <td>${escapeHtml(d.unidad || '-')}</td>
                <td>$${escapeHtml(d.precio)}</td>
            </tr>`;
        }).join("");
        $("#inventarioTablaBody").html(filas);
    })
    .catch(error => {
        console.log(error);
        $("#inventarioOrigenTexto").text("Error de red al obtener el detalle de inventario");
    });
});

$("body").on("click", "#btnCopiarJsonInventario", function(){
    if(!inventarioJsonActual){
        notify("No hay datos para copiar", "warning", 2);
        return;
    }
    navigator.clipboard.writeText(JSON.stringify(inventarioJsonActual, null, 2))
        .then(() => notify("JSON copiado al portapapeles", "success", 2))
        .catch(() => notify("No se pudo copiar el JSON", "error", 2));
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
    if(facturasEnProceso.has(cod_orden)) return;

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
          if(!iniciarAccionBoton(cod_orden, "btnAnular")) return;
          anularFactura(cod_orden).then(() => {
              finalizarAccionBoton(cod_orden);
              getFacturasUnificadas(true);
          });
       }
    });
});

$("body").on("click", ".btnReintentarInventario", function(){
    let cod_orden = $(this).data("id");
    if(facturasEnProceso.has(cod_orden)) return;

    swal.fire({
       title: 'Se reintentará el débito de inventario',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          if(!iniciarAccionBoton(cod_orden, "btnReintentarInventario")) return;
          reintentarInventario(cod_orden).then(() => {
              finalizarAccionBoton(cod_orden);
              getFacturasUnificadas(true);
          });
       }
    });
});

$("body").on("click", ".btnReintentarReversionInventario", function(){
    let cod_orden = $(this).data("id");
    if(facturasEnProceso.has(cod_orden)) return;

    swal.fire({
       title: 'Se reintentará la reversión de inventario',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          if(!iniciarAccionBoton(cod_orden, "btnReintentarReversionInventario")) return;
          reintentarReversionInventario(cod_orden).then(() => {
              finalizarAccionBoton(cod_orden);
              getFacturasUnificadas(true);
          });
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

function reintentarInventario(cod_orden) {
    let ruta = `${ApiUrl}/facturacion/reintentar-inventario`;

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
        notify("Error de red al reintentar el débito de inventario", "error", 2);
        return { success: 0 };
    });
}

function reintentarReversionInventario(cod_orden) {
    let ruta = `${ApiUrl}/facturacion/reintentar-reversion-inventario`;

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
        notify("Error de red al reintentar la reversión de inventario", "error", 2);
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
        + `&estado=NO_ENVIADA`;

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

function escapeHtml(texto) {
    return $("<div>").text(texto == null ? "" : texto).html();
}

// Exporta todo el listado actualmente filtrado (facturasActuales), sin la columna de Acciones.
// Se arma como una tabla HTML con MIME de Excel: Excel la abre directamente como hoja de cálculo,
// sin depender de la extensión de Buttons de DataTables (que se ocultó a pedido).
function descargarExcel() {
    if(!facturasActuales || facturasActuales.length === 0){
        notify("No hay datos para descargar", "warning", 2);
        return;
    }

    let encabezados = ["Orden", "Sucursal", "Cliente", "Documento", "Fecha", "Total", "Forma de pago", "Estado"];
    let filas = facturasActuales.map(function(o){
        let estado = o.estado_envio + (o.estado_factura ? ` (${o.estado_factura})` : "");
        return [
            o.cod_orden,
            o.sucursal,
            o.cliente,
            o.num_factura || "-",
            o.fecha,
            parseFloat(o.total || 0).toFixed(2),
            o.formas_pago || "-",
            estado
        ];
    });

    let html = '<table border="1"><thead><tr>'
        + encabezados.map(h => `<th>${escapeHtml(h)}</th>`).join("")
        + '</tr></thead><tbody>'
        + filas.map(fila => '<tr>' + fila.map(c => `<td>${escapeHtml(c)}</td>`).join("") + '</tr>').join("")
        + '</tbody></table>';

    let blob = new Blob(['﻿' + html], { type: 'application/vnd.ms-excel' });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    a.download = `facturas_${today()}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
