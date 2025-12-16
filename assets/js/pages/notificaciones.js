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

function notificarACliente(id) {
    let titulo = $("#clientModal .txtNotificaionTitulo").val().trim();
    let descripcion = $("#clientModal .textRecordatorio").val().trim();
    let tipo = $("#clientModal .cmbTipoNotificacion").val().trim();

    if(titulo == "") {
        messageDone("Ingrese título de la notificación", 'error');
        return;
    }

    if(descripcion == "") {
        messageDone("Aségurese de llenar la descripción de la notificación", 'error');
        return;
    }
    
    if(tipo == "") {
        messageDone("Aségurese de llenar la descripción de la notificación", 'error');
        return;
    }

    let info = {
       cod_usuario: id,
       titulo,
       descripcion,
       tipo
    }

    OpenLoad("Enviando...")
    fetch(`controllers/controlador_notificaciones.php?metodo=notificarClientes`,{
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        CloseLoad();
        if(response.success == 1){
            messageDone(response.mensaje, 'success');
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