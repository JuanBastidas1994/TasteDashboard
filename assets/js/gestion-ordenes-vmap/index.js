let ApiUrl = "https://api.mie-commerce.com/taste/v2";
let ApiUrlMotorizado = "https://api.mie-commerce.com/motorizados/v1";
let ApiKey = "";
let WorkerRecordatorio;
const GOVersion = 2;

let sucursal_id = 0;
let casherId = 0;

let firstTime = true;

$(function() {
    sucursal_id = $("#cod_sucursal").val();
    ApiKey = $("#apikey_empresa").val();
    casherId = $("#casher_id").val();
    // console.log("sucursal_id", sucursal_id);

    if(sucursal_id == 0){
        $("#btnAcceptSelectionModal").hide();
        $("#sectionOffices").show();
    }else{
        //Abrir un modal indicando opciones activas, ejemplo: Impresión, sonido, recursividad, etc!
        $("#btnAcceptSelectionModal").show();
        $("#sectionOffices").hide();
        $("#sectionSettings").removeClass("mt-5");
    }
    verifyVersion();
    //openOfficesSelections();
    initPrinters();

    // Open Mail Sidebar on resolution below or equal to 991px.
    $('.mail-menu').on('click', function(e){
        $(this).parents('.mail-box-container').children('.tab-title').addClass('mail-menu-show')
        $(this).parents('.mail-box-container').children('.mail-overlay').addClass('mail-overlay-show')
    });

    // Close sidebar when clicked on ovelay ( and ovelay itself ).
    $('.mail-overlay').on('click', function(e){
        $(this).parents('.mail-box-container').children('.tab-title').removeClass('mail-menu-show')
        $(this).removeClass('mail-overlay-show')
    });

    //Close Sidebar
    closeSidebar();

    $(".changeTipo").on("click", function(){
        $(".changeTipo").removeClass("active");
        $(this).addClass("active");
        var tipo = $(this).data("value");
        $("#is_envio").val(tipo);
        $(".list-actions").show();
        if(tipo == "delivery")    
            $(".envio-0").hide();
        else if(tipo == "pickup")    
            $(".envio-1").hide();
        else if(tipo == "onsite")    
            $(".envio-1").hide();
        getListaOrdenes();
    });

    /*CLICK EN LOS FILTROS POR ESTADO*/
    $("body").on("click", ".list-actions", function(){
        $(".list-actions").removeClass("active");
        $(this).addClass("active");
        getListaOrdenes();
        updateBadge($(this).attr("id"), true);
    });

    $('body').on('click',".mail-item", function(event) {    //ABRIR LA ORDEN
        $("#modalOrdenesAnteriores").modal("hide");
        var id = $(this).attr("data-value");
        $(this).removeClass("unread-mail");
        openOrden(id);
    });

    //CONSULTAR SI ESTÁ CONFIGURADA LA SUCURSAL
    loadPrintersServices();
    //CONSULTAR SI ESTÁN CONFIGURADOS LOS RECORDATORIOS
    getReminderSettings();
});

$(window).resize(function(event) {
    closeSidebar();
});

function closeSidebar(){
    setTimeout(() => {
        if(!$(".navbar-expand-sm").hasClass("expand-header")){
            $(".sidebarCollapse").trigger("click");
        }
    }, 300);
}

function verifyVersion(){
    let version = 0;
    if (JSON.parse(localStorage.getItem('gestion_ordenes_version')) !== null) {
        version = JSON.parse(localStorage.getItem('gestion_ordenes_version'));   
    }

    if(parseInt(version) < parseInt(GOVersion)){
        $("#bienvenidaModal").modal();
        initCarousel('.wrap-slider');
        setTimeout(() => {
            $(".lottieHide").each(function( index ) {
                $input = $(this);
                // console.log($input.val());

                $input.parent().html('<lottie-player src="'+$input.val()+'"  background="transparent"  speed="1" loop autoplay style="height: 400px;"></lottie-player>');
            });
        }, 500);
    }else{
        openOfficesSelections();
    }
}

function getRecordatorio(){
    if (JSON.parse(localStorage.getItem('recordatorio')) === null) {
        let recordatorio = {
            permiso: 1,
            tiempo: 10,
            asignacion: 0,
            tiempo_asignacion: 10
        }
        localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
        return recordatorio;
    }
    else{
        recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
        return recordatorio;
    }
}

