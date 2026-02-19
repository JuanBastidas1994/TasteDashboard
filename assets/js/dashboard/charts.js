function addWidgetText(title, value, shared='', type = 'text', chartdata=[], height=120, column=4 ){
    const $divWidget = $(template({
        title: title,
        value: value,
        sharedText: shared,
        type: type,
        chartdata: JSON.stringify(chartdata),
        height: height,
        column: column
    }));
    $("#widgetsInfo").append($divWidget);
    return $divWidget;
}

function generateChart($el, data){
    const chart = new ApexCharts($el, data);
    return chart.render().then(() => $el);
}

function createBarChartData(data, title='', height=100, shared=false){
    const labels = Object.keys(data);
    const values = Object.values(data);

    let config = {
        chart: { type: 'bar', height: height, toolbar: { show: false }, sparkline: { enabled: false }, animations: { enabled: true } },
        series: [{ name: title, data: values }],
        xaxis: { 
            categories: labels,
            labels: {
                show: true,
                rotate: -45, // opcional, para que no se solapen
                style: { fontSize: '12px', fontFamily: 'inherit' }
            }
        },
        yaxis: {
            show: true,
            labels: {
                show: true,
                style: { fontSize: '12px', fontFamily: 'inherit' }
            }
        },
        grid: { show: true },
        colors: chartColors,
        plotOptions: { bar: { columnWidth: '80%', borderRadius: 4, distributed: true } }, 
        dataLabels: { enabled: true },
        states: {
            normal: { filter: { type: 'none' } },
            hover: { filter: { type: 'darken', value: 0.5 } }, 
            active: { filter: { type: 'none' } }
        },
        tooltip: {
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                const label = w.globals.labels[dataPointIndex];
                const value = series[seriesIndex][dataPointIndex];
                return `<div style="padding:5px; font-size:14px;">${label} <strong>${value}</strong></div>`;
            },
            style: { fontSize: '14px', fontFamily: 'inherit' }
        }
    }
    return config;
}

function createPieChartData(data, height=250, title){
    const labels = Object.keys(data);
    const values = Object.values(data);
    let config = {
        chart: { type: 'pie', height: height, toolbar: { show: false }, animations: { enabled: true } },
        labels: labels,
        series: values,
        colors: chartColors,
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.globals.labels[opts.seriesIndex] + ': ' + val.toFixed(1) + '%';
            }
        },
        tooltip: {
            y: {
                formatter: function (val, opts) {
                    return val;
                }
            }
        },
        legend: {
            show: true,
            position: 'bottom',
            fontSize: '14px',
            formatter: function(val, opts) {
                // Mostrar valor numérico al lado del label
                const value = opts.w.globals.series[opts.seriesIndex];
                return `${val}: ${value}`;
            }
        },
    }
    return config;
}

function createLineChartData(trend, title = '', height = 300) {

    let config = {
        chart: {
            type: 'line',
            height: height,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true }
        },

        series: trend.series,   // 👈 ya viene listo del backend

        xaxis: {
            categories: trend.categories,
            labels: {
                show: true,
                rotate: -45,
                style: { fontSize: '12px', fontFamily: 'inherit' }
            }
        },

        yaxis: {
            labels: {
                formatter: function (value) {
                    return '$' + value.toLocaleString();
                },
                style: { fontSize: '12px', fontFamily: 'inherit' }
            }
        },

        stroke: {
            curve: 'smooth', // smooth | straight | stepline
            width: 3
        },

        markers: {
            size: 4,
            hover: {
                size: 6
            }
        },

        grid: {
            show: true,
            strokeDashArray: 4
        },

        colors: chartColors,

        dataLabels: {
            enabled: false
        },

        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left'
        },

        tooltip: {
            shared: true,   // 👈 importante si luego agregas más sucursales
            intersect: false,
            y: {
                formatter: function (value) {
                    return '$' + value.toLocaleString();
                }
            }
        }
    };

    return config;
}


//COMPARTIR
function sharedBarChartData(config, title){
    console.log(config);
    config.chart.sparkline = { enabled: false };
    config.chart.animations.enabled = false;
    config.chart.height = 500;
    config.yaxis.show = true;

    config.xaxis.labels = { show: true };
    config.yaxis.labels = { show: true };
    config.grid.show = true;
    config.dataLabels.enabled = true;
    config.title = {
        text: '',
        align: 'center',
        style: { fontSize: '14px', fontWeight: 'bold' }
    };
    return config;
}

function sharedPieChartData(config, title){
    console.log(config);
    config.chart.sparkline = { enabled: false };
    config.chart.height = 500;
    config.chart.animations = { enabled: false };

    config.dataLabels = {
        enabled: true,
        formatter: function (val, opts) {
            const label = opts.w.globals.labels[opts.seriesIndex];
            return `${label}: ${val.toFixed(1)}%`;
        },
        style: {
            fontSize: '14px'
        }
    };

    config.legend = {
        show: true,
        position: 'bottom',
        fontSize: '14px'
    };

    config.title = {
        text: '',
        align: 'center',
        style: { fontSize: '14px', fontWeight: 'bold' }
    };
    return config;
}

function getMaxValue(element){
    if (!element || typeof element !== 'object' || Array.isArray(element)) return "";

    const keys = Object.keys(element);
    if (keys.length === 0) return "";

    let maxKey = keys.reduce((a, b) => {
        return element[a] > element[b] ? a : b;
    });
    return maxKey;
}