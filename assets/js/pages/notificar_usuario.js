/* Mensaje push (Expo) a UN usuario — solo mensaje, el título es el nombre de la empresa.
   - Campana en listados (clientes.php): .btnNotificarUsuario[data-value][data-nombre] abre #notificarUsuarioModal
   - Tarjeta en cliente_detalle.php / usuario_detalle.php: #txtNotificarUsuario (con emojis) + #btnNotificarUsuario[data-usuario] */

function enviarNotificacionUsuario(cod_usuario, mensaje) {
    return $.ajax({
        url: 'controllers/controlador_notificaciones_expo.php?metodo=enviarUsuario',
        type: 'POST',
        data: { cod_usuario: cod_usuario, mensaje: mensaje }
    });
}

/* ---------- Campana en listados (modal) ---------- */
let notificarUsuarioActual = null;

$('body').on('click', '.btnNotificarUsuario', function (event) {
    event.preventDefault();
    notificarUsuarioActual = parseInt($(this).attr('data-value'));
    if (!notificarUsuarioActual) return;
    $('#notificarUsuarioNombre').text($(this).attr('data-nombre') || '');
    $('#notificarUsuarioMensaje').val('');
    $('#notificarUsuarioModal').modal();
});

$('body').on('click', '#btnEnviarNotificacionUsuario', function () {
    const mensaje = $('#notificarUsuarioMensaje').val().trim();
    if (!mensaje) {
        notify('Escribe un mensaje', 'error', 2);
        return;
    }
    if (!notificarUsuarioActual) return;

    const $btn = $(this);
    $btn.prop('disabled', true);
    enviarNotificacionUsuario(notificarUsuarioActual, mensaje)
        .done(function (response) {
            if (response.success != 1) {
                notify(response.mensaje || 'No se pudo enviar', 'error', 3);
                return;
            }
            $('#notificarUsuarioModal').modal('hide');
            notify('Notificación enviada', 'success', 2);
        })
        .fail(function (error) {
            console.error('Error al notificar:', error);
            notify('Error al enviar', 'error', 3);
        })
        .always(function () {
            $btn.prop('disabled', false);
        });
});

/* ---------- Tarjeta "Notificar" en páginas de detalle ---------- */
$(function () {
    const $txt = $('#txtNotificarUsuario');
    if (!$txt.length) return;

    const editor = $txt.emojioneArea({
        pickerPosition: 'bottom',
        placeholder: $txt.attr('placeholder')
    })[0].emojioneArea;

    $('#btnNotificarUsuario').on('click', function () {
        const mensaje = editor.getText().trim();
        if (!mensaje) {
            messageDone('La notificación no puede estar vacía', 'error');
            return;
        }

        const $btn = $(this);
        $btn.addClass('disabled');
        enviarNotificacionUsuario($btn.data('usuario'), mensaje)
            .done(function (response) {
                if (response.success == 1) {
                    messageDone(response.mensaje, 'success');
                    editor.setText('');
                } else {
                    messageDone(response.mensaje, 'error');
                }
            })
            .fail(function () {
                messageDone('Error al enviar la notificación, intenta nuevamente', 'error');
            })
            .always(function () {
                $btn.removeClass('disabled');
            });
    });
});
