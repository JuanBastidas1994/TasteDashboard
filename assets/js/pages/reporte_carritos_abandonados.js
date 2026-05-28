var tabla = null;

$(document).ready(function () {
    var hoy   = new Date().toISOString().split('T')[0];
    var priMes = hoy.substring(0, 8) + '01';
    $("#f_ini").val(priMes);
    $("#f_fin").val(hoy);

    cargarDatos();

    $("#btnGenerar").on("click", function () {
        cargarDatos();
    });
});

function cargarDatos() {
    var params = {
        f_ini:        $("#f_ini").val(),
        f_fin:        $("#f_fin").val(),
        estado:       $("#cmb_estado").val(),
        origen:       $("#cmb_origen").val(),
        recovery_src: $("#cmb_recovery").val(),
        cod_sucursal: $("#cmb_sucursal").val(),
        tipo_usuario: $("#cmb_tipo_usuario").val()
    };

    OpenLoad("Cargando datos...");

    $.ajax({
        url:  "controllers/controlador_reporte_carritos_abandonados.php?metodo=getDatos",
        type: "GET",
        data: params,
        success: function (response) {
            if (response.success == 1) {
                renderMetrics(response.metrics, response.recovery);
                renderTabla(response.rows);
                $("#seccionResultados").show();
                feather.replace();
            } else {
                messageDone(response.mensaje || "Error al cargar", "error");
            }
        },
        error: function () {
            messageDone("Error al conectar con el servidor", "error");
        },
        complete: function () {
            CloseLoad();
        }
    });
}

function fmt$(val) {
    return "$" + parseFloat(val || 0).toLocaleString("es-EC", {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

function fmtDate(val) {
    if (!val || val === "0000-00-00 00:00:00") return "—";
    return val.substring(0, 16).replace("T", " ");
}

function renderMetrics(m, recovery) {
    $("#mTotal").text(m.total || 0);
    $("#mAbandonados").text(m.abandonados || 0);
    $("#mConvertidos").text(m.convertidos || 0);
    $("#mTasaAbandono").text((m.tasa_abandono || 0) + "%");
    $("#mTasaConversion").text((m.tasa_conversion || 0) + "%");
    $("#mMontoAbandonado").text(fmt$(m.monto_abandonado));
    $("#mMontoConvertido").text(fmt$(m.monto_convertido));

    // Recovery por canal
    var recHtml = "";
    var canales = ["EMAIL", "PUSH", "WHATSAPP"];
    canales.forEach(function (c) {
        var cnt = recovery[c] || 0;
        recHtml += '<span class="badge badge-secondary mr-2" style="font-size:.9em;padding:6px 10px;">'
                 + c + ': <strong>' + cnt + '</strong></span>';
    });
    if (!recHtml) recHtml = '<span class="text-muted">Sin datos</span>';
    $("#mRecovery").html(recHtml);
}

var estadoBadge = {
    ACTIVO:     '<span class="badge badge-success">ACTIVO</span>',
    ABANDONADO: '<span class="badge badge-danger">ABANDONADO</span>',
    CONVERTIDO: '<span class="badge badge-primary">CONVERTIDO</span>',
    EXPIRADO:   '<span class="badge badge-secondary">EXPIRADO</span>'
};

var origenBadge = {
    WEB: '<span class="badge badge-info">WEB</span>',
    APP: '<span class="badge badge-warning">APP</span>'
};

function renderTabla(rows) {
    if (tabla) {
        tabla.destroy();
        $("#tblCarritos tbody").empty();
        tabla = null;
    }

    var tbody = "";
    (rows || []).forEach(function (r) {
        tbody += "<tr>"
            + "<td>" + (r.cart_token || "—") + "</td>"
            + "<td>" + (r.nombre_usuario || (r.cod_usuario ? r.cod_usuario : '<em class="text-muted">Anón.</em>')) + "</td>"
            + "<td>" + (r.email     || "—") + "</td>"
            + "<td>" + (r.telefono  || "—") + "</td>"
            + "<td>" + (origenBadge[r.origen]  || r.origen  || "—") + "</td>"
            + "<td>" + (estadoBadge[r.estado]  || r.estado  || "—") + "</td>"
            + "<td class='text-right'>" + fmt$(r.total_estimado) + "</td>"
            + "<td class='text-center'>" + (r.cantidad_productos || 0) + "</td>"
            + "<td>" + fmtDate(r.updated_at) + "</td>"
            + "<td>" + fmtDate(r.abandoned_at) + "</td>"
            + "<td>" + fmtDate(r.recovered_at) + "</td>"
            + "<td>" + fmtDate(r.converted_at) + "</td>"
            + "<td>" + (r.recovery_source || "—") + "</td>"
            + "<td>" + (r.cod_preorden || "—") + "</td>"
            + "<td>" + (r.cod_orden    || "—") + "</td>"
            + "</tr>";
    });

    $("#tblCarritos tbody").html(tbody);

    tabla = $("#tblCarritos").DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f>>>><"col-md-12"rt><"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
        buttons: {
            buttons: [
                { extend: "copy",  className: "btn" },
                { extend: "excel", className: "btn" },
                { extend: "pdf",   className: "btn" }
            ]
        },
        scrollX: true,
        order: [[8, "desc"]],
        pageLength: 25,
        oLanguage: {
            oPaginate: {
                sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                sNext:     '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            },
            sInfo:           "Mostrando _START_ a _END_ de _TOTAL_ registros",
            sInfoEmpty:      "Sin registros",
            sInfoFiltered:   "(filtrado de _MAX_ total)",
            sSearch:         '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            sSearchPlaceholder: "Buscar...",
            sLengthMenu:     "Resultados: _MENU_",
            sEmptyTable:     "No se encontraron carritos",
            sZeroRecords:    "No se encontraron registros"
        }
    });
}
