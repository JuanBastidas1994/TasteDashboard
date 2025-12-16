$(document).ready(function () {
    $("#cmbOrigen").select2();
});

$("body").on('click', ".btnReporte", function (event) {
    event.preventDefault();
    var cod_empresa = $(this).data("empresa");
    var alias = $(this).data("alias");
    var sucursal = $("#cmb_sucursal").val();
    var f_inicio = $("#fecha_inicio").val();
    var f_fin = $("#fecha_fin").val();
    var origen = $("#cmbOrigen").val();

    if (f_inicio != "" && f_fin != "") {
        if (f_fin < f_inicio) {
            messageDone("Las fecha fin no puede ser menor que la de incio,intentelo nuevamente ", 'error');
            return;
        }
    } else {
        messageDone("Debe completar todos los campos, intentelo nuevamente", 'error');
        return;
    }

    if ('' == origen) {
        messageDone("Debe escoger mínimo un origen", 'error');
        return;
    }

    var parametros = {
        "cod_empresa": cod_empresa,
        "alias": alias,
        "sucursal": sucursal,
        "f_inicio": f_inicio,
        "f_fin": f_fin,
        "origen": origen
    }

    $.ajax({
        beforeSend: function () {
            OpenLoad("Cargando datos, por favor espere...");
        },
        url: 'controllers/controlador_reporte_ventas.php?metodo=lista_ordenes',
        type: 'POST',
        data: parametros,
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $('#Content-tabs').css("display", "");
                $('#style-3').DataTable().destroy();
                $('#style-4').DataTable().destroy();
                $("#lstOrdenes").html(response['tabla']);

                //AGREGAR ACUMULADORES
                let acumHtml = "";
                acumHtml = `
                        <tr>
                            <td class="text-white">0</td>
                            <td>Descuento</td>
                            <td>$${response['descuento']}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-white">0</td>
                            <td>Subtotal</td>
                            <td>$${response['subtotal']}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-white">0</td>
                            <td>Envío</td>
                            <td>$${response['envio']}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-white">0</td>
                            <td>IVA</td>
                            <td>$${response['iva']}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-white">0</td>
                            <td>Total</td>
                            <td>$${response['total']}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                `;
                $("#style-4 tbody").append(acumHtml);

                $("#txt_descuento").html(response['descuento']);
                $(".txt_subtotal").html(response['subtotal']);
                $("#txt_envio").html(response['envio']);
                $("#txt_iva").html(response['iva']);
                $("#txt_total").html(response['total']);
                $("#RespventasMeses").val(response['reporte']);
                $("#RespMes").val(response['respMes']);
                $("#nomMes").val(response['nomMes']);
                $("#lstMeses").html(response['conteMeses']);
                $("#lstFormasPago").html(response['formasPago']);
                $("#lstOrigen").html(response['listaOrigen']);
                $("#lstProductos").html(response['tablaProductos']);


                console.log(response['reporte']);
                reloadChart(response['reporte2'], response['labelsChart']);


                console.log(response['iva']);
                cargarComponentes();
                feather.replace();
            }
            else {
                $('#Content-tabs').css("display", "none");
                notify(response['mensaje'], "info", 2);
                //messageDone(response['mensaje'], 'error');
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

function reloadChart(infoChart, labelsChart) {
    $("#grafico").html("");
    var options = {
        series: infoChart,
        labels: labelsChart,
        chart: {
            height: 450,
            type: 'area'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
    };

    var chart2 = new ApexCharts(document.querySelector("#grafico"), options);
    chart2.render();

}

$('#cmbOrigen').on('select2:select', function (e) {
    var data = e.params.data;
    if (-1 == data.id) {
        $("#cmbOrigen").val(null);
        $("#cmbOrigen").val('-1');
        $("#cmbOrigen").removeAttr("multiple");
        $("#cmbOrigen").trigger("change");
    }
    else {
        $("#cmbOrigen").attr("multiple", "multiple");
        $("#cmbOrigen").trigger("change");
    }
    console.log(data);
});

function cargarComponentes() {
    $('#style-3').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [{
                    extend: 'copy',
                    className: 'btn'
                },
                {
                    extend: 'excel',
                    className: 'btn'
                },
                {
                    extend: 'pdf',
                    className: 'btn'
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
                "pdf": "PDF",
                "create": "Crear",
                "edit": "Editar",
                "remove": "Remover",
                "upload": "Subir"
            }
        },
        "stripeClasses": [],
        "lengthMenu": [7, 10, 20, 50],
        "pageLength": 5,
        "order": [
            [1, "desc"]
        ]
    });

    $('#style-4').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
        buttons: {
            buttons: [{
                    extend: 'copy',
                    className: 'btn',
                    footer: true
                },
                {
                    extend: 'excel',
                    className: 'btn',
                    footer: true
                },
                {
                    extend: 'pdf',
                    className: 'btn',
                    footer: true
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
        "order": [
            [0, "desc"]
        ]
    });
}