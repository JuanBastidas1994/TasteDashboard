let showNewOrdersFirebase = false;
let timeOutPage = null;
$(function() {    
    //LISTA DE ORDENES
    db.ref('ordenes').once('value', function(data) {
        
        console.log("Firebase Ordenes",data.val());

        let companies = data.val();
        Object.keys(companies).forEach(key => {
            let aliasCompany = key;
            db.ref('ordenes/'+aliasCompany).on('child_added', function(data){
                if(showNewOrdersFirebase){
                    console.log("ADDED", data.val());
                    procesarOrdenNueva(data.val());
                }
            });
            db.ref('ordenes/'+aliasCompany).on('child_changed', function(data){
                if(showNewOrdersFirebase){
                    console.log("CHANGED", data.val());
                    procesarOrdenEditada(data.val());
                }
            });

        });
        setTimeout(() => {
            showNewOrdersFirebase = true;
        }, 3000);
    });

    function procesarOrdenNueva(orden){ //ORDENES NUEVAS
        console.log("Last orden", orden);
        updateBadge(orden.estado);
        //showToastNewOrder(orden);
        //sendNotificationNewOrder(orden.id);

        //Si el estado al que cae esta escogido se refresca
        if($("#ENTRANTE").hasClass("active")){
            console.log("Estado entrante esta activo, REFRESCAR LISTA DE ORDENES!!!");
            getListaOrdenes();
        }else{
            console.log("Estado entrante no esta activo en pantalla");
        }
        
        if(parseInt(orden.sonar) == 1)
            encenderSirena();
    }

    function procesarOrdenEditada(orden){   //ORDENES ACTUALIZADAS
        console.log("CHANGED", orden);
        updateBadge(orden.estado);

        //Verificar si orden esta en pantalla
        if($("#orden"+orden.id).length){
            let $ordenItem = $("#orden"+orden.id);
            if($ordenItem.data("estado") !== orden.estado){
                if(orden.estado != "PUNTO_RECOGIDA" && orden.estado != "PUNTO_ENTREGA"){
                    $ordenItem.hide("slow", function(){ $(this).remove(); });
                }else{
                    notify('Cambio a PUNTO RECOGIDA o PUNTO ENTREGA', 'warning', 2);
                }
            }
        }

        //Si el estado al que cae esta escogido se refresca
        if($("#"+orden.estado).hasClass("active")){
            getListaOrdenes();
        }
        
        //Si la orden fue ENTREGADA, debe facturar la orden
        if(orden.estado === "ENTREGADA"){
            successSoundPlay();
            console.log("ORDEN ENTREGADA, ENVIANDO A FACTURAR");
        }

        //Una orden el courier no la entrego!!
        if(orden.estado === "NO_ENTREGADA"){
            errorSoundPlay();
            console.log("ORDEN CANCELADA POR EL COURIER", orden.id);
        }
    }


    //IS CONECTED??
    db.ref(".info/connected").on('value', function(snap){
        if (snap.val() == true) {
            console.log("FIREBASE CONECTED TRUE");
            onFirebase();
        } else {
            console.log("FIREBASE CONECTED FALSE");
        }
    });
});

function showToastNewOrder(orden){
    let envioIcon = "truck";
    let envioText = "Delivery";
    let paymentIcon = "credit-card";
    let paymentText = "Tarjeta";

    if(orden.forma_pago == "E"){
        paymentIcon = "dollar-sign";
        paymentText = "Efectivo";
    }else if(orden.forma_pago == "TB"){
        paymentIcon = "smartphone";
        paymentText = "Transferencia";
    }else if(orden.forma_pago == "DB"){
        paymentIcon = "trello";
        paymentText = "Depósito";
    }else if(orden.forma_pago == "TC"){
        paymentIcon = "credit-card";
        paymentText = "Tarjeta en casa";
    }else if(orden.forma_pago == "P"){
        paymentIcon = "start";
        paymentText = "Puntos";
    }

    if(orden.envio !== "envio"){
        envioIcon = "box";
        envioText = "Pickup";
    }

    let toastJc = new ToastJc({
        title: "Orden nueva #" + orden.id,
        position: "bottom-left",
        delay: 20000,
        icon: "success",
        progress: true,
        showProgressTimer: false,
        description: `<div class="d-flex">
            <div>
                <i data-feather="${paymentIcon}"></i> ${paymentText}
            </div>
            <div class="ml-auto">
                <i data-feather="${envioIcon}"></i> ${envioText}
            </div>
            <div class="ml-3">
                <b>$${orden.total}</b>
            </div>
        </div>`
    });
    toastJc.on('render', (data) => {
        feather.replace();
    } );
    toastJc.on('click', (data) => {
        console.log("Toast click", data);
        openOrden(data);
    } );
    toastJc.open(orden.id);
}

