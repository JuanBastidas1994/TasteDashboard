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
