var MESES_SIMPLE = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

$(document).ready(function () {
    $(".basic").select2();

    $("#btnGenerar").on("click", function () {
        var anio         = $("#cmb_anio").val();
        var cod_sucursal = $("#cmb_sucursal").val();

        if (!anio) {
            messageDone("Selecciona un año", "error");
            return;
        }

        cargarTabla(anio, cod_sucursal);
    });
});

function cargarTabla(anio, cod_sucursal) {
    OpenLoad("Generando reporte...");

    $.ajax({
        url: "controllers/controlador_reporte_historico_simple.php?metodo=getTabla",
        type: "POST",
        data: { anio: anio, cod_sucursal: cod_sucursal },
        success: function (r) {
            $("#seccionResultados").show();
            if (r.success == 1 && r.data.length > 0) {
                $("#sinDatos").hide();
                renderTabla(r.data);
            } else {
                $("#bodyHistorico").html("");
                $("#sinDatos").show();
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

function renderTabla(data) {
    var html = "";
    data.forEach(function (row) {
        var periodo = (parseInt(row.mes) === 0) ? "Anual" : MESES_SIMPLE[parseInt(row.mes) - 1];
        html += "<tr>" +
            "<td>" + periodo + "</td>" +
            "<td>" + row.nombre_sucursal + "</td>" +
            "<td>$" + parseFloat(row.total_ventas || 0).toFixed(2) + "</td>" +
            "<td>" + parseInt(row.total_ordenes || 0) + "</td>" +
            "<td>" + parseInt(row.total_pickup || 0) + "</td>" +
            "<td>" + parseInt(row.total_delivery || 0) + "</td>" +
            "<td>" + parseInt(row.total_mesa || 0) + "</td>" +
            "<td>" + parseInt(row.clientes_nuevos || 0) + "</td>" +
            "<td>" + parseInt(row.clientes_recurrentes || 0) + "</td>" +
            "</tr>";
    });
    $("#bodyHistorico").html(html);
}
