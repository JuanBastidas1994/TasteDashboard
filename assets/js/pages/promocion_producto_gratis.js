$(function () {

    // Si hay un producto previamente seleccionado, mostrar fila nombre y actualizar overlay
    if ($('#cmb_producto').val()) {
        $('#rowNombreProducto').show();
        updateOverlay();
    }

    // ── Dropify ────────────────────────────────────────────────────────────────
    var drPG = $('#inputImgPG').dropify({
        messages: {
            'default': 'Click para subir o arrastra (PNG / JPG)',
            'remove':  'X',
            'replace': 'Cambiar imagen'
        }
    });

    // ── Croppie ────────────────────────────────────────────────────────────────
    var croppiePG = null;

    drPG.on('dropify.fileReady', function () {
        if (croppiePG) { croppiePG.destroy(); croppiePG = null; }
        $('#modalCroppiePG').modal({ backdrop: 'static', keyboard: false });
    });

    $('#modalCroppiePG').on('shown.bs.modal', function () {
        var file = $('#inputImgPG').get(0).files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#pgImgCrop').attr('src', e.target.result);
            croppiePG = new Croppie($('#pgImgCrop')[0], {
                viewport:          { width: 500, height: 300 },
                boundary:          { width: 520, height: 380 },
                showZoomer:        true,
                enableResize:      false,
                enableOrientation: true
            });

            $('#pgCropGet').off('click').on('click', function () {
                croppiePG.result({ type: 'base64', format: 'jpeg', quality: 1 }).then(function (dataImg) {
                    $('#txt_crop').val(dataImg);
                    $('#pgImgPreview').attr('src', dataImg);
                    $('#pgImgContainer').show();
                    $('#pgImgUploader').hide();
                    $('#modalCroppiePG').modal('hide');
                    updateOverlay();
                    if (typeof feather !== 'undefined') feather.replace();
                });
            });

            $('.pg-crop-rotate').off('click').on('click', function () {
                croppiePG.rotate(parseInt($(this).data('deg')));
            });
        };
        reader.readAsDataURL(file);
    });

    $('#modalCroppiePG').on('hidden.bs.modal', function () {
        if (croppiePG) { croppiePG.destroy(); croppiePG = null; }
    });

    // ── Quitar imagen ──────────────────────────────────────────────────────────
    $('#btnQuitarImgPG').on('click', function () {
        $('#txt_crop').val('');
        $('#imagen_actual').val('');
        $('#pgImgContainer').hide();
        var dr = $('#inputImgPG').data('dropify');
        if (dr) dr.resetPreview();
        $('#pgImgUploader').show();
        if (typeof feather !== 'undefined') feather.replace();
    });

    // ── Producto select → poblar nombre editable ───────────────────────────────
    $('#cmb_producto').on('change', function () {
        var nombre = $(this).find('option:selected').data('nombre') || '';
        if (nombre) {
            $('#txt_producto_nombre').val(nombre);
            $('#rowNombreProducto').show();
        } else {
            $('#rowNombreProducto').hide();
        }
        updateOverlay();
    });

    // ── Overlay en tiempo real ─────────────────────────────────────────────────
    $('#txt_monto_minimo, #txt_producto_nombre').on('input', updateOverlay);

    // ── Guardar ────────────────────────────────────────────────────────────────
    $('#btnGuardarPG').on('click', function () {
        var sucursales = [];
        $('.chkSucursal:checked').each(function () {
            sucursales.push($(this).val());
        });

        var cod_producto    = $('#cmb_producto').val();
        var nombre_producto = $.trim($('#txt_producto_nombre').val());
        var monto           = parseFloat($('#txt_monto_minimo').val());

        if (!cod_producto || cod_producto === '') {
            messageDone('Selecciona un producto', 'error');
            return;
        }
        if (!nombre_producto) {
            messageDone('Ingresa el nombre del producto', 'error');
            return;
        }
        if (isNaN(monto) || monto <= 0) {
            messageDone('Ingresa un monto mínimo válido', 'error');
            return;
        }
        if (sucursales.length === 0) {
            messageDone('Selecciona al menos una sucursal', 'error');
            return;
        }

        swal.fire({
            title: '¿Guardar configuración?',
            text: 'Se aplicará el producto gratis en ' + sucursales.length + ' sucursal(es).',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.value) return;

            var formData = new FormData();
            formData.append('cod_producto',        cod_producto);
            formData.append('txt_producto_nombre', nombre_producto);
            formData.append('txt_monto_minimo',    monto);
            formData.append('fecha_inicio',        $('#fecha_inicio').val());
            formData.append('fecha_fin',           $('#fecha_fin').val());
            formData.append('is_web',              $('#chk_is_web').is(':checked') ? 1 : 0);
            formData.append('is_app',              $('#chk_is_app').is(':checked') ? 1 : 0);
            formData.append('txt_titulo',          $('#txt_titulo').val());
            formData.append('txt_crop',            $('#txt_crop').val());
            formData.append('imagen_actual',       $('#imagen_actual').val());
            sucursales.forEach(function (s) {
                formData.append('chkSucursal[]', s);
            });

            $.ajax({
                beforeSend: function () { OpenLoad('Guardando...'); },
                url:         'controllers/controlador_promocion_producto_gratis.php?metodo=guardar',
                type:        'POST',
                data:         formData,
                contentType:  false,
                processData:  false,
                success: function (response) {
                    messageDone(response['mensaje'], response['success'] == 1 ? 'success' : 'error');
                    if (response['success'] == 1 && $('#imagen_actual').val() === '' && $('#txt_crop').val() !== '') {
                        $('#txt_crop').val('');
                    }
                },
                error: function () {
                    messageDone('Error al conectar con el servidor', 'error');
                },
                complete: function () { CloseLoad(); }
            });
        });
    });

});

function updateOverlay() {
    var monto  = parseFloat($('#txt_monto_minimo').val()) || 0;
    var nombre = $.trim($('#txt_producto_nombre').val()) || '—';
    $('#ovMonto').text('$' + monto.toFixed(2));
    $('#ovProducto').text(nombre);
}
