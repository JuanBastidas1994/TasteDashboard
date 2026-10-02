/* Notificaciones push (Expo) — notificaciones.php: crear/programar campañas, audiencia y recompra */
$(document).ready(function () {

    const URL_CONTROLADOR = 'controllers/controlador_notificaciones_expo.php?metodo=';

    const editor = $('#txtMensaje').emojioneArea({
        pickerPosition: 'bottom',
        placeholder: $('#txtMensaje').attr('placeholder')
    })[0].emojioneArea;

    function getMensaje() {
        return editor.getText().trim();
    }

    /* ---------- Vista previa y contadores ---------- */
    function actualizarPreview() {
        const titulo = $('#txtTitulo').val().trim();
        const mensaje = getMensaje();
        $('#previewTitulo').text(titulo || 'Título de la notificación');
        $('#previewMensaje').text(mensaje || 'Aquí se verá tu mensaje.');
        $('#contTitulo').text($('#txtTitulo').val().length + '/50');
        $('#contMensaje').text(mensaje.length + '/200');
    }

    $('#txtTitulo').on('input', actualizarPreview);
    editor.on('keyup change paste emojibtn.click', function () {
        setTimeout(actualizarPreview, 0);
    });
    setTimeout(actualizarPreview, 300);

    /* ---------- Chips (tipo y cuándo) ---------- */
    function seleccionarChip($chip) {
        const $grupo = $chip.closest('.nt-chips');
        const valor = $chip.data('value');
        $grupo.find('.nt-chip').removeClass('activo');
        $chip.addClass('activo');
        $($grupo.data('input')).val(valor);

        if ($grupo.data('input') === '#txtTipo') {
            $('.nt-extra[data-tipo]').hide();
            $('.nt-extra[data-tipo="' + valor + '"]').show();
        } else {
            $('.nt-extra[data-cuando]').toggle(valor === 'programar');
            $('#btnEnviar').text(valor === 'programar' ? 'Programar' : 'Enviar ahora');
        }
    }

    $('.nt-chip').on('click', function () {
        seleccionarChip($(this));
    });
    seleccionarChip($('.nt-chips[data-input="#txtTipo"] .nt-chip[data-value="' + $('#txtTipo').val() + '"]'));

    /* ---------- Audiencia ---------- */
    let alcanceActual = parseInt($('#lblAlcance').text()) || 0;

    function actualizarAlcance() {
        const audiencia = $('#cmbAudiencia').val();
        $('.nt-extra[data-audiencia]').hide();
        $('.nt-extra[data-audiencia="' + audiencia + '"]').show();

        $.post(URL_CONTROLADOR + 'contarAudiencia', {
            audiencia: audiencia,
            dias_inactivo: $('#cmbDiasInactivo').val(),
            cod_sucursal: $('#cmbSucursal').val()
        }, function (response) {
            alcanceActual = response.total || 0;
            $('#lblAlcance').text(alcanceActual);
        });
    }

    $('#cmbAudiencia, #cmbDiasInactivo, #cmbSucursal').on('change', actualizarAlcance);

    /* Fecha programada: se envía al server como Y-m-d H:i, al usuario se le muestra d/m/Y H:i */
    const pickerProgramada = flatpickr('#txtFechaProgramada', {
        enableTime: true,
        time_24hr: true,
        dateFormat: 'Y-m-d H:i',
        altInput: true,
        altFormat: 'd/m/Y H:i',
        minDate: 'today',
        minuteIncrement: 5,
        disableMobile: true,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
            },
            months: {
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
        }
    });

    /* ---------- Enviar / programar ---------- */
    $('#btnEnviar').on('click', function () {
        const titulo = $('#txtTitulo').val().trim();
        const mensaje = getMensaje();
        const tipo = $('#txtTipo').val();
        const programar = $('#txtCuando').val() === 'programar';

        if (titulo === '' || mensaje === '') {
            messageDone('Escribe el título y el mensaje', 'error');
            return;
        }
        if (tipo === 'promo' && !$('#cmbPromocion').val()) {
            messageDone('Selecciona la promoción', 'error');
            return;
        }
        if (tipo === 'producto_nuevo' && !$('#cmbProducto').val()) {
            messageDone('Selecciona el producto', 'error');
            return;
        }
        if (programar && !$('#txtFechaProgramada').val()) {
            messageDone('Indica la fecha y hora de envío', 'error');
            return;
        }
        if (programar && pickerProgramada.selectedDates[0] <= new Date()) {
            messageDone('La fecha programada debe ser en el futuro', 'error');
            return;
        }
        if (!programar && alcanceActual === 0) {
            messageDone('Ningún cliente de esa audiencia tiene notificaciones activas', 'error');
            return;
        }

        const texto = programar
            ? 'Se enviará el ' + pickerProgramada.altInput.value.replace(' ', ' a las ') + ' a los clientes de esa audiencia.'
            : 'Le llegará a ' + alcanceActual + ' clientes y no se puede deshacer.';

        Swal.fire({
            title: programar ? '¿Programar notificación?' : '¿Enviar notificación?',
            text: texto,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: programar ? 'Sí, programar' : 'Sí, enviar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (!result.value) return;

            const datos = $('#frmNotificacion').serializeArray();
            datos.push({ name: 'mensaje', value: mensaje });

            $.ajax({
                beforeSend: function () {
                    OpenLoad(programar ? 'Programando...' : 'Enviando notificación, por favor espere...');
                },
                url: URL_CONTROLADOR + 'crearNotificacion',
                type: 'POST',
                data: $.param(datos),
                success: function (response) {
                    if (response.success == 1) {
                        const detalle = response.total_enviados !== undefined ? ' (' + response.total_enviados + ' clientes)' : '';
                        messageDone(response.mensaje + detalle, 'success');
                        setTimeout(function () { window.location.href = 'notificaciones.php'; }, 1500);
                    } else {
                        messageDone(response.mensaje, 'error');
                    }
                },
                error: function (data) {
                    console.log(data);
                    messageDone('Ocurrió un error al enviar la notificación', 'error');
                },
                complete: function () {
                    CloseLoad();
                }
            });
        });
    });

    /* ---------- Cancelar programada ---------- */
    $('body').on('click', '.btnCancelarProgramada', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: '¿Cancelar este envío?',
            text: 'La notificación programada no se enviará.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No',
            padding: '2em'
        }).then(function (result) {
            if (!result.value) return;
            $.post(URL_CONTROLADOR + 'cancelarProgramada', { id: id }, function (response) {
                if (response.success == 1) {
                    messageDone(response.mensaje, 'success');
                    setTimeout(function () { window.location.reload(); }, 1000);
                } else {
                    messageDone(response.mensaje, 'error');
                }
            });
        });
    });

    /* ---------- Recompra ---------- */
    $('#btnGuardarRecompra').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true);
        $.post(URL_CONTROLADOR + 'guardarRecompra', $('#frmRecompra').serialize(), function (response) {
            messageDone(response.mensaje, response.success == 1 ? 'success' : 'error');
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    $('#btnBack').on('click', function () {
        window.location.href = $(this).attr('data-module-back');
    });
});