function removeOrderItem(order_id, estado){
    if($("#orden"+order_id).length){
        let $ordenItem = $("#orden"+order_id);
        if($ordenItem.data("estado") !== estado){
            $ordenItem.hide("slow", function(){ $(this).remove(); })
        }
    }
}

function updateBadge(estado, reset = false){
    let $badge = $(".badge-"+estado);
    let count = "";
    if(!reset){
        count = ($badge.text() === "") ? 0 : parseInt($badge.text());
        count = count + 1;
    }
    $badge.html(count);
}

function updateOrderFirebase(order_id, status){
    /*db.ref(firebaseColumn+'/'+order_id).update({
        estado: status
    });*/
}

function offFirebase(){
    db.goOffline();
    console.log("SE APAGO EL FIREBASE");
}

function onFirebase(){
    db.goOnline();
    console.log("SE ENCENDIO EL FIREBASE");
}


//VERIFICAR CONEXION
window.addEventListener('online', isOnline);
window.addEventListener('offline', isOnline);

function isOnline(){
	if(navigator.onLine){
		notify('Se ha reestablecido la conexión a internet', 'success', 2);
        onFirebase();
        $(".alert-box-disconect").hide();
	}else{
		notify('Se ha perdido tu conexión a internet', 'error', 2);
        offFirebase();
        $(".alert-box-disconect").show();
        sendNotificationOffline();
	}
}

function sendNotificationOffline(){
    console.log(Notification.permission);
    if(Notification.permission === "granted"){
        const notification = new Notification("Sin conexión",{
            body: 'Se ha perdido la conectividad con internet!',
            icon: 'assets/img/couriers/digital.png',
            vibrate: true
        });

        notification.addEventListener('click', (event) => {
            console.log(event);
            parent.focus();
            this.close();
        });
    }else{
        notify('Activa las notificaciones por favor', 'error', 2);
    }
}

function sendNotificationNewOrder(order_id){
    console.log(Notification.permission);
    if(Notification.permission === "granted"){
        const notification = new Notification("Nueva Orden",{
            body: 'Orden nueva #' + order_id,
            icon: 'assets/img/couriers/digital.png',
            vibrate: true,
            data: order_id
        });

        notification.addEventListener('click', (event) => {
            console.log(event);
            parent.focus();
            this.close();

            let noti = event.target;
            openOrden(noti.data);
        });
    }else{
        notify('Activa las notificaciones por favor', 'error', 2);
    }
}


//RESUME TAB PAGE
document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      timeOutPage = Date.now();
    } else {
        //Verificar si firebase esta conectado!!
        db.ref(".info/connected").on('value', function(snap){
            console.log(snap.val());
            if (snap.val() == true) {
                //console.log("FIREBASE CONECTED TRUE");
            } else {
                offFirebase();
                onFirebase();
            }
        });

        //Calcular tiempo fuera de la página para traer nuevamente las ordenes entrantes
        const seconds = (Date.now() - timeOutPage) / 1000;
        const minutes = Math.floor(seconds / 60);
        if(minutes >= 4){
            notify('Buscando ordenes entrantes!!', 'warning', 2);
            $("#ENTRANTE").trigger("click"); //Recargar todas las ordenes entrantes
        }

        notify('Minutos fuera de la página: ' + minutes, 'info', 2, 'top-center');
    }
  });


/*----------------USERS ONLINE---------------*/
function getAllUsersOnline(){
    let usersActives = [];
    db.ref('usuarios').once('value', function(data) {
        let users = data.val();
        Object.keys(users).forEach(key => {
            let user = users[key];
            usersActives.push({
                "id": user.id,
                "date": user.date,
                "status": user.status,
            });
        });
        console.log(usersActives);
    });
}