$(function () {
    var TIPOS = ["image/jpeg", "image/png", "image/webp"];
    var colaMasiva = [];
    var cardDestino = null;

    // Alias en mayúsculas -> tarjeta del producto
    var cardsPorAlias = {};
    $(".cardImagen").each(function () {
        cardsPorAlias[$(this).attr("data-alias")] = $(this);
    });
    actualizarContador();

    // Nombre del archivo sin extensión, en mayúsculas, para comparar con el alias
    function aliasDeArchivo(nombre) {
        return $.trim(nombre.replace(/\.[^.]+$/, "")).toUpperCase();
    }

    function escapar(texto) {
        return $("<div>").text(texto).html();
    }

    function tamanio(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + " MB" : Math.round(bytes / 1024) + " KB";
    }

    function subirImagen(card, file) {
        var estado = card.find(".estadoSubida");
        if (TIPOS.indexOf(file.type) === -1) {
            card.removeClass("ok").addClass("error");
            estado.html('<span class="text-danger">Solo JPG, PNG o WEBP</span>');
            return $.Deferred().reject().promise();
        }

        var formData = new FormData();
        formData.append("cod_producto", card.attr("data-id"));
        formData.append("imagen", file);

        card.removeClass("ok error").addClass("subiendo");
        estado.html("Subiendo " + tamanio(file.size) + "...");

        return $.ajax({
            url: "controllers/controlador_productos.php?metodo=subir_imagen_directa",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false
        }).done(function (response) {
            if (response["success"] == 1) {
                card.addClass("ok");
                card.attr("data-sin-imagen", 0);
                card.find(".contImg").html('<img src="' + response["imagen"] + '" alt="">');
                estado.html('<span class="text-success">✓ ' + escapar(response["detalle"]) + "</span>");
            } else {
                card.addClass("error");
                estado.html('<span class="text-danger">' + escapar(response["mensaje"]) + "</span>");
            }
        }).fail(function () {
            card.addClass("error");
            estado.html('<span class="text-danger">Error de conexión</span>');
        }).always(function () {
            card.removeClass("subiendo");
            actualizarContador();
        });
    }

    // Evita que el navegador abra la imagen si se suelta fuera de una zona
    $(document).on("dragover drop", function (e) {
        e.preventDefault();
    });

    /* SUBIDA POR TARJETA */
    $(".gridProductos").on("dragover dragenter", ".cardImagen", function (e) {
        e.preventDefault();
        $(this).addClass("arrastrando");
    });
    $(".gridProductos").on("dragleave drop", ".cardImagen", function (e) {
        if (e.type === "dragleave" && this.contains(e.originalEvent.relatedTarget))
            return;
        $(this).removeClass("arrastrando");
    });
    $(".gridProductos").on("drop", ".cardImagen", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 1) {
            notify("Suelta una sola imagen por producto. Para varias usa la carga masiva", "error", 3);
            return;
        }
        if (files.length === 1)
            subirImagen($(this), files[0]);
    });

    $(".gridProductos").on("click", ".contImg", function () {
        cardDestino = $(this).closest(".cardImagen");
        $("#fileUnico").val("").trigger("click");
    });
    $("#fileUnico").on("change", function () {
        if (this.files.length && cardDestino)
            subirImagen(cardDestino, this.files[0]);
    });

    /* COPIAR NOMBRE DE ARCHIVO */
    $(".gridProductos").on("click", ".btnCopiar", function () {
        var texto = $(this).attr("data-texto");
        var boton = $(this);
        copiarTexto(texto).then(function () {
            boton.text("¡Copiado!");
            setTimeout(function () { boton.text("Copiar"); }, 1200);
        });
    });

    function copiarTexto(texto) {
        if (navigator.clipboard && window.isSecureContext)
            return navigator.clipboard.writeText(texto);
        var aux = $("<textarea>").val(texto).css({ position: "fixed", opacity: 0 }).appendTo("body");
        aux[0].select();
        document.execCommand("copy");
        aux.remove();
        return Promise.resolve();
    }

    /* CARGA MASIVA: se asigna por alias == nombre de archivo */
    var zona = $("#zonaMasiva");
    zona.on("click", function () {
        $("#fileMasivo").val("").trigger("click");
    });
    zona.on("dragover dragenter", function (e) {
        e.preventDefault();
        zona.addClass("arrastrando");
    });
    zona.on("dragleave", function () {
        zona.removeClass("arrastrando");
    });
    zona.on("drop", function (e) {
        e.preventDefault();
        zona.removeClass("arrastrando");
        prepararMasivo(e.originalEvent.dataTransfer.files);
    });
    $("#fileMasivo").on("change", function () {
        prepararMasivo(this.files);
    });

    function prepararMasivo(files) {
        colaMasiva = [];
        var filas = "";
        var usados = {};
        $.each(files, function (i, file) {
            var alias = aliasDeArchivo(file.name);
            var card = cardsPorAlias[alias];
            var motivo = "";
            if (TIPOS.indexOf(file.type) === -1)
                motivo = "Formato no permitido";
            else if (!card)
                motivo = "No hay producto con alias " + alias;
            else if (usados[alias])
                motivo = "Archivo repetido para el mismo producto";

            if (motivo == "") {
                usados[alias] = true;
                colaMasiva.push({ file: file, card: card, fila: i });
            }
            filas += '<tr id="filaMasiva' + i + '">'
                + "<td>" + escapar(file.name) + " <small>(" + tamanio(file.size) + ")</small></td>"
                + "<td>" + (card && motivo == "" ? escapar(card.find(".nombre").text()) : "—") + "</td>"
                + '<td class="estado">' + (motivo == "" ? "Listo para subir" : '<span class="text-danger">' + escapar(motivo) + "</span>") + "</td>"
                + "</tr>";
        });
        $("#respMasivo").html(filas);
        $("#btnSubirMasivo").prop("disabled", colaMasiva.length == 0).text("Subir " + colaMasiva.length + " imagen" + (colaMasiva.length == 1 ? "" : "es"));
        $("#boxMasivo").show();
    }

    $("#btnCancelarMasivo").on("click", function () {
        colaMasiva = [];
        $("#boxMasivo").hide();
    });

    // Se suben de una en una para no saturar el servidor
    $("#btnSubirMasivo").on("click", function () {
        var boton = $(this).prop("disabled", true);
        var pendientes = colaMasiva.slice();
        colaMasiva = [];
        var ok = 0, errores = 0;

        function siguiente() {
            if (pendientes.length == 0) {
                boton.text("Subir imágenes");
                notify(ok + " subidas" + (errores ? ", " + errores + " con error" : ""), errores ? "error" : "success", 3);
                return;
            }
            var item = pendientes.shift();
            var celda = $("#filaMasiva" + item.fila + " .estado").text("Subiendo...");
            subirImagen(item.card, item.file).done(function (response) {
                if (response["success"] == 1) {
                    ok++;
                    celda.html('<span class="text-success">✓ Subida</span>');
                } else {
                    errores++;
                    celda.html('<span class="text-danger">' + escapar(response["mensaje"]) + "</span>");
                }
            }).fail(function () {
                errores++;
                celda.html('<span class="text-danger">Error</span>');
            }).always(siguiente);
        }
        siguiente();
    });

    /* FILTROS */
    $("#txtBuscar, #chkSinImagen").on("input change", filtrar);

    function filtrar() {
        var texto = $.trim($("#txtBuscar").val()).toLowerCase();
        var soloSinImagen = $("#chkSinImagen").is(":checked");
        $(".cardImagen").each(function () {
            var card = $(this);
            var coincide = texto == ""
                || card.attr("data-nombre").indexOf(texto) !== -1
                || card.attr("data-alias").toLowerCase().indexOf(texto) !== -1;
            if (soloSinImagen && card.attr("data-sin-imagen") != 1)
                coincide = false;
            card.toggle(coincide);
        });
        actualizarContador();
    }

    function actualizarContador() {
        var total = $(".cardImagen").length;
        var sinImagen = $(".cardImagen[data-sin-imagen=1]").length;
        $("#lblContador").text(total + " productos · " + sinImagen + " sin imagen");
    }
});
