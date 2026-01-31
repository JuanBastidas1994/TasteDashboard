try {

    const currentYear = new Date().getFullYear();
    const shortYear = currentYear % 100;

    /*
        ==============================
        |    @Options Charts Script   |
        ==============================
    */

    /*
        =============================
            Daily Sales | Options
        =============================
    */
    var d_2options1 = {
        chart: {
            height: 160,
            type: 'bar',
            stacked: true,
            stackType: '100%',
            toolbar: {
                show: false,
            }
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 1,
        },
        colors: ['#e2a03f', '#e0e6ed'],
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        series: [{
            name: 'Sales',
            data: [44, 55, 41, 67, 22, 43, 21]
        }, {
            name: 'Last Week',
            data: [13, 23, 20, 8, 13, 27, 33]
        }],
        xaxis: {
            labels: {
                show: false,
            },
            categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
        },
        yaxis: {
            show: false
        },
        fill: {
            opacity: 1
        },
        plotOptions: {
            bar: {
                horizontal: false,
                endingShape: 'rounded',
                columnWidth: '25%',
            }
        },
        legend: {
            show: false,
        },
        grid: {
            show: false,
            xaxis: {
                lines: {
                    show: false
                }
            },
            padding: {
                top: 10,
                right: 0,
                bottom: -40,
                left: 0
            },
        },
    }

    /*
        =============================
            Total Orders | Options
        =============================
    */
    var d_2options2 = {
        chart: {
            id: 'sparkline1',
            group: 'sparklines',
            type: 'area',
            height: 280,
            sparkline: {
                enabled: true
            },
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            opacity: 1,
        },
        series: [{
            name: 'Sales',
            data: [28, 40, 36, 52, 38, 60, 38, 52, 36, 40]
        }],
        labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
        yaxis: {
            min: 0
        },
        grid: {
            padding: {
                top: 125,
                right: 0,
                bottom: 36,
                left: 0
            },
        },
        fill: {
            type: "gradient",
            gradient: {
                type: "vertical",
                shadeIntensity: 1,
                inverseColors: !1,
                opacityFrom: .40,
                opacityTo: .05,
                stops: [45, 100]
            }
        },
        tooltip: {
            x: {
                show: false,
            },
            theme: 'dark'
        },
        colors: ['#fff']
    }

    /*
        =================================
            Revenue Monthly | Options
        =================================
    */
    var jsonVentas = atob($("#Respventas").val());
    jsonVentas = JSON.parse(jsonVentas);
    // console.log("jsonVentas", jsonVentas);

    for (i = 0; i < jsonVentas.length; i++) {
        let data = [];
        data = jsonVentas[i]["data"];
        for (j = 0; j < data.length; j++) {
            let value = data[j];
            value = value.replace(',', "");
            data[j] = Number.parseFloat(value).toFixed(2);
        }
        jsonVentas[i]["data"] = data;
    }
    // console.log("jsonVentas2", jsonVentas);

    var options1 = {
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
            text: 'Ingresos del año',
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
            text: 'Ventas por sucursal',
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
        series: jsonVentas,
        labels: ['Ene' + shortYear, 'Feb' + shortYear, 'Mar' + shortYear, 'Abr' + shortYear, 'May' + shortYear, 'Jun' + shortYear, 'Jul' + shortYear, 'Ago' + shortYear, 'Sep' + shortYear, 'Oct' + shortYear, 'Nov' + shortYear, 'Dic' + shortYear],
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
            show: false
        },
        // legend: {
        //     position: 'top',
        //     horizontalAlign: 'right',
        //     offsetY: -50,
        //     fontSize: '16px',
        //     fontFamily: 'Nunito, sans-serif',
        //     markers: {
        //         width: 10,
        //         height: 10,
        //         strokeWidth: 0,
        //         strokeColor: '#fff',
        //         fillColors: undefined,
        //         radius: 12,
        //         onClick: undefined,
        //         offsetX: 0,
        //         offsetY: 0
        //     },
        //     itemMargin: {
        //         horizontal: 0,
        //         vertical: 20
        //     }
        // },
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

    /*
        ==================================
            Sales By Category | Options
        ==================================
    */



    /*
        ==============================
        |    @Render Charts Script    |
        ==============================
    */


    /*
        ============================
            Daily Sales | Render
        ============================
    */
    var d_2C_1 = new ApexCharts(document.querySelector("#daily-sales"), d_2options1);
    d_2C_1.render();

    /*
        ============================
            Total Orders | Render
        ============================
    */
    var d_2C_2 = new ApexCharts(document.querySelector("#total-orders"), d_2options2);
    d_2C_2.render();

    /*
        ================================
            Revenue Monthly | Render
        ================================
    */
    var chart1 = new ApexCharts(
        document.querySelector("#revenueYearly"),
        options1
    );

    chart1.render();

    /*
        =================================
            Sales By Category | Render
        =================================
    */
    var chart = new ApexCharts(
        document.querySelector("#chart-2"),
        options
    );

    chart.render();

    /*
        =============================================
            Perfect Scrollbar | Recent Activities
        =============================================
    */
    const ps = new PerfectScrollbar(document.querySelector('.mt-container'));


} catch (e) {
    console.log(e);
}