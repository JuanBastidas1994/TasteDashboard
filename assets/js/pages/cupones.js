var myTable = $('#style-3').DataTable({
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
    "pageLength": 7
});


$('.bs-tooltip').tooltip();

$('.dropify').dropify({
    messages: { 'default': 'Click to Upload or Drag n Drop', 'remove': '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
});


$(function () {
    lista();
});

function lista() {
    let parametros = {
        metodo: "getCoupons"
    }
    $.ajax({
        url: 'controllers/controlador_codigo_promociona.php',
        data: parametros,
        type: "GET",
        success: function (response) {
            console.log(response);
            if (response.success == 1) {
                let html = "";
                let data = response.data;
                data.forEach(cupon => {
                    let badge = "primary";
                    if (cupon.estado == "Inactivo")
                        badge = "danger"

                    html += `
                    <tr>
                        <td>
                            ${cupon.cod_cupon}
                        </td>
                        <td>
                            ${cupon.titulo}
                        </td>
                        <td>
                            <img src="${cupon.imagen}" class="profile-img">
                        </td>
                        <td>
                            ${cupon.cantidad_dias_disponibles}
                        </td>
                        <td>
                            ${cupon.tipo}
                        </td>
                        <td class="text-center">
                            <span class="shadow-none badge badge-${badge}">${cupon.estado}</span>
                        </td>
                        <td class="text-center">
                            <ul class="table-controls">
                                <li>
                                    <a href="javascript:void(0)" class="btnEditarCupon" data-cupon="${cupon.cod_cupon}">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="btnEliminarCupon" data-cupon="${cupon.cod_cupon}">
                                        <i data-feather="trash"></i>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                `;
                });
                $("#style-3 tbody").html(html);
                feather.replace();
            }
            else {
                messageDone(response.mensaje, "error");
            }
        },
        error: function (data) {
            console.log(data);
        },
        complete: function () {
        },
    });
}

$("#btnOpenModal").on("click", function () {
    $("#frmSave").trigger("reset");
    $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
    $("#crearModal").modal();
});

$("#btnGuardar").on("click", function () {
    let form = $("#frmSave");
    console.log(form);
    form.validate();
    if (form.valid() == false) {
        notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
        return false;
    }

    let formData = new FormData(form[0]);
    $.ajax({
        url: 'controllers/controlador_codigo_promociona.php?metodo=setCoupon',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                $("#crearModal").modal("hide");
                form.trigger("reset");
                lista();
                messageDone(response.mensaje, "success");
            }
            else {
                messageDone(response.mensaje, "error");
            }
        },
        error: function (data) {
            console.log(data);
        },
        complete: function () {
        },
    });
});

$("body").on("click", ".btnEditarCupon", function(){
    let dataCupon = $(this).data("cupon");
    let parametros = {
        metodo: "getCoupon",
        cod_cupon: dataCupon
    }
    console.log(parametros);
    $.ajax({
        url: 'controllers/controlador_codigo_promociona.php',
        data: parametros,
        type: "GET",
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                let data = response.data;
                $("#cod_cupon").val(data.cod_cupon);
                $("#txt_codigo").val(data.titulo);
                $("#cmbTipo").val(data.tipo);
                $("#txt_cantidad").val(data.cantidad_dias_disponibles);
                $("#cmbEstado").val(data.estado);
                $("#txtDescripcion").val(data.descripcion);
                $(".dropify-render img").attr("src", data.imagen);
                $("#crearModal").modal();
            }
            else {
                messageDone(response.mensaje, "error");
            }
        },
        error: function (data) {
            console.log(data);
        },
        complete: function () {
        },
    });
});