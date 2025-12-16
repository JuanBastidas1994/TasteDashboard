let ApiUrl = "https://api.mie-commerce.com/taste/v1";
let ApiKey = "";

$(document).ready(function() {
    $("#fecha_inicio").val(today());
    $("#fecha_fin").val(today());
    flatpickr(document.getElementsByClassName('picker'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    getRucs();
});

function today() {
    let date = new Date();
    let d = date.getDate();
    let day = d.toString().padStart(2, "0");
    let m = date.getMonth() + 1;
    let month = m.toString().padStart(2, "0");
    let year = date.getFullYear();
    return `${year}-${month}-${day}`;
}

function getRucs() {
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_facturas.php?metodo=getRucs`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let target = $("#cmbRuc");
            let template = Handlebars.compile($("#rucs-template").html());
            target.html(template(response.data));
        }
        else{
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

function getFacturas() {
    datatableReInit();
    let target = $("#style-3 tbody");

    let ruc = $("#cmbRuc").val();
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();

    if(ruc == "") {
        notify("Escoja un RUC", "error", 2);
        return;
    }
    if(fecha_inicio == "") {
        notify("Ingrese fecha inicio", "error", 2);
        return;
    }
    if(fecha_fin == "") {
        notify("Ingrese fecha fin", "error", 2);
        return;
    }

    let fI = new Date(fecha_inicio);
    let fF = new Date(fecha_fin);

    if(fI > fF) {
        notify("La fecha inicio no puede ser mayor a la fecha fin", "error", 2);
        return;
    }

    let params = `?metodo=getFacturas&ruc=${ruc}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`;
    
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_facturas.php${params}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let template = Handlebars.compile($("#documentos-template").html());
            target.html(template(response.data));
            feather.replace();
        }
        else{
            target.html("");
            notify(response.mensaje, "error", 2);
            datatableReInit();
        }
        CloseLoad();
    })
    .catch(error=>{
        target.html("");
        notify("Error al realizar la petición", "error", 2);
        CloseLoad();
        console.log(error);
        datatableReInit();
    });
}

function getOrdenesNoFActuradas() {
    datatableReInit();
    let target = $("#style-3 tbody");

    let ruc = $("#cmbRuc").val();
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();

    if(ruc == "") {
        notify("Escoja un RUC", "error", 2);
        return;
    }
    if(fecha_inicio == "") {
        notify("Ingrese fecha inicio", "error", 2);
        return;
    }
    if(fecha_fin == "") {
        notify("Ingrese fecha fin", "error", 2);
        return;
    }

    let fI = new Date(fecha_inicio);
    let fF = new Date(fecha_fin);

    if(fI > fF) {
        notify("La fecha inicio no puede ser mayor a la fecha fin", "error", 2);
        return;
    }

    let params = `?metodo=getOrdenesNoFActuradas&ruc=${ruc}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`;
    
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_facturas.php${params}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let template = Handlebars.compile($("#no-facturados-template").html());
            target.html(template(response.data));
            feather.replace();
        }
        else{
            target.html("");
            notify(response.mensaje, "error", 2);
            datatableReInit();
        }
        CloseLoad();
    })
    .catch(error=>{
        target.html("");
        notify("Error al realizar la petición", "error", 2);
        CloseLoad();
        console.log(error);
        datatableReInit();
    });
}

function datatableReInit() {
    $('#style-3').DataTable().clear().destroy();
    $('#style-3').DataTable({
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdfHtml5', className: 'btn' },
            ]
        },
        stripeClasses: [],
        lengthMenu: [7, 10, 20, 50],
        pageLength: 20,
        order: [[0, "desc"]]
    });
}

$("body").on("click", ".btnFacturar", function(){
    let cod_orden = $(this).data("id");
    swal.fire({
       title: 'Se creará una factura',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          facturar_inventario(cod_orden);
       }
    }); 
});

function facturar_inventario(cod_orden) {
    ApiKey = $("#apiEmpresa").val();
    facturaElectronica(cod_orden, true);
    setInventario(cod_orden, "EGR");
}

function anular_facturar_inventario(cod_orden) {
    ApiKey = $("#apiEmpresa").val();
    facturaElectronica(cod_orden, false);
    setInventario(cod_orden, "ING");
}

function facturaElectronica(cod_orden, crear){
    let ruta = `${ApiUrl}/facturas/anular`;
    if(crear)
        ruta = `${ApiUrl}/facturas/electronica`;
    
    let info = {
        id: cod_orden
    }
    
    fetch(ruta,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            notify(response.mensaje,'success',2);
        }
        else if(response.success == 0){
            notify(response.mensaje,'error',2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function setInventario(cod_orden, tipo) {
    fetch(`${ApiUrl}/contifico/inventario/${tipo}`,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify({cod_orden})
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}