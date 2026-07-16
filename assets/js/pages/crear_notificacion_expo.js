$(document).ready(function () {

    var metodosPorTipo = {
        "promo": "enviarPromo",
        "producto_nuevo": "enviarProductoNuevo",
        "evento": "enviarEvento"
    };

    $(".btnSendNotification").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmSave");
        form.validate();
        var isForm = form.valid();

        if (isForm == false) {
            notify("Falta llenar informacion", "success", 2);
            return false;
        }

        var tipo = $("#txt_tipo").val();
        var metodo = metodosPorTipo[tipo];
        if (!metodo) {
            messageDone("Tipo de notificacion no valido", "error");
            return false;
        }

        var formData = new FormData($("#frmSave")[0]);

        $.ajax({
            beforeSend: function () {
                OpenLoad("Enviando notificacion, por favor espere...");
            },
            url: 'controllers/controlador_notificaciones_expo.php?metodo=' + metodo,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'] + " (" + response['total_enviados'] + " clientes)", 'success');
                    setTimeout(function () {
                        window.location.href = $("#btnBack").attr("data-module-back");
                    }, 1500);
                }
                else {
                    messageDone(response['mensaje'], 'error');
                }
            },
            error: function (data) {
                console.log(data);
                messageDone("Ocurrió un error al enviar la notificación", 'error');
            },
            complete: function (resp) {
                CloseLoad();
            }
        });
    });

});
