function datatableReInit() {
    $('#style-3').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'pdf', className: 'btn' },
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
                "excel": "Excel",
                "pdf": "PDF",
                "create": "Crear",
                "edit": "Editar",
                "remove": "Remover",
                "upload": "Subir"
            }
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
    let parametros = {
        metodo: "getOffices"
    }
    $.ajax({
        url: 'controllers/controlador_reporte_motorizados.php',
        data: parametros,
        type: "GET",
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $("#cmb_sucursal").html('<option value="0">Todas las sucursales</option>')
                response.data.forEach(office => {
                    $("#cmb_sucursal").append(`<option value="${office.cod_sucursal}">${office.nombre}</option>`);
                });
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

$("body").on('click', ".btnReporte", function (event) {
    event.preventDefault();
    var cod_sucursal = $("#cmb_sucursal").val();
    var fInicio = $("#fecha_inicio").val();
    var fFin = $("#fecha_fin").val();

    if (fInicio == "" || fFin == "") {
        messageDone("Debe completar todos los campos, inténtelo nuevamente", 'error');
        return;
    }
   
    var parametros = {
        cod_sucursal,
        fInicio,
        fFin
    }

    $.ajax({
        beforeSend: function () {
            OpenLoad("Cargando datos, por favor espere...");
        },
        url: 'controllers/controlador_reporte_motorizados.php?metodo=getReport',
        type: 'POST',
        data: parametros,
        success: function (response) {
            console.log(response);
            $('#style-3').DataTable().clear().destroy();
            if (response['success'] == 1) {
                response.data.forEach(orden => {
                    let badge = "primary";
                    let fSalida = "";
                    let fLlegada = "";

                    if(orden.estado == "ENTREGADA")
                        badge = "success";
                    if(orden.fecha_salida)
                        fSalida = orden.fecha_salida;
                    if(orden.fecha_llegada)
                        fLlegada = orden.fecha_llegada;

                    $(".tbInfo").append(`
                        <tr>
                            <td>
                                ${orden.cod_orden}
                            </td>
                            <td>
                                ${orden.fecha_asignacion}
                            </td>
                            <td>
                                ${orden.motorizado}
                            </td>
                            <td>
                                ${fSalida}
                            </td>
                            <td>
                                ${fLlegada}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-${badge}">
                                    ${orden.estado}
                                </span>
                            </td>
                            <td class="text-center">
                                <ul class="table-controls">
                                    <li>
                                        <a href ="orden_detalle.php?id=${orden.cod_orden}" target="_blank">
                                            <i data-feather="eye"></i>
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    `);
                });
                $('#Content-tabs').show();
                datatableReInit();
                feather.replace();
            }
            else {
                $('#Content-tabs').hide();
                notify(response['mensaje'], 'error', 2);

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
var f4 = flatpickr(document.getElementsByClassName('datetimes'), {
    enableTime: false,
    dateFormat: "Y-m-d"
});

/*Combos sucursales*/
var ss = $(".basic").select2({
    tags: true,
    enableTime: false,
    dateFormat: "Y-m-d"
});