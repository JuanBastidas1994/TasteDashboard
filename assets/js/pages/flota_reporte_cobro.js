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
        fechaFin: $("#txtFechaFin").val()
    };
}

function pintarIndicadores(ind) {
    $("#indOrdenesEmpresa").text(ind.ordenes_empresa);
    $("#indNombreEmpresa").text($("#cmbComercio option:selected").text());
    $("#indOrdenesTodas").text(ind.ordenes_todas);
    $("#indTotalTaste").text(dinero(ind.total_taste));
    $("#indFormulaTaste").text(ind.ordenes_todas + ' órdenes x ' + dinero(ind.tarifa_taste));
}

function pintarDetalle(detalle) {
    if (detalle.length === 0) {
        $("#cobroBody").html('<tr><td colspan="7" class="text-center text-muted">No hay órdenes de esta empresa en este lapso de tiempo</td></tr>');
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
                <td class="text-right">${dinero(d.subtotal)}</td>
                <td class="text-right">${d.grava_iva ? dinero(d.iva) : '-'}</td>
                <td class="text-right">${dinero(d.envio)}</td>
            </tr>`;
    });
    $("#cobroBody").html(filas);
}

function pintarTotales(tot) {
    const fila = (titulo, valor, clase = '') =>
        `<tr class="${clase}"><th colspan="6">${titulo}</th><th>${dinero(valor)}</th></tr>`;

    let footer = '';
    // Si algún envío cobra IVA se desglosa; si no, solo el total
    if (tot.iva > 0) {
        if (tot.subtotal_0 > 0)
            footer += fila('Subtotal 0%', tot.subtotal_0);
        footer += fila('Subtotal IVA', tot.subtotal_iva);
        footer += fila('IVA', tot.iva);
    }
    footer += fila('TOTAL A COBRAR', tot.total, 'total-final');
    $("#cobroFooter").html(footer);
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

$("#btnExcel").on("click", function () {
    if (!filtrosGenerados) {
        notify("Primero genera el reporte", "info", 2);
        return;
    }
    window.location.href = 'excel_export/flota_reporte_cobro.php?' + $.param(filtrosGenerados);
});
