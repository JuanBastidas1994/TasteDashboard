let ApiUrl = "https://api.mie-commerce.com/taste-front/v6";
let ApiKey = "";
let Path = "/ordenes/preorden";
let tr = "";
const apiv4Array = [
    'Api-BOLONCITYa151AWE215',
    'API-roll-it-X8MDQZN2I1Z209B',
    'API-portones-QNDMYI46O6JL9LY',
];

$(function () {
    initDatatable();
    ApiKey = $("#apiEmpresa").val();
});

function initDatatable() {
    $('#style-3').dataTable({
        processing: true,
        serverSide: true,
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        ajax: {
            url: 'controllers/controlador_preordenes.php?metodo=datatable',
            type: 'GET',
            error: function (e) {
                console.log(e);
            },
            complete: function () {
                feather.replace();
                if ($(".copy").length > 0) {
                    var clipboard = new Clipboard('.copy');
                    clipboard.on('success', function (e) {
                        notify('Copiado', 'success', 2);

                        console.info('Action:', e.action);
                        console.info('Text:', e.text);
                        console.info('Trigger:', e.trigger);

                        e.clearSelection();
                    });
                }
                $(".bs-tooltip").tooltip();
            }
        }
    });
}

$("body").on("click", ".btnCrearOrden", async function () {
    let tr = $(this).parents("tr");
    let json = $(this).data("orden");
    let cod_preorden = $(this).data("preorden");

    if (!cod_preorden || cod_preorden == '') {
        messageDone("Falta ID de la preorden", "error");
        return;
    }

    const { value: result } = await Swal.fire({
        title: 'Se creará la orden',
        text: '¿Continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em',
        input: "text",
        inputPlaceholder: "Ingresa ID de transacción (opcional)",
        inputValue: "",
        inputValidator: (value) => {
            if (value)
                json.paymentId = value;
            crearOrden(JSON.stringify(json), tr, cod_preorden, value);
          }
    });
});

function crearOrden(json, tr, cod_preorden, paymentId) {
    const paymentAuth = 'unknown';
    let xson = JSON.parse(json);
    console.log(xson);
    let metodoPago = xson.metodoPago.find((m) => m.tipo === 'T');
    let paymentProvider = metodoPago ? 2 : 0; // POR DEFECTO NUVEI
    let body = JSON.stringify({ cod_preorden, paymentId, paymentAuth, paymentProvider});

    if (!json) {
        messageDone("Falta JSON de la orden", "error");
        return;
    }

    if (ApiKey == "") {
        messageDone("Falta APIKEY", "error");
        return;
    }

    if(ApiKey === 'API-sambolon-GV4H5TZCZBXG-FC') {
        ApiUrl = 'https://api.mie-commerce.com/sambolon/v1';
        Path = '/ordenes';
        body = json;
    }

    if(ApiKey === 'Api-OAHU154662154'){
        const order = JSON.parse(json);
        if(order.origen == 'IOS' || order.origen == 'ANDROID') {
            ApiUrl = 'https://api.mie-commerce.com/v10';
            Path = '/ordenes';
            body = json;
        }
    }
    
    if(apiv4Array.includes(ApiKey)){
        const order = JSON.parse(json);
        if(order.origen == 'IOS' || order.origen == 'ANDROID')
            ApiUrl = 'https://api.mie-commerce.com/taste-front/v4';
    }

    OpenLoad("Creando orden...");
    fetch(ApiUrl + Path, {
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body
    })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                tr.remove();
                messageDone(response.mensaje);
                preordenCreada(cod_preorden, response.id);
            }
            else {
                messageDone(response.mensaje, "error");
            }
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
            messageDone("Ocurrió un error", "error");
        });
}

function preordenCreada(cod_preorden, cod_orden) {
    let info = {
        cod_preorden,
        cod_orden,
        estado: 'PAGADA'
    }
    fetch(`controllers/controlador_preordenes.php?metodo=actualizarEstadoPreorden`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response.success == 1) {
                messageDone(response.mensaje);
            }
            else {
                messageDone(response.mensaje, "error");
            }
        })
        .catch(error => {
            console.log(error);
            messageDone("Ocurrió un error", "error");
        });
}

$("body").on("click", ".btnOpenPreorden", function () {

    let json = $(this).attr("data-preorden");
    $("#txtJson").html(JSON.stringify(json));
    $("#modalPreorden").modal();

    /* let clipb = new Clipboard('#copyPreorder');
    clipb.on('success', function (e) {
        notify(e.text, 'success', 2);

        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
    }); */

    $("#copyPreorder").attr("data-clipboard-text", "hola");

    try {
        json = JSON.parse(json);
    } catch (error) {
        messageDone("El formato del JSON no es válido", "error");
        return;
    }
    let jsonFormatted = new JsonEditor('#json-display', json);
    jsonFormatted.load(json);
});