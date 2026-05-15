let typeWidget = 'sales';
let period = 'month';
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
    // loadingWidgets();
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
    })
    .catch(error => {
        messageDone(error,'error');
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
    addWidgetText('Número de Ordenes', `${widget.total}`, sharedText);

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
    let PiePlatformData = createPieChartData(widget.topPlatforms, 300);
    let $divChartPlatform = addWidgetText('Ventas por plataforma', '', '', 'piechart', PieData, 320);
    generateChart($divChartPlatform.find('.widget-chart')[0], PiePlatformData);

    //Ordenes por estado
    sharedText = `El día que mas generó fue ${getMaxValue(widget.ordenesPorEstado)}`;
    chartData = createBarChartData(widget.ordenesPorEstado, 'Ventas por Día', 300);
    let $divChartStatuses = addWidgetText('Ordenes por estado', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartStatuses.find('.widget-chart')[0], chartData);

}