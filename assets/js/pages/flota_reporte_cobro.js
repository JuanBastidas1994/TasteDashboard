// Filtros con los que se generó el último reporte (los usa el export a Excel)
let filtrosGenerados = null;

function dinero(valor) {
    return '$' + parseFloat(valor || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(texto) {
    return $('<div>').text(texto == null ? '' : texto).html();
}

function leerFiltros() {
    return {
        cod_empresa: $("#cmbComercio").val(),
        fechaInicio: $("#txtFechaInicio").val(),
        fechaFin: $("#txtFechaFin").val(),
        forma_pago: $("#cmbFormaPago").val()
    };
}

function pintarIndicadores(ind) {
    $("#indOrdenesEmpresa").text(ind.ordenes_empresa);
    $("#indNombreEmpresa").text($("#cmbComercio option:selected").text());
    $("#indOrdenesTodas").text(ind.ordenes_todas);
    $("#indTotalGenerado").text(dinero(ind.total_generado));
    $("#indTotalACobrar").text(dinero(ind.total_a_cobrar));
    $("#indTotalTaste").text(dinero(ind.total_taste));
    $("#indFormulaTaste").text(ind.ordenes_todas + ' órdenes x ' + dinero(ind.tarifa_taste));
}

function pintarDetalle(detalle) {
    if (detalle.length === 0) {
        $("#cobroBody").html('<tr><td colspan="8" class="text-center text-muted">No hay órdenes de esta empresa en este lapso de tiempo</td></tr>');
        return;
    }

    let filas = '';
    detalle.forEach(d => {
        const badge = d.estado === 'ENTREGADA' ? 'badge-success' : 'badge-info';
        filas += `<tr>
                <td>#${d.cod_orden}</td>
                <td>${escapeHtml(d.fecha)}</td>
                <td>${escapeHtml(d.motorizado)}</td>
                <td><span class="badge ${badge}">${d.estado}</span></td>
                <td>${escapeHtml(d.forma_pago_nombre)}</td>
                <td class="text-right">${dinero(d.subtotal)}</td>
                <td class="text-right">${d.grava_iva ? dinero(d.iva) : '-'}</td>
                <td class="text-right">${dinero(d.envio)}</td>
            </tr>`;
    });
    $("#cobroBody").html(filas);
}

function pintarTotales(tot) {
    const fila = (titulo, valor, clase = '') =>
        `<tr class="${clase}"><th colspan="7">${titulo}</th><th>${dinero(valor)}</th></tr>`;

    let footer = '';
    // Si algún envío cobra IVA se desglosa; si no, solo el total
    if (tot.iva > 0) {
        if (tot.subtotal_0 > 0)
            footer += fila('Subtotal 0%', tot.subtotal_0);
        footer += fila('Subtotal IVA', tot.subtotal_iva);
        footer += fila('IVA', tot.iva);
    }
    footer += fila('TOTAL GENERADO', tot.total, 'total-final');
    $("#cobroFooter").html(footer);
}

// El desglose siempre cubre todas las órdenes del lapso, aunque la tabla esté filtrada
function pintarDesglose(desglose) {
    let filas = '';
    let ordenes = 0;
    let envios = 0;
    desglose.forEach(d => {
        ordenes += d.ordenes;
        envios += d.envios;
        const situacion = d.ya_cobrado
            ? '<span class="badge badge-success">Ya cobrado al despachar</span>'
            : '<span class="badge badge-danger">Por cobrar</span>';
        filas += `<tr>
                <td>${escapeHtml(d.nombre)}</td>
                <td class="text-right">${d.ordenes}</td>
                <td class="text-right">${dinero(d.envios)}</td>
                <td>${situacion}</td>
            </tr>`;
    });
    $("#desgloseBody").html(filas);
    $("#desgloseFooter").html(`<tr><th>Total</th><th class="text-right">${ordenes}</th><th class="text-right">${dinero(envios)}</th><th></th></tr>`);
}

$("#btnGenerar").on("click", function () {
    const filtros = leerFiltros();

    if (filtros.cod_empresa === "" || filtros.fechaInicio === "" || filtros.fechaFin === "") {
        messageDone("Debe completar todos los campos, inténtelo nuevamente", 'error');
        return;
    }
    if (filtros.fechaFin < filtros.fechaInicio) {
        messageDone("La fecha fin no puede ser menor que la de inicio", 'error');
        return;
    }

    $.ajax({
        beforeSend: function () {
            OpenLoad("Generando reporte, por favor espere...");
        },
        url: 'controllers/controlador_reporte_flota_cobro.php?metodo=getReporte',
        type: 'POST',
        data: filtros,
        success: function (response) {
            if (response['success'] == 1) {
                pintarIndicadores(response.indicadores);
                pintarDetalle(response.detalle);
                pintarTotales(response.totales);
                pintarDesglose(response.desglose);
                $("#resultado").show();

                filtrosGenerados = filtros;
                $("#btnExcel").prop("disabled", false);
            } else {
                notify(response['mensaje'], "info", 2);
                $("#resultado").hide();
                filtrosGenerados = null;
                $("#btnExcel").prop("disabled", true);
            }
        },
        error: function (data) {
            console.log(data);
            notify("Ocurrió un error al generar el reporte", "error", 2);
        },
        complete: function () {
            CloseLoad();
        }
    });
});

// Si cambian los filtros, el Excel ya no corresponde a lo que se ve en pantalla
$("#cmbComercio, #txtFechaInicio, #txtFechaFin").on("change", function () {
    filtrosGenerados = null;
    $("#btnExcel").prop("disabled", true);
});

// El filtro de forma de pago acota la tabla ya generada: se vuelve a pedir el reporte (si los otros
// filtros cambiaron, filtrosGenerados es null y hay que pulsar Generar)
$("#cmbFormaPago").on("change", function () {
    if (filtrosGenerados) {
        $("#btnGenerar").trigger("click");
    }
});

$("#btnExcel").on("click", function () {
    if (!filtrosGenerados) {
        notify("Primero genera el reporte", "info", 2);
        return;
    }
    window.location.href = 'excel_export/flota_reporte_cobro.php?' + $.param(filtrosGenerados);
});
