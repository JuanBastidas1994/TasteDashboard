let showNewOrdersFirebase = false;
let firebaseColumn = "";
let timeOutPage = null;
$(function() {
    firebaseColumn = 'ordenes/' + $("#alias_empresa").val();

    //LISTA DE ORDENES
    db.ref(firebaseColumn).once('value', function(data) {
        showNewOrdersFirebase = true;
        console.log("Firebase Ordenes",data.val());
    });

    //ORDENES NUEVAS
    db.ref(firebaseColumn).limitToLast(1).on('child_added', function(data){
        if(showNewOrdersFirebase){
            let orden = data.val();
            console.log("Firebase Orden Nueva",orden);
            if(orden.sucursal != sucursal_id){
                console.log("Este pedido es de otra sucursal", sucursal_id);
                return;
            }
            updateBadge(orden.estado);
            toast(`Orden nueva #${orden.id}`, "success", 2, {
                    "order_id": orden.id
                }, function(){
                    let orden = this.data;
                    openOrden(orden.order_id);
                });
            sendNotificationNewOrder(orden.id);

            //Si el estado al que cae esta escogido se refresca
            if($("#ENTRANTE").hasClass("active")){
                console.log("Estado entrante esta activo, REFRESCAR LISTA DE ORDENES!!!");
                getListaOrdenes();
            }else{
                console.log("Estado entrante no esta activo en pantalla");
            }
            encenderSirena();
        }
    });

    //ORDENES ACTUALIZADAS
    db.ref(firebaseColumn).limitToLast(1).on('child_changed', function(data){
        let orden = data.val();
        console.log("Firebase Orden Actualizada",orden);
        if(orden.sucursal != sucursal_id){
            console.log("Este pedido es de otra sucursal");
            return;
        }
        updateBadge(orden.estado);
        //encenderSirena();

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


    });


    //IS CONECTED??
    db.ref(".info/connected").on('value', function(snap){
        if (snap.val() == true) {
            console.log("FIREBASE CONECTED TRUE");
        } else {
            console.log("FIREBASE CONECTED FALSE");
        }
    });
});

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
    db.ref(firebaseColumn+'/'+order_id).set({
        id: order_id,
        estado: status,
        sucursal: sucursal_id
    });
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
            if (snap.val() == true) {
                console.log("FIREBASE CONECTED TRUE");
            } else {
                console.log("FIREBASE CONECTED FALSE");
                offFirebase();
                onFirebase();
            }
        });

        //Calcular tiempo fuera de la página para traer nuevamente las ordenes entrantes
        const seconds = (Date.now() - timeOutPage) / 1000;
        const minutes = Math.floor(seconds / 60);
        console.log(minutes, seconds);
        if(minutes >= 4){
            notify('Buscando ordenes entrantes!!', 'warning', 2);
        }

        notify('Minutos fuera de la página: ' + minutes, 'info', 2, 'top-center');
    }
  });