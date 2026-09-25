/* Notificaciones push (Expo) — envío general a todos los clientes desde notificaciones.php */
$(document).ready(function () {

    var editor = $("#txt_descripcion").emojioneArea({
        pickerPosition: "bottom",
        placeholder: $("#txt_descripcion").attr("placeholder")
    })[0].emojioneArea;

    function getMensaje() {
        return editor.getText().trim();
    }

    function actualizarPreview() {
        var titulo = $("#txt_titulo").val().trim();
        var mensaje = getMensaje();
        $("#previewTitulo").text(titulo || "Título de la notificación");
        $("#previewMensaje").text(mensaje || "Aquí se verá tu mensaje.");
        $("#contTitulo").text($("#txt_titulo").val().length + "/65");
    }

    $("#txt_titulo").on("input", actualizarPreview);
    editor.on("keyup change paste emojibtn.click", function () {
        setTimeout(actualizarPreview, 0);
    });

    $("#btnBack").on("click", function () {
        window.location.href = $(this).attr("data-module-back");
    });

    $(".btnSendNotification").on("click", function (event) {
        event.preventDefault();

        var titulo = $("#txt_titulo").val().trim();
        var mensaje = getMensaje();
        if (titulo == "" || mensaje == "") {
            messageDone("Escribe el título y el mensaje", "error");
            return;
        }

        var total = $(this).data("total");
        Swal.fire({
            title: "¿Enviar notificación?",
            text: "Le llegará a " + total + " clientes y no se puede deshacer.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, enviar",
            cancelButtonText: "Cancelar",
            padding: "2em"
        }).then(function (result) {
            if (!result.value) return;

            $.ajax({
                beforeSend: function () {
                    OpenLoad("Enviando notificación, por favor espere...");
                },
                url: "controllers/controlador_notificaciones_expo.php?metodo=enviarEvento",
                type: "POST",
                data: { titulo: titulo, descripcion: mensaje },
                success: function (response) {
                    if (response["success"] == 1) {
                        messageDone(response["mensaje"] + " (" + response["total_enviados"] + " clientes)", "success");
                        setTimeout(function () { window.location.reload(); }, 1500);
                    } else {
                        messageDone(response["mensaje"], "error");
                    }
                },
                error: function (data) {
                    console.log(data);
                    messageDone("Ocurrió un error al enviar la notificación", "error");
                },
                complete: function () {
                    CloseLoad();
                }
            });
        });
    });
});
