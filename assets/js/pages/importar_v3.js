var templateItemTabla = Handlebars.compile($("#itemTabla").html());

$(document).ready(function () {

    $("#btn_importar").on("click", function () {
        var excel = document.getElementById("excel");

        if (excel.files.length === 0) {
            messageDone("Seleccione un archivo Excel primero", 'error');
            return;
        }

        if (!confirm("Se procesará el archivo y se crearán o actualizarán los productos. ¿Desea continuar?")) {
            return;
        }

        $("#btn_importar").prop("disabled", true);
        $("#divCargando").show();
        $("#divDatos").hide();

        var formData = new FormData($("#frmImportar")[0]);

        $.ajax({
            url: "controllers/controlador_importar_v3.php?metodo=importar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#btn_importar").prop("disabled", false);
                $("#divCargando").hide();

                if (response.success == 1) {
                    var data = response.data;
                    var ok   = data.filter(function (r) { return r.importado; }).length;
                    var err  = data.length - ok;

                    $("#resumen").html(
                        '<span class="badge badge-success mr-2">Exitosos: ' + ok + '</span>' +
                        '<span class="badge badge-danger">Con error: ' + err + '</span>'
                    );

                    $("#datos").html(templateItemTabla(data));
                    $("#divDatos").show();
                    messageDone(response.mensaje, "success");
                } else {
                    messageDone(response.mensaje, "error");
                }
            },
            error: function (xhr) {
                $("#btn_importar").prop("disabled", false);
                $("#divCargando").hide();
                messageDone("Error de comunicación con el servidor", "error");
                console.error(xhr.responseText);
            }
        });
    });

});
