//AutoAsignar
$(function(){
    //autoAsignarOrden(64726);

    //TOAST PARA CUANDO LLEGUE UNA NUEVA ORDEN - No terminado aun
    /*
    let toastJc = new ToastJc({
        title: "Orden nueva #65302",
        position: "bottom-left",
        delay: 150000,
        progress: true,
        showProgressTimer: false,
        icon: "success",
        description: `
        <div class="d-flex">
            <div>
                <i data-feather="credit-card"></i> Tarjeta
            </div>
            <div class="ml-auto">
                <i data-feather="truck"></i> Delivery
            </div>
            <div class="ml-3">
                <b>$15.59</b>
            </div>
        </div>`
    });
    toastJc.on('render', (data) => {
        console.log("Toast hay que renderizar", data);
        feather.replace();
    } );
    toastJc.on('timerFinish', (data) => {
        console.log("Toast Se cerro", data);
    } );
    toastJc.on('click', (data) => {
        console.log("Toast click", data);
        openOrden(data);
        console.log(this);
    } );
    toastJc.open(65307);*/

});

function autoAsignarOrden(order_id, envio){
    let config = getConfigGestionOrdenes();
    let recordatorio = getRecordatorio();
    console.log("AUTO", recordatorio);

    let couriers = config.couriers;
    if(recordatorio.asignacion === 1 && couriers.length > 0){
        //Toast para mostrar una orden que se va a autoasignar
        if(envio == "envio"){
            let courier = couriers[0];
            let toastJc = new ToastJc({
                title: "Se autoasignará la orden",
                description: `<div class="row">
                            <div class="col-2 text-center">
                                <img src="${courier.imagen}" class="rounded-circle" style="width: 40px;" alt="">
                            </div>
                            <div class="col-9">
                                La orden #<span class="ordenId">${order_id}</span> se autoasignará a ${courier.courier}
                            </div>
                        </div>`,
                delay: (recordatorio.tiempo_asignacion * 1000)
            });
            toastJc.open(order_id);
            toastJc.on('timerFinish', (p_order_id) => {
                //Si es pickup solo se debe aceptar la orden
                console.log("ASignando Orden", p_order_id, courier);
                asignarOrden(p_order_id, courier.id, 0);
                printOrder(p_order_id);
                apagarSirena();
            } );
        }else{
            let toastJc = new ToastJc({
                title: "Se aceptará la orden",
                description: `<div class="row">
                            <div class="col-9">
                                La orden #<span class="ordenId">${order_id}</span> se aceptará automaticamente
                            </div>
                        </div>`,
                delay: (recordatorio.tiempo_asignacion * 1000)
            });
            toastJc.open(order_id);
            toastJc.on('timerFinish', (p_order_id) => {
                //Si es pickup solo se debe aceptar la orden
                console.log("Aceptando Orden", p_order_id);
                changeStatusOrder(p_order_id, "ACEPTADA");
                printOrder(p_order_id);
                apagarSirena();
            } );
        }
    }
}