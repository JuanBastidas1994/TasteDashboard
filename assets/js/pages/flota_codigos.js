// Invitar/reactivar motorizados por código — ver api_flotas/controllers/Flotas.php::generar-codigo
const { API_FLOTAS } = window.__CONFIG__;

// TODO: reemplazar por el link real de descarga (APK estable o Play Store) cuando exista uno.
const APP_DOWNLOAD_URL = 'PENDIENTE: pega aquí el link de descarga de la app';

let ApiKeyFlota = '';
let nombreObjetivoActual = '';

$(function () {
    ApiKeyFlota = $('#apikey_flota').val();
});

function generarCodigo(tipo, cod_usuario) {
    const body = { tipo };
    if (cod_usuario) body.cod_usuario = cod_usuario;

    fetch(`${API_FLOTAS}/flotas/generar-codigo`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKeyFlota, 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) {
                alert(response.mensaje || 'No se pudo generar el código');
                return;
            }
            mostrarCodigoModal(response.data, tipo);
        })
        .catch(error => console.error('Error al generar código:', error));
}

function mostrarCodigoModal(data, tipo) {
    const esGeneral = tipo === 'general';

    $('#codigoModalTitle').text(esGeneral ? 'Código de invitación' : 'Código de reactivación');
    $('#codigoModalDesc').text(esGeneral
        ? 'Cualquiera que use este código con su cuenta de Google se une a tu flota, marcado como invitado.'
        : `Este código solo sirve para que ${nombreObjetivoActual || 'este motorizado'} recupere su cuenta actual con su correo real de Google.`);
    $('#codigoModalValor').text(data.codigo);
    $('#codigoModalExpira').text(new Date(data.fecha_expiracion.replace(' ', 'T')).toLocaleString('es-EC'));

    const mensaje = esGeneral
        ? `Hola! Te invito a trabajar como motorizado. Descarga la app aquí: ${APP_DOWNLOAD_URL}\n\nAl entrar, inicia sesión con tu cuenta de Google y usa este código para unirte a la flota: ${data.codigo}`
        : `Hola! Vamos a actualizar tu cuenta a tu correo real. Descarga la app aquí: ${APP_DOWNLOAD_URL}\n\nInicia sesión con TU cuenta de Google (la que uses de verdad) y usa este código: ${data.codigo}`;
    $('#codigoModalMensaje').val(mensaje);

    $('#codigoModal').modal();
}

$('#btnInvitar').on('click', function () {
    nombreObjetivoActual = '';
    generarCodigo('general');
});

$('body').on('click', '.btnReactivar', function (event) {
    event.preventDefault();
    const id = parseInt($(this).attr('data-value'));
    if (!id) return;
    nombreObjetivoActual = $(this).closest('tr').find('td').eq(1).text().trim();
    generarCodigo('reactivacion', id);
});

$('#btnCopiarMensaje').on('click', function () {
    const el = document.getElementById('codigoModalMensaje');
    el.select();
    document.execCommand('copy');
    if (typeof notify === 'function') notify('Mensaje copiado', 'success', 2);
});

$('body').on('click', '.btnQuitarInvitado', function (event) {
    event.preventDefault();
    const $link = $(this);
    const id = parseInt($link.attr('data-value'));
    if (!id) return;

    messageConfirm('¿Quitar la marca de invitado?', 'Este motorizado dejará de mostrarse como invitado.', 'warning')
    .then(function (ok) {
        if (!ok) return;

        fetch(`${API_FLOTAS}/flotas/quitar-invitado`, {
            method: 'POST',
            headers: { 'Api-Key': ApiKeyFlota, 'Content-Type': 'application/json' },
            body: JSON.stringify({ cod_usuario: id }),
        })
            .then(res => res.json())
            .then(response => {
                if (response.success != 1) {
                    if (typeof notify === 'function') notify(response.mensaje || 'No se pudo actualizar', 'error', 3);
                    return;
                }
                $link.remove();
                if (typeof notify === 'function') notify('Ya no es invitado', 'success', 2);
            })
            .catch(error => {
                console.error('Error al quitar invitado:', error);
                if (typeof notify === 'function') notify('Error al actualizar', 'error', 3);
            });
    });
});

let notificarCodUsuarioActual = null;

$('body').on('click', '.btnNotificar', function (event) {
    event.preventDefault();
    notificarCodUsuarioActual = parseInt($(this).attr('data-value'));
    if (!notificarCodUsuarioActual) return;
    $('#notificarModalNombre').text($(this).attr('data-nombre') || '');
    $('#notificarModalMensaje').val('');
    $('#notificarModal').modal();
});

$('#btnEnviarNotificacion').on('click', function () {
    const mensaje = $('#notificarModalMensaje').val().trim();
    if (!mensaje) {
        if (typeof notify === 'function') notify('Escribe un mensaje', 'error', 2);
        return;
    }
    if (!notificarCodUsuarioActual) return;

    const $btn = $(this);
    $btn.prop('disabled', true);
    fetch(`${API_FLOTAS}/flotas/notificar-motorizado`, {
        method: 'POST',
        headers: { 'Api-Key': ApiKeyFlota, 'Content-Type': 'application/json' },
        body: JSON.stringify({ cod_usuario: notificarCodUsuarioActual, mensaje }),
    })
        .then(res => res.json())
        .then(response => {
            if (response.success != 1) {
                if (typeof notify === 'function') notify(response.mensaje || 'No se pudo enviar', 'error', 3);
                return;
            }
            $('#notificarModal').modal('hide');
            if (typeof notify === 'function') notify('Notificación enviada', 'success', 2);
        })
        .catch(error => {
            console.error('Error al notificar:', error);
            if (typeof notify === 'function') notify('Error al enviar', 'error', 3);
        })
        .finally(() => $btn.prop('disabled', false));
});
