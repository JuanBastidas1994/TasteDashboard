$('#style-3').DataTable({
    dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
    buttons: {
        buttons: [
            { extend: 'copy', className: 'btn' },
            { extend: 'csv', className: 'btn' },
            { extend: 'excel', className: 'btn' },
            { extend: 'pdf', className: 'btn' },
            { extend: 'print', className: 'btn' }
        ]
    },
    "oLanguage": {
        "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
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
            "csv": "CSV",
            "excel": "Excel",
            "pdf": "PDF",
            "print": "Imprimir",
            "create": "Crear",
            "edit": "Editar",
            "remove": "Remover",
            "upload": "Subir"
        }
    },
    "stripeClasses": [],
    "lengthMenu": [7, 10, 20, 50],
    "pageLength": 10
});

function cargarTable() {
    $('#style-3').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
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
                "csv": "CSV",
                "excel": "Excel",
                "pdf": "PDF",
                "print": "Imprimir",
                "create": "Crear",
                "edit": "Editar",
                "remove": "Remover",
                "upload": "Subir"
            }
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 10
    });
}

$(function() {
    flatpickr(".date", {
        dateFormat: "Y-m-d"
    });
});

$("#buscar").on("click", function () {
    getIntentosPagos();
});

function getIntentosPagos() {
    let fechaInicio = $("#fechaInicio").val().trim();
    let fechaFin = $("#fechaFin").val().trim();

    if (fechaInicio == "") {
        messageDone("Escoja una fecha inicio", "error");
        return;
    }
    if (fechaFin == "") {
        messageDone("Escoja una fecha fin", "error");
        return;
    }

    // VALIDAR FECHAS
    let fInicio = new Date(fechaInicio);
    let fFin = new Date(fechaFin);
    if (fInicio > fFin) {
        messageDone("La fecha inicio no puede ser mayor a la fecha fin", "error");
        return;
    }

    let data = {
        fechaInicio,
        fechaFin
    };

    $.ajax({
        url: 'controllers/controlador_reporte_intento_pagos.php?metodo=getIntentosPagos',
        data,
        type: "POST",
        headers: {
            Accept: 'application/json'
        },
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $('#style-3').DataTable().destroy();
                let target = $(".infoTabla");
                let template = Handlebars.compile($("#item-reporte-template").html());
                target.html(template(response.data));
                cargarTable();
            }
            else {
            }
        },
        error: function (data) {
        },
        complete: function () {
        },
    });
}