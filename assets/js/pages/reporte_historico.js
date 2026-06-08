var chartVentas    = null;
var chartEntrega   = null;
var chartClientes  = null;
var chartProductos = null;
var chartMedios    = null;
var chartHoras     = null;

var MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

$(document).ready(function () {
    $(".basic").select2();

    $("#btnGenerar").on("click", function () {
        var anio         = $("#cmb_anio").val();
        var cod_sucursal = $("#cmb_sucursal").val();

        if (!anio) {
            messageDone("Selecciona un año", "error");
            return;
        }

        $("#seccionResultados").show();
        feather.replace();
        cargarTodo(anio, cod_sucursal);
    });
});

// ── Carga todos los datos en paralelo ─────────────────────────────────────────
function cargarTodo(anio, cod_sucursal) {
    OpenLoad("Generando reporte...");

    var params = { anio: anio, cod_sucursal: cod_sucursal };
    var base   = "controllers/controlador_reporte_historico.php";
    var pending = 6;

    function tick() {
        pending--;
        if (pending === 0) CloseLoad();
    }

    $.ajax({
        url: base + "?metodo=getResumenGeneral", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1) {
                var t = r.data.totales;
                $("#kpiVentas").text("$" + parseFloat(t.total_ventas  || 0).toFixed(2));
                $("#kpiOrdenes").text(parseInt(t.total_ordenes || 0));
                $("#kpiTicket").text("$"  + parseFloat(t.ticket_promedio || 0).toFixed(2));
                if (r.data.tendencia.length > 0) {
                    $("#sinVentas").hide();
                    buildChartVentas(r.data.tendencia);
                } else {
                    if (chartVentas) { chartVentas.destroy(); chartVentas = null; }
                    $("#sinVentas").show();
                }
            }
        },
        complete: tick
    });

    $.ajax({
        url: base + "?metodo=getTiposEntrega", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1 && r.data.length > 0) {
                $("#sinEntrega").hide();
                buildChartEntrega(r.data);
            } else {
                if (chartEntrega) { chartEntrega.destroy(); chartEntrega = null; }
                $("#sinEntrega").show();
            }
        },
        complete: tick
    });

    $.ajax({
        url: base + "?metodo=getClientes", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1 && r.data.length > 0) {
                $("#sinClientes").hide();
                buildChartClientes(r.data);
            } else {
                if (chartClientes) { chartClientes.destroy(); chartClientes = null; }
                $("#sinClientes").show();
            }
        },
        complete: tick
    });

    $.ajax({
        url: base + "?metodo=getProductos", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1 && r.data.length > 0) {
                $("#sinProductos").hide();
                buildChartProductos(r.data);
            } else {
                if (chartProductos) { chartProductos.destroy(); chartProductos = null; }
                $("#sinProductos").show();
            }
        },
        complete: tick
    });

    $.ajax({
        url: base + "?metodo=getMedios", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1 && r.data.length > 0) {
                $("#sinMedios").hide();
                buildChartMedios(r.data);
            } else {
                if (chartMedios) { chartMedios.destroy(); chartMedios = null; }
                $("#sinMedios").show();
            }
        },
        complete: tick
    });

    $.ajax({
        url: base + "?metodo=getHoras", type: "POST", data: params,
        success: function (r) {
            if (r.success == 1 && r.data.length > 0) {
                $("#sinHoras").hide();
                buildChartHoras(r.data);
            } else {
                if (chartHoras) { chartHoras.destroy(); chartHoras = null; }
                $("#sinHoras").show();
            }
        },
        complete: tick
    });
}

// ── Tab 1: Ventas por mes por sucursal (área multi-serie) ─────────────────────
function buildChartVentas(tendencia) {
    var map = {};
    tendencia.forEach(function (row) {
        var key = row.cod_sucursal;
        if (!map[key]) {
            map[key] = { name: row.nombre_sucursal, data: Array(12).fill(0) };
        }
        map[key].data[parseInt(row.mes) - 1] = parseFloat(row.total_ventas);
    });

    var series = Object.values(map);

    if (chartVentas) { chartVentas.destroy(); }
    chartVentas = new ApexCharts(document.querySelector("#chartVentas"), {
        chart: {
            type:        "area",
            height:      360,
            fontFamily:  "Nunito, sans-serif",
            toolbar:     { show: false },
            dropShadow: { enabled: true, opacity: 0.2, blur: 4, left: -5, top: 18 }
        },
        series: series,
        colors: ['#1b55e2','#e7515a','#1abc9c','#e2a03f','#5c1ac3','#ffc107'],
        dataLabels: { enabled: false },
        stroke:     { curve: "smooth", width: 2 },
        xaxis:      { categories: MESES },
        yaxis:      { labels: { formatter: function (v) { return "$" + v.toFixed(0); } } },
        tooltip:    { y: { formatter: function (v) { return "$" + v.toFixed(2); } } },
        fill:       { type: "gradient", gradient: { opacityFrom: 0.25, opacityTo: 0.03 } },
        legend:     { position: "top", horizontalAlign: "right" },
        grid:       { borderColor: "#e0e6ed", strokeDashArray: 5 }
    });
    chartVentas.render();
}

