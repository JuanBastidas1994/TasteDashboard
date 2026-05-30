var tablaDetalle = null;
var chartResumen = null;

flatpickr(document.getElementById('fecha_inicio'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

flatpickr(document.getElementById('fecha_fin'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

$(document).ready(function () {
    $(".basic").select2();

    var hoy    = new Date().toISOString().split('T')[0];
    var priMes = hoy.substring(0, 8) + '01';
    $("#fecha_inicio").val(priMes);
    $("#fecha_fin").val(hoy);

    $("#btnReporte").on("click", function (e) {
        e.preventDefault();
        var f_inicio = $("#fecha_inicio").val();
        var f_fin    = $("#fecha_fin").val();

        if (!f_inicio || !f_fin) {
            messageDone("Debe completar las fechas", "error");
            return;
        }
        if (f_fin < f_inicio) {
            messageDone("La fecha fin no puede ser menor que la de inicio", "error");
            return;
        }

        cargarResumen(f_inicio, f_fin);
        cargarDetalle(f_inicio, f_fin);
        $("#seccionResultados").show();
        feather.replace();
    });
});

// ── Tab 1: Resumen por sucursal ────────────────────────────────────────────────
function cargarResumen(f_inicio, f_fin) {
    $.ajax({
        beforeSend: function () { OpenLoad("Cargando resumen..."); },
        url:  "controllers/controlador_reporte_tiempo_aceptacion.php?metodo=getResumen",
        type: "POST",
        data: { f_inicio: f_inicio, f_fin: f_fin },
        success: function (response) {
            if (response.success == 1) {
                renderChart(response.data);
                $("#sinDatosResumen").hide();
            } else {
                if (chartResumen) { chartResumen.destroy(); chartResumen = null; }
                $("#sinDatosResumen").show();
            }
        },
        error: function () { messageDone("Error al cargar el resumen", "error"); },
        complete: function () { CloseLoad(); }
    });
}

function renderChart(data) {
    var sucursales = data.map(function (d) { return d.sucursal; });
    var promedios  = data.map(function (d) { return parseFloat(d.promedio_minutos); });
    var totales    = data.map(function (d) { return parseInt(d.total_pedidos); });

    var colores = promedios.map(function (v) {
        return v <= 5 ? '#1abc9c' : v <= 15 ? '#f6a623' : '#e55353';
    });

    var options = {
        chart: {
            type:    'bar',
            height:  Math.max(300, sucursales.length * 52),
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal:   true,
                distributed:  true,
                barHeight:    '60%',
                dataLabels:   { position: 'bottom' }
            }
        },
        colors: colores,
        legend: { show: false },
        series: [{
            name: 'Promedio',
            data: promedios
        }],
        xaxis: {
            categories: sucursales,
            labels: {
                formatter: function (val) { return formatMin(val); }
            },
            title: { text: 'Tiempo promedio de aceptación' }
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            formatter: function (val) { return formatMin(val); },
            style: { fontSize: '12px', colors: ['#fff'] },
            offsetX: 6
        },
        tooltip: {
            custom: function (opts) {
                var i      = opts.dataPointIndex;
                var prom   = formatMin(promedios[i]);
                var total  = totales[i];
                var suc    = sucursales[i];
                return '<div style="padding:10px 14px;font-size:13px;">' +
                    '<b>' + suc + '</b><br>' +
                    'Promedio: <b>' + prom + '</b><br>' +
                    'Pedidos: <b>' + total + '</b>' +
                    '</div>';
            }
        },
        grid: { borderColor: '#e0e6ed' }
    };

    if (chartResumen) { chartResumen.destroy(); }
    chartResumen = new ApexCharts(document.querySelector("#chartResumen"), options);
    chartResumen.render();
}

// ── Tab 2: Tabla detalle ───────────────────────────────────────────────────────
function cargarDetalle(f_inicio, f_fin) {
    var sucursal = $("#cmb_sucursal").val();

    $.ajax({
        url:  "controllers/controlador_reporte_tiempo_aceptacion.php?metodo=getTiempos",
        type: "POST",
        data: { sucursal: sucursal, f_inicio: f_inicio, f_fin: f_fin },
        success: function (response) {
            if (response.success == 1) {
                if (tablaDetalle) { tablaDetalle.destroy(); tablaDetalle = null; }
                $("#lstTiempos").html(response.tabla);
                $("#promedioTiempo").html(response.promedio);
                tablaDetalle = initDatatable($("#table-tiempos"));
                feather.replace();
            } else {
                if (tablaDetalle) { tablaDetalle.destroy(); tablaDetalle = null; }
                $("#lstTiempos").html('<tr><td colspan="6" class="text-center text-muted py-4">Sin pedidos en este período</td></tr>');
                $("#promedioTiempo").html("--");
            }
        },
        error: function () { messageDone("Error al cargar el detalle", "error"); }
    });
}

// ── Helpers ────────────────────────────────────────────────────────────────────
function formatMin(minutos) {
    minutos = parseFloat(minutos);
    if (minutos < 60) return Math.round(minutos) + ' min';
    var h = Math.floor(minutos / 60);
    var m = Math.round(minutos % 60);
    return m > 0 ? h + 'h ' + m + 'min' : h + 'h';
}

function initDatatable($table) {
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
            sInfo:            "Mostrando _START_ a _END_ de _TOTAL_ registros",
            sInfoEmpty:       "Sin registros",
            sInfoFiltered:    "(filtrado de _MAX_ total)",
            sSearch:          '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            sSearchPlaceholder: "Buscar...",
            sLengthMenu:      "Resultados: _MENU_",
            sEmptyTable:      "No se encontraron resultados",
            sZeroRecords:     "No se encontraron resultados"
        },
        stripeClasses: [],
        lengthMenu:    [10, 20, 50],
        pageLength:    20,
        order:         [[0, "desc"]]
    });
}
