$(function () {
    getOrdenesNoProcesadasAll()
});

function getOrdenesNoProcesadasAll() {
    $("#ordenesNoProcesadas").html(0);

    fetch(`${ApiUrl}/ordenes/no-procesadas`, {
        method: 'GET',
        headers: {
            'Api-Key': ApiKey
        },
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                $("#ordenesNoProcesadasValue").html(response.data[0].cantidad);
            }
        })
        .catch(error => {
            console.log(error);
        });
}

function getOrdenesNoProcesadas() {
    fetch(`${ApiUrl}/ordenes/no-procesadas?agrupar=true`, {
        method: 'GET',
        headers: {
            'Api-Key': ApiKey
        },
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                let target = $("#ordenesNoProcesadas");
                let template = Handlebars.compile($("#order-no-proceda-template").html());
                target.html(template(response.data));

                feather.replace();

                $("#ordenesNoProcesadasModal").modal();
            }
            else {
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error => {
            console.log(error);
        });
}