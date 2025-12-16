var templateItemTabla = Handlebars.compile($("#itemTabla").html());
$(document).ready(function() {
    
    $("body").on("click", ".btnEditar", function(){
        var cod_categoria = $(this).data("value");
        $("#id").val(cod_categoria);
        var nom_cat = $(this).parents('tr').find(".nom_cat").html();
        $("#exampleModalLabel").html("Agregar Adicionales - " + nom_cat);
        var parametros = {
                    "cod_categoria": cod_categoria
        }
        
        $.ajax({
            beforeSend: function(){
                OpenLoad("Por favor espere...");
            },
            url: 'controllers/controlador_personalizar_web.php?metodo=get_adicionales',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {   
                    $("#id").val(cod_categoria);
                    $("#bloque_items").html(response['html']);
                    $("#modalDescripcion").modal();
                } 
                else
                {
                    $("#modalDescripcion").modal();
                    $("#bloque_items").html("");
                } 
                                        
            },
            error: function(data){
                console.log(data);
                
            },
            complete: function(resp)
            {
                CloseLoad();
            }
        });
    });
    
    $("body").on("click", ".btnSaveDesc", function(){
        var cod_categoria = $(this).data("value");
        
       var formData = new FormData($("#frmDescripcion")[0]);
        
        $.ajax({
            beforeSend: function(){
                OpenLoad("Por favor espere...");
            },
            url: 'controllers/controlador_personalizar_web.php?metodo=set_adicionales',
            type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {
                    messageDone(response['mensaje'],'success');
                    $("#modalDescripcion").modal("hide");
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
        });
    });
    
    $("body").on("click", "#btn_anadir", function(e){
        e.preventDefault();
        if($("#cmb_categorias").length > 0){
            var existe = false;
            var cod_categoria = $("#cmb_categorias").val();
            var categoria = $("#cmb_categorias option:selected").text();
            $("input[name='txt_cod_item[]']").each(function(indice, elemento) {
                if(cod_categoria == $(elemento).val()){
                    existe = true;
                }
            });
            if(existe == false){
                var input = "<tr data-codigo=\"\"> \
                                <td>"+categoria+"<input type=\"hidden\" name=\"txt_cod_item[]\" value=\""+cod_categoria+"\"</td> \
                                <td><input type=\"text\" class=\"form-control\" name=\"txt_titulo[]\" value=\"\"></td> \
                                <td style=\"text-align: center;\"><button class=\"btn btn-danger btn-sm btnEliminarItem\">x</button></td> \
                            </tr>";
                $("#bloque_items").append(input);
            }
        }
        else{
            var input = "   <tr data-codigo=\"\"> \
                                <td>"+categoria+"<input type=\"hidden\" name=\"txt_cod_item[]\" value=\""+cod_categoria+"\"</td> \
                                <td><input type=\"text\" class=\"form-control\" name=\"txt_titulo[]\" value=\"\"></td> \
                                <td style=\"text-align: center;\"><button class=\"btn btn-danger btn-sm btnEliminarItem\">x</button></td> \
                            </tr>";
            $("#bloque_items").append(input);
        }
    });
    
    $("body").on("click", ".btnEliminarItem", function(e){
        e.preventDefault();
        $(this).parents('tr').remove();
    });
    
    
    
  $("#bloque_items").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#bloque_items>tr').each(function() {
                
                selectedData.push($(this).attr("data-codigo"));
            });
            var formData = new FormData($("#frmDescripcion")[0]);
    
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Por favor espere...");
                },
                url: 'controllers/controlador_personalizar_web.php?metodo=ordenar',
                type: 'POST',
                  data: formData,
                  contentType: false,
                  processData: false,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        //messageDone(response['mensaje'],'success');
                    } 
                    else
                    {
                        //messageDone(response['mensaje'],'error');
                    } 
                                            
                },
                error: function(data){
                    console.log(data);
                    
                },
                complete: function(resp)
                {
                    CloseLoad();
                }
            });
        }
    });
});

$("#btnActualizarInfo").on("click",function(event){
    event.preventDefault();
    var codigo=$(this).attr("data-id");
    var direccion=$("#txt_direccion").val();
    var telefono=$("#txt_telefono").val();
    var correo=$("#txt_correo").val();
    
    if(direccion=="" || telefono == "" || correo == "" ){
            alert("Debes llenar los campos obligatorios");
            return;
        }
        
        swal({
              title: '¿Desea Confirmar Datos?',
              text: 'No se puede revertir los cambios',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Actualizar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
            }).then(function(result) {
              if (result.value) {
                
                var parametros = {
                    "direccion": direccion,
                    "telefono":telefono,
                    "correo": correo,
                    "codigo":codigo
                }
                console.log(parametros);
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=update_Info',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $("input[name='txt_redes[]']").each(function(indice, elemento) {
                                if($(elemento).val() != "")
                                {
                                    UpdateRedes(this.id,$(elemento).val(),codigo);    
                                }
                            });
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
                });


              }
            });
});

