$(document).ready(function () {

});

$("body").on("change", "#cmbAmbienteGacela", function () {
    var parametros = {
        "id": $("#id").val(),
        "ambiente": $("#cmbAmbienteGacela").val()
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Por favor espere...");
        },
        url: 'controllers/controlador_empresa.php?metodo=InfoDelivery',
        type: 'GET',
        data: parametros,
        success: function (response) {
            console.log(response);
            $("#tablaDelivery").html(response['info']);
            feather.replace();
        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });
});

$("body").on("click", ".btnEditarGacela", function () {

    var codigoSucursal = $(this).attr("data-codigo");
    var codigoGacela = $(this).attr("data-id");
    if ($(this).html() == "Editar") {
        $(this).html("Guardar");
        $("#txt_empresaG" + codigoSucursal).prop("disabled", false);
        $("#txt_sucursalG" + codigoSucursal).prop("disabled", false);
    }
    else {
        var parametros = {
            "api": $("#txt_empresaG" + codigoSucursal).val(),
            "token": $("#txt_sucursalG" + codigoSucursal).val(),
            "ambiente": $("#cmbAmbienteGacela").val(),
            "cod_gacela_sucursal": codigoGacela,
            "cod_sucursal": codigoSucursal,
            "cod_empresa": $("#id").val()
        }
        console.log(parametros);
        $.ajax({
            beforeSend: function () {
                OpenLoad("Verificando datos, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=verificarTokens',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 0) {
                    messageDone(response['mensaje'], 'error');
                }
                else if (response['success'] == 1) {
                    $("#editar_" + codigoSucursal).html("Editar");
                    $("#txt_empresaG" + codigoSucursal).prop("disabled", true);
                    $("#txt_sucursalG" + codigoSucursal).prop("disabled", true);
                    messageDone(response['mensaje'], 'success');
                    if (codigoGacela == 0) {
                        $("#editar_" + codigoSucursal).attr("data-id", response['idGacela']);
                    }
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

$("body").on("click", ".btnVerconfigGacela", function () {
    var api = $(this).attr("data-api");
    var token = $(this).attr("data-token");
    var parametros = {
        "api": api,
        "token": token,
        "ambiente": $("#cmbAmbienteGacela").val()
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Por favor espere...");
        },
        url: 'controllers/controlador_empresa.php?metodo=viewConfigGacela',
        type: 'GET',
        data: parametros,
        success: function (response) {
            console.log(response);
            messageDone(response['info']['status'], 'success');
        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });

});

$("body").on("click", ".btnEditarLaar", function () {

    var codigoSucursal = $(this).attr("data-codigo");
    var codigoLaar = $(this).attr("data-id");
    if ($(this).html() == "Editar") {
        $(this).html("Guardar");
        $("#txt_userL" + codigoSucursal).prop("disabled", false);
        $("#txt_passL" + codigoSucursal).prop("disabled", false);
    }
    else {
        var user = $("#txt_userL" + codigoSucursal).val();
        var pass = $("#txt_passL" + codigoSucursal).val();

        alert(user);
        alert(pass);
        alert(codigoLaar);

        if (user == "") {
            messageDone("Campo Obligatorio..", 'error');
            return;
        }

        if (pass == "") {
            messageDone("Campo Obligatorio..", 'error');
            return;
        }

        var parametros = {
            "user": user,
            "pass": pass,
            "cod_laar_sucursal": codigoLaar,
            "cod_sucursal": codigoSucursal,
            "cod_empresa": $("#id").val()
        }
        $.ajax({
            beforeSend: function () {
                OpenLoad("Verificando datos, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=SaveTokensLaar',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 0) {
                    messageDone(response['mensaje'], 'error');
                }
                else if (response['success'] == 1) {
                    $("#editar_L" + codigoSucursal).html("Editar");
                    $("#txt_userL" + codigoSucursal).prop("disabled", true);
                    $("#txt_passL" + codigoSucursal).prop("disabled", true);
                    messageDone("Token Guardado Correctamente", 'success');
                    if (codigoLaar == 0) {
                        $("#editar_L" + codigoSucursal).attr("data-id", response['idLaar']);
                    }
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

$("body").on("change", "#cmbAmbientePicker", function () {
    var parametros = {
        "id": $("#id").val(),
        "ambiente": $("#cmbAmbientePicker").val()
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Por favor espere...");
        },
        url: 'controllers/controlador_empresa.php?metodo=InfoDeliveryPicker',
        type: 'GET',
        data: parametros,
        success: function (response) {
            console.log(response);
            $("#tablaDeliveryPicker").html(response['info']);
            feather.replace();
        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });
});

$("body").on("click", ".btnVerconfigPicker", function () {
    var api = $(this).attr("data-api");
    var token = $(this).attr("data-token");
    var parametros = {
        "api": api,
        "token": token,
        "ambiente": $("#cmbAmbientePicker").val()
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Por favor espere...");
        },
        url: 'controllers/controlador_empresa.php?metodo=viewConfigPicker',
        type: 'GET',
        data: parametros,
        success: function (response) {
            console.log(response);
            messageDone(response['info']['status'], 'success');
        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });

});

$("body").on("click", ".btnEditarPicker", function () {

    var codigoSucursal = $(this).attr("data-codigo");
    var codigoPicker = $(this).attr("data-id");
    if ($(this).html() == "Editar") {
        $(this).html("Guardar");
        $("#txt_empresaP" + codigoSucursal).prop("disabled", false);
        $("#txt_sucursalP" + codigoSucursal).prop("disabled", false);
    }
    else {
        var parametros = {
            "api": $("#txt_empresaP" + codigoSucursal).val(),
            "ambiente": $("#cmbAmbientePicker").val(),
            "cod_picker_sucursal": codigoPicker,
            "cod_sucursal": codigoSucursal,
            "cod_empresa": $("#id").val()
        }
        console.log(parametros);
        $.ajax({
            beforeSend: function () {
                OpenLoad("Verificando datos, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=verificarTokensPicker',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 0) {
                    messageDone(response['mensaje'], 'error');
                }
                else if (response['success'] == 1) {
                    $("#editarP_" + codigoSucursal).html("Editar");
                    $("#txt_empresaP" + codigoSucursal).prop("disabled", true);
                    $("#txt_sucursalP" + codigoSucursal).prop("disabled", true);
                    messageDone(response['mensaje'], 'success');
                    if (codigoPicker == 0) {
                        $("#editarP_" + codigoSucursal).attr("data-id", response['idGacela']);
                    }
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

$("body").on("change", ".ckMisMotos", function () {
    let input = $(this);
    let office = input.val();
    let courier = input.data("courier");
    let status = "I";
    if (input.is(":checked"))
        status = "A";

    let data = {
        office,
        courier,
        status
    }

    console.log(data);

    $.ajax({
        url: 'controllers/controlador_sucursal.php?metodo=setCourierOffice',
        data,
        type: "POST",
        headers: {
            Accept: 'application/json'
        },
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
        },
        complete: function () {
        },
    });
});

$("body").on("change", ".ckMiFlota", function () {
    let input = $(this);
    let office = input.val();
    let courier = input.data("courier");
    let status = "I";
    if (input.is(":checked"))
        status = "A";

    let data = {
        office,
        courier,
        status
    }

    console.log(data);

    $.ajax({
        url: 'controllers/controlador_sucursal.php?metodo=setFlotaOffice',
        data,
        type: "POST",
        headers: {
            Accept: 'application/json'
        },
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
        },
        complete: function () {
        },
    });
});

$("body").on("click", ".btnEditarPedidosYa", function () {
    var codigoSucursal = $(this).attr("data-codigo");
    var codigoPedidosYa = $(this).attr("data-id");
    var estado = "A";
    if(!$(this).parent().prev().find(".estadoToken").is(":checked"))
        estado = "I";
    if ($(this).html() == "Editar") {
        $(this).html("Guardar");
        $("#txt_empresaPYA" + codigoSucursal).prop("disabled", false);
        $("#txt_sucursalPYA" + codigoSucursal).prop("disabled", false);
    }
    else {
        let data = {
            token: $("#txt_empresaPYA" + codigoSucursal).val(),
            ambiente: $("#cmbAmbientePedidosYa").val(),
            cod_pedidosya_sucursal: codigoPedidosYa,
            cod_sucursal: codigoSucursal,
            cod_empresa: $("#id").val(),
            estado
        };
       
        fetch(`controllers/controlador_empresa.php?metodo=setPedidosYa`,{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if (response['success'] == 0) {
                messageDone(response.mensaje, 'error');
            }
            else if (response.success == 1) {
                $("#editar_ya" + codigoSucursal).html("Editar");
                $("#txt_empresaPYA" + codigoSucursal).prop("disabled", true);
                $("#txt_sucursalPYA" + codigoSucursal).prop("disabled", true);
                messageDone(response.mensaje, 'success');
                if (codigoPedidosYa == 0) {
                    $("#editar_ya" + codigoSucursal).attr("data-id", response.cod_pedidosya_sucursal);
                }
            }
        })
        .catch(error=>{
            console.log(error);
        });
    }
});

$("body").on("click", ".tabCourier", function() {
    let id = $(this).data("id");
    switch (id) {
        case 5: //PEDIDOSYA
            $("#cmbAmbientePedidosYa").trigger("change");
            break;
    }
});

$("body").on("change", "#cmbAmbientePedidosYa", function () {
    getDataPedidosYa();
});

function getDataPedidosYa() {
    let target = $("#tablaDeliveryPedidosYa");
    target.html("");

    var parametros = {
        id: $("#id").val(),
        ambiente: $("#cmbAmbientePedidosYa").val()
    }
    $.ajax({
        beforeSend: function () {
            OpenLoad("Por favor espere...");
        },
        url: 'controllers/controlador_empresa.php?metodo=InfoDeliveryPedidosYa',
        type: 'GET',
        data: parametros,
        success: function (response) {
            console.log(response);
            if(response.success == 1) {
                let template = Handlebars.compile($("#pedidosya-template").html());
                target.html(template(response.data));
            }
            else {
                messageDone(response.mensaje, "error");
            }
            
            feather.replace();
        },
        error: function (data) {
            console.log(data);

        },
        complete: function (resp) {
            CloseLoad();
        }
    });
}