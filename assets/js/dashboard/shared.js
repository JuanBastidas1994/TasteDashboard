$("body").on("click", ".btnShare", async function(){
    let { type, title, value, shared, options } = $(this).data();
    console.log(type, options);
    
    if(type !== "text"){
        clearChart();
        $('#share-chart-container').show();
    }

    if(type == "text"){
        if (navigator.share) {
            navigator.share({
                title: title,
                text: shared,
                // url: 'https://app.getglamify.com'
            });
        } else {
            const url = `https://wa.me/?text=${encodeURIComponent(`${title}: ${value}`)}`;
            window.open(url, '_blank');
        }
    }
    else if(type == "barchart"){
        const shareRender = $('#shared-chart-render');
        generateChart(shareRender[0], sharedBarChartData(options, shared)).then((widgetChart) => {
            capturarYCompartir(widgetChart, title, shared);
        }).catch(err => {
            console.error('Error al renderizar el gráfico de barras:', err);
            notify("Error generando el grafico de barras", "danger", 2);
        });
    }
    else if(type == "piechart"){
        const shareRender = $('#shared-chart-render');
        generateChart(shareRender[0], sharedPieChartData(options)).then((widgetChart) => {
            capturarYCompartir(widgetChart, title, shared);
        }).catch(err => {
            notify("Error generando el grafico de pie", "danger", 2);
            console.error('Error al renderizar el gráfico pie:', err);
            
        });
    }
    else{
        widgetChart = $(this).parents('.card').find('.widget-chart')[0];
        capturarYCompartir(widgetChart, title, shared);
    }
    
});

function capturarYCompartir(widgetChart, title, shared, time=300) {
    setTimeout(() => {
        $("#shared-title").html(title);
        $("#shared-footer").html(shared);
        const chartDiv = $('#share-chart-container')[0];
        html2canvas(chartDiv).then(canvas => {
            const imageData = canvas.toDataURL('image/png');
            const blob = dataURItoBlob(imageData);
            const file = new File([blob], "chart.png", { type: "image/png" });
    
            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                navigator.share({
                    files: [file],
                    title: title,
                    text: shared,
                }).then(() => {
                    console.log('Compartido con éxito');
                    notify("Compartido con exito", "success", 2);
                    clearChart();
                }).catch(err => {
                    console.warn('Cancelado o falló el share:', err);
                    notify("Cancelado o falló el share", "danger", 2);
                    clearChart();
                });
            } else {
                notify("Tu navegador no soporta compartir imágenes", "danger", 2);
            }
        }).catch(error => {
            notify("Error generando la captura con html2canvas", "danger", 2);
        });
    }, time + 50);
}


function dataURItoBlob(dataURI) {
    const byteString = atob(dataURI.split(',')[1]);
    const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ab], { type: mimeString });
}

function clearChart(){
    $('#share-chart-container').hide();
    $("#shared-chart-render").empty();
}

function sharedText(title, text){
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            // url: 'https://app.getglamify.com'
        });
    } else {
        const url = `https://wa.me/?text=${encodeURIComponent(`${title}: ${text}`)}`;
        window.open(url, '_blank');
    }
}