function initPrinters(){
    /*CONFIG PUERTO IMPRESORA*/
    if (JSON.parse(localStorage.getItem('printer')) === null) {
        printer.puerto = "8890";
        localStorage.setItem('printer', JSON.stringify(printer));
    }
    else{
        printer = JSON.parse(localStorage.getItem('printer'));
        $("#txtPuerto").val(printer.puerto);
    }
}

function setConfigGestionOrdenes(data){
    localStorage.setItem('gestion_ordenes', JSON.stringify(data));
}

function getConfigGestionOrdenes(){
    return JSON.parse(localStorage.getItem('gestion_ordenes'));
}

function getConfigBySucursal(office_id){
    //Enviar usuario por cabecera
    var promesa = new Promise(function(resolve, reject){
        fetch(`${ApiUrl}/gestion-ordenes/${office_id}`,{
            method: 'GET',
            headers: {
                'Api-Key':ApiKey,
                'Casher-Id':casherId
            }
        })
        .then(res => res.json())
        .then(response => {
            // console.log(response);
            if(response.success == 1){
                setConfigGestionOrdenes(response.data);
                resolve(response.data);
            }else{
                reject(response.mensaje); 
            }
        })
        .catch(error=>{
            reject('Ocurrió un error al obtener información de la configuracion');
        });
    });
    return promesa;
}

