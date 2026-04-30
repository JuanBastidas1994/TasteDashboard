let ApiUrl = "https://api.mie-commerce.com/front";
let Path = "/ordenes/preorden";
let table;

$(function () {
    initDatatable();

    $("#filtroEmpresa, #filtroEstado").on("change", function () {
        table.ajax.reload();
    });
});

function initDatatable() {
    table = $('#style-3').DataTable({
        processing: true,
        serverSide: true,
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        columnDefs: [{ visible: false, targets: [11] }],
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        ajax: {
            url: 'controllers/controlador_preordenes_full.php?metodo=datatable',
            type: 'GET',
            data: function (d) {
                d.cod_empresa    = $("#filtroEmpresa").val();
                d.estado_filtro  = $("#filtroEstado").val();
                return d;
            },
            error: function (e) {
                console.log(e);
            },
            complete: function () {
                feather.replace();
                if ($(".copy").length > 0) {
                    var clipboard = new Clipboard('.copy');
                    clipboard.on('success', function (e) {
                        notify('Copiado', 'success', 2);
                        e.clearSelection();
                    });
                }
                $(".bs-tooltip").tooltip();
            }
        }
    });
}

$("body").on("click", ".btnCrearOrden", async function () {
    let tr          = $(this).parents("tr");
    let json        = $(this).data("orden");
    let cod_preorden = $(this).data("preorden");
    let apiKey      = $(this).data("apikey");

    if (!cod_preorden || cod_preorden == '') {
        messageDone("Falta ID de la preorden", "error");
        return;
    }

    if (!apiKey || apiKey == '') {
        messageDone("La empresa no tiene API Key configurada", "error");
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
            crearOrden(JSON.stringify(json), tr, cod_preorden, value, apiKey);
        }
    });
});

function crearOrden(json, tr, cod_preorden, paymentId, apiKey) {
    const paymentAuth = 'unknown';
    let xson = JSON.parse(json);
    let metodoPago = xson.metodoPago.find((m) => m.tipo === 'T');
    let paymentProvider = metodoPago ? 2 : 0;
    let body = JSON.stringify({ cod_preorden, paymentId, paymentAuth, paymentProvider });

    if (!json) {
        messageDone("Falta JSON de la orden", "error");
        return;
    }

    OpenLoad("Creando orden...");
    fetch(ApiUrl + Path, {
        method: 'POST',
        headers: {
            'Api-Key': apiKey
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
            } else {
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
    };
    fetch(`controllers/controlador_preordenes_full.php?metodo=actualizarEstadoPreorden`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
        .then(res => res.json())
        .then(response => {
            if (response.success == 1) {
                messageDone(response.mensaje);
            } else {
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
    $("#copyPreorder").attr("data-clipboard-text", json);

    try {
        json = JSON.parse(json);
    } catch (error) {
        messageDone("El formato del JSON no es válido", "error");
        return;
    }
    let jsonFormatted = new JsonEditor('#json-display', json);
    jsonFormatted.load(json);
});
