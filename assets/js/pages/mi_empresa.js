$("body").on("click", "#btnActualizarImpuesto", function () {
    var cod_empresa = $("#cod_empresa").val();
    var impuesto    = parseInt($("#txt_impuesto").val());
    var tipo        = $("#cmb_tipo").val();

    if (isNaN(impuesto) || impuesto < 0 || impuesto > 100) {
        messageDone("Ingrese un porcentaje válido entre 0 y 100", "error");
        return;
    }

    swal.fire({
        title: "¿Actualizar el impuesto?",
        text: "Esta acción actualizará el impuesto y recalculará los precios de todos los productos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, actualizar",
        cancelButtonText: "Cancelar"
    }).then(function (result) {
        if (!result.value) return;

        $.ajax({
            beforeSend: function () {
                OpenLoad("Actualizando impuesto, por favor espere...");
            },
            url: "controllers/controlador_empresa.php?metodo=actualizarImpuesto",
            type: "GET",
            data: {
                cod_empresa: cod_empresa,
                impuesto:    impuesto,
                tipo:        tipo
            },
            success: function (response) {
                if (response["success"] == 1) {
                    messageDone(response["mensaje"], "success");
                    // Actualizar el badge del impuesto visible
                    $(".badge.badge-primary").text(impuesto + "%");
                } else {
                    messageDone(response["mensaje"], "error");
                }
            },
            error: function () {
                messageDone("Error al conectar con el servidor", "error");
            },
            complete: function () {
                CloseLoad();
            }
        });
    });
});
