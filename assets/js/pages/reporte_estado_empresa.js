/* ============================================================
   reporte_estado_empresa.js
   Lógica de pantalla: filtros, llamadas al servidor,
   renderizado de widgets y gráficas en el dashboard.
   El PDF lo maneja reporte_estado_empresa_pdf.js
   ============================================================ */

var template;
let chartInstances = [];  // compartido con el PDF
let kpiData        = [];  // compartido con el PDF
let widgetData     = {};  // datos crudos del backend, compartido con el PDF

$(function () {
    template = Handlebars.compile($("#widget-template").html());

    const today    = new Date();
    // const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    // $('#fecha_inicio').val(isoDate(firstDay));
    // $('#fecha_fin').val(isoDate(today));

    $('#cmbEmpresa').on('change', function () { loadSucursales($(this).val()); });
    $('#btnGenerar').on('click', generarReporte);
    $('#btnGenerarPDF').on('click', generarPDF);   // generarPDF vive en el otro archivo

    feather.replace();
});

function isoDate(d) { return d.toISOString().split('T')[0]; }

/* ------------------------------------------------------------------ */
/*  SUCURSALES                                                          */
/* ------------------------------------------------------------------ */

function loadSucursales(codEmpresa) {
    const $sel = $('#cmbSucursal');
    $sel.html('<option value="0">Todas las sucursales</option>');
    if (!codEmpresa) return;

    fetch('controllers/controlador_reporte_estado_empresa.php?metodo=getSucursales', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cod_empresa: parseInt(codEmpresa) })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success && resp.data)
            resp.data.forEach(s =>
                $sel.append(`<option value="${s.cod_sucursal}">${s.nombre}</option>`)
            );
    })
    .catch(() => {});
}

/* ------------------------------------------------------------------ */
/*  GENERAR REPORTE (pantalla)                                          */
/* ------------------------------------------------------------------ */

function generarReporte() {
    const codEmpresa  = $('#cmbEmpresa').val();
    const codSucursal = $('#cmbSucursal').val();
    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin    = $('#fecha_fin').val();

    if (!codEmpresa)               { messageDone('Selecciona una empresa.', 'error'); return; }
    if (!fechaInicio || !fechaFin) { messageDone('Ingresa el rango de fechas.', 'error'); return; }
    if (fechaInicio > fechaFin)    { messageDone('La fecha inicio no puede ser mayor a la fecha fin.', 'error'); return; }

    const $btn = $('#btnGenerar');
    $btn.prop('disabled', true).html('Cargando\u2026');
    $('#reportContent').hide();
    $('#widgetsInfo').html('');

    fetch('controllers/controlador_reporte_estado_empresa.php?metodo=getWidgetsEmpresa', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || ''
        },
        body: JSON.stringify({
            cod_empresa:  parseInt(codEmpresa),
            cod_sucursal: parseInt(codSucursal),
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin
        })
    })
    .then(r => r.json())
    .then(response => {
        if (!response.success) {
            messageDone(response.mensaje || 'Error al obtener datos.', 'error');
            return;
        }

        const empresaName  = $('#cmbEmpresa option:selected').text().trim();
        const sucursalName = parseInt(codSucursal) === 0
            ? 'Todas las sucursales'
            : $('#cmbSucursal option:selected').text().trim();

        $('#reportTitle').text('Reporte de Estado \u2014 ' + empresaName);
        $('#reportSubtitle').html(
            `<i data-feather="map-pin" style="width:14px;height:14px;vertical-align:middle;"></i> ${sucursalName}
             &nbsp;&nbsp;
             <i data-feather="calendar" style="width:14px;height:14px;vertical-align:middle;"></i>
             Del ${fechaInicio} al ${fechaFin}`
        );

        processWidgets(response);
        feather.replace();
        $('#reportContent').fadeIn(300);
        $('html, body').animate({ scrollTop: $('#reportContent').offset().top - 20 }, 400);
    })
    .catch(() => messageDone('Error de conexi\u00F3n. Intenta de nuevo.', 'error'))
    .finally(() => {
        $btn.prop('disabled', false).html(
            '<i data-feather="bar-chart-2" style="width:16px;height:16px;"></i>&nbsp;Generar'
        );
        feather.replace();
    });
}

/* ------------------------------------------------------------------ */
/*  WIDGETS EN PANTALLA                                                 */
/* ------------------------------------------------------------------ */

function trackChart(el, data, title) {
    data.chart.animations = { enabled: false };
    const chart = new ApexCharts(el, data);
    chartInstances.push({ title, el, chart, rendered: chart.render() });
}

function processWidgets(widget) {
    chartInstances = [];
    kpiData        = [];
    widgetData     = widget;   // guardamos los datos crudos para el PDF

    const totalClientes = (widget.clientes_recurrentes.nuevos     || 0)
                        + (widget.clientes_recurrentes.recurrentes || 0);

    kpiData.push({ title: 'Ventas Totales',  value: `$${widget.totalAmount.toFixed(2)}`, iconKey: 'ventas',   bg: '#ede9fe', fg: '#7c3aed' });
    kpiData.push({ title: '\u00D3rdenes',    value: `${widget.total}`,                   iconKey: 'ordenes',  bg: '#dbeafe', fg: '#2563eb' });
    kpiData.push({ title: 'Clientes',        value: `${totalClientes}`,                  iconKey: 'clientes', bg: '#d1fae5', fg: '#059669' });
    kpiData.push({ title: 'Ticket Promedio', value: `$${widget.ticketPromedio}`,          iconKey: 'ticket',   bg: '#fef3c7', fg: '#d97706' });
    kpiData.forEach(k => addWidgetText(k.title, k.value, '', 'text', [], 120, 3));

    let chartData, $div;

    // [0] Evolución de Ventas
    chartData = createLineChartData(widget.trend, 'Ventas Totales', 300);
    $div = addWidgetText('Evoluci\u00F3n de Ventas', '', '', 'barchart', chartData, 300, 12);
    trackChart($div.find('.widget-chart')[0], chartData, 'EVOLUCI\u00D3N DE VENTAS');

    // [1] Ventas por Hora
    chartData = createBarChartData(widget.topHours, '\u00D3rdenes por Hora', 300);
    chartData.plotOptions.bar.horizontal = true;
    $div = addWidgetText('Ventas por Hora del d\u00EDa', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR HORA');

    // [2] Ventas por Canal
    chartData = createBarChartData(widget.deliveryTotals, 'Ventas por Tipo', 300);
    $div = addWidgetText('Ventas por Tipo de Entrega', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR CANAL');

    // [3] Clientes Recurrentes
    const pieData = createPieChartData(widget.clientes_recurrentes, 300);
    $div = addWidgetText('Clientes Recurrentes', '', '', 'piechart', pieData, 320);
    trackChart($div.find('.widget-chart')[0], pieData, 'CLIENTES RECURRENTES');

    // [4] Ventas por Día
    chartData = createBarChartData(widget.topDaysSales, 'Ventas por D\u00EDa', 300);
    $div = addWidgetText('Ventas por D\u00EDa', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, 'VENTAS POR D\u00CDA');

    // [5] Plataforma (opcional — solo si hay más de 1)
    if (Object.keys(widget.topPlatforms).length > 1) {
        const piePlat = createPieChartData(widget.topPlatforms, 300);
        $div = addWidgetText('Ventas por Plataforma', '', '', 'piechart', piePlat, 320);
        trackChart($div.find('.widget-chart')[0], piePlat, 'VENTAS POR PLATAFORMA');
    }

    // [6] Órdenes por Estado
    chartData = createBarChartData(widget.ordenesPorEstado, '\u00D3rdenes por Estado', 300);
    chartData.plotOptions.bar.horizontal = true;
    $div = addWidgetText('\u00D3rdenes por Estado', '', '', 'barchart', chartData, 320);
    trackChart($div.find('.widget-chart')[0], chartData, '\u00D3RDENES POR ESTADO');
}