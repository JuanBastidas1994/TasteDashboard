let urlApi = "https://api.mie-commerce.com/gestion-errores/v1";
let urlApi2 = "https://api.mie-commerce.com/taste/v2";
let apikey = "";

$(function() {
    apikey = $("#apikey").val();
    initPicker();
});

function getOrdenesNoProcesadas() {
    $("#all-users").hide();
    let cod_empresa = $("#cod_empresa").val();
    let fechaInicio = $("#fecha-inicio").val();
    let fechaFin = $("#fecha-fin").val();
    let target = $("#style-3 tbody");
    target.html("");

    OpenLoad("Cargando...");
    fetch(`${urlApi}/ordenes/no-procesadas-by-dates?cod_empresa=${cod_empresa}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`,{
        method: 'GET',
        headers: {
            'Api-Key': apikey
        },
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        if(response.success == 1) {
            if(response.data.length > 0) {
                $("#all-users").show();
                let template = Handlebars.compile($("#orden-no-procesada-template").html());
                target.html(template(response.data));
            }
            else {
                let template = Handlebars.compile($("#no-orden-no-procesada-template").html());
                target.html(template({text: response.mensaje}));
            }
            feather.replace();
        }
        else if(response.success == 0) {
            let template = Handlebars.compile($("#no-orden-no-procesada-template").html());
            target.html(template({text: response.mensaje}));
            // messageDone(response.mensaje, 'error');
        }
        else {
            messageDone(response.mensaje, 'error');
        }
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
        messageDone("Ocurrió un error",'error');
    });
}

function procesarOrden(cod_usuario, onlyOne=true) {
    OpenLoad("Cargando...");
    fetch(`${urlApi2}/puntos/calcular/${cod_usuario}`,{
        method: 'GET',
        headers: {
            'Api-Key': apikey
        },
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        if(response.success == 1) {
            messageDone(response.mensaje, "success");
            if(onlyOne)
                getOrdenesNoProcesadas();
        }
        else {
            messageDone(response.mensaje, "error");
        }
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
        messageDone("Ocurrió un error",'error');
    });
}

function procesarTodas() {
    Swal.fire({
        title: 'Se procesarán todas las órdenes pendientes',
        text: 'Podría demorar unos minutos ¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function(result){
        if (result.value) {
                let users = $(".tr-users");
                users.each(function() {
                    procesarOrden($(this).data('user-id'), false);
                });
                getOrdenesNoProcesadas();
        }
    }); 
}

function initPicker() {
    let fechaInicio = moment().startOf('month').format('YYYY-MM-DD');
    let fechaFin = moment().endOf('month').format('YYYY-MM-DD');
    let fechaActual = moment().format('YYYY-MM-DD');
   
    flatpickr(document.getElementById('fecha-inicio'), {
        enableTime: false,
        dateFormat: "Y-m-d",
        minDate: "2024-02-01",
        defaultDate: fechaActual
    });
    
    flatpickr(document.getElementById('fecha-fin'), {
        enableTime: false,
        dateFormat: "Y-m-d",
        defaultDate: fechaActual
    });

    getOrdenesNoProcesadas();
}