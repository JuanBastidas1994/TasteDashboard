$(document).ready(function () {
    $("#cmbOrigen").select2();
});

$("body").on('click', ".btnDetalle", function (event) {
    let data = $(this).data();
    $("#name-delivery").html(data.name);
    $("#total-delivery").html(`$${data.total}`);
    $("#numitems-delivery").html(`${data.numitems} items`);
    console.log(data);
    
    var parametros = {
        "cod_motorizado": data.id,
        "sucursal": data.office,
        "f_inicio": data.start,
        "f_fin": data.end
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Cargando datos, por favor espere...");
        },
        url: 'controllers/controlador_reporte_delivery.php?metodo=getDetalle',
        type: 'POST',
        data: parametros,
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $('#table-detalles').DataTable().destroy();
                $("#lstDetalle").html(response['tabla']);

                initDatatable($('#table-detalles'));
                feather.replace();
            }
            else {
                notify(response['mensaje'], "info", 2);
                $("#lstDetalle").html("");
            }

        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });
    
    
    $("#detalleModal").modal();
});

$("body").on('click', ".btnReporte", function (event) {
    event.preventDefault();
    var cod_empresa = $(this).data("empresa");
    var alias = $(this).data("alias");
    var sucursal = $("#cmb_sucursal").val();
    var courier = $("#cmb_courier").val();
    var f_inicio = $("#fecha_inicio").val();
    var f_fin = $("#fecha_fin").val();

    if (f_inicio !== "" && f_fin !== "") {
        if (f_fin < f_inicio) {
            messageDone("Las fecha fin no puede ser menor que la de incio,intentelo nuevamente ", 'error');
            return;
        }
    } else {
        messageDone("Debe completar todos los campos, intentelo nuevamente", 'error');
        return;
    }

    var parametros = {
        "cod_empresa": cod_empresa,
        "alias": alias,
        "cod_sucursal": sucursal,
        "cod_courier": courier,
        "fechaInicio": f_inicio,
        "fechaFin": f_fin
    }

    $.ajax({
        beforeSend: function () {
            OpenLoad("Cargando datos, por favor espere...");
        },
        url: 'controllers/controlador_reporte_courier.php?metodo=getEnvios',
        type: 'POST',
        data: parametros,
        success: function (response) {
         //   console.log("Este es " + response['query']);
           // alert(response['mensaje'])
            if (response['success'] == 1) {
                $('#table-ordenes').DataTable().destroy();
                $("#lstOrdenes").html(response['tabla']);
                $("#totalAmount").html(response['total']);
                
                let template = Handlebars.compile($("#table-sucursales-template").html());
                $("#compendio").html(template(response['compendio']));

              //  initDatatable($('#table-ordenes'));
                feather.replace();
            }
            else {
                notify(response['mensaje'], "info", 2);
                $("#lstOrdenes").html("");
                $("#totalAmount").html("0.00");
            }

        },
        error: function (data) {
            console.log(data);

        },
       complete: function (resp) {
            CloseLoad();
        }
    });
});



/*Combos fecha*/
var f4 = flatpickr(document.getElementById('fecha_inicio'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

var f4 = flatpickr(document.getElementById('fecha_fin'), {
});

/*Combos sucursales*/
var ss = $(".basic").select2({
    tags: true,
    enableTime: false,
    dateFormat: "Y-m-d"
});



function initDatatable($table) {
    $table.DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn',
                    footer: true
                },
                {
                    extend: 'print',
                    className: 'btn',
                    footer: true,
                    exportOptions: {
                        columns: ':not(:last-child)'   // Oculta la última columna
                    }
                },
            ]
        },
        "oLanguage": {
            "oPaginate": {
                "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            },
            "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
            "sInfoEmpty": "Mostrando pag. 1",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Buscar...",
            "sLengthMenu": "Resultados :  _MENU_",
            "sEmptyTable": "No se encontraron resultados",
            "sZeroRecords": "No se encontraron resultados",
            "buttons": {
                "copy": "Copiar",
                "excel": "Excel",
                "print": "Imprimir",
                "create": "Crear",
                "edit": "Editar",
                "remove": "Remover",
                "upload": "Subir"
            }
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 20,
        "order": [
            [0, "desc"]
        ]
    });
}