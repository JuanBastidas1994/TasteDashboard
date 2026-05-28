var donutChart = null;
var lineChart  = null;

$(document).ready(function () {
    cargarDatos();

    $("#cmb_sucursal, #cmb_periodo").on("change", function () {
        cargarDatos();
    });
});

function cargarDatos() {
    var cod_sucursal = $("#cmb_sucursal").val();
    var periodo      = $("#cmb_periodo").val();

    OpenLoad("Cargando datos...");

    $.ajax({
        url: "controllers/controlador_reporte_carritos_abandonados.php?metodo=getDatos",
        type: "GET",
        data: { cod_sucursal: cod_sucursal, periodo: periodo },
        success: function (response) {
            if (response.success == 1) {
                renderStats(response);
                renderDonut(response);
                renderLine(response);
                renderTip(response);
                $("#seccionStats, #seccionCharts, #seccionTip").show();
                feather.replace();
            } else {
                messageDone(response.mensaje || "Error al cargar los datos", "error");
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

function formatMoney(value) {
    return "$" + parseFloat(value).toLocaleString("es-EC", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function renderDelta(val, elementId) {
    if (val === null || val === undefined) {
        $("#" + elementId).html("");
        return;
    }
    var arrow = val <= 0 ? "▼" : "▲";
    var color = val <= 0 ? "rgba(255,255,255,0.85)" : "#ffe0e0";
    var sign  = val >= 0 ? "+" : "";
    $("#" + elementId).html(
        '<span style="color:' + color + ';">' + arrow + " " + sign + val + "% este período</span>"
    );
}

function renderStats(data) {
    $("#statCount").text(data.count_aban);
    $("#statAmount").text(formatMoney(data.amount_aban));
    renderDelta(data.delta_count,  "statDeltaCount");
    renderDelta(data.delta_amount, "statDeltaAmount");
}

function renderDonut(data) {
    var countAban      = parseInt(data.count_aban_donut)      || 0;
    var countCompleted = parseInt(data.count_completed_donut) || 0;
    var total          = countAban + countCompleted;
    var pct            = total > 0 ? Math.round((countAban / total) * 100) : 0;

    var options = {
        chart: { type: "donut", height: 300 },
        series: [countAban, countCompleted],
        labels: ["En Abandono", "Órdenes Completadas"],
        colors: ["#f6a623", "#1abc9c"],
        plotOptions: {
            pie: {
                donut: {
                    size: "65%",
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: "",
                            fontWeight: 700,
                            fontSize: "22px",
                            color: "#333",
                            formatter: function () { return pct + "%"; }
                        }
                    }
                }
            }
        },
        legend: {
            position: "right",
            offsetY: 30,
            markers: { width: 12, height: 12, radius: 50 }
        },
        dataLabels: { enabled: false },
        tooltip: {
            y: { formatter: function (val) { return val + " carritos"; } }
        }
    };

    if (donutChart) {
        donutChart.destroy();
        donutChart = null;
    }
    donutChart = new ApexCharts(document.querySelector("#chartDonut"), options);
    donutChart.render();
}

function renderLine(data) {
    var labels = data.trend_labels || [];
    var values = data.trend_values || [];

    var options = {
        chart: {
            type: "area",
            height: 265,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        series: [{ name: "Monto Abandonado ($)", data: values }],
        xaxis: { categories: labels },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "$" + parseFloat(val).toLocaleString("es-EC", { minimumFractionDigits: 0 });
                }
            }
        },
        colors: ["#f6a623"],
        fill: {
            type: "gradient",
            gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 }
        },
        stroke: { curve: "smooth", width: 3 },
        markers: { size: 5, colors: ["#f6a623"], strokeWidth: 0 },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$" + parseFloat(val).toLocaleString("es-EC", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        },
        dataLabels: { enabled: false },
        grid: { borderColor: "#f1f1f1" }
    };

    if (lineChart) {
        lineChart.destroy();
        lineChart = null;
    }
    lineChart = new ApexCharts(document.querySelector("#chartLine"), options);
    lineChart.render();
}

function renderTip(data) {
    var msg = "";
    if (data.delta_count === null || data.delta_count === undefined) {
        msg = "No hay datos del período anterior para comparar. Monitorea tus carritos abandonados regularmente.";
    } else if (data.delta_count <= 0) {
        msg = "Tus carritos en abandono bajaron este período. <strong>Reenvía un recordatorio con descuento</strong> para recuperar ventas perdidas.";
    } else {
        msg = "Tus carritos en abandono aumentaron un <strong>" + data.delta_count + "%</strong> este período. Considera enviar recordatorios personalizados a los usuarios.";
    }
    $("#txtTip").html(msg);
}
