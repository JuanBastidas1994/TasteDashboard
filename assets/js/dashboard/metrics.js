let chartColors = [
    '#2962FF', // azul
    '#00C853', // verde
    '#FF6D00', // naranja
    '#D50000', // rojo
    '#AA00FF', // morado
    '#00B8D4', // cian
    '#FFD600', // amarillo
    '#FF4081', // rosa
    '#37474F', // gris oscuro (solo uno)
    '#795548'  // marrón
];
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
    chartData = createBarChartData(widget.deliveryTotals, 'Ventas por Hora', 300);
    // chartData.plotOptions.bar.horizontal = true;
    let $divChartTypeDelivery = addWidgetText('Ventas por tipo de Entrega', '', sharedText, 'barchart', chartData, 320);
    generateChart($divChartTypeDelivery.find('.widget-chart')[0], chartData);

    //Top Payment Methods
    let PieData = createPieChartData(widget.clientes_recurrentes, 300);
    $divChartPayments = addWidgetText('Clientes Recurrentes', '', '', 'piechart', PieData, 320);
    generateChart($divChartPayments.find('.widget-chart')[0], PieData);

    //Ticket Promedio
    sharedText = `Este mes generé $${widget.ticketPromedio} desde Taste 📈`;
    addWidgetText('Ticket Promedio', `$${widget.ticketPromedio}`, sharedText);
    // addWidgetText('Indicador 2', `$${widget.ticketPromedio}`, sharedText);
    // addWidgetText('Indicador 3', `$${widget.ticketPromedio}`, sharedText);

}