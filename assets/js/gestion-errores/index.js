/*
 * GESTION DE ERRORES - monitor de SOLO LECTURA
 * Muestra las órdenes de hoy de todas las empresas y sucursales.
 * Consume únicamente GET /gestion-errores/* de api_gestion_ordenes. No ejecuta ninguna acción sobre las órdenes.
 */
const { API_TASTE } = window.__CONFIG__;
const ApiUrl = `${API_TASTE}/gestion-errores`;
const REFRESH_MS = 60000;

let ApiKey = "";
let casherId = 0;

$(function() {
    ApiKey = $("#apikey_empresa").val();
    casherId = $("#casher_id").val();

    loadConfig(true);

    // Tipo de orden
    $(".changeTipo").on("click", function(){
        $(".changeTipo").removeClass("active");
        $(this).addClass("active");
        var tipo = $(this).data("value");
        $("#is_envio").val(tipo);
        getListaOrdenes(true);
    });

    // Estado
    $("body").on("click", ".list-actions", function(){
        $(".list-actions").removeClass("active");
        $(this).addClass("active");
        getListaOrdenes(true);
    });

    // Abrir detalle
    $("body").on("click", ".mail-item", function(){
        openOrden($(this).attr("data-value"));
    });

    // Refresco automático (sin loader) para no tener que recargar la pantalla
    setInterval(function(){
        if(document.hidden) return;
        loadConfig(false);
    }, REFRESH_MS);
});

function apiGet(path){
    return fetch(`${ApiUrl}${path}`, {
        method: 'GET',
        headers: {
            'Api-Key': ApiKey,
            'Casher-Id': casherId
        }
    }).then(res => res.json());
}

/* Estados con su cantidad + indicadores */
function loadConfig(first){
    apiGet('')
        .then(response => {
            if(response.success != 1){
                messageDone(response.mensaje, 'error');
                return;
            }
            let data = response.data;

            // Estados (conserva el activo en los refrescos)
            let activo = $(".list-actions.active").attr("id");
            let template = Handlebars.compile($("#status-list").html());
            $("#pills-tab").html(template(data.estados));
            let current = activo ? $(`.list-actions[id="${activo}"]`) : $();
            (current.length ? current : $(".list-actions:first")).addClass("active");

            // Tipo: ocultar estados que no aplican
            let tipo = $("#is_envio").val();
            if(tipo == "delivery") $(".envio-0").hide();
            else if(tipo == "pickup") $(".envio-1").hide();

            // Indicadores
            $("#preordenesErrors").html(data.errores.preordenesErrors);
            $("#ordenesNoFacturadas").html(data.errores.ordenesNoFacturadas);
            $("#ordenesErrorFacturacion").html(data.errores.ordenesErrorFacturacion);
            $("#ordenesErrorAsignacion").html(data.errores.ordenesErrorAsignacion);
            $("#ordenesCanceledByCourier").html(data.errores.ordenesCanceledByCourier);
            $("#ordenesCanceledByUser").html(data.errores.ordenesCanceledByUser);

            feather.replace();
            getListaOrdenes(first);
        })
        .catch(error => {
            console.log(error);
            if(first) messageDone('Ocurrió un error al obtener la información inicial', 'error');
        });
}

function getListaOrdenes(showLoader){
    let status = $(".list-actions.active").attr("id") || "ENTRANTE";
    let tipo = $("#is_envio").val();

    if(showLoader) OpenLoad("Buscando ordenes, por favor espere...");
    apiGet(`/ordenes?estado=${encodeURIComponent(status)}&tipo=${encodeURIComponent(tipo)}`)
        .then(response => {
            if(showLoader) CloseLoad();
            // Si el usuario cambió de estado mientras llegaba la respuesta, se descarta
            if(($(".list-actions.active").attr("id") || "ENTRANTE") !== status) return;

            if(response.success == 1){
                let template = Handlebars.compile($("#order-list").html());
                $("#lista_ordenes").html(template({ ordenes: response.data }));
            }else{
                let template = Handlebars.compile($("#lottieAnimation").html());
                $("#lista_ordenes").html(template({
                    animation: "https://assets9.lottiefiles.com/packages/lf20_9pdquWhGGG.json",
                    text: response.mensaje
                }));
            }
            feather.replace();
        })
        .catch(error => {
            if(showLoader) CloseLoad();
            console.log(error);
        });
}

function getOrden(order_id){
    return new Promise(function(resolve, reject){
        apiGet(`/ordenes/${order_id}`)
            .then(response => {
                if(response.success == 1) resolve(response.data);
                else reject(response.mensaje);
            })
            .catch(error => {
                console.log(error);
                reject('Ocurrió un error al obtener información de la orden');
            });
    });
}

/* Detalle de solo lectura: sin botones de aceptar, cancelar, asignar ni cambiar estado */
function openOrden(id){
    OpenLoad("Abriendo orden, por favor espere...");
    getOrden(id)
        .then(order => {
            CloseLoad();
            $("#orden-detalle-body").html(Handlebars.compile($("#order-detalle-body").html())(order));
            $("#orden-detalle-header").html(Handlebars.compile($("#order-detalle-header").html())(order));

            if($("#mapa").length){
                var mapa = document.getElementById("mapa");
                var pos = {
                    lat: parseFloat(mapa.getAttribute("data-latitud")),
                    lng: parseFloat(mapa.getAttribute("data-longitud"))
                };
                var map = new google.maps.Map(mapa, { zoom: 14, center: pos });

                new google.maps.Marker({ position: pos, map: map });
                new google.maps.Marker({
                    position: {
                        lat: parseFloat(order.sucursal.latitud),
                        lng: parseFloat(order.sucursal.longitud),
                    },
                    icon: 'assets/img/marker-office.png',
                    map: map
                });
            }

            feather.replace();
            $("#OrdenDetailModal").modal();

            if($(".copy-location").length > 0) {
                var clipboard = new Clipboard('.copy-location');
                clipboard.on('success', function(e) {
                    notify('Copiado:'+e.text, 'success', 2);
                    e.clearSelection();
                });
            }
        })
        .catch(error => {
            CloseLoad();
            messageDone(error, 'error');
        });
}

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});
