function datatableReInit() {
    $('#style-3').DataTable().clear().destroy();
    $('#style-3').DataTable({
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
            ]
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 20,
        "order": [[0, "desc"]]
    });
}

$(function () {
    getOffices();
});

function getOffices() {
    let target = $("#cmbSucursales");

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_sucursal.php?metodo=lista`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        if(response.success == 1){
            let template = Handlebars.compile($("#sucursales-template").html());
            target.append(template(response.data));

            $(".basic").select2({
                tags: false
            });
        }
        else {
            messageDone(response.mensaje, "error");
        }
        CloseLoad();
    })
    .catch(error=>{
        console.log(error);
        CloseLoad();
    });
}

$("body").on('click', ".btnReporte", function () {
    let target = $("#lstSucursales");
    target.html("");

    var cod_sucursal = $("#cmbSucursales").val();
    var fechaInicio = $("#fecha_inicio").val();
    var fechaFin = $("#fecha_fin").val();
    var dIni = new Date(fechaInicio + " 00:00:00");
    var dFin = new Date(fechaFin + " 23:59:59");

   
    if (fechaInicio == "") {
        messageDone("Escoja fecha inicio", 'error');
        return;
    }
    if (fechaFin == "") {
        messageDone("Escoja la fecha fin", 'error');
        return;
    }

    if(dFin < dIni) {
        messageDone("La fecha fin no puede ser mayor a la fecha inicio", 'error');
        return;
    }

    var info = {
        cod_sucursal,
        fechaInicio,
        fechaFin
    }

    OpenLoad("Cargando datos, por favor espere...");
    fetch(`controllers/controlador_reporte_couriers.php?metodo=getReport`,{
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let template = Handlebars.compile($("#table-sucursales-template").html());
            target.html(template(response.data));
        }
        else{
            messageDone(response.mensaje, "error");
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
});

/*Combos fecha*/
var f4 = flatpickr(document.getElementsByClassName('datetimes'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});