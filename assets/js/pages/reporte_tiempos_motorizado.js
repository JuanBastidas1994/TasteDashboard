$(document).ready(function () {
    $(".basic").select2({ tags: true });
});

flatpickr(document.getElementById('fecha_inicio'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

flatpickr(document.getElementById('fecha_fin'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

$("body").on('click', ".btnReporte", function (event) {
    event.preventDefault();
    var alias    = $(this).data("alias");
    var sucursal = $("#cmb_sucursal").val();
    var f_inicio = $("#fecha_inicio").val();
    var f_fin    = $("#fecha_fin").val();

    if (f_inicio !== "" && f_fin !== "") {
        if (f_fin < f_inicio) {
            messageDone("La fecha fin no puede ser menor que la de inicio, inténtelo nuevamente", 'error');
            return;
        }
    } else {
        messageDone("Debe completar todos los campos, inténtelo nuevamente", 'error');
        return;
    }

    var parametros = {
        "alias"    : alias,
        "sucursal" : sucursal,
        "f_inicio" : f_inicio,
        "f_fin"    : f_fin
    };

    $.ajax({
        beforeSend: function () {
            OpenLoad("Cargando datos, por favor espere...");
        },
        url: 'controllers/controlador_reporte_tiempos_motorizado.php?metodo=getTiempos',
        type: 'POST',
        data: parametros,
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $('#table-tiempos').DataTable().destroy();
                $("#lstTiempos").html(response['tabla']);
                $("#promedioAceptar").html(response['promedio_aceptar']);
                $("#promedioCamino").html(response['promedio_camino']);
                $("#promedioTotal").html(response['promedio_total']);
                initDatatable($('#table-tiempos'));
                feather.replace();
            } else {
                notify(response['mensaje'], "info", 2);
                $("#lstTiempos").html("");
                $("#promedioAceptar").html("--");
                $("#promedioCamino").html("--");
                $("#promedioTotal").html("--");
            }
        },
        error: function (data) {
            console.log(data);
        },
        complete: function () {
            CloseLoad();
        }
    });
});

function initDatatable($table) {
    $table.DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                { extend: 'copy',  className: 'btn', footer: true },
                { extend: 'excel', className: 'btn', footer: true },
                { extend: 'pdf',   className: 'btn', footer: true }
            ]
        },
        "oLanguage": {
            "oPaginate": {
                "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                "sNext":     '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            },
            "sInfo":             "Mostrando pag. _PAGE_ de _PAGES_",
            "sInfoEmpty":        "Mostrando pag. 1",
            "sInfoFiltered":     "(filtrado de un total de _MAX_ registros)",
            "sSearch":           '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder":"Buscar...",
            "sLengthMenu":       "Resultados :  _MENU_",
            "sEmptyTable":       "No se encontraron resultados",
            "sZeroRecords":      "No se encontraron resultados",
            "buttons": {
                "copy": "Copiar", "excel": "Excel", "pdf": "PDF",
                "create": "Crear", "edit": "Editar", "remove": "Remover", "upload": "Subir"
            }
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 20,
        "order": [[0, "desc"]]
    });
}
