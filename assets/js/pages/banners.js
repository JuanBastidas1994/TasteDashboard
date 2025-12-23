$(document).ready(function () {

    $("#btnOpenModal").on("click", function (event) {
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src", 'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });

    $("body").on("click", ".btnEditar", function (event) {
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if (id == 0) {
            alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
            return;
        }
        var parametros = {
            "cod_banner": id
        }
        $.ajax({
            beforeSend: function () {
                OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_banner.php?metodo=get',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    var data = response['data'];
                    $("#id").val(data['cod_banner']);
                    $("#txt_titulo").val(data['titulo']);
                    $("#txt_subtitulo").val(data['subtitulo']);
                    $("#txt_descuento").val(data['descuento']);
                    $("#txt_text_boton").val(data['text_boton']);
                    $("#txt_url").val(data['url_boton']);

                    $("#ckEstado").removeAttr("checked");
                    if (data["estado"] == "A") {
                        $("#ckEstado").attr("checked", true);
                    }

                    let d = new Date();
                    $(".dropify-render img").attr("src", data['image_min'] + "?nocache=" + d.getMilliseconds());
                    $("#crearModal").modal();
                }
                else {
                    messageDone(response['mensaje'], 'error');
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

    $("body").on("click", ".btnEliminar", function (event) {
        event.preventDefault();
        var id = parseInt($(this).attr("data-value"));
        if (id == 0) {
            alert("No se pudo traer el usuario, por favor intentelo mas tarde");
            return;
        }

        swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {
                eliminar(id);
            }
        });
    });

    function eliminar(id) {
        var parametros = {
            "cod_banner": id,
            "estado": "D"
        }
        $.ajax({
            beforeSend: function () {
                OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_banner.php?metodo=set_estado',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    notify(response.mensaje, "success", 2);
                    $("#lstBanners #" + id).remove();
                }
                else {
                    messageDone(response['mensaje'], 'error');
                }

            },
            error: function (data) {
                console.log(data);

            },
            complete: function (resp) {
                CloseLoad();
            }
        });
    }

    $("#btnGuardar").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmSave");
        form.validate();
        if (form.valid() == false) {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
        }

        var formData = new FormData($("#frmSave")[0]);
        var id = parseInt($("#id").val());

        let estado = "A";
        if (!$("#ckEstado").is(":checked"))
            estado = "I";

        formData.append('estado', estado);
        if (id > 0) {
            formData.append('cod_banner', id);
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_banner.php?metodo=crear',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    // messageDone(response['mensaje'], 'success');
                    notify(response.mensaje, "success", 2);
                    let newId = response['id'];
                    let banner = response['banner'];
                    $("#id").val(newId);

                    let estado = (banner['estado'] == 'A') ? 'Activo' : 'Inactivo';
                    let badge = (banner['estado'] == 'A') ? 'primary' : 'danger';
                    let d = new Date();
                    let image = banner['image_min'] + "?nocache=" + d.getMilliseconds();
                    let rowBanner = `<tr id="${newId}"  data-codigo="${newId}">
                                <td><img src="${image}" class="profile-img" alt="Imagen" style="width: 250px; height: auto;"></td>
                                <td class="text-center"><span class="shadow-none badge badge-${badge}">${estado}</span></td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li><a href="javascript:void(0);" data-value="${newId}" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>

                                        <li><a href="javascript:void(0);" data-value="${newId}" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a></li>
                                    </ul>
                                </td>
                            </tr>`;

                    //QUITAR CACHÉ IMÁGENES
                    if (id > 0) {
                        $("#lstBanners #" + newId).replaceWith(rowBanner);
                    }else{
                        $("#lstBanners").append(rowBanner);
                    }
                    feather.replace();
                    $("#crearModal").modal("hide");
                }
                else {
                    messageDone(response['mensaje'], 'error');
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

    $("#lstBanners").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#lstBanners>tr').each(function () {
                selectedData.push($(this).attr("data-codigo"));
            });
            ordenarItems(selectedData);
        }
    });

    function ordenarItems(data) {
        var parametros = {
            "banners": data,
        }
        console.log(parametros);

        $.ajax({
            url: 'controllers/controlador_banner.php?metodo=actualizar',
            type: 'POST',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    notify("Actualizado correctamente", "success", 2);
                }
                //alert(response['mensaje']);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    $('.dropify').dropify({
        messages: { 'default': 'Click to Upload or Drag n Drop', 'remove': '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

});