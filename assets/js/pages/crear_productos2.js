let impuesto = 0;
$(document).ready(function () {
    impuesto = $("#txt_impuesto").val();
    calculateNoTax();
    
    let dias = $("#dias_disponibles").val();
    if(dias !== ""){
        $("#ciertosDias").prop("checked", true);
        $(".chooseDias").show();
        
        $.each(dias.split(","), function(i,e){
            $("#cmbDias option[value='" + e + "']").prop("selected", true);
        });
    }else{
        console.log("TODOS LOS DIAS", dias);
        $("#todosDias").prop("checked", true);
        $(".chooseDias").hide();
    }
    
    
    $("#btnOpenModal").on("click", function (event) {
        $("#id").val(0);
        $("#frmSave").trigger("reset");

    });

    /*NUEVO*/
    $("#btnAbrirModal").on("click", function (event) {
        $("#addItem").attr("data-tipo", "G");
        $("#btnEditOpcion").hide();
        ocultarElementosdeOpciones();
        $("#frmOpciones").trigger("reset");
        $(".tbodyOPc").empty();
        $("#modalItems").modal();
        $("#cmb_tipo_opcion").removeAttr('disabled');
        $("#cmb_tipo_opcion").trigger("change");
    });

    function CalcularPesoApr() {
        var totalPeso = 0;
        $("input[name='txt_cantidadCombo[]']").each(function (indice, elemento) {
            var cantidad = $(elemento).val();
            var peso = $(elemento).attr("data-peso");
            var total = parseInt(cantidad) * parseFloat(peso);
            console.log("total" + total);
            totalPeso = totalPeso + total;
        });
        console.log(totalPeso);
        $(".txt_pesoApr").html(totalPeso + " Kg");
    }

    $("#txt_precio").on("input", function () {
        calculateNoTax();
    });
    
    $("#chk_base").on("change", function () {
        calculateNoTax();
    });
    
    function calculateNoTax(){
        var precio = $("#txt_precio").val();
        var notax = parseFloat(precio) / parseFloat(impuesto);
        var iva = parseFloat(precio) - parseFloat(notax);

        if ($("#chk_base").is(':checked')) {
            $("#txt_baseC").val("checked");
            $("#txt_iva").val(iva.toFixed(4));
            $("#txt_precio_no_tax").val(notax.toFixed(4));
            $("#txt_ivaC").val(iva.toFixed(4));
            $("#txt_precio_no_taxC").val(notax.toFixed(4));
        }
        else {
            $("#txt_baseC").val("");
            $("#txt_iva").val(0);
            $("#txt_precio_no_tax").val(parseFloat(precio));
            $("#txt_ivaC").val(0);
            $("#txt_precio_no_taxC").val(parseFloat(precio));
        }
    }


    $("#btnImportar").on("click", function (event) {

        $("#frmOpcionesImportar").trigger("reset");
        $("#modalImporar").modal();
    });

    $(".btnview").on("click", function (event) {

        var padre = $(this).parents(".trImport");
        var vista = padre.find(".viewImport").val();
        var id = padre.find(".codopcion").val();
        //alert(id);
        if (vista === "0") {
            $(".view_" + id).show();
            padre.find(".viewImport").val(1);
        }
        else {
            $(".view_" + id).hide();
            padre.find(".viewImport").val(0);
        }
    });

    $(".btnImportarOpcion").on("click", function (event) {
        var padre = $(this).parents(".trOpImport");
        var id = parseInt($("#id").val());
        if (id > 0) {
            var parametros = {
                "cod_producto": id,
                "cod_productoOpciones": $(this).attr("data-id")
            };
        } else {
            messageDone('Debe guardar primero el producto para importar opciones', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=importar',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    var cod = padre.find(".codopcion").val();

                    var nombre = padre.find(".nameIm").html();
                    var productos = padre.find(".opIm").html();
                    var preciomin = padre.find(".minIm").html();
                    var preciomax = padre.find(".maxIm").html();
                    //falta traer codproductoopcion creado
                    var cod_prodOpcion = response['id'];
                    var trs = ` <tr data-id="` + cod_prodOpcion + `">
                                  <td>`+ nombre + `</td>
                                  <td>`+ productos + `</td>
                                  <td style="text-align: center;">`+ preciomin + `</td>
                                  <td style="text-align: center;">`+ preciomax + `</td>
                                  <td>
                                    <a href="javascript:void(0);" data-value="`+ cod_prodOpcion + `"  class="bs-tooltip btnEditarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i data-feather="edit-2"></i></a>
                                    <a href="javascript:void(0);" data-value="`+ cod_prodOpcion + `"  class="bs-tooltip btnEliminarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                                  </td>
                                </tr>`;

                    $(".respOpciones").append(trs);
                    feather.replace();
                    padre.remove();
                    count = ($(".view_" + cod).length);
                    if (count === 0) {
                        $(".product_" + cod).remove();
                    }
                    body = ($('#style-4>tr').length);
                    if (body === 0) {
                        trs = `<tr><td colspan="4">No hay opciones para importar</td></tr>`;
                        $(".respOpcionesImportar").append(trs);
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

    });


    $("#btnAgregarOpcion2").on("click", function (event) {
        var nombre = "";
        var tags = "";
        var precio = "";
        var id = "";
        var trs = ` <tr>
                      <td>`+ nombre + `</td>
                      <td>productos</td>
                      <td>`+ precio + `</td>
                      <td>
                        <a href="javascript:void(0);" data-value="`+ id + `"  class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                      </td>
                    </tr>`;
        $(".respOpciones").append(trs);
    });



    /*NUEVO*/

    $("#btnPersonalizar").on("click", function () {
        var alias = $(this).attr('data-alias');
        window.location.href = "personalizar_productos.php?id=" + alias;
    });

    $("#btnGuardar").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmSave");
        form.validate();
        var isForm = form.valid();

        var form2 = $("#frmPrecios");
        form2.validate();
        var isForm2 = form2.valid();

        if (isForm == false || isForm2 == false) {
            notify("Falta llenar informacion", "success", 2);
            return false;
        }

        var formData = new FormData($("#frmSave")[0]);
        var data = CKEDITOR.instances.editor1.getData();
        formData.append('desc_larga', data);
        formData.append('txt_crop', $("#txt_crop").val());
        formData.append('txt_crop_min', $("#txt_crop_min").val());
        var id = parseInt($("#id").val());
        if (id > 0)
            formData.append('cod_producto', id);

        //PRECIOS
        var poData = $("#frmPrecios").serializeArray();
        for (var i = 0; i < poData.length; i++)
            formData.append(poData[i].name, poData[i].value);


        //DISPONIBILIDAD
        var disData = $("#frmDisponibilidad").serializeArray();
        for (var i = 0; i < disData.length; i++)
            formData.append(disData[i].name, disData[i].value);

        //ETIQUETAS
        var etiData = $("#frmEtiquetas").serializeArray();
        for (var i = 0; i < etiData.length; i++)
            formData.append(etiData[i].name, etiData[i].value);
        
        //DIAS
        var diasData = $("#frmDias").serializeArray();
        for (var i = 0; i < diasData.length; i++)
            formData.append(diasData[i].name, diasData[i].value);    
        
        //EMPAQUE
        var empaqueData = $("#frmEmpaque").serializeArray();
        for (var i = 0; i < empaqueData.length; i++)
            formData.append(empaqueData[i].name, empaqueData[i].value);    
            

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=crear',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $("#id").val(response['id']);
                    $("#titulo").html($("#txt_nombre").val());
                    $(".btnAcciones").show();

                    window.history.pushState(response, "Crear Producto", "crear_productos.php?id=" + response['alias']);

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

    $("#btnUploadImg").on("click", function (event) {
        event.preventDefault();
        var form = $("#frmUploadImg");
        form.validate();
        if (form.valid() == false) {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
        }

        var formData = new FormData($("#frmUploadImg")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=upload_img',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $("#frmUploadImg").trigger("reset");
                    $(".respGalery").prepend(response['html']);
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

    $(".respGalery").on("click", ".deleteImg", function (event) {
        event.preventDefault();
        var cod_imagen = parseInt($(this).attr("data-value"));
        if (cod_imagen == 0) {
            alert("No se pudo traer la imagen, por favor intentelo mas tarde");
            return;
        }
        var element = $(this);

        swal.fire({
            title: '¿Estas seguro?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_imagen": cod_imagen,
                    "estado": "D"
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Eliminando imagen, por favor espere...");
                    },
                    url: 'controllers/controlador_productos.php?metodo=delete_img',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            $(element).parent().remove();
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
        });
    });

    //OPCIONES
    $("#btnAgregarOpcion").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmOpciones");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmOpciones")[0]);
        var id = parseInt($("#id").val());
        var tipo = $("#cmb_tipo_opcion").val();
        if (id > 0) {
            formData.append('cod_producto', id);
            formData.append('tipo_opcion', tipo);
        } else {
            messageDone('Debe guardar primero el producto para asignar opciones', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=add_opcion',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $(".respOpciones").append(response['html']);
                    $("#id_det_cab").val(response['id']);
                    feather.replace();
                    $("#frmOpciones").trigger("reset");
                    $('#cmb_productos').val(null).trigger('change');
                    $("#modalItems").modal("hide");
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

    //OPCIONES
    $("#btnGuardarCombo").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmOpcionesCombo");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmOpcionesCombo")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        } else {
            messageDone('Debe guardar primero el producto para asignar opciones', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=add_combo',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $(".tbodyCombo").html(response['html']);
                    feather.replace();
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

    $(".respOpcionesCombo").on("click", ".btnEliminarCombo", function () {
        var cod_opcion = parseInt($(this).attr("data-value"));
        if (cod_opcion == 0) {
            $(this).parent().parent().remove();
            CalcularPesoApr();
            var padre = $(this).parents(".trItem");
            var txt = padre.find("input[name='txt_nomCombo[]']").val();
            alert(txt);
            return;
        }
        var element = $(this);

        swal.fire({
            title: '¿Estas seguro?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_opcion": cod_opcion,
                    "estado": "D"
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Eliminando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_productos.php?metodo=delete_opcionCombo',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            $(element).parent().parent().remove();
                            CalcularPesoApr();
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
        });
    });

    $(".respOpciones").on("click", ".btnEliminarOpciones", function () {
        var cod_opcion = parseInt($(this).attr("data-value"));
        if (cod_opcion == 0) {
            alert("No se pudo obtener la informacion de la opcion, por favor intentelo mas tarde");
            return;
        }
        var element = $(this);

        swal.fire({
            title: '¿Estas seguro?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_opcion": cod_opcion,
                    "estado": "D"
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Eliminando imagen, por favor espere...");
                    },
                    url: 'controllers/controlador_productos.php?metodo=delete_opcion',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            $(element).parent().parent().remove();
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
        });
    });

    //VARIANTES
    $("#btnAgregarVariante").on("click", function (event) {
        var cant = $(".tagging").length - 1;
        var element = `<div class="row">
                                  <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                      <label>Caracter&iacute;stica <span class="asterisco">*</span></label>
                                      <input type="text" placeholder="Ej. Talla" name="txt_opcion_titulo[]" class="form-control" required="required" autocomplete="off" value="">
                                  </div>
                                  <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                      <label>Atributos <span class="asterisco">*</span>
                                      <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoja los productos que el usuario tendra que decidir a escoger"></span><span><i>&nbsp;Separar las opciones con una coma</i></span></label>
                                      <select multiple="multiple" name="cmb_variante_productos[`+ cant + `][]" class="form-control tagging" required="required">
                                        </select>
                                  </div>
                                  <div class="form-group col-md-2 col-sm-2 col-xs-12">
                                        <label>Tipo</label>
                                        <select name="cmb_variante_tipo[]" class="form-control" required="required">
                                            <option value="texto">Texto</option>
                                            <option value="color">Color</option>
                                        </select>
                                    </div>
                                  
                              </div>`;
        $(".VariantesSeleccion").append(element);
        $(".tagging").select2({
            closeOnSelect: false,
            tags: true,
            tokenSeparators: [',']
        });
    });

    $("#btnValidarVariante").on("click", function (event) {
        var formData = new FormData($("#frmCaracteristicas")[0]);
        formData.append('tipo_empresa', $("#txt_cod_tipo_empresa").val());
        formData.append('sku_padre', $("#txt_sku").val());
        $.ajax({
            beforeSend: function () {
                OpenLoad("Validando información, por favor espere...");
            },
            url: 'controllers/controlador_productos2.php?metodo=detectarColores',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    //messageDone(response['mensaje'],'success');
                    $(".respAtributosValidar").html(response['html']);
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
        console.log($(this).val());
    });


    $("#btnGuardarCaracteristicas").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmCaracteristicas");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmCaracteristicas")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        } else {
            messageDone('Debe guardar primero el producto para guardar sus características', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando características del producto, por favor espere...");
            },
            url: 'controllers/controlador_productos2.php?metodo=guardar_caracteristicas',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    //location.reload();

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

    $("#btnGuardarVariante").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmVariantes");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmVariantes")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        } else {
            messageDone('Debe guardar primero el producto para guardar sus variantes', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos2.php?metodo=guardar_variantes',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    location.reload();

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

    $(".btnActualizarAtributosVariante").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmVarianteEditarCaracteristicas");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmVarianteEditarCaracteristicas")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        } else {
            messageDone('Debe guardar primero el producto para guardar sus características', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Asignando atributos al producto, por favor espere...");
            },
            url: 'controllers/controlador_productos2.php?metodo=guardar_atributos_variante',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    //location.reload();

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

    $("#btnAddNewVariante").on("click", function (event) {
        var atributos = $("#cmb_new_variante_atributos").val();
        console.log(atributos);
        var json = JSON.stringify(atributos);
        console.log(json);
        var base64 = window.btoa(unescape(encodeURIComponent(json)));
        console.log(base64);
        $("#txt_new_variante_atributos").val(base64);
        $("#btnGuardarVariante").trigger("click");
    });

    $("body").on("change", ".txt_cantidadCombo", function () {
        CalcularPesoApr();
    });

    $("body").on("click", ".btnEditarVariante", function () {
        var alias = $(this).attr("data-value");
        swal.fire({
            title: '¿Estas seguro?',
            text: "¡Perderás todos los cambios que no hayas guardado!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {
                window.location.href = "crear_productos.php?id=" + alias;
            }
        });
    });

    //DISPONIBILIDAD
    $(".chkDisponibilidad").on("change", function () {
        var padre = $(this).parents(".itemSucursal");
        if ($(this).is(':checked')) {
            padre.find("input[type=number]").removeAttr("readonly");
            padre.find(".sucSelect").val(1);
        }
        else {
            padre.find("input[type=number]").attr("readonly", "readonly");
            padre.find(".sucSelect").val(0);
        }
    })

    //PRECIO

    $(".chkPrecio").on("change", function () {
        var padre = $(this).parents(".itemSucursal");
        if ($(this).is(':checked')) {
            padre.find(".contentPrecio").css("display", "block");
            padre.find(".sucPrecio").val(1);
        }
        else {
            padre.find(".contentPrecio").css("display", "none");
            padre.find(".sucPrecio").val(0);
        }
    })

    $("#btnGuardarDisponibilidad").on("click", function (event) {
        event.preventDefault();

        var form = $("#frmDisponibilidad");
        form.validate();
        if (form.valid() == false) {
            messageDone('Debes llenar todos los campos', 'error');
            return false;
        }

        var formData = new FormData($("#frmDisponibilidad")[0]);
        var id = parseInt($("#id").val());
        if (id > 0) {
            formData.append('cod_producto', id);
        } else {
            messageDone('Debe guardar primero el producto para asignar opciones', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=setDisponibilidad',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $(".respOpciones").append(response['html']);
                    feather.replace();
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

    //CONTIFICO
    $("#btnCrearSistemaContable").on("click", function (event) {
        event.preventDefault();

        var id = parseInt($("#id").val());
        if (id == 0) {
            messageDone('Debe guardar primero el producto para crearlo en el sistema contable', 'error');
            return;
        }

        var urlAjax = 'controllers/controlador_contifico.php?metodo=crear_producto';
        var identificador = $("#identificador_fact").val();
        if (identificador == "CONTIFICO") {
            urlAjax = 'controllers/controlador_contifico.php?metodo=crear_producto';
        } else if (identificador == "FACTMOVIL") {
            urlAjax = 'controllers/controlador_fact_movil.php?metodo=crear_producto';
        } else {
            messageDone('Proveedor de sistema contable no reconocido, por favor ponerse en contacto con soporte', 'error');
            return;
        }

        var parametros = {
            "id": id
        };

        $.ajax({
            beforeSend: function () {
                OpenLoad("Creando producto en Sistema contable, por favor espere...");
            },
            url: urlAjax,
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    //$(".respOpciones").append(response['html']);
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

    $("#btnActualzarIdSistemaContable").on("click", function (event) {
        event.preventDefault();

        var id = parseInt($("#id").val());
        if (id == 0) {
            messageDone('Debe guardar primero el producto para asignar opciones', 'error');
            return;
        }

        if ($("#txt_id_contifico").val().trim() == "") {
            messageDone('Debes proporcionar un ID de un producto que se encuentra en el sistema contable', 'error');
            return;
        }

        var urlAjax = 'controllers/controlador_contifico.php?metodo=set_id_producto';
        var identificador = $("#identificador_fact").val();
        if (identificador == "CONTIFICO") {
            urlAjax = 'controllers/controlador_contifico.php?metodo=set_id_producto';
        } else if (identificador == "FACTMOVIL") {
            urlAjax = 'controllers/controlador_fact_movil.php?metodo=set_id_producto';
        } else {
            messageDone('Proveedor de sistema contable no reconocido, por favor ponerse en contacto con soporte', 'error');
            return;
        }

        var parametros = {
            "id": id,
            "idFact": $("#txt_id_contifico").val().trim(),
        };

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: urlAjax,
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    //$(".respOpciones").append(response['html']);
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

    $(".rbProductoContifico").on("change", function () {
        var aux = $(this).val();
        if (aux == 1) {
            $(".contificoExiste").hide();
            $(".contificoCrear").show();
        } else {
            $(".contificoExiste").show();
            $(".contificoCrear").hide();
        }
    });
    
    $(".rbDisponibleDias").on("change", function () {
        var aux = $(this).val();
        if (aux == 1) {
            $(".chooseDias").show();
        } else {
            $(".chooseDias").hide();
        }
    });

    //COMPONENTES
    CKEDITOR.replace("editor1");

    var selectDropify = "PERFIL";
    //DROPIFY PERFIL
    var resize = null;
    var drEvent = $('#dropifyPerfil').dropify({
        messages: {
            'default': 'Click para subir o arrastra',
            'remove': 'X',
            'replace': 'Sube o Arrastra y suelta'
        },
        error: {
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
    drEvent.on('dropify.beforeImagePreview', function (event, element) {
        if (resize != null)
            resize.destroy();

        selectDropify = "PERFIL";

        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    });

    //DROPIFY GALERIA 
    var resize = null;
    var drGaleria = $('#dropifyGaleria').dropify({
        messages: {
            'default': 'Click para subir o arrastra',
            'remove': 'X',
            'replace': 'Sube o Arrastra y suelta'
        },
        error: {
            'imageFormat': 'Solo se adminte imagenes cuadradas.'
        }
    });
    drGaleria.on('dropify.beforeImagePreview', function (event, element) {
        if (resize != null)
            resize.destroy();

        selectDropify = "GALERIA";

        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    });

    $('#modalCroppie').on('shown.bs.modal', function () {
        let tipoRecorte = $("#tipoRecorte").val();

        /*OBTENER TAMAÑOS CROPPIE*/
        let minWidth = $("#minWidth").val();
        let minHeight = $("#minHeight").val();
        let maxWidth = $("#maxWidth").val();
        let maxHeight = $("#maxHeight").val();
        let quality = $("#quality").val();

        // let boundaryWidth = 500;
        // let boundaryHeight = 500;
        let boundaryWidth = maxWidth;
        let boundaryHeight = maxHeight;

        /*if ("rectangle" == tipoRecorte) {
            boundaryWidth = 333.3333
            boundaryHeight = 500
        }*/
        if ("rectangle" == tipoRecorte) {
            boundaryWidth = 600
            boundaryHeight = 400
        }

        console.log(minWidth, minHeight, maxWidth, maxHeight);

        if (selectDropify == "PERFIL")
            var aux = $(".dropify").get(0);
        else
            var aux = $(".dropify").get(1);
        var file = aux.files[0];
        console.log("fileeeeee", file.type);

        //OBTENER FORMATO
        let formato = "jpeg";
        // quality = 0.8;
        fondoImg = "#FFFFFF";
        if (file.type === "image/png") {
            formato = "png";
            fondoImg = "";
            // quality = 0.7;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#my-image').attr('src', e.target.result);

            resize = new Croppie($('#my-image')[0], {
                enableExif: true,
                viewport: { width: maxWidth, height: maxHeight }, //tamaño de la foto que se va a obtener
                boundary: { width: boundaryWidth, height: boundaryHeight }, //la imagen total
                showZoomer: true, // hacer zoom a la foto
                enableResize: false,
                enableOrientation: true, // para q funcione girar la imagen 
                mouseWheelZoom: 'ctrl'
            });
            $('#crop-get').on('click', function () { // boton recortar
                resize.result({ type: 'base64', size: 'viewport', format: formato, quality: quality, backgroundColor: fondoImg }).then(function (dataImg) {
                    var InsertImgBase64 = dataImg;
                    if (selectDropify == "PERFIL") {
                        $("#txt_crop").val(InsertImgBase64);
                        var imagen = $(".dropify-render img")[0];
                        $(imagen).attr("src", InsertImgBase64);
                    } else {
                        $("#txt_crop_galeria").val(InsertImgBase64);
                        var imagen = $(".dropify-render img")[1];
                        $(imagen).attr("src", InsertImgBase64);
                    }
                    /*MINIATURA*/
                    resize.result({ type: 'base64', size: { width: minWidth, height: minHeight }, format: formato, quality: quality, backgroundColor: fondoImg }).then(function (dataImg) {
                        $("#txt_crop_min").val(dataImg);
                    });
                    $("#modalCroppie").modal('hide');
                });



            });
            $('.crop-rotate').on('click', function (ev) {
                resize.rotate(parseInt($(this).data('deg')));
            });


        }
        reader.readAsDataURL(file);
    });


    $(".tagging").select2({
        closeOnSelect: false,
        tags: true,
        tokenSeparators: [',']
    });


    $("#cmb_categoria").select2();
    $("#cmbDias").select2();
    //$("#cmb_productos").select2();
    $("#cmb_productos").select2({
        closeOnSelect: false,
        tags: true,
        tokenSeparators: [',']
    });

    $("#cmb_productosCombo").select2({
        closeOnSelect: false,
        tags: true,
        tokenSeparators: [',']
    });

    $("#btnBack").on("click", function () {
        var link = $(this).attr("data-module-back");
        if (typeof link === "undefined") {
            link = "index.php";
        }
        swal.fire({
            title: '¿Estas seguro?',
            text: "¡Perderas todos los cambios que no hayas guardado!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Salir',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {
                window.location.href = link;
            }
        });
    });

    $("#btnNuevo").on("click", function () {
        swal.fire({
            title: '¿Estas seguro?',
            text: "¡Perderás todos los cambios que no hayas guardado!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {
                window.location.href = "crear_productos.php";
            }
        });
    });

    $("#btnEliminar").on("click", function () {
        var id = parseInt($("#id").val());
        if (id <= 0) {
            messageDone('Error al eliminar el producto', 'error');
            return;
        }

        swal.fire({
            title: '¿Estas seguro?',
            text: "¡Perderás todos los cambios que no hayas guardado!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_producto": id,
                    "estado": "D"
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Buscando informacion, por favor espere...");
                    },
                    url: 'controllers/controlador_productos.php?metodo=set_estado',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            setTimeout(function () {
                                window.location.href = "productos.php"
                            }, 1000);

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
                });//FIN AJAX

            }
        });
    });

    $("#cmb_tipo_opcion").on("change", function () {
        var isAbierta = $("#cmb_tipo_opcion").val();

        if (isAbierta == 0) {
            $('#cmb_productos').html("");
            $('#cmb_productos').select2('destroy');
            $("#cmb_productos").select2({
                closeOnSelect: false,
                tags: true,
                tokenSeparators: [',']
            });
        }
        else {
            $('#cmb_productos').html("");
            $('#cmb_productos').select2('destroy');
            $.ajax({
                beforeSend: function () {
                    OpenLoad("Buscando informacion, por favor espere...");
                },
                url: 'controllers/controlador_productos.php?metodo=getOpciones',
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    if (response['success'] == 1) {
                        $("#cmb_productos").html(response['html']);
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
            });//FIN AJAX
            $("#cmb_productos").select2();

        }
    });

    $("#chk_combo").on("change", function () {
        if ($(this).prop("checked")) {
            $(".dataCombo").show();
            $('#cmb_productosCombo').html("");
            $('#cmb_productosCombo').select2('destroy');
            var id = parseInt($("#id").val());
            var parametros;
            if (id > 0) {
                parametros = {
                    "cod_producto": id,
                }
            }

            $.ajax({
                beforeSend: function () {
                    OpenLoad("Buscando informacion, por favor espere...");
                },
                url: 'controllers/controlador_productos.php?metodo=getOpcionesCombo',
                type: 'GET',
                data: parametros,
                success: function (response) {
                    console.log(response);
                    if (response['success'] == 1) {
                        $("#cmb_productosCombo").html(response['html']);
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
            });//FIN AJAX
            $("#cmb_productosCombo").select2();
        }
        else {
            $(".dataCombo").hide();
        }
    });

    
    $("#addItem").on("click", function () {
        //alert($(".txtnomDet").length);
        if ($(".txtnomDet").length > 0) {

            var tipo = $("#cmb_tipo_opcion").val();
            var concatValor = "";
            var txt = "";
            var cod_prod = "";
            var precio = 0;
            $("#cmb_productos option:selected").each(function () {
                var pasar = true;
                if (tipo == 0) {
                    if ($(this).val() != "") {
                        concatValor = $(this).val();
                    }
                }
                else {
                    concatValor = $(this).html();
                    cod_prod = $(this).val();
                    precio = $(this).data("precio");
                }

                console.log("c--" + concatValor);
                $("input[name='txt_nomItemDet[]']").each(function (indice, elemento) {
                    txt = $(elemento).val();
                    //alert(txt); 
                    console.log(txt);
                    if (txt == concatValor) {
                        pasar = false;
                    }
                });

                if (pasar) {
                    var nuevaLinea = `<tr class="trItem" >
                                            <td>
                                                <input class="form-control txt_id_det" name="cod_detalle[]" value="0" style="display:none">
                                                <input class="form-control txtnomDet" name="txt_nomItemDet[]" value="`+ concatValor + `"  readonly>
                                                <input type="hidden" class="form-control" name="txt_codItemDet[]" value="`+ cod_prod + `">
                                            </td>
                                            <td style="text-align: center;">
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                <input class="form-control chk_is" name="chk_is[]" value="0" type="hidden">
                                                      <input class="precioCheck" type="checkbox" name="precioCheck[]"/>
                                                      <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td><input type="number" class="form-control txt_precio" name="txt_precio[]" placeholder="precio" readonly value="`+ precio.toFixed(2) + `" style="text-align: right;"></td>
                                            <td class="d-flex" style="text-align: center;">
                                                <button type="button" class="btn btn-danger btnDelItem mr-1">
                                                    <i data-feather="trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-success btnModalIngredientes no-show-ingredients">
                                                    <i data-feather="coffee"></i>
                                                </button>
                                            </td>
                                        </tr>`;
                    $(".tbodyOPc").append(nuevaLinea);
                    feather.replace();
                }
            });


        }
        else {
            $("#cmb_productos option:selected").each(function () {

                var tipo = $("#cmb_tipo_opcion").val();
                var concatValor = "";
                var cod_prod = "";
                var precio = 0;
                if (tipo == 0) {
                    if ($(this).val() != "") {
                        concatValor = $(this).val();
                        //alert($(this).html());
                    }
                }
                else {
                    concatValor = $(this).html();
                    cod_prod = $(this).val();
                    precio = $(this).data("precio");
                }
                //if(pasar){
                var nuevaLinea = `<tr class="trItem">
                                        <td>
                                            <input class="form-control txt_id_det" name="cod_detalle[]" value="0" type="hidden">
                                            <input class="form-control txtnomDet" name="txt_nomItemDet[]" value="`+ concatValor + `"  readonly>
                                            <input type="hidden" class="form-control" name="txt_codItemDet[]" value="`+ cod_prod + `">
                                        </td>
                                        <td style="text-align: center;">
                                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                            <input class="form-control chk_is" name="chk_is[]" value="0" type="hidden">
                                                  <input class="precioCheck" type="checkbox" name="precioCheck[]"/>
                                                  <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td><input type="number" class="form-control txt_precio" name="txt_precio[]" placeholder="precio" readonly value="`+ precio.toFixed(2) + `" style="text-align: right;"></td>
                                        <td class="d-flex" style="text-align: center;">
                                            <button type="button" class="btn btn-danger btnDelItem mr-1"><i data-feather="trash"></i></button>
                                            <button type="button" class="btn btn-success btnModalIngredientes no-show-ingredients">
                                                <i data-feather="coffee"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                $(".tbodyOPc").append(nuevaLinea);
                feather.replace();
                //}
            });
        }

        if ($(".txtnomDet").length > 0)
            mostrarElementosdeOpciones();

    });

    $("#addItemCombo").on("click", function () {
        var totalPeso = 0;
        var peso = 0;
        $("#cmb_productosCombo option:selected").each(function () {
            var concatValor = $(this).html();
            var cod_productoHijo = $(this).val();
            peso = $(this).attr("data-peso");
            //totalPeso = parseInt(totalPeso) + parseInt(peso);
            var pasar = true;
            $("input[name='txt_nomCombo[]']").each(function (indice, elemento) {
                txt = $(elemento).val();
                if (txt == concatValor) {
                    pasar = false;
                }
            });

            if (pasar) {
                var nuevaLinea = `<tr class="trItem">
                                        <td>
                                            <input class="form-control txt_cod_producDetalle" name="txt_cod_producDetalle[]" value="0" type="hidden">
                                            <input class="form-control txt_cod_hijo" name="txt_cod_hijo[]" value="`+ cod_productoHijo + `" type="hidden">
                                            <input class="form-control txt_peso_combo" name="txt_peso_combo[]" value="`+ peso + `" type="hidden">
                                            <input class="form-control txt_nomCombo" name="txt_nomCombo[]" value="`+ concatValor + `"  readonly>
                                        </td>
                                        <td><input type="number" class="form-control txt_cantidadCombo" name="txt_cantidadCombo[]" data-peso="`+ peso + `" placeholder="cantidad" value="1" min="1" style="text-align: right;"></td>
                                        <td>
                                            <a href="javascript:void(0);" data-value="0"  class="bs-tooltip btnEliminarCombo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                                          </td>
                                    </tr>`;
                $(".tbodyCombo").append(nuevaLinea);
                feather.replace();

            }

        });
        CalcularPesoApr();
    });

    $("body").on("change", ".precioCheck", function (event) {
        var padre = $(this).parents(".trItem");
        inpPrecio = padre.find(".txt_precio");
        chk_is = padre.find(".chk_is");
        if ($(this).prop("checked")) {
            chk_is.val(1);
            inpPrecio.removeAttr("readonly");
        }
        else {
            chk_is.val(0);
            inpPrecio.attr("readonly", "readonly");
            inpPrecio.val(0);
        }

    });

    $("body").on("click", ".btnDelItem", function (event) {
        var id = $(this).parents(".trItem").find(".txt_id_det").val();
        var tr = $(this).parents(".trItem")
        // alert(id);

        if (id == 0) {
            tr.remove();
            return false;
        }

        swal.fire({
            title: '¿Estas seguro?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_opcion": id,
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Eliminando imagen, por favor espere...");
                    },
                    url: 'controllers/controlador_productos.php?metodo=eliminarUnaOpcionDetalle',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
                            tr.remove();
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
        });
    });

    $("body").on("click", ".btnEditarOpciones", function (event) {
        event.preventDefault();

        $("#addItem").attr("data-tipo", "E");
        $("#btnAgregarOpcion").hide();
        $("#btnEditOpcion").show();

        var cod_producto_opcion = $(this).data("value");

        $("#id_det_cab").val(cod_producto_opcion);

        var parametros = {
            "cod_producto_opcion": cod_producto_opcion
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=select_opcion',
            type: 'GET',
            data: parametros,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    $("#txt_opcion_titulo").val(response['data']['titulo']);
                    $("#txt_opciones_cantidad").val(response['data']['cantidad_min']);
                    $("#txt_opciones_cantidad_max").val(response['data']['cantidad']);
                    $("#cmb_tipo_opcion").val(response['data']['isDatabase']);
                    $("#cmb_tipo_opcion").prop('disabled', 'disabled');
                    $("#cmb_isCheck").val(response['data']['isCheck']);

                    $(".tbodyOPc").html(response['html']);

                    $('#cmb_productos').html("");
                    $('#cmb_productos').select2('destroy');
                    if(response['data']['isDatabase'] == 1){
                        $("#cmb_productos").html(response['opcSelesct']);
                        $("#cmb_productos").select2();
                    }else{
                        $("#cmb_productos").select2({
                            closeOnSelect: false,
                            tags: true,
                            tokenSeparators: [',']
                        });
                    }
                    

                    feather.replace();
                    $("#modalItems").modal();
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

    $("#btnEditOpcion").on("click", function (event) {
        event.preventDefault();

        var cod_producto_opcion = $("#id_det_cab").val();

        var formData = new FormData($("#frmOpciones")[0]);
        var id = parseInt($("#id").val());
        var tipo = $("#cmb_tipo_opcion").val();
        if (id > 0) {
            formData.append('cod_producto', id);
            formData.append('tipo_opcion', tipo);
            formData.append('cod_producto_opcion', cod_producto_opcion);
        } else {
            messageDone('Debe guardar primero el producto para asignar opciones', 'error');
            return;
        }

        $.ajax({
            beforeSend: function () {
                OpenLoad("Guardando datos, por favor espere...");
            },
            url: 'controllers/controlador_productos.php?metodo=edit_opcion',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);

                if (response['success'] == 1) {
                    messageDone(response['mensaje'], 'success');
                    $("#modalItems").modal("hide");
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

    $("#style-33").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#style-33>tr').each(function () {
                selectedData.push($(this).attr("data-id"));
            });
            ordenarItems(selectedData, "opciones");
        }
    });

    $(".tbodyOPc").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('.tbodyOPc>tr').each(function () {
                selectedData.push($(this).attr("data-id"));
            });
            ordenarItems(selectedData, "detalles");
        }
    });

    function ordenarItems(data, tipo) {

        var parametros = {
            "datos": data,
            "tipo": tipo
        }
        console.log(parametros);
        $.ajax({
            url: 'controllers/controlador_productos.php?metodo=actualizar',
            type: 'POST',
            data: parametros,
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    notify("Actualizado correctamente", "success", 2);
                }
                //alert(response['mensaje']);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    function mostrarElementosdeOpciones() {
        $("#tituOpciones").show();
        $("#divTablaOpciones").show();
        $("#divBotonesOpciones").show();
        if ($("#addItem").data("tipo") == "G")
            $("#btnAgregarOpcion").show();
    }

    function ocultarElementosdeOpciones() {
        $("#tituOpciones").hide();
        $("#divTablaOpciones").hide();
        $("#divBotonesOpciones").hide();
    }

    $("body").on("change", "#cmbVarianteVisualizacion", function () {
        let tipo = $(this).val();
        let id = parseInt($("#id").val());
        let parametros = {
            "tipo": tipo,
            "cod_producto": id
        }
        $.ajax({
            url: 'controllers/controlador_productos2.php?metodo=cambiarVarianteVisualizacion',
            data: parametros,
            type: "GET",
            success: function (response) {
                console.log(response);
                if (response['success'] == 1) {
                    notify(response['mensaje'], "success", 2);
                }
                else {
                    notify(response['mensaje'], "error", 2);
                }
            },
            error: function (data) {
            },
            complete: function () {
            },
        });
    });

});

function getIngredientes() {
    let cod_producto = $("#id").val();
    if(cod_producto == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }
    
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_productos.php?metodo=getIngredientes&cod_producto=${cod_producto}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            getProductosIngredientes();
            let target = $("#cmbIngredientes");
            let template = Handlebars.compile($("#lista-ingredientes-template").html());
            target.html(template(response.data));

            $("#cmbIngredientes").trigger("change");

            let nombreProducto = $("#titulo").text();
            $("#modalIngredientes .modal-title").text(nombreProducto);
            $("#modalIngredientes").modal();
        }
        else{
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

function getProductosIngredientes() {
    let cod_producto = $("#id").val();
    if(cod_producto == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_productos.php?metodo=getProductosIngredientes&cod_producto=${cod_producto}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let target = $(".lstProductosIngredientes");
            let template = Handlebars.compile($("#lista-producto-ingredientes-template").html());
            target.html(template(response.data));
            feather.replace();
        }
        else{
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

$("body").on("change", "#cmbIngredientes", function(){
    let data = $(this).find("option:selected").data("ingrediente");
    console.log(data);
    $("#unidadMedida").text(data.cod_unidad_medida);
});

function agregarIngredienteProducto() {
    let cod_producto = $("#id").val();
    let cod_ingrediente = $("#cmbIngredientes").val();
    let valor = $("#cantidadIngrediente").val();

    let exists = false;
    $(".lstProductosIngredientes .row").each(function() {
        let xcod_ingrediente = $(this).data("ingrediente");
        if(cod_ingrediente == xcod_ingrediente)
            exists = true;
    });

    if(exists) {
        messageDone("Este ingrediente ya fue añadido", "error");
        return;
    }

    if(cod_producto == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }
    if(cod_ingrediente == "") {
        messageDone("Error al elegir ingrediente", "error");
        return;
    }
    if(valor == "") {
        messageDone("Ingrese un valor en la cantidad", "error");
        return;
    }
    if(isNaN(valor)) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }else if(valor <= 0) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }

    let info = {
        cod_producto,
        cod_ingrediente,
        valor
    };

    OpenLoad("Guardando información...");
    fetch(`controllers/controlador_productos.php?metodo=addProductosIngredientes`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
            getProductosIngredientes();
        }
        else {
            messageDone(response.mensaje, "error");
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

function editProductoIngrediente(cod_producto_ingrediente) {
    let cod_producto = $("#id").val();
    let valor = $("#cantidadIngrediente" + "-" + cod_producto_ingrediente).val();

    if(cod_producto == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }
    if(valor == "") {
        messageDone("Ingrese un valor en la cantidad", "error");
        return;
    }
    if(isNaN(valor)) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }else if(valor <= 0) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }

    let info = {
        cod_producto_ingrediente,
        valor
    };

    fetch(`controllers/controlador_productos.php?metodo=editProductosIngredientes`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function deleteProductoIngrediente(cod_producto_ingrediente) {
    fetch(`controllers/controlador_productos.php?metodo=deleteProductosIngredientes&cod_producto_ingrediente=${cod_producto_ingrediente}`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            getProductosIngredientes();
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

$("body").on("click", ".btnEditProductoIngrediente", function(){
    let cod_producto_ingrediente = $(this).data("id");
    editProductoIngrediente(cod_producto_ingrediente);
});

$("body").on("click", ".btnDeleteProductoIngrediente", function(){
    let cod_producto_ingrediente = $(this).data("id");
    swal.fire({
       title: ' Se eliminará este ingrediente del producto',
       text: '¿Continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
           deleteProductoIngrediente(cod_producto_ingrediente);
       }
    }); 
});

// OPCIONES

$("body").on("click", ".btnModalIngredientes", function(){
    let nombreOpcion = $(this).parents(".trItem").find(".txtnomDet").val();
    $("#modalOpcionesIngredientes .modal-title").text(nombreOpcion);
    $("#modalOpcionesIngredientes").modal("toggle");
    $("#modalItems").modal("toggle");
});

function getIngredientesEnOpciones(cod_producto_opcion) {
    $("#idOpcion").val(cod_producto_opcion);
    let target = $("#cmbOpcionesIngredientes");
    target.html("");
    let cod_producto = $("#id").val();
    if(cod_producto == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }
    
    OpenLoad("Cargando...");
    fetch(`controllers/controlador_productos.php?metodo=getIngredientes`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            getProductosOpcionesIngredientes(cod_producto_opcion);
            
            let template = Handlebars.compile($("#lista-ingredientes-template").html());
            target.html(template(response.data));

            $("#cmbOpcionesIngredientes").trigger("change");
            $("#modalOpcionesIngredientes").modal();
        }
        else{
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

$("body").on("change", "#cmbOpcionesIngredientes", function(){
    let data = $(this).find("option:selected").data("ingrediente");
    console.log(data);
    $("#unidadMedidaOpciones").text(data.cod_unidad_medida);
});

function getProductosOpcionesIngredientes() {
    let cod_producto_opcion = $("#idOpcion").val();
    let target = $(".lstProductosOpcionesIngredientes");
    
    target.html("");

    if(cod_producto_opcion == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }

    OpenLoad("Cargando...");
    fetch(`controllers/controlador_productos.php?metodo=getProductosOpcionesIngredientes&cod_producto_opcion=${cod_producto_opcion}`,{
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            let template = Handlebars.compile($("#lista-producto-opciones-ingredientes-template").html());
            target.html(template(response.data));
            feather.replace();
        }
        else{
            notify(response.mensaje, "error", 2);
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

function agregarIngredienteProductoOpciones() {
    let cod_producto_opcion = $("#idOpcion").val();
    let cod_ingrediente = $("#cmbOpcionesIngredientes").val();
    let valor = $("#cantidadOpcionesIngrediente").val();

    let exists = false;
    $(".lstProductosOpcionesIngredientes .row").each(function() {
        let xcod_ingrediente = $(this).data("ingrediente");
        if(cod_ingrediente == xcod_ingrediente)
            exists = true;
    });

    if(exists) {
        messageDone("Este ingrediente ya fue añadido", "error");
        return;
    }

    if(cod_producto_opcion == 0) {
        messageDone("Primero debe guardar la opción", "error");
        return;
    }
    if(cod_ingrediente == "") {
        messageDone("Error al elegir ingrediente", "error");
        return;
    }
    if(valor == "") {
        messageDone("Ingrese un valor en la cantidad", "error");
        return;
    }
    if(isNaN(valor)) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }else if(valor <= 0) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }

    let info = {
        cod_producto_opcion,
        cod_ingrediente,
        valor
    };

    OpenLoad("Guardando información...");
    fetch(`controllers/controlador_productos.php?metodo=addProductosOpcionesIngredientes`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
            getProductosOpcionesIngredientes();
        }
        else {
            messageDone(response.mensaje, "error");
        }
        CloseLoad();
    })
    .catch(error=>{
        CloseLoad();
        console.log(error);
    });
}

function editProductoOpcionesIngrediente(cod_producto_opcion_ingrediente) {
    let cod_producto_opcion = $("#idOpcion").val();
    let valor = $("#cantidadIngredienteOpcion" + "-" + cod_producto_opcion_ingrediente).val();

    if(cod_producto_opcion == 0) {
        messageDone("Primero debe guardar el producto", "error");
        return;
    }
    if(valor == "") {
        messageDone("Ingrese un valor en la cantidad", "error");
        return;
    }
    if(isNaN(valor)) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }else if(valor <= 0) {
        messageDone("Cantidad debe ser un número real positivo", "error");
        return;
    }

    let info = {
        cod_producto_opcion_ingrediente,
        valor
    };

    fetch(`controllers/controlador_productos.php?metodo=editProductosOpcionesIngredientes`, {
        method: 'POST',
        body: JSON.stringify(info)
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

function deleteProductoOpcionesIngrediente(cod_producto_opcion_ingrediente) {
    fetch(`controllers/controlador_productos.php?metodo=deleteProductosOpcionesIngredientes&cod_producto_opcion_ingrediente=${cod_producto_opcion_ingrediente}`, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1) {
            getProductosOpcionesIngredientes();
            notify(response.mensaje, "success", 2);
        }
        else {
            notify(response.mensaje, "error", 2);
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

$("body").on("click", ".btnEditProductoOpcionIngrediente", function(){
    let cod_producto_opcion_ingrediente = $(this).data("id");
    editProductoOpcionesIngrediente(cod_producto_opcion_ingrediente);
});

$("body").on("click", ".btnDeleteProductoOpcionIngrediente", function(){
    let cod_producto_opcion_ingrediente = $(this).data("id");
    swal.fire({
        title: ' Se eliminará este ingrediente de la opción',
        text: '¿Continuar?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
    }).then(function (result) {
        if (result.value) {
            deleteProductoOpcionesIngrediente(cod_producto_opcion_ingrediente);
        }
    });
});

$("body").on("click", "#btnCloseModalOpcionesIngredientes", function(){
    $("#modalOpcionesIngredientes").modal("hide");
    $("#modalItems").modal();
});