/*FUNCIONES EVENTOS*/
var resize = null;
let imgBase64 = "";
$("#image_evento").on("change", function(){
    if (resize != null)
        resize.destroy();

    if(this.files.length > 0){
        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    }
});

$('#modalCroppie').on('shown.bs.modal', function() {
    var aux = $("#image_evento").get(0);
    var file = aux.files[0];
    var reader = new FileReader();
    reader.onload = function (e) { 
      $('#my-image').attr('src', e.target.result);
      
      resize = new Croppie($('#my-image')[0], {
        viewport: { width: 500, height: 500 }, //tamaño de la foto que se va a obtener
        boundary: { width: 600, height: 600 }, //la imagen total
        showZoomer: true, // hacer zoom a la foto
        enableResize: false,
        enableOrientation: true, // para q funcione girar la imagen 
        mouseWheelZoom: 'ctrl'
      });
      $('#crop-get').on('click', function() { // boton recortar
        resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
          //uploadImageCumple(dataImg);
          imgBase64 = dataImg;
          $("#crop").val(dataImg);
          $(".imgEvento").attr("src",dataImg);
          $("#modalCroppie").modal('hide');
        });
      });
      $('.crop-rotate').on('click', function(ev) {
        resize.rotate(parseInt($(this).data('deg')));
      });

      
    } 
    reader.readAsDataURL(file);
});

$(".btnGuardar").on("click",function(event){
    event.preventDefault();
    
    var form = $("#frmSave");
    form.validate();
    if(form.valid()==false)
    {
      notify("Falta llenar informacion", "success", 2);
      return false;
    }

    var formData = new FormData($("#frmSave")[0]);
    var id = parseInt($("#id").val());
    if(id > 0){
        formData.append('id', id);
    }

    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando datos, por favor espere...");
         },
        url: 'controllers/controlador_modales.php?metodo=crear',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            if( response['success'] == 1){
              messageDone(response['mensaje'],'success');
              listaModalesEventos();
            } 
            else{
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
    });
});

$("body").on("click", ".btnEditarAnuncio", function(e){
    let id = $(this).data("value");
    let parametros = {
        id: id
    }
    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando datos, por favor espere...");
         },
        url: 'controllers/controlador_modales.php?metodo=get',
        type: 'GET',
        data: parametros,
        success: function(response){
            console.log(response);
            if( response['success'] == 1){
                let data = response['data'];
                $("#txt_titulo").val(data.titulo);
                $("#cmbAccion").val(data.accion_id);
                showDetalleAccion(data.accion_id);
                loadDetalleAccion(data.accion_id, data.accion_desc);

                $("#hora_ini").val(data.fecha_inicio);
                $("#hora_fin").val(data.fecha_fin);
                $("#uid").val(data.cod_modal_evento);
            } 
            else{
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
    });
});

function listaModalesEventos(){
    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando datos, por favor espere...");
         },
        url: 'controllers/controlador_modales.php?metodo=lista',
        type: 'GET',
        success: function(response){
            console.log(response);
            if( response['success'] == 1){
              $("#datos").html(templateItemTabla(response['data']));
              feather.replace();
            } 
            else{
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
    });
}


$(".tabModalesEventos").on("click", function(){
    listaModalesEventos();
});

//ACCION 
$(".cmbAccion").on("change", function(){
    showDetalleAccion($(this).val());
});

function showDetalleAccion(accion){
$(".inputAccion").hide();
if(accion == "URL" || accion == "FILTER"){
    $("#txt_accion_desc").show();
}else if(accion == "PRODUCTO"){
    $("#cmbProductos").show();
}else if(accion == "NOTICIA"){
    $("#cmbNoticias").show();
}
}

function loadDetalleAccion(accion, val){
if(accion == "URL" || accion == "FILTER"){
    $("#txt_accion_desc").val(val);
}else if(accion == "PRODUCTO"){
    $("#cmbProductos").val(val);
}else if(accion == "NOTICIA"){
    $("#cmbNoticias").val(val);
}
}


var picker = document.getElementById("hora_ini");
flatpickr(picker, {
    enableTime: true,
    dateFormat: "Y-m-d H:i"
});

var picker2 = document.getElementById("hora_fin");
flatpickr(picker2, {
    enableTime: true,
    dateFormat: "Y-m-d H:i"
});