let cityCircle = null;
$(document).ready(function () {
    getRangos($("#cod_sucursal").val());

    setTimeout(function () { armarRadio(); }, 1500);

    $("#btnOpenModal").on("click", function (event) {
        $("#cod_sucursal").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src", 'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });

    $("body").on("click", ".btnEditar", function (event) {
        event.preventDefault();

        var cod_sucursal = parseInt($(this).attr("data-value"));
        if (cod_sucursal == 0) {
            alert("No se pudo traer la sucursal, por favor intentelo mas tarde");
            return;
        }
        var parametros = {
            "cod_sucursal": cod_sucursal
        }
        $.ajax({
            beforeSend: function () {
                OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_sucursal.php?metodo=get',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    var data = response['data'];
                    $("#cod_sucursal").val(data['cod_sucursal']);
                    $("#txt_nombre").val(data['nombre']);
                    $("#txt_emisor").val(data['emisor']);
                    $("#txt_cobertura").val(data['distancia_km']);
                    $("#timeFlatpickr").val(data['hora_ini']);
                    $("#timeFlatpickr2").val(data['hora_fin']);
                    $("#txt_direccion").val(data['direccion']);
                    $("#txt_latitud").val(data['latitud']);
                    $("#txt_longitud").val(data['longitud']);

                    console.log(data['image']);
                    $(".dropify-render img").attr("src", data['image']);
                    $(".gllpUpdateButton").trigger("click");
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
        var cod_sucursal = parseInt($(this).attr("data-value"));
        if (cod_sucursal == 0) {
            alert("No se pudo traer la sucursal, por favor intentelo mas tarde");
            return;
        }
        var element = $(this);

        Swal.fire({
            title: '¿Estas seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_sucursal": cod_sucursal,
                    "estado": "D"
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Buscando informacion, por favor espere...");
                    },
                    url: 'controllers/controlador_sucursal.php?metodo=set_estado',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            var myTable = $('#style-3').DataTable();
                            var tr = $(element).parents("tr");
                            myTable.row(tr[0]).remove().draw();
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
        });
    });

    /*--NUEVO--*/

    $(".chkDia").on("change", function () {
        var padre = $(this).parents(".itemDisponibilidad");
        if ($(this).is(':checked')) {
            padre.find(".hora_iniD").prop("disabled", false);
            padre.find(".hora_finD").prop("disabled", false);
            padre.find(".sucSelectDia").val(1);
        }
        else {
            padre.find(".hora_iniD").prop("disabled", true);
            padre.find(".hora_finD").prop("disabled", true);
            padre.find(".sucSelectDia").val(0);
        }
    })


    $(".chkEstadoSuc").on("change", function () {
        if ($(this).is(':checked')) {
            $("#cmbEstado").val("A");
        }
        else {
            $("#cmbEstado").val("I");
        }
    })
    //PRECIO

    $(".chkPrecio").on("change", function () {
        var padre = $(this).parents(".itemProductos");
        if ($(this).is(':checked')) {
            padre.find(".sucPrecio").val(1);
        }
        else {
            padre.find(".txt_precio_sucursal").val(0);
            padre.find(".txt_precio_anterior_sucursal").val(0);
            padre.find(".sucPrecio").val(0);
        }
    })

    $(".btnGuardarDisProduct").on("click", function (event) {
        event.preventDefault();
        var cod_sucursal = parseInt($("#cod_sucursal").val());
        if (cod_sucursal === 0) {
            messageDone("Debe guardar la sucursal primero", 'error');
            return false;
        }
        var formData = new FormData($("#frmProductos")[0]);
        formData.append('cod_sucursal', cod_sucursal);

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_sucursal.php?metodo=editProductoSucursal',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
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
    /*--NUEVO--*/

    $("#btnGuardar").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmSave");
        form.validate();
        if (form.valid() == false) {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
        }

        var tipo = "I";
        var formData = new FormData($("#frmSave")[0]);
        formData.append('txt_crop', $("#txt_crop").val());
        formData.append('txt_crop_min', $("#txt_crop_min").val());

        var cod_sucursal = parseInt($("#cod_sucursal").val());
        if (cod_sucursal > 0) {
            formData.append('cod_sucursal', cod_sucursal);
            tipo = "U";
        }

        //DISPONIBILIDAD
        var disData = $("#frmDisponibilidad").serializeArray();
        for (var i = 0; i < disData.length; i++)
            formData.append(disData[i].name, disData[i].value);

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_sucursal.php?metodo=crear',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $("#cod_sucursal").val(response['id']);
                    $("#titulo").html($("#txt_nombre").val().trim());
                    // changesTable(tipo, response['id'], response['imagen']);
                    window.history.pushState(response, "Crear Sucursal", "crear_sucursales.php?id=" + response['id']);
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

    function changesTable(tipo, codigo, ruta_imagen) {
        var data = new Array();
        data[0] = '<img src="' + ruta_imagen + '" class="profile-img" alt="Imagen">';
        data[1] = $("#txt_nombre").val().trim();
        data[2] = $("#txt_direccion").val().trim();
        data[3] = $("#hora_ini").val().trim();
        data[4] = $("#hora_fin").val().trim();
        data[5] = $("#txt_emisor").val().trim();
        data[6] = tableEstado('A');
        data[7] = tableAcciones(codigo);

        var myTable = $('#style-3').DataTable();
        if (tipo == "I") {  //INSERTAR
            myTable.row.add(data).draw();
        } else { //EDITAR
            var tr = $('#style-3').find("[data-value='" + codigo + "']");
            //var data = myTable.row(tr[0]).data();
            myTable.row(tr[0]).data(data).draw();
        }
        feather.replace();
    }

    /*time picker*/
    /*
    var f4 = flatpickr(document.getElementById('hora_ini'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    var f5 = flatpickr(document.getElementById('hora_fin'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });*/

    /*--NUEVO--*/
    /*time picker*/
    flatpickr('.hora_iniD', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    flatpickr('.hora_finD', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    /*IMAGEN*/
    var resize = null;
    var drEvent = $('.dropify').dropify({
        messages: {
            'default': 'Click para subir o arrastra',
            'remove': 'X',
            'replace': 'Sube o Arrastra y suelta'
        },
        error: {
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
    drEvent.on('dropify.beforeImagePreview', function (event, element) {
        if (resize != null)
            resize.destroy();

        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    });

    $('#modalCroppie').on('shown.bs.modal', function () {
        $("#crearModal").modal('hide');
        $("#modalCroppie").css("overflow", "scroll");
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#my-image').attr('src', e.target.result);
            resize = new Croppie($('#my-image')[0], {
                viewport: { width: 600, height: 200 }, //tamaño de la foto que se va a obtener
                boundary: { width: 600, height: 200 }, //la imagen total
                showZoomer: true, // hacer zoom a la foto
                enableResize: false,
                enableOrientation: true // para q funcione girar la imagen 

            });
            $('#crop-get').on('click', function () { // boton recortar
                resize.result({ type: 'base64', size: 'viewport', format: 'jpeg', quality: 0.8 }).then(function (dataImg) {
                    var InsertImgBase64 = dataImg;
                    $("#txt_crop").val(InsertImgBase64);
                    var imagen = $(".dropify-render img")[0];
                    $(imagen).attr("src", InsertImgBase64);
                    $("#modalCroppie").modal('hide');
                    $("#crearModal").modal();
                    $("#crearModal").css("overflow", "scroll");
                });
                /*MINIATURA*/
                resize.result({ type: 'base64', size: { width: 200, height: 66 }, format: 'jpeg', quality: 0.8 }).then(function (dataImg) {
                    console.log("IMAGEN MINIATURAAAAA");
                    console.log(dataImg);
                    $("#txt_crop_min").val(dataImg);
                });
            });
            $('.crop-rotate').on('click', function (ev) {
                resize.rotate(parseInt($(this).data('deg')));
            });


        }
        reader.readAsDataURL(file);
    });
    /*IMAGEN*/

    $("#cmbProvincias").on("change", function () {
        var provincia = $("#cmbProvincias").val();
        //alert(provincia);
        var parametros = {
            "provincia": provincia
        }
        $.ajax({
            url: 'controllers/controlador_sucursal.php?metodo=ciudades',
            data: parametros,
            type: "POST",
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    $("#cmbCiudades").html(response['ciudadesHtml']);
                }
                else {
                    messageDone(response['mensaje'], 'error');
                }
            },
            error: function (data) {
                console.log(data);
            },
            complete: function () {

            }
        });

    });

    $("body").on("change", ".chkEstado", function (e) {
        e.preventDefault();
        var check = $(this);
        var padre = $(this).parents(".itemProductos");
        if (check.is(':checked'))
            padre.find(".sucSelect").val(1);
        else
            padre.find(".sucSelect").val(0);
    });

});

$("body").on("click", ".btnGuardarProgramar", function () {
    let checkb = $("#chk_programar");
    let diasProgramar = $("#txt_cant_dias_programar").val();
    let cod_sucursal = $(".btnGuardarProgramar").data("sucursal");
    let programa = 0;
    if (checkb.is(":checked"))
        programa = 1;
    else
        programa = 0;

    var parametros = {
        "cod_sucursal": cod_sucursal,
        "programa": programa,
        "diasProgramar": diasProgramar
    }

    $.ajax({
        url: 'controllers/controlador_sucursal.php?metodo=setProgramarPedido',
        data: parametros,
        type: "GET",
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                messageDone(response['mensaje'], "success");
            }
            else {
                messageDone(response['mensaje'], "error");
            }
        },
        error: function (data) {
        },
        complete: function () {
        },
    });
});

$("#txt_cobertura").on("input", function () {
    armarRadio();
});

$("#txt_latitud").on("change", function () {
    armarRadio();
});

$(".gllpUpdateButton").on("click", function () {
    armarRadio();
});

function armarRadio() {
    let cobertura = $("#txt_cobertura").val();
    if (cobertura === "")
        return;

    if (cityCircle !== null) {
        cityCircle.setMap(null);
    }

    let lat = parseFloat($("#txt_latitud").val());
    let lon = parseFloat($("#txt_longitud").val());
    var posMarker = { lat: lat, lng: lon };
    console.log(posMarker);
    //ADHERIR COBERTURA
    let randomColor = "#" + Math.floor(Math.random() * 16777215).toString(16);
    cityCircle = new google.maps.Circle({
        strokeColor: randomColor,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: randomColor,
        fillOpacity: 0.35,
        map: window.mapGeneral,
        center: posMarker,
        radius: cobertura * 1000,
    });
}

$("#btnBack").on("click", function () {
    let url = $(this).data("module-back");
    swal.fire({
        title: '¿Estas seguro?',
        text: '¡Perderas todos los cambios que no hayas guardado!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Salir',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            window.location.href = url;
        }
    });
});

// RANGOS
function addRango(rango = null) {
    let target = $(".lst-rangos");
    let template = Handlebars.compile($("#rango-template").html());

    if (rango == null) {
        rango = {
            id: 0,
            distancia_ini: "",
            distancia_fin: "",
            precio: ""
        };
    }

    target.append(template(rango));
    feather.replace();
}

function getRangos(cod_sucursal = 0) {
    if (cod_sucursal == 0) return;

    if($(".lst-rangos").length == 0) return;

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_sucursal.php?metodo=getCostosEnvioRango&cod_sucursal=${cod_sucursal}`, {
        method: 'GET',
    })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                if (response.data.length == 0) {
                    addRango();
                }
                else {
                    response.data.forEach(costo => {
                        addRango(costo);
                    });
                }
            }
        })
        .catch(error => {
            CloseLoad();
            console.log(error);
        });
}

function saveRangos() {
    let cod_sucursal = $("#cod_sucursal").val();
    let rangos = [];
    let rangosElement = $(".rango");
    let validated = true;

    if (cod_sucursal == 1) {
        messageDone('Primero aségurese de guardar la sucursal', 'error');
        return;
    }

    if (rangosElement.length == 0) {
        messageDone('Primero agregue un rango', 'error');
        return;
    }

    for (let i = 0; i < rangosElement.length; i++) {
        const id = document.getElementsByClassName('rango-id')[i].value;
        const distancia_ini = document.getElementsByClassName('distancia-ini')[i].value;
        const distancia_fin = document.getElementsByClassName('distancia-fin')[i].value;
        const precio = document.getElementsByClassName('rango-precio')[i].value;

        if (distancia_ini.trim() == "") {
            validated = false;
            messageDone('Ingrese distancia inicial', 'error');
            return;
        }
        if (distancia_fin.trim() == "") {
            validated = false;
            messageDone('Ingrese distancia final', 'error');
            return;
        }
        if (precio.trim() == "") {
            validated = false;
            messageDone('Ingrese precio', 'error');
            return;
        }

        rangos.push({ id, distancia_ini, distancia_fin, precio });
    }

    if (!validated) {
        messageDone('Error en validación, por favor revise los datos ingresados', 'error');
        return;
    }

    let info = {
        cod_sucursal,
        rangos
    }

    OpenLoad("Guardando...");
    fetch(`controllers/controlador_sucursal.php?metodo=saveCostosEnvioRango`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if (response.success == 1) {
                messageDone(response.mensaje, 'success');
            }
            else {
                messageDone(response.mensaje, 'error');
            }
        })
        .catch(error => {
            console.log(error);
            CloseLoad();
            messageDone('Ocurrió un error', 'error');
        });
}


$("body").on("click", ".btnRemoverRango", function(){
    let element = $(this);
    let id = element.data("id");
    console.log(id);

    if(id === 0) {
        element.parents('.rango').remove();
        return;
    }

    Swal.fire({
        title: 'Se eliminará este rango costo de envío, no se podrá revertir',
        text: '¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            OpenLoad("Eliminando rango...");
            fetch(`controllers/controlador_sucursal.php?metodo=removeCostosEnvioRango&id=${id}`, {
                method: 'GET',
                headers: {
                    'Api-Key': config.apikey
                },
            })
                .then(res => res.json())
                .then(response => {
                    CloseLoad();
                    if (response.success == 1) {
                        element.parents('.rango').remove();
                        messageDone(response.mensaje, 'success');
                    }
                    else {
                        messageDone(response.mensaje, 'error');
                    }
                })
                .catch(error => {
                    console.log(error);
                    CloseLoad();
                });
        }
    });
});

$(".flLogos").change(function(){
    let cod_sucursal = parseInt($("#cod_sucursal").val());
    let inputfile = this.files[0];
    let formData = new FormData($("#frmLogos")[0]);
    formData.append("cod_sucursal", cod_sucursal);
    formData.append("type", $(this).data('image'));
    formData.append("inputFile", inputfile);

    $.ajax({
       url:'controllers/controlador_sucursal.php?metodo=subirImagenesAdicionales',
       data: formData,
       type: "POST",
       contentType: false,
       processData: false,
       success: function(response){
          console.log(response);
          if(response['success']==1){
            notify(imgSubida + " subida con éxito", "success");
              //img.attr("src", response['rutaImage']);
          }
          else{
            notify(response['mensaje'], "error");
          }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
});