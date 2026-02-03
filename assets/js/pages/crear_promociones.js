$(function() {
    
    $("#cmbDias").select2();
    flatpickr("#hora_ini", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultHour: 7, 
        defaultMinute: 0,
    });

    flatpickr("#hora_fin", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        defaultHour: 23, 
        defaultMinute: 0,
    });

    flatpickr(".time-start", {
        enableTime: true,
        noCalendar: true, 
        dateFormat: "H:i",
        defaultHour: 7, 
        defaultMinute: 0,
    });

    flatpickr(".time-end", {
        enableTime: true,
        noCalendar: true, 
        dateFormat: "H:i",
        defaultHour: 23, 
        defaultMinute: 0,
    });

    $("#btnGuardar").on("click", function (event) {
        event.preventDefault();

        let data = getInfoForms();
        if (data === false){
            notify("Falta llenar informacion", "error", 2);
            return false;
        }

        if ($("#cmb_tipo_descuento").val() == 0) { //Si el combo es descuento debe llenar cuando dara de descuento
            let porcentaje = parseFloat($("#porcentaje_descuento").val());
            if (isNaN(porcentaje) || porcentaje <= 0) {
                notify("Debes ingresar un porcentaje válido mayor a 0", "error", 2);
                return false;
            }
        }

        var id = parseInt($("#id").val());
        if (id > 0) {
            data.append('id', id);
        }
        storePromotion(data);

    });
});

function storePromotion(formData){
    $.ajax({
      beforeSend: function () {
        OpenLoad("Guardando datos, por favor espere...");
      },
      url: 'controllers/controlador_promociones.php?metodo=crearNew',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log(response);

        if (response['success'] == 1) {
            messageDone(response['mensaje'], 'success');
            $("#id").val(response['id']);
            const newUrl = `crear_promociones.php?id=${response['id']}`;
            window.history.replaceState(null, '', newUrl);
            if (response['new'] === true) {
                setTimeout(function () {
                    window.location.href = 'promociones.php';
                }, 1000);
            }
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

function getInfoForms() {
    let forms = ["#frmSave", "#frmDisponibilidad", "#frmProductos", "#frmDias", "#frmTipoEntrega"];
    let formData = new FormData();

    for (let i = 0; i < forms.length; i++) {
        let form = $(forms[i]);

        // Validar formulario
        form.validate();
        if (!form.valid()) {
            return false;
        }

        // Serializar y agregar al FormData
        let data = form.serializeArray();
        data.forEach(item => {
            formData.append(item.name, item.value);
        });
    }

    return formData;
}

$(".rbDisponibleDias").on("change", function () {
    var aux = $(this).val();
    if (aux == 1) {
        $(".chooseDias").show();
    } else {
        $(".chooseDias").hide();
    }
});

$(".rbTiposEntregas").on("change", function () {
    var aux = $(this).val();
    if (aux == 1) {
        $(".chooseTiposEntregas").show();
    } else {
        $(".chooseTiposEntregas").hide();
    }
});

$('.chk-day').on('change', function () {
    const row = $(this).closest('.day-item');
    row.find('input[type="text"]').prop('disabled', !this.checked);
});

$('#btnReplicarHorario').on('click', function () {

    // Buscar el primer día activo
    const firstActive = $('.chk-day:checked').first().closest('.day-item');

    if (!firstActive.length) {
        notify('Primero activa al menos un día', "error", 2);
        return;
    }

    const start = firstActive.find('.time-start').val();
    const end   = firstActive.find('.time-end').val();

    if (!start || !end) {
        notify('El primer día activo debe tener horario completo', "error", 2);
        return;
    }

    // Replicar a todos los días activos
    $('.chk-day:checked').each(function () {
        const row = $(this).closest('.day-item');
        row.find('.time-start').val(start);
        row.find('.time-end').val(end);
    });
});

$('.input-category').on('click', function () {
    let isChecked = $(`.cat-${$(this).data('category')}`).prop('checked')
    $(`.cat-${$(this).data('category')}`).prop('checked', $(this).prop('checked'))
})

$('.input-padre').on('click', function () {
        let isChecked = $(`.padre-${$(this).data('padre')}`).prop('checked')
        $(`.padre-${$(this).data('padre')}`).prop('checked', $(this).prop('checked'))
        isChecked = $(`.cat-${$(this).data('categoria')}`).prop('checked')
        $(`.cat-${$(this).data('categoria')}`).prop('checked', !isChecked)
})

$('.input-hijo').on('change', function () {
    if($(`.padre-${$(this).data('padre')}`).length == 0)
    $(`.input-padre${$(this).data('padre')}`).prop('checked', false)
    else
    $(`.input-padre${$(this).data('padre')}`).prop('checked', true)
})

$("#cmb_tipo_descuento").on('change', function(){
    let val = $(this).val();
    if(val == 0){
        $(".inputPorcentaje").show();
    }else{
        $(".inputPorcentaje").hide();
    }
});

$("#btnNuevo").on("click",function(){
    messageConfirm('¿Estas seguro?', '¡Perderas todos los cambios que no hayas guardado!', "warning")
    .then(function(result) {
        if (result) {
            window.location.href = "crear_promociones.php";
        }
    });
});

$("#btnEliminar").on("click",function(){
    var id = parseInt($("#id").val());
    if(id <= 0){
        messageDone('Error al eliminar la promoción','error');
        return;
    }
    messageConfirm('¿Estas seguro de eliminar esta promoción?', 'No se puede revertir los cambios', "warning")
    .then(function(result) {
        if (result) {
            var parametros = {
            "cod_promocion": id,
        }
        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando informacion, por favor espere...");
                },
            url: 'controllers/controlador_promociones.php?metodo=delete',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {
                    messageDone(response['mensaje'],'success');
                    setTimeout(function(){ 
                        window.location.href="promociones.php"
                    }, 1000);
                    
                } 
                else
                {
                    messageDone(response['mensaje'],'error');
                } 
                                            
            },
            error: function(data){
                console.log(data);
                
            },
            complete: function(resp)
            {
                CloseLoad();
            }
        });//FIN AJAX
        }
    });
});

$("#btnBack").on("click",function(){
    var link = $(this).attr("data-module-back");
    if (typeof link === "undefined") {
        link = "index.php";
    }
    messageConfirm('¿Estas seguro?', '¡Perderas todos los cambios que no hayas guardado!', "warning")
    .then(function(result) {
        if (result) {
            window.location.href = link;
        }
    });
});