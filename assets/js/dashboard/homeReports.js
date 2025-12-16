$(function () {
    moment.locale('es-mx');
    getSalesMonthly();
    getSalesWeekly();
    getRanking();

    getOffices();
    getMonths();
    getMonthlySalesByOrigin();
});

$('.revenueYearly').on('click', function() {
    hideGraphics('yearly');
});
$('.revenueMonthly').on('click', function() {
    hideGraphics('monthly');
});
$('.revenueWeekly').on('click', function() {
    hideGraphics('weekly');
});

function hideGraphics(graphic) {
    $('#tab-revenueYearly').addClass('d-none');
    $('#tab-revenueMonthly').addClass('d-none');
    $('#tab-revenueWeekly').addClass('d-none');

    if(graphic == 'yearly')
        $('#tab-revenueYearly').removeClass('d-none');
    else if(graphic == 'monthly')
        $('#tab-revenueMonthly').removeClass('d-none');
    else
        $('#tab-revenueWeekly').removeClass('d-none');
}

function getSalesMonthly() {
    const day = moment().daysInMonth();
    const month = (moment().month() + 1).toString().padStart(2, '0');
    const year = moment().year();
    const dateStart = `${year}-${month}-01`;
    const dateEnd = `${year}-${month}-${day}`;

    fetch(`controllers/controlador_reporte_ventas.php?metodo=getSalesDayByDay&dateStart=${dateStart}&dateEnd=${dateEnd}&numDays=${day}`, {
        method: 'GET',
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                let sales = [];
                let labels = [];
                response.data.forEach((item, index) => {
                    labels.push(index + 1);
                    sales.push(parseFloat(item.total).toFixed(2));
                });

                let series = [
                    {
                        name: 'Ventas',
                        data: sales
                    }
                ];

                let options = loadGraphic('Ventas totales', 'Ingreso del mes actual', series, labels);
                const chart = new ApexCharts(
                    document.querySelector("#revenueMonthly"),
                    options
                );
                chart.render();
            }
            else {
            }
        })
        .catch(error => {
            console.log(error);
        });
}

function getSalesWeekly() {
    const dateStart = moment().startOf('week');
    console.log({dateStart});
    const dayZero = dateStart; // dateStart.subtract(1, 'day');
    const days = [];
    for (let index = 1; index <= 7; index++) {
        days.push(dayZero.add(1, 'day').format('YYYY-MM-DD'));
    }

    const info = { days };

    fetch(`controllers/controlador_reporte_ventas.php?metodo=getSalesDayByDay2`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                let sales = [];
                let labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                response.data.forEach((item, index) => {
                    sales.push(parseFloat(item.total).toFixed(2));
                });

                let series = [
                    {
                        name: 'Ventas',
                        data: sales
                    }
                ];

                let options = loadGraphic('Ventas totales', 'Ingreso de la semana actual', series, labels);
                const chart = new ApexCharts(
                    document.querySelector("#revenueWeekly"),
                    options
                );
                chart.render();
            }
            else {
            }
        })
        .catch(error => {
            console.log(error);
        });
}

function getRanking() {
    fetch(`controllers/controlador_reporte_ventas.php?metodo=getRanking`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        let rows = "";
        if(response.success == 1) {
            response.data.forEach((item, index) => {
                rows+=`<tr>
                    <td class="text-center">${ getPosition(index) }</td>
                    <td>${ item.dia_semana }</td>
                    <td class="text-right">$${ parseFloat(item.total_ventas).toFixed(2) }</td>
                </tr>`;
            });

            $("#ranking-table tbody").html(rows);
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function getPosition(position) {
    position = position + 1;
    let html = `<span>${ position }</span>`;

    if(position <=3 )
        html = `<img src="./assets/img/reports/medal${ position }.png" width="25"/>`;

    return html;
}

function loadGraphic(title, subtitle, series, labels) {
    return {
        chart: {
            fontFamily: 'Nunito, sans-serif',
            height: 365,
            type: 'area',
            zoom: {
                enabled: false
            },
            dropShadow: {
                enabled: true,
                opacity: 0.3,
                blur: 5,
                left: -7,
                top: 22
            },
            toolbar: {
                show: false
            },
        },
        colors: ['#1b55e2', '#e7515a', '#ffc107'],
        dataLabels: {
            enabled: false
        },
        markers: {
            discrete: [{
                seriesIndex: 0,
                dataPointIndex: 7,
                fillColor: '#000',
                strokeColor: '#000',
                size: 5
            }, {
                seriesIndex: 2,
                dataPointIndex: 11,
                fillColor: '#000',
                strokeColor: '#000',
                size: 4
            }]
        },
        subtitle: {
            text: subtitle,
            align: 'left',
            margin: 0,
            offsetX: -10,
            offsetY: 35,
            floating: false,
            style: {
                fontSize: '14px',
                color: '#888ea8'
            }
        },
        title: {
            text: title,
            align: 'left',
            margin: 0,
            offsetX: -10,
            offsetY: 0,
            floating: false,
            style: {
                fontSize: '25px',
                color: '#0e1726'
            },
        },
        stroke: {
            show: true,
            curve: 'smooth',
            width: 2,
            lineCap: 'square'
        },
        series,
        labels,
        xaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                show: true
            },
            labels: {
                offsetX: 0,
                offsetY: 5,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif',
                    cssClass: 'apexcharts-xaxis-title',
                },
            }
        },
        yaxis: {
            labels: {
                formatter: function (value, index) {
                    return '$' + value.toFixed(2);
                },
                offsetX: -22,
                offsetY: 0,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif',
                    cssClass: 'apexcharts-yaxis-title',
                },
            }
        },
        grid: {
            borderColor: '#e0e6ed',
            strokeDashArray: 5,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: false,
                }
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: -10
            },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -50,
            fontSize: '16px',
            fontFamily: 'Nunito, sans-serif',
            markers: {
                width: 10,
                height: 10,
                strokeWidth: 0,
                strokeColor: '#fff',
                fillColors: undefined,
                radius: 12,
                onClick: undefined,
                offsetX: 0,
                offsetY: 0
            },
            itemMargin: {
                horizontal: 0,
                vertical: 20
            }
        },
        tooltip: {
            theme: 'dark',
            marker: {
                show: true,
            },
            x: {
                show: false,
            }
        },
        fill: {
            type: "gradient",
            gradient: {
                type: "vertical",
                shadeIntensity: 1,
                inverseColors: !1,
                opacityFrom: .28,
                opacityTo: .05,
                stops: [45, 100]
            }
        },
        responsive: [{
            breakpoint: 575,
            options: {
                legend: {
                    offsetY: -30,
                },
            },
        }]
    }
}