// ── Tab 2: Tipo de entrega por mes (barra apilada) ────────────────────────────
function buildChartEntrega(data) {
    var pickup   = Array(12).fill(0);
    var delivery = Array(12).fill(0);
    var mesa     = Array(12).fill(0);

    data.forEach(function (row) {
        var i      = parseInt(row.mes) - 1;
        pickup[i]   = parseFloat(row.pickup   || 0);
        delivery[i] = parseFloat(row.delivery || 0);
        mesa[i]     = parseFloat(row.mesa     || 0);
    });

    if (chartEntrega) { chartEntrega.destroy(); }
    chartEntrega = new ApexCharts(document.querySelector("#chartEntrega"), {
        chart: {
            type:       "bar",
            height:     360,
            fontFamily: "Nunito, sans-serif",
            stacked:    true,
            toolbar:    { show: false }
        },
        series: [
            { name: "Pickup",   data: pickup },
            { name: "Delivery", data: delivery },
            { name: "Mesa",     data: mesa }
        ],
        colors:      ['#1b55e2','#e7515a','#1abc9c'],
        dataLabels:  { enabled: false },
        plotOptions: { bar: { columnWidth: "55%" } },
        xaxis:       { categories: MESES },
        yaxis:       { labels: { formatter: function (v) { return "$" + v.toFixed(0); } } },
        tooltip:     { y: { formatter: function (v) { return "$" + v.toFixed(2); } } },
        legend:      { position: "top", horizontalAlign: "right" },
        grid:        { borderColor: "#e0e6ed", strokeDashArray: 5 }
    });
    chartEntrega.render();
}

// ── Tab 3: Clientes nuevos vs recurrentes (barra agrupada) ───────────────────
function buildChartClientes(data) {
    var nuevos      = Array(12).fill(0);
    var recurrentes = Array(12).fill(0);

    data.forEach(function (row) {
        var i          = parseInt(row.mes) - 1;
        nuevos[i]      = parseInt(row.nuevos      || 0);
        recurrentes[i] = parseInt(row.recurrentes || 0);
    });

    if (chartClientes) { chartClientes.destroy(); }
    chartClientes = new ApexCharts(document.querySelector("#chartClientes"), {
        chart: {
            type:       "bar",
            height:     360,
            fontFamily: "Nunito, sans-serif",
            toolbar:    { show: false }
        },
        series: [
            { name: "Nuevos",      data: nuevos },
            { name: "Recurrentes", data: recurrentes }
        ],
        colors:      ['#1abc9c','#5c1ac3'],
        dataLabels:  { enabled: false },
        plotOptions: { bar: { columnWidth: "55%", grouped: true } },
        xaxis:       { categories: MESES },
        legend:      { position: "top", horizontalAlign: "right" },
        grid:        { borderColor: "#e0e6ed", strokeDashArray: 5 }
    });
    chartClientes.render();
}

// ── Tab 4: Top 10 productos (barra horizontal) ────────────────────────────────
function buildChartProductos(data) {
    var nombres    = data.map(function (p) { return p.nombre_producto; });
    var cantidades = data.map(function (p) { return parseInt(p.cantidad); });
    var altura     = Math.max(320, data.length * 42);

    if (chartProductos) { chartProductos.destroy(); }
    chartProductos = new ApexCharts(document.querySelector("#chartProductos"), {
        chart: {
            type:       "bar",
            height:     altura,
            fontFamily: "Nunito, sans-serif",
            toolbar:    { show: false }
        },
        series:      [{ name: "Unidades", data: cantidades }],
        colors:      ['#e2a03f'],
        dataLabels:  { enabled: true, style: { fontSize: "12px" } },
        plotOptions: { bar: { horizontal: true, barHeight: "65%", borderRadius: 4 } },
        xaxis:       { categories: nombres },
        legend:      { show: false },
        grid:        { borderColor: "#e0e6ed", strokeDashArray: 5 }
    });
    chartProductos.render();
}

// ── Tab 5a: Ventas por canal (donut) ─────────────────────────────────────────
function buildChartMedios(data) {
    var labels = data.map(function (m) { return m.medio_compra || "OTRO"; });
    var values = data.map(function (m) { return parseInt(m.total_ordenes); });

    if (chartMedios) { chartMedios.destroy(); }
    chartMedios = new ApexCharts(document.querySelector("#chartMedios"), {
        chart:  { type: "donut", height: 340, fontFamily: "Nunito, sans-serif" },
        series: values,
        labels: labels,
        colors: ['#1b55e2','#e7515a','#1abc9c','#e2a03f','#5c1ac3','#ffc107'],
        dataLabels: { enabled: false },
        legend:     { position: "bottom" },
        plotOptions: {
            pie: {
                donut: {
                    size: "65%",
                    labels: {
                        show:  true,
                        total: {
                            show:      true,
                            showAlways: true,
                            label:     "Total",
                            color:     "#888ea8",
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                            }
                        }
                    }
                }
            }
        }
    });
    chartMedios.render();
}

// ── Tab 5b: Órdenes por hora del día (barra) ─────────────────────────────────
function buildChartHoras(data) {
    var categorias = [];
    var values     = Array(24).fill(0);

    for (var i = 0; i < 24; i++) {
        categorias.push(String(i).padStart(2, "0") + ":00");
    }

    data.forEach(function (row) {
        values[parseInt(row.hora)] = parseInt(row.total_ordenes);
    });

    if (chartHoras) { chartHoras.destroy(); }
    chartHoras = new ApexCharts(document.querySelector("#chartHoras"), {
        chart: {
            type:       "bar",
            height:     340,
            fontFamily: "Nunito, sans-serif",
            toolbar:    { show: false }
        },
        series:      [{ name: "Órdenes", data: values }],
        colors:      ['#5c1ac3'],
        dataLabels:  { enabled: false },
        plotOptions: { bar: { columnWidth: "60%", borderRadius: 3 } },
        xaxis:       { categories: categorias, labels: { rotate: -45, style: { fontSize: "11px" } } },
        legend:      { show: false },
        grid:        { borderColor: "#e0e6ed", strokeDashArray: 5 }
    });
    chartHoras.render();
}