function initConfigGestionOrdenes(){
    getConfigBySucursal(sucursal_id)
        .then(data => {
            //STATUS
            var template = Handlebars.compile($("#status-list").html());
            $("#pills-tab").html(template(data.estados));
            $(".list-actions:first").addClass("active");

            //ORDENES
            getListaOrdenes();

            //SUCURSAL
            // console.log(data);
            data.sucursal.sucursal_input = $("#cod_sucursal").val();
            var template = Handlebars.compile($("#office-selected").html());
            $(".office-selected-detail").html(template(data.sucursal));

            feather.replace();

            //CONFIGURACION RECORDATORIOS
            localStorage.setItem('recordatorio', JSON.stringify(data.casher.recordatorio));

            setUserToFirebase("Online");

            //WORKERS
            initializeWorkers();
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

function getListaOrdenes(){
    let status = $(".list-actions.active");
    if(status[0])
        status = $(".list-actions.active").attr("id");
    else
        status = "ENTRANTE";

    // console.log('status', status);

    let info = {
        "cod_sucursal": sucursal_id,
        "estado": status,
        "tipo": $("#is_envio").val()
    };
    OpenLoad("Buscando ordenes, por favor espere...");
    fetch(`${ApiUrl}/ordenes`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log(response);
            if(response.success == 1){
                let config = getConfigGestionOrdenes();
                let data = {
                    ordenes: response.data,
                    permisos: config.permisos
                }
                var template = Handlebars.compile($("#order-list").html());
                $("#lista_ordenes").html(template(data));
                feather.replace();
            }else{
                let text = "";
                let animation = "https://assets9.lottiefiles.com/packages/lf20_9pdquWhGGG.json";

                switch (status) {
                    case "ENTRANTE":
                        text = "No hay pedidos entrantes";
                        break;
                    case "ASIGNADA":
                        text = "No hay pedidos asignados";
                        break;
                    case "ACEPTADO":
                        text = "No hay pedidos aceptados";
                        break;
                    case "ENVIANDO":
                        text = "No hay pedidos en envío";
                        break;
                    case "ENTREGADA":
                        text = "No hay pedidos entregados";
                        break;
                    case "NO_ENTREGADA":
                        text = "No hay pedidos no entregados";
                        animation = "https://assets5.lottiefiles.com/packages/lf20_5f2kwbj1.json";
                        break;
                    case "ANULADA":
                        text = "No hay pedidos anulados";
                        animation = "https://assets5.lottiefiles.com/packages/lf20_5f2kwbj1.json";
                        break;
                }

                let lottie = {
                    animation,
                    text
                }                    
                let template = Handlebars.compile($("#lottieAnimation").html());
                $("#lista_ordenes").html(template(lottie));
            }
        })
        .catch(error=>{
            CloseLoad();
            // console.log(error);
        });
}

function getOrden(order_id){
    var promesa = new Promise(function(resolve, reject){
		fetch(`${ApiUrl}/ordenes/${order_id}`,{
            method: 'GET',
            headers: {
            'Api-Key':ApiKey
            }
        })
        .then(res => res.json())
        .then(response => {
            // console.log(response);
            if(response.success == 1)
                resolve(response.data);
            else
                reject(response.mensaje); 
        })
        .catch(error=>{
            console.log(error);
            reject('Ocurrió un error al obtener información de la orden');
        });
    });
    return promesa;
}

function openOrden(id){
    apagarSirena();
    OpenLoad("Abriendo orden, por favor espere...");
    getOrden(id)
        .then(order => {
            CloseLoad();
            var template = Handlebars.compile($("#order-detalle-body").html());
            $("#orden-detalle-body").html(template(order));

            var template = Handlebars.compile($("#order-detalle-header").html());
            $("#orden-detalle-header").html(template(order));

            config = getConfigGestionOrdenes();
            order.couriers = config.couriers;
            // console.log(order);
            var template = Handlebars.compile($("#order-detalle-footer").html());
            $("#orden-detalle-footer").html(template(order));

            if($("#mapa").length){
                var mapa = document.getElementById("mapa");
                var latitud = parseFloat(mapa.getAttribute("data-latitud"));
                var longitud = parseFloat(mapa.getAttribute("data-longitud"));
                pos = {lat: latitud, lng: longitud};
                var map = new google.maps.Map(mapa, {
                    zoom: 14,
                    center: pos
                });

                //MARKER CLIENT
                marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    id: 15
                });

                //MARKET SUCURSAL
                marker2 = new google.maps.Marker({
                    position: {
                        lat: parseFloat(order.sucursal.latitud),
                        lng: parseFloat(order.sucursal.longitud),
                    },
                    icon: 'assets/img/marker-office.png',
                    map: map,
                    id: 16
                });
            }

            feather.replace();
            $("#OrdenDetailModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            // console.log(order);

            // INICIALIZAR CLIPBOARD
            if($(".copy-location").length > 0) {
                var clipboard = new Clipboard('.copy-location');
                clipboard.on('success', function(e) {
                    notify('Copiado:'+e.text, 'success', 2);
        
                    console.info('Action:', e.action);
                    console.info('Text:', e.text);
                    console.info('Trigger:', e.trigger);
        
                    e.clearSelection();
                });
            }

            // INICIALIZAR CLIPBOARD
            if($(".user-clipboard").length > 0) {
                var clipboard = new Clipboard('.user-clipboard');
                clipboard.on('success', function(e) {
                    e.text = e.text.replace('+593', '0');
                    if(e.text.substring(0,3) == '593')
                        e.text = '0' + e.text.substring(3, e.text.length);

                    notify('Copiado:'+e.text, 'success', 2);

                    console.info('Action:', e.action);
                    console.info('Text:', e.text);
                    console.info('Trigger:', e.trigger);

                    e.clearSelection();
                });
            }

            $('[data-toggle="popover"]').popover();
        })
        .catch(error=>{
            CloseLoad();
            messageDone(error,'error');
        });
}

function preAsignarOrden(order_id, courier_id, name, cod_flota = 0){
    if(courier_id == 99){   //Mis Motorizados
        let config = getConfigGestionOrdenes();
        if(config.motorizados.length == 0){
            messageDone('No tiene motorizados configurados', 'error')
            return;
        }
        openMisMotorizados(order_id);
    }else if(courier_id == 100){ //Link Motorizado
        openLinkModal(order_id);
    }else if(courier_id == 101){ //Flota adicional
        messageConfirm("Se asignará el pedido al courier "+name+" ¿Deseas continuar?", "", "question")
            .then(function(result) {
                if (result) {
                    asignarOrdenFlota(order_id, cod_flota)
                }
            });
    }else{
        messageConfirm("Se asignará el pedido a "+name+" ¿Deseas continuar?", "", "question")
            .then(function(result) {
                if (result) {
                    asignarOrden(order_id, courier_id, 0)
                }
            });
    }
}

//Asignación orden
function asignarOrden(order_id, courier_id, motorizado_id){
    let info = {
        cod_courier: courier_id,
        cod_orden: order_id,
        motorizado_id: motorizado_id
    }
    fetch(`${ApiUrl}/ordenes/asignar`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log(response);
            if(response.success == 1){
                $("#MisMotorizadosModal").modal('hide');
                $("#OrdenDetailModal").modal('hide');
                messageDone(response.mensaje,'success');
                notify(response.mensaje, "success", 2);
                updateOrderFirebase(order_id, 'ASIGNADA');
                facturar_inventario(order_id, 'ASIGNADA');
                openRecipientes(order_id);
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
}

function asignarOrdenFlota(order_id, flota_id){
    let info = {
        cod_flota: flota_id,
        cod_orden: order_id,
    }
    fetch(`${ApiUrl}/ordenes/asignar-flota`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log(response);
            if(response.success == 1){
                $("#MisMotorizadosModal").modal('hide');
                $("#OrdenDetailModal").modal('hide');
                messageDone(response.mensaje,'success');
                notify(response.mensaje, "success", 2);
                updateOrderFirebase(order_id, 'ASIGNADA');
                facturar_inventario(order_id, 'ASIGNADA');
                openRecipientes(order_id);
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
}

function preCancelarAsignacionOrden(order_id){
    messageConfirm("¿Estás seguro de cancelar la asignación al courier?", "Se cancelará la petición al courier y te deberás volver a asignar la orden", "question")
        .then(function(result) {
            if (result) {
                cancelarAsignacionOrden(order_id);
            }
        });
}

function cancelarAsignacionOrden(order_id){
    OpenLoad("Revirtiendo asignación");
    let info = {
        cod_orden: order_id,
    };
    fetch(`${ApiUrl}/ordenes/cancelar-courier`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log("ORDEN ANULADA", response);
            if(response.success == 1){
                //$("#OrdenDetailModal").modal('hide');
                openOrden(order_id);
                notify(response.mensaje, "success", 2);
                updateOrderFirebase(order_id, 'ANULADA');
                anular_facturar_inventario(order_id, "ASIGNADA");
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
}



//Cancelación orden
function preCancelarOrden(order_id){
    SwalInput("¿Estás seguro de Cancelar la orden?", "Si el pago fue con tarjeta se intentará revertir el pago", "Escriba aquí el motivo de la cancelación")
        .then(function(result) {
            if (result) {
                cancelarOrden(order_id, result);
            }
        });
}

function cancelarOrden(order_id, motivo){
    OpenLoad("Anulando Orden");
    let info = {
        cod_orden: order_id,
        motivo: motivo,
        estado: "ANULADA",
    };
    fetch(`${ApiUrl}/ordenes/cancelar`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log("ORDEN ANULADA", response);
            if(response.success == 1){
                $("#OrdenDetailModal").modal('hide');
                $("#anulacionOrdenModal").modal();
                notify(response.mensaje, "success", 2);
                updateOrderFirebase(order_id, 'ANULADA');
                anular_facturar_inventario(order_id, 'ASIGNADA');

                var templateStatus = Handlebars.compile($("#anular-detalle-template").html());
                if(response.anularPago !== false){
                    $("#anula-pago-orden").show();
                    revertirPago(order_id)
                        .then(pago => {
                            notify(pago, "success", 2);
                            $("#anula-pago-orden").html(templateStatus({"success": 1, "mensaje": pago}));
                        })
                        .catch(error=>{                            
                            notify(error, "danger", 2);
                            $("#anula-pago-orden").html(templateStatus({"success": 0, "mensaje": error}));
                        });
                }
                if(response.anularFactura !== false){
                    $("#anula-factura-orden").show();
                }
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
}

function updateCancelDetail(detalle){
    var template = Handlebars.compile($("#anular-detalle-template").html());
    $(".anularDetalle").html(template(detalle));
    feather.replace();
}

function revertirPago(order_id){
    var promesa = new Promise(function(resolve, reject){
        let info = {
            cod_orden: order_id,
        }
        
        fetch(`${ApiUrl}/ordenes/revertir-pago`,{
                method: 'POST',
                headers: {
                'Api-Key':ApiKey
                },
                body: JSON.stringify(info)
            })
            .then(res => res.json())
            .then(response => {
                // console.log("PAGO REGRESADO", response);
                if(response.success == 1){
                    resolve(response.mensaje);
                }else{
                    reject(response.mensaje); 
                }
            })
            .catch(error=>{
                reject('Error: Ocurrió un error al devolver el dinero');
            });
    });
    return promesa;
}

function preChangeStatus(order_id, estado){
    let title = "";
    let desc = "";
    if(estado == "ACEPTADA"){
        title = "¿Deseas aceptar la orden?";
        desc = "La orden será aceptada";
    }else if(estado == "ENVIANDO"){
        title = "Deseas cambiar el estado de la orden?";
        desc = "La orden cambiará a estado ENVIANDO";
    }else if(estado == "ENTREGADA"){
        title = "Deseas cambiar el estado de la orden?";
        desc = "La orden cambiará a estado ENTREGADA";
    }
    messageConfirm(title, desc, "question")
            .then(function(result) {
                if (result) {
                    changeStatusOrder(order_id, estado);
                }
            });
}

function changeStatusOrder(order_id, estado){
    OpenLoad("Cambiando estado Orden");
    let info = {
        cod_orden: order_id,
        estado: estado,
    };
    fetch(`${ApiUrl}/ordenes/set-estado`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log("ORDEN CAMBIO ESTADO", response);
            if(response.success == 1) {
                if(response.is_envio != undefined) {
                    // console.log("response.is_envio", response.is_envio);
                    if(estado == "ENTREGADA") {
                        calcularPuntosUsuarioByOrden(order_id);
                        if(response.is_envio == 0){
                            facturar_inventario(order_id, 'ASIGNADA');
                            facturar_inventario(order_id, 'ENTREGADA');
                        }
                    }
                }
                notify(response.mensaje, "success", 2);
                $("#OrdenDetailModal").modal('hide');
                removeOrderItem(order_id, estado);
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            messageDone('Ocurrió un error','error');
        });
}

//

function abrirConfiguracion(){
    $("#configModal").modal();
    let config = getConfigGestionOrdenes();
    let sonidoStorage = JSON.parse(localStorage.getItem('sonido'));
    sonidoStorage.volumen = sonidoStorage.volumen * 10;
    var template = Handlebars.compile($("#config-template").html());
    $("#config-detail").html("");
    
    $("#config-detail").append(template({
        "sonidos": getSoundsAndActive(),
        "sonido": sonidoStorage,
        "recordatorio": JSON.parse(localStorage.getItem('recordatorio')),
        "printer": JSON.parse(localStorage.getItem('printer')),
        "permisos": config.permisos
    }));
    feather.replace();

    loadPrintersServices();
}

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

function openOfficesSelections(){
    $("#btnCloseSelectionModal").show();
    if(firstTime){
        firstTime = false;
        $("#btnCloseSelectionModal").hide();
    }
    $("#officesSelectionModal").modal();
}

$(".offices-items").on("click", function(){
    let office = $(this).data();
    sucursal_id = office.id;
    $("#officesSelectionModal").modal('hide');
    initConfigGestionOrdenes();
    testSoundInit();
});


$('#OrdenDetailModal').on('shown.bs.modal', function (e) {
    // do something...
    // console.log("SE ABRIO UNA ORDENNNNN");
})

// HISTORIAL
function abrirHistorial(){
    obtenerHistorialOrdenes();
    $("#historialModal").modal();
}

function obtenerHistorialOrdenes() {
    fetch(`${ApiUrl}/ordenes/historial?sucursal_id=${sucursal_id}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        // console.log(response);
        if(response.success == 1){
            let template = Handlebars.compile($("#history-template").html());
            $("#history-detail").html(template(response.data));
            feather.replace();
        }
        else{
            messageDone(response.mensaje, "error");
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

$("body").on("click", ".history-order", function(){
    let id = $(this).data("order");
    $("#historialModal").modal("hide");
    openOrden(id);
});

$("body").on("click", "#btnAcceptSelectionModal", function(){
    $("#officesSelectionModal").modal('hide');
    initConfigGestionOrdenes();
    testSoundInit();
});
/* 
$('#buscador').on('keyup', function(e) {
    const buscador = this.value.toLowerCase()
    $('.name_product').each(function() {
        const item = $(this).html().toLowerCase()
        if (!item.includes(buscador))
            $(this).parents('.form-row').hide()
        else
            $(this).parents('.form-row').show()
    })
}) */

//WORKERS
function activateWorkers(data){
    data.ApiUrl = ApiUrl;
    data.ApiKey = ApiKey;  
    data.Office = sucursal_id;
    if (typeof(Worker) !== "undefined") {
        if(typeof(WorkerRecordatorio) == "undefined") {
            WorkerRecordatorio = new Worker("assets/js/gestion-ordenes/workers/find_orders_entrantes.js");
            WorkerRecordatorio.onmessage = showResponseWorker;
            WorkerRecordatorio.postMessage(data);    //CONFIGURACION
        }
    } else {
        notify("No es compatible con workers",'error',2);
    }
}

function showResponseWorker(event){
    let response = event.data;
    // console.log("WORKER!!",response);
    if(response.success == 1){
        notify(response.mensaje,'success',8);
        //testSoundInit();
        encenderSirena();

        // Autoasignar órdenes recordadas
        response.ordenes.forEach(orden => {
            let isEnvio = "envio";
            if(orden.is_envio == 0)
                isEnvio = "";
            autoAsignarOrden(orden.cod_orden, isEnvio);
            // console.log("Se debe autoasignar", orden.cod_orden, isEnvio);
        });
    }else if(response.success == -1){
        notify(response.mensaje,'warning',2);
    }else{
        notify(response.mensaje,'error',2);
    }
}

function disableWorkers(){
    if(typeof(WorkerRecordatorio) != "undefined") {
        WorkerRecordatorio.terminate();
        WorkerRecordatorio = undefined;
    }
}

function initializeWorkers(){
    disableWorkers();
    let recordatorio = getRecordatorio();
    if(recordatorio.permiso == 1){
        activateWorkers(recordatorio);
    }
}

function sendNotify(target, topic, title, message, type, cod_usuario = 0){
    /* TARGET: usuarios, motorizados */
    /* TYPES: GENERAL | USUARIOS | PEDIDOS*/

    if(cod_usuario > 0)
        topic = topic + cod_usuario;

    let url = `${ApiUrl}/notificar`;
    if(target == "motorizados")
        url = `${ApiUrlMotorizado}/usuarios/notificar`;

    let info = {
        topic, 
        title, 
        message, 
        type
    }

    fetch(url,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        // console.log("Notificaciones", response);
    })
    .catch(error=>{
        console.log(error);
    });
}

function updateIcons(element, icon, textColor){
    element.html("");
    element.removeClass();
    element.attr("data-feather", icon);
    element.addClass(textColor + " feather-18");
    feather.replace();
}

function getReminderSettings(){
    let recordatorios = localStorage.getItem("recordatorio");
    updateIcons($("#iconReminderStatus"), "x-circle", "text-danger");
    if(recordatorios != null){
        recordatorios = JSON.parse(recordatorios);
        if(recordatorios.permiso == 1){
            updateIcons($("#iconReminderStatus"), "check-circle", "text-success");
        }
    }
}

function initCarousel(className){
    if($(className).length>0){
		$(className).each(function(){
			var data = $(this).data();
			$(this).owlCarousel({
                nav:true,
				itemsCustom:data.itemscustom,
				autoPlay:data.autoplay,
				navigationText:[
                    '<i data-feather="arrow-left"></i>',
                    '<i data-feather="arrow-right"></i>'
                ],
			});
            feather.replace();
		});
	}
}

function omitirBienvenida(){
    localStorage.setItem('gestion_ordenes_version', GOVersion);
    $("#bienvenidaModal").modal('hide');
    openOfficesSelections();
}

/*ORDENES ANTIGUAS*/

function abrirOrdenesAntiguas(){
    //VALIDAR HORA CIERRE
    validarHoraCierre();

    obternerOrdenesAntiguas(); //ORDENES ANTIGUAS Y NO FINALIZADAS
    $("#modalOrdenesAnteriores").modal();
}

function obternerOrdenesAntiguas(){
    fetch(`${ApiUrl}/ordenes/sin-finalizar?sucursal_id=${sucursal_id}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        // console.log(response);
        let target = $("#ordenes-antiguas");
        if(response.success == 1){
            let config = getConfigGestionOrdenes();
            let data = {
                ordenes: response.data,
                permisos: config.permisos
            }
            let template = Handlebars.compile($("#order-list").html());
            target.html(template(data));
            feather.replace();
        }
        else{
            target.html("<p>No hay órdenes</p>");
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function prefinalizarOrdenesAntiguas(){
    Swal.fire({
       title: 'Las órdenes se pondrán en ENTREGADAS',
       text: '¿Desea continuar?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
            finalizarOrdenesAntiguas();
       }
    }); 
}

function finalizarOrdenesAntiguas(){
    // console.log(sucursal_id);
    fetch(`${ApiUrl}/ordenes/finalizar-antiguas?sucursal_id=${sucursal_id}`,{
        method: 'GET',
        headers: {
            'Api-Key':ApiKey
        },
    })
    .then(res => res.json())
    .then(response => {
        // console.log(response);
        if(response.success == 1){
            notify(response.mensaje,'success',2);
        }
        else{
            notify(response.mensaje,'error',2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
    $("#modalOrdenesAnteriores").modal("hide");
}

function validarHoraCierre(){
    let d = new Date;
    let fecha = `${d.getFullYear()}-${d.getMonth()}-${d.getDate()}T`;
    let horaActual = `${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`;
    let go = localStorage.getItem("gestion_ordenes");
    if(go != null){
        go = JSON.parse(go);
        // console.log(go);
        let horaCierre = go.sucursal.hora_fin;
        // console.log("gethours", horaActual, horaCierre, d.getTime(), fecha);
    }
}

function openLinkModal(order_id){
    let config = getConfigGestionOrdenes();
    let couriers = config.couriers;
    
    const courier = couriers.find(courier => courier.id === '100');
    if(!courier) return;
    
    
    SwalText("Generar Link para motorizado", "Proporcionados el número teléfonico del motorizado", "Teléfono (+593)", courier.detalle)
        .then(function(result) {
            if (result) {
                generarLinkMotorizado(order_id, result);
            }
        });
}

function generarLinkMotorizado(order_id, phoneNumber){
    var clipboard = new Clipboard('.linkCopied');
        clipboard.on('success', function(e) {
            notify('Copiado:'+e.text, 'success', 2);
            e.clearSelection();
    });
    
    OpenLoad("Creando Link para el motorizado");
    let info = {
        cod_orden: order_id,
        phone: phoneNumber
    };
    fetch(`${ApiUrl}/ordenes/generar-link`,{
            method: 'POST',
            headers: {
            'Api-Key':ApiKey
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            // console.log("LINK GENERADO", response);
            if(response.success == 1){
                //Limpiar todo
                $("#MisMotorizadosModal").modal('hide');
                $("#OrdenDetailModal").modal('hide');
                notify(response.mensaje, "success", 2);
                updateOrderFirebase(order_id, 'ASIGNADA');
                facturar_inventario(order_id, 'ASIGNADA');
                openRecipientes(order_id);
                
                //ENviar link al motorizado
                let link = `https://pedidos.demo.mie-commerce.com/pedidos/?id=${response.token}`;
                window.open(`https://api.whatsapp.com/send?phone=${response.phone}&text=${link}`, '_blank');
                $(".linkCopied").attr("data-clipboard-text",link);
                $(".linkCopied").click();
                
                Swal.fire({
        	      title: "Enlace generado correctamente",
        	      text: link,
        	      icon: 'success',
        	      allowOutsideClick: false,
        	      showCancelButton: true,
        	      confirmButtonText: 'Copiar el link',
        	      cancelButtonText: 'Cerrar',
        	      closeOnConfirm: false,
        	      padding: '2em',
        	      preConfirm: () => {
        	        //   console.log("Evita el cierre del sweetalert, link copiado!!!");
        	          $(".linkCopied").click();
        	          return false;
        	      }
        	    }).then(function(result) {
        	      if (result.value) {
        	          
        	        $(".linkCopied").click();
        	        return false;
        	      }
        	    });
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            CloseLoad();
            console.log(error);
            messageDone('Ocurrió un error','error');
        });
}