// Monthly Sales Graphic
let monthlySalesChart;

function getMonths() {
    $("#monthly-sales-month-select").html('');
    moment.months().forEach((month, index) => {
        const monthSelected = (moment().month() == index) ? 'selected' : '';
        $("#monthly-sales-month-select").append(`<option value="${ (index + 1).toString().padStart(2, '0') }" ${monthSelected}>${ month.charAt(0).toUpperCase() + month.slice(1).toLowerCase() }</option>`);
    });
}

function getOffices() {
    $("#monthly-sales-office-select").html('<option value="">Todas las sucursales</option>');
    
    fetch(`controllers/controlador_sucursal.php?metodo=lista`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            response.data.forEach((item)=>{
                $("#monthly-sales-office-select").append(`<option value="${ item.cod_sucursal }">${ item.nombre }</option>`);
            });
        }
        else{
            console.log(response);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function getMonthlySalesByOrigin() {
    $("#successful-sales-chart").addClass('d-none');
    $("#error-sales-chart").addClass('d-none');

    const month = $("#monthly-sales-month-select").val();
    const office = $("#monthly-sales-office-select").val() !== '' ? $("#monthly-sales-office-select").val() : 0;
    const currentDate = moment().month(month - 1);
    const dateStart = currentDate.startOf("month").format("YYYY-MM-DD");
    const dateEnd = currentDate.endOf("month").format("YYYY-MM-DD");

    fetch(`controllers/controlador_reporte_ventas.php?metodo=getMonthlySales&month=${month}&sucursal=${office}&fIni=${dateStart}&fFin=${dateEnd}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log("getMonthlySales", response);
        if(response.success == 1) {
            loadMonthlySalesGraphic(response.data);
            $("#successful-sales-chart").removeClass('d-none');
        } 
        else {
            $("#error-sales-chart").removeClass('d-none');
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function loadMonthlySalesGraphic(data) {
    const origin = data.map(item => item.medio_compra);
    const values = data.map(item => parseInt(item.cantidad));

    const options = {
        chart: {
            type: 'donut',
            width: 380
        },
        colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            markers: {
                width: 10,
                height: 10,
            },
            itemMargin: {
                horizontal: 0,
                vertical: 8
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '29px',
                            fontFamily: 'Nunito, sans-serif',
                            color: undefined,
                            offsetY: -10
                        },
                        value: {
                            show: true,
                            fontSize: '26px',
                            fontFamily: 'Nunito, sans-serif',
                            color: '20',
                            offsetY: 16,
                            formatter: function (val) {
                                return val
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            color: '#888ea8',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce(function (a, b) {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        stroke: {
            show: true,
            width: 25,
        },
        series: values,
        labels: origin,
        responsive: [{
            breakpoint: 1599,
            options: {
                chart: {
                    width: '350px',
                    height: '400px'
                },
                legend: {
                    position: 'bottom'
                }
            },

            breakpoint: 1439,
            options: {
                chart: {
                    width: '250px',
                    height: '390px'
                },
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                        }
                    }
                }
            },
        }]
    }


    if(monthlySalesChart) {
        monthlySalesChart.destroy();
    }

    monthlySalesChart = new ApexCharts(
        document.querySelector("#successful-sales-chart"),
        options
    );

    monthlySalesChart.render();
} 