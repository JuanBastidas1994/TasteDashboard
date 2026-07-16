let typeWidget = 'sales';
let period = 'month';
let isFirstLoad = true;
var template = Handlebars.compile($("#widget-template").html());
$(function () {
    getWidgetInfo();
});

 $("#cmbOffice").on('change', function(){
    getWidgetInfo();
});

$(".rbPeriod").on('change', function(){
    period = $(this).val();
    getWidgetInfo();
});

function getWidgetInfo(){
    let info = {
        office_id: $("#cmbOffice").val(),
        type: typeWidget,
        period: period
    }
    fetch(`controllers/controlador_metricas.php?metodo=getWidgets`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        $("#widgetsInfo").html('');
        processWidgets(response);
        feather.replace();
        if (isFirstLoad) {
            $('#dashboardLoader').hide();
            $('#bottomTablesRow').show();
            isFirstLoad = false;
        }
    })
    .catch(error => {
        messageDone(error,'error');
        if (isFirstLoad) {
            $('#dashboardLoader').hide();
        }
    });
}

function processWidgets(widget){
    let sharedText = '';

    //Ticket Promedio
    sharedText = `Mi ticket promedio es de $${widget.ticketPromedio} en ventas por Taste 📈`;
    addWidgetText('Ticket Promedio', `$${widget.ticketPromedio}`, sharedText);
    
    //Ventas Totales
    sharedText = `Este mes generé $${widget.ticketPromedio} desde Taste 📈`;
    addWidgetText('Ventas Totales', `$${widget.totalAmount}`, sharedText);
    
    //Numero de ordenes
    sharedText = `Este mes ingresaron ${widget.ticketPromedio} ordenes 📈`;
    addWidgetText('Número de Ordenes entregadas', `${widget.total}`, sharedText);

    //Grafico venta por sucursal
    sharedText = `Estas son mis ventas generales 📈`;
    chartData = createLineChartData(widget.trend, 'Ventas Totales', 300);
    let $divChartOfficesSales = addWidgetText('Ventas por sucursal', '', sharedText, 'barchart', chartData, 300, 12);
    generateChart($divChartOfficesSales.find('.widget-chart')[0], chartData);

    //Ordenes por Hora
    sharedText = `El rango de hora en el que más tengo ventas es ${getMaxValue(widget.topHours)}`;
    chartData = createBarChartData(widget.topHours, 'Ventas por Hora', 300);
    chartData.plotOptions.bar.horizontal = true;
    let $divChartSales = addWidgetText('Ventas por Hora del día', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartSales.find('.widget-chart')[0], chartData);
    
    //Ordenes por tipo de entrega
    sharedText = `El tipo de entrega que mas generó fue ${getMaxValue(widget.deliveryTotals)}`;
    chartData = createBarChartData(widget.deliveryTotals, 'Ventas por tipo de Entrega', 300);
    let $divChartTypeDelivery = addWidgetText('Ventas por tipo de Entrega', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartTypeDelivery.find('.widget-chart')[0], chartData);

    //Clientes recurrentes
    let PieData = createPieChartData(widget.clientes_recurrentes, 300);
    $divChartPayments = addWidgetText('Clientes Recurrentes', '', '', 'piechart', PieData, 320);
    generateChart($divChartPayments.find('.widget-chart')[0], PieData);

    //Día que mas se vende
    sharedText = `El día que mas generó fue ${getMaxValue(widget.topDaysSales)}`;
    chartData = createBarChartData(widget.topDaysSales, 'Ventas por Día', 300);
    let $divChartDays = addWidgetText('Ventas por Día', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartDays.find('.widget-chart')[0], chartData);

    //Plataforma
    //topPlatforms
    if(widget.topPlatforms.length > 1){
        let PiePlatformData = createPieChartData(widget.topPlatforms, 300);
        let $divChartPlatform = addWidgetText('Ventas por plataforma', '', '', 'piechart', PieData, 320);
        generateChart($divChartPlatform.find('.widget-chart')[0], PiePlatformData);
    }

    //Ordenes por estado
    // sharedText = `El día que mas generó fue ${getMaxValue(widget.ordenesPorEstado)}`;
    // chartData = createBarChartData(widget.ordenesPorEstado, 'Ventas por Día', 300);
    // let $divChartStatuses = addWidgetText('Ordenes por estado', '', sharedText, 'barchart', chartData, 320);
    // generateChart($divChartStatuses.find('.widget-chart')[0], chartData);


    //Ordenes por estado
    sharedText = `El rango de hora en el que más tengo ventas es ${getMaxValue(widget.topHours)}`;
    chartData = createBarChartData(widget.ordenesPorEstado, 'Ordenes por estado', 300);
    chartData.plotOptions.bar.horizontal = true;
    let $divChartStatuses = addWidgetText('Ordenes por estado', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartStatuses.find('.widget-chart')[0], chartData);

    renderIndexTables(widget);
}

const _IDX_DOT_COLORS = ['#e7515a', '#2196f3', '#4caf50', '#ff9800', '#ffc107'];

function fmtMoneyIdx(v) {
    return '$' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function renderIndexTables(widget) {
    const $tp = $('#tbodyTopProductos').empty();
    if (widget.topProductos && widget.topProductos.length) {
        widget.topProductos.forEach(function (p, i) {
            $tp.append(
                '<tr>' +
                '<td><span style="display:inline-block;width:9px;height:9px;border-radius:50%;' +
                    'background:' + _IDX_DOT_COLORS[i] + ';margin-right:7px;vertical-align:middle;"></span>' +
                    p.nombre + '</td>' +
                '<td class="text-right">' + p.cantidad.toLocaleString('en-US') + '</td>' +
                '<td class="text-right">' + fmtMoneyIdx(p.total_ventas) + '</td>' +
                '</tr>'
            );
        });
    } else {
        $tp.append('<tr><td colspan="3" class="text-center text-muted py-3">Sin datos en el período</td></tr>');
    }

    const $ts = $('#tbodySucursales').empty();
    if (widget.rendimientoSucursal && widget.rendimientoSucursal.length) {
        widget.rendimientoSucursal.forEach(function (s, i) {
            $ts.append(
                '<tr>' +
                '<td><span style="display:inline-block;width:9px;height:9px;border-radius:50%;' +
                    'background:' + _IDX_DOT_COLORS[i] + ';margin-right:7px;vertical-align:middle;"></span>' +
                    s.sucursal + '</td>' +
                '<td class="text-right">' + fmtMoneyIdx(s.ventas) + '</td>' +
                '<td class="text-right">' + s.porcentaje + '%</td>' +
                '<td class="text-right">' + s.ordenes.toLocaleString('en-US') + '</td>' +
                '<td class="text-right">' + fmtMoneyIdx(s.ticket_promedio) + '</td>' +
                '</tr>'
            );
        });
    } else {
        $ts.append('<tr><td colspan="5" class="text-center text-muted py-3">Sin datos en el período</td></tr>');
    }
}