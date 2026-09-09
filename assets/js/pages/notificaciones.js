$(document).ready(function () {
    $(".btnSendNotification").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmSave");
        form.validate();
        var isForm = form.valid();

        if (isForm == false) {
            notify("Falta llenar informacion", "success", 2);
            return false;
        }

        var formData = new FormData($("#frmSave")[0]);
        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_notificaciones.php?metodo=notificar',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $("#txt_titulo").val("");
                    $("#txt_descripcion").val("");
                    $(".emojionearea-editor").html("");
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
});

function resetNotificarMotivos(){
    $(".notificar-motivo-row").removeClass("selected").css({ borderColor: "#edf0f5", background: "#fff" });
    $(".notificar-motivo-row .notificar-radio-dot").css({ borderColor: "#cbd5e0", background: "" }).empty();
}

$("body").on("click", ".notificar-motivo-row", function(){
    resetNotificarMotivos();

    $(this).addClass("selected").css({ borderColor: "#2d3748", background: "#f8f9fb" });
    $(this).find("input[type=radio]").prop("checked", true);
    $(this).find(".notificar-radio-dot")
        .css({ borderColor: "#2d3748", background: "#2d3748" })
        .html('<div style="width:6px;height:6px;border-radius:50%;background:#fff;margin:auto;"></div>');

    if ($(this).data("value") === "OTRO") {
        $("#notificar_otro_wrap").show();
    } else {
        $("#notificar_otro_wrap").hide();
    }
});

function notificarACliente() {
    let $selected = $(".notificar-motivo-row.selected");
    if ($selected.length === 0) {
        messageDone("Selecciona qué quieres avisarle al cliente", 'error');
        return;
    }

    let ordenId = $("#clientModal").data("ordenId");
    let titulo, descripcion;

    if ($selected.data("value") === "OTRO") {
        titulo = $("#clientModal .txtNotificaionTitulo").val().trim();
        descripcion = $("#clientModal .textRecordatorio").val().trim();

        if(titulo == "") {
            messageDone("Ingrese título de la notificación", 'error');
            return;
        }

        if(descripcion == "") {
            messageDone("Aségurese de llenar la descripción de la notificación", 'error');
            return;
        }
    } else {
        titulo = $selected.data("titulo");
        descripcion = $selected.data("mensaje");
    }

    let info = {
       cod_orden: ordenId,
       titulo,
       mensaje: descripcion
    }

    OpenLoad("Enviando...")
    fetch(`${ApiUrl}/ordenes/notificar-cliente`,{
        method: 'POST',
        headers: {
            'Api-Key': ApiKey
        },
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        if(response.success == 1){
            messageDone(response.mensaje, 'success');
            $(".notificar-motivo-row").removeClass("selected").css({ borderColor: "#edf0f5", background: "#fff" });
            $("#notificar_otro_wrap").hide();
            $(".txtNotificaionTitulo").val("");
            $(".textRecordatorio").val("");
            $(".emojionearea-editor").html("");
        }
        else{
            messageDone(response.mensaje, 'error');
        }
    })
    .catch(error=>{
        CloseLoad();
        messageDone("Ocurrió un error al enviar la notificación", 'error');
        console.log(error);
    });
}