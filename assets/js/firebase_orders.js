let gestionOrdenesActiva = false;
let firebaseListenerRef = null;

let startClickEvent = false;
let startMessage = false;
let firebase_column = "";
let office_id = 0; 

$(function() {
    getUser();
});

function getUser(){
    let token = localStorage.getItem("token");
    console.log('TOKEN AUTH',token);
    $.ajax({
        url:'controllers/controlador_usuario.php?metodo=getUserByToken',
        data: { token },
        type: "POST",
        success: function(response){
            console.log(response);
            if(response.success==1){
                let { alias, cod_sucursal } = response.data;
                firebase_column = 'ordenes/'+alias;
                office_id = cod_sucursal;
                console.log(alias, office_id, firebase_column);
                
                const pathname = window.location.pathname;
                if(office_id !== 0 && !pathname.includes('gestion_ordenes')){
                    getChannel();
                }
                
                
            }else{
                // messageDone(response['mensaje'],'error');
            }
        },
        error: function(data){
          console.log(data);
        },
        complete: function()
        {
          
        }
    });
}

function getChannel(){
    const bc = new BroadcastChannel("gestion-ordenes");
    bc.onmessage = (event) => {
        if (event.data === "gestion_ordenes_activa") {
            gestionOrdenesActiva = true;
    
            // Si ya estábamos escuchando Firebase, lo apagamos
            if (firebaseListenerRef) {
                firebaseListenerRef.off();
                console.log("Se detectó gestion_ordenes, se apagó Firebase listener.");
            } else {
                console.log("No se inicializó Firebase listener, todo bien.");
            }
        }
    };
    
    
    // Esperamos un poco para ver si alguien responde
    setTimeout(() => {
        const pathname = window.location.pathname;
    
        if (office_id !== 0 && !pathname.includes('gestion_ordenes') && !gestionOrdenesActiva) {
            console.log("No se detectó gestion_ordenes, escuchando Firebase...");
            listenNewOrders(); // Este inicia el listener y guarda la referencia
        } else {
            console.log("gestion_ordenes activa, no se escuchará Firebase.");
        }
    }, 300);
}


function listenNewOrders(){
    // Referencia al nodo Firebase
    firebaseListenerRef = db.ref(firebase_column);

    // Obtener una vez la lista inicial (opcional)
    firebaseListenerRef.once('value', function(data) {
        startMessage = true;
        console.log("Firebase Ordenes", data.val());
    });

    //ORDENES NUEVAS
    firebaseListenerRef.limitToLast(1).on('child_added', function(data){
        if (gestionOrdenesActiva) {
            console.log("Gestion_ordenes apareció, ignorando orden nueva.");
            return;
        }
        
        if(startMessage){
            let orden = data.val();
            console.log("Firebase Orden Nueva",orden);
            if (orden.sucursal != office_id) return;
            
            if(parseInt(orden.sonar) == 1){
                encenderSirena();
                showCustomToastJc(orden);
            }
        }
    });
}

function showCustomToastJc(orden){
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
        // delay: 20000,
        delay: 900000,
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
        window.location.href = 'gestion_ordenes_v5.php';
    } );
    toastJc.open(orden.id);
}

$("body").on('click', function(){
    if(!startClickEvent){
        ion.sound.play("alarma-auto", {
            loop: false,
            volume: 0.1
        });
        startClickEvent= true;
    }
});