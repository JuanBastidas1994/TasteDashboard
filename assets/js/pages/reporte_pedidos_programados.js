var tablaOrdenes = null;

$(document).ready(function () {
    $(".basic").select2();

    var hoy = new Date().toISOString().split('T')[0];
    $("#fecha_inicio").val(hoy);
    $("#fecha_fin").val(hoy);
});

flatpickr(document.getElementById('fecha_inicio'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

flatpickr(document.getElementById('fecha_fin'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

$("body").on('click', ".btnReporte", function (event) {
    event.preventDefault();

    var f_inicio = $("#fecha_inicio").val();
    var f_fin    = $("#fecha_fin").val();
    var sucursal = $("#cmb_sucursal").val();

    if (!f_inicio || !f_fin) {
        messageDone("Debe completar las fechas", 'error');
        return;
    }
    if (f_fin < f_inicio) {
        messageDone("La fecha fin no puede ser menor que la de inicio", 'error');
        return;
    }

    var params = { sucursal: sucursal, f_inicio: f_inicio, f_fin: f_fin };

    cargarConsolidado(params);
    cargarOrdenes(params);

    $("#seccionMetricas").show();
    $("#seccionResultados").show();
});

// ── Tab 1: Consolidado de producción ──────────────────────────────────────────
function cargarConsolidado(params) {
    $.ajax({
        beforeSend: function () { OpenLoad("Cargando consolidado..."); },
        url:  "controllers/controlador_reporte_pedidos_programados.php?metodo=getProductos",
        type: "POST",
        data: params,
        success: function (response) {
            if (response.success == 1) {
                $("#consolidadoContent").html(response.html);
                $("#sinDatosConsolidado").hide();
                $("#mPedidos").text(response.total_pedidos);
                $("#mUnidades").text(response.total_unidades);
                $("#mMonto").text("$" + response.total_monto);
                feather.replace();
            } else {
                $("#consolidadoContent").html('');
                $("#sinDatosConsolidado").show();
                $("#mPedidos, #mUnidades, #mMonto").text("0");
                messageDone(response.mensaje, 'info');
            }
        },
        error: function () { messageDone("Error al cargar el consolidado", "error"); },
        complete: function () { CloseLoad(); }
    });
}

// ── Tab 2: Lista de órdenes ───────────────────────────────────────────────────
function cargarOrdenes(params) {
    $.ajax({
        url:  "controllers/controlador_reporte_pedidos_programados.php?metodo=getOrdenes",
        type: "POST",
        data: params,
        success: function (response) {
            if (tablaOrdenes) { tablaOrdenes.destroy(); tablaOrdenes = null; }

            if (response.success == 1) {
                $("#lstOrdenes").html(response.tabla);
                $("#sinDatosOrdenes").hide();
                tablaOrdenes = initDatatableOrdenes($("#table-ordenes"));
                feather.replace();
            } else {
                $("#lstOrdenes").html('');
                $("#sinDatosOrdenes").show();
            }
        },
        error: function () { messageDone("Error al cargar las órdenes", "error"); }
    });
}

function initDatatableOrdenes($table) {
    return $table.DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f>>>><"col-md-12"rt><"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
        buttons: {
            buttons: [
                { extend: 'copy',  className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf',   className: 'btn' }
            ]
        },
        oLanguage: {
            oPaginate: {
                sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                sNext:     '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            },
            sInfo:              "Mostrando _START_ a _END_ de _TOTAL_ registros",
            sInfoEmpty:         "Sin registros",
            sInfoFiltered:      "(filtrado de _MAX_ total)",
            sSearch:            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            sSearchPlaceholder: "Buscar...",
            sLengthMenu:        "Resultados: _MENU_",
            sEmptyTable:        "No se encontraron órdenes",
            sZeroRecords:       "No se encontraron resultados"
        },
        stripeClasses: [],
        lengthMenu:    [10, 20, 50],
        pageLength:    20,
        order:         [[2, "asc"]]
    });
}
