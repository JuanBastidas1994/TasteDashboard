$(document).ready(function() {
    $("#btnNuevo").on("click", function(){
        window.location.href = "crear_demo.php";
    });
    
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });

    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
    
          var formData = new FormData($("#frmSave")[0]);
          
          var formaPago = $("#frmFormaPago").serializeArray();
          
          formData.append('txt_crop', $("#txt_crop").val());
          
          for (var i=0; i<formaPago.length; i++)
              formData.append(formaPago[i].name, formaPago[i].value);
          
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_empresa', id);
              
          }
          //alert(id);
     
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_demo.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    $("#id").val(response['id']);
                    $("#titulo").html($("#txt_nombre").val());
                    $("#alias").val(response['alias']);
                    $("#infoAlias").html(response['html']);
                    console.log(response['html']);
                    sendMail(event);
                    if(id == 0){
                    window.history.pushState(response, "Crear Empresa", "crear_empresa.php?id="+response['alias']);
                    }
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
    
    $("body").on("click","#btnEliminar", function(event){
        var cod_demo=$(this).attr("data-codigo");
       
            var parametros = {
                "cod_demo": cod_demo,
                "estado": "D"
            }
            
            messageConfirm('¿Estas seguro?', 'No se puede revertir los cambios', "warning")
            .then(function(result) {
                if (result) {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Eliminando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_demo.php?metodo=delete_demo',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
                                window.location.href = "demos.php";
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
    
    function sendMail(event)
    {
        var id = $("#id").val();
        var pass = $("#txt_password").val();
        event.preventDefault();
        //ENVIAR CORREO
	            $.ajax({
	            	url: 'correos/nuevo_usuario.php?id='+id+'&pass='+pass,
	            	type:'GET',
	            });
    }
    
    $("#btnBack").on("click",function(event){
    window.location.href = "demos.php";
});


    //TELEGRAM
    $(".btnBot").on("click", function(event){
      event.preventDefault();
          
          var form = $("#frmBot");
          form.validate();
          if(form.valid()==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
    
          var formData = new FormData($("#frmBot")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_empresa', id);
              formData.append('alias', $("#alias").val());
          }else{
            messageDone("Debes guardar primero la empresa",'error');
            return;
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_telegram.php?metodo=setBot',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
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

    //COMPONENTES
    var resize = null;
    var drEvent = $('.dropify').dropify({
      messages: { 
        'default': 'Click para subir o arrastra', 
        'remove':  'X', 
        'replace': 'Sube o Arrastra y suelta'
      },
      error:{
        'imageFormat': 'Solo se adminte imagenes cuadradas.'
      }
    });
    drEvent.on('dropify.beforeImagePreview', function(event, element){
      if (resize != null)
        resize.destroy();

        $("#modalCroppie").modal({
          closeExisting: false,
          backdrop: 'static',
          keyboard: false,
        });
    });

    $('#modalCroppie').on('shown.bs.modal', function() {  
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) { 
          $('#my-image').attr('src', e.target.result);
          
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: 400, height: 400 }, //tamaño de la foto que se va a obtener
            boundary: { width: 400, height: 400 }, //la imagen total
            showZoomer: true, // hacer zoom a la foto
            enableResize: false,
            enableOrientation: true // para q funcione girar la imagen 
          });
          $('#crop-get').on('click', function() { // boton recortar
            resize.result('base64').then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');

            });
          });
          $('.crop-rotate').on('click', function(ev) {
            resize.rotate(parseInt($(this).data('deg')));
          });

          
        } 
        reader.readAsDataURL(file);
    });


    $('.dropify').dropify({
      messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });
    $(".basic").select2({
      tags: true,
    });
    $(".tagging").select2({
      tags: true
    });


    /*PAGINAS*/
    $("#cmbRoles").on("change", function(event){
      var id = parseInt($("#id").val());
      if(id == 0){
          messageDone('Debe guardar primero la empresa','error');
          $("#cmbRoles").val("");
          return;
      }

      if($(this).val() == ""){
        $("#lstPaginas").html("");
        $("#lstOrden").html("");
        return;
      }


      var parametros = {
        "cod_rol": $(this).val(),
        "cod_empresa": id
      }
      $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_empresa.php?metodo=menuRol',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    $("#lstPaginas").html(response['paginas']);
                    $("#lstOrden").html(response['menu']);
                    feather.replace();

                    $("#lstOrden").sortable({
                        connectWith: ".connectedSortable",
                        update: function (event, ui) {
                            var selectedData = new Array();
                            $('#lstOrden>tr').each(function() {
                                selectedData.push($(this).attr("data-id"));
                            });
                            console.log(selectedData);
                            ordenarItems(selectedData);
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
      });

    function ordenarItems(data){
      console.log(data);
      var parametros = {
          "cod_rol": $("#cmbRoles").val(),
          "cod_empresa": $("#id").val(),
          "paginas": data
        }
      $.ajax({
          url:'controllers/controlador_empresa.php?metodo=actualizar',
          type:'POST',
          data:parametros,
          success:function(response){
            console.log(response);
            if(response['success']==1){
              notify("Actualizado correctamente", "success", 2);
            }
              //alert(response['mensaje']);
          },
          error: function(data){
            console.log(data);
          }
      });
    }

    $("body").on("click",".chkAddPagina", function(event){
      var parametros = {
        "cod_pagina": $(this).val(),
        "activo": $(this).is(":checked"),
        "cod_rol": $("#cmbRoles").val(),
        "cod_empresa": $("#id").val()
      }
      console.log(parametros);
      $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando Informacion");
             },
            url:'controllers/controlador_empresa.php?metodo=addPage',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    $("#cmbRoles").trigger("change");
                } 
                else
                {
                    alert(response['mensaje']);
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
    
    $("#btnActualizarCostoEnvio").on("click",function(event){
        event.preventDefault();
        var codigo=$(this).attr("data-id");
        var base_dinero=$("#base_dinero").val();
        var base_km=$("#base_km").val();
        var adicional_km=$("#adicional_km").val();
        var cod_empresa=$("#id").val();

        if($("#base_dinero").val().trim().length == 0 || $("#base_km").val().trim().length == 0 || $("#adicional_km").val().trim().length == 0){
 
             messageDone("Debe llenar todos los campos, vuelva a ingresarlos",'error');
            return;
        }

        if(base_dinero<=0 || base_km<=0 || adicional_km<=0)
        {
            messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "base_dinero": $("#base_dinero").val().trim(),
                "base_km": $("#base_km").val().trim(),
                "codigo": codigo,
                "cod_empresa": cod_empresa,
                "adicional_km": $("#adicional_km").val().trim()
            }
            console.log(parametros);
            if(codigo!=0)
            {
                swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                }).then(function(result) {
                  if (result.value) {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=update_costo_envio',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
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
            }
            else   
            {
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Insertando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=insert_costo_envio',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $("#btnActualizarCostoEnvio").attr("data-id",response['id']);
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
        }


     });
     
    $(".btnFidelizacion").on("click",function(event){
        var codigo=$(this).attr("data-id");
        var divisor=$("#txt_divisor_puntos").val();
        var puntos=$("#txt_monto_puntos").val();
        var cod_empresa=$("#id").val();
    
        if(divisor<=0 || puntos<=0)
        {
            messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "codigo": codigo,
                "divisor": divisor,
                "cod_empresa": cod_empresa,
                "puntos": puntos
            }
            if(codigo!=0)
            {
                  swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                }).then(function(result) {
                  if (result.value) {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando informacion, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=update_fidelizacion',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
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
            }
            else
            {
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Insertando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=insert_fidelizacion',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $(".btnFidelizacion").attr("data-id",response['id']);
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
        }
    });
    
    $("body").on("click",".btnInsertNiveles", function(event){
        var cod_empresa=$("#id").val();
        var metodo=$(this).attr("data-registro");
         
        var n1=$("#txt_nombre1").val();
        var n2=$("#txt_nombre2").val();
        var n3=$("#txt_nombre3").val();
        
        var i2=$("#txt_inicio2").val();
        var i3=$("#txt_inicio3").val();
        
        var f1=$("#txt_fin1").val();
        var f2=$("#txt_fin2").val();
        
        var m1=$("#txt_monto1").val();
        var m2=$("#txt_monto2").val();
        var m3=$("#txt_monto3").val();
        
        var str = $( "#frmNiveles" ).serialize();
        console.log(str);
        console.log(new FormData($("#frmNiveles")[0]));
        if(n1!="" ||n2!="" ||n3!="")
        {
            if(i2<=0 ||i3<=0 ||f1<=0 ||f2<=0 ||m1<=0 ||m2<=0 ||m3<=0)
            {
              messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
            }
            else
            {
            swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                }).then(function(result) {
                  if (result.value) {
                    if(metodo==0)
                    {
                  
                      $.ajax({
                        beforeSend: function(){
                            OpenLoad("Insertando los niveles, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=insert_niveles&cod_empresa='+cod_empresa,
                        type: 'POST',
                        data:new FormData($("#frmNiveles")[0]),
                        contentType: false,
                        processData: false,
                        success: function(response){
                          
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
                                $(".btnInsertNiveles").attr("data-registro",1);
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
                    else
                    {
                        $.ajax({
                            beforeSend: function(){
                                OpenLoad("Editando los niveles, por favor espere...");
                            },
                            url: 'controllers/controlador_configuraciones.php?metodo=edit_niveles&cod_empresa='+cod_empresa,
                            type: 'POST',
                            data:new FormData($("#frmNiveles")[0]),
                            contentType: false,
                            processData: false,
                            success: function(response){
                              
                                if( response['success'] == 1)
                                {
                                    messageDone(response['mensaje'],'success');
                                    $(".btnInsertNiveles").attr("data-registro",1);
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
                  }
                });
            } 
        }
        else
        {
             messageDone("Los nombres de los niveles no pueden estar vacios, vuelva a ingresarlos",'error');
        }
    });

    $(".btnNotificacionMotorizado").on("click",function(event){
       var token=$("#txt_token_motorizado").val();
       var topic=$("#txt_topic_motorizado").val();
       var codigo=$(this).attr("data-codigo");
       var cod_empresa=$("#id").val();
        var tipo="MOTORIZADOS";
        if(token=="" || topic=="")
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
                var parametros = {
                    "token": token,
                    "topic": topic,
                    "cod_empresa": cod_empresa,
                    "codigo": codigo
                }
                if(codigo!=0)
                {
                    swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                  }).then(function(result) {
                  if (result.value) {
                       $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=edit_notificacion',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
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
                }
                else
                {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Insertando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=insert_notificacion&tipo='+tipo,
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
                                $(".btnNotificacionMotorizado").attr("data-codigo",response['id']);
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
        }
    });
    
    $(".btnNotificacionUsuario").on("click",function(event){
       var token=$("#txt_token_usuario").val();
       var topic=$("#txt_topic_usuario").val();
       var codigo=$(this).attr("data-codigo");
       var cod_empresa=$("#id").val();
        var tipo="USUARIOS";
        if(token=="" || topic=="")  
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
             
            var parametros = {
                "token": token,
                "topic": topic,
                "cod_empresa": cod_empresa,
                "codigo": codigo
            }
            if(codigo!=0)
            {
                swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                }).then(function(result) {
                  if (result.value) {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=edit_notificacion',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
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
            }
            else
            {
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Insertando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=insert_notificacion&tipo='+tipo,
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $(".btnNotificacionUsuario").attr("data-codigo",response['id']);
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
        }
    });
    
    $(".btnCrearModulo").on("click",function(event){
        var nombre_modulo=$("#text_nuevo_modulo").val();
        var cod_empresa=$("#id").val();
        var descripcion = $("#txt_desc_items").val();
        if(nombre_modulo=="")  
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "nombre_modulo": nombre_modulo,
                "cod_empresa": cod_empresa,
                "descripcion": descripcion
            }
            
             $.ajax({
                beforeSend: function(){
                    OpenLoad("Insertando datos, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=crear_modulo',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
                        $("#tablaModulos").append(response['html']);
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
    
    $(".btnCrearAnuncio").on("click",function(event){
        var nombre_anuncio=$("#text_nuevo_anuncio").val();
        var cod_empresa=$("#id").val();
        var descripcion = $("#txt_desc_anuncio").val();
        var width=$("#txt_width").val();
        var height=$("#txt_height").val();
        if(nombre_anuncio=="" || width=="" || height=="")  
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "nombre_anuncio": nombre_anuncio,
                "cod_empresa": cod_empresa,
                "descripcion": descripcion,
                "width": width,
                "height": height
            }
            
             $.ajax({
                beforeSend: function(){
                    OpenLoad("Insertando datos, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=crear_anuncio',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
                        $("#tablaAnuncios").append(response['html']);
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
    
    $("body").on("click",".btnEditarModulo", function(event){
        var codigo=$(this).attr("data-codigo");
        var nombre_modulo=$("#txt_modulo"+codigo).val();
        var descripcion = $("#txa_modulo"+codigo).val();
        if(nombre_modulo=="")  
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "nombre_modulo": nombre_modulo,
                "codigo": codigo,
                "descripcion": descripcion
            }
            
             $.ajax({
                beforeSend: function(){
                    OpenLoad("Editando datos, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=editar_modulo',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
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
    
    $("body").on("click",".btnEditarAnuncio", function(event){
        var codigo=$(this).attr("data-codigo");
        var nombre_modulo=$("#txt_anuncio"+codigo).val();
        var descripcion = $("#txa_anuncio"+codigo).val();
        var width=$("#txt_width"+codigo).val();
        var height = $("#txt_height"+codigo).val();
        if(nombre_modulo=="")  
        {
            messageDone("Debe completar todos los campos, vuelva a ingresarlos",'error');
        }
        else
        {
            var parametros = {
                "nombre_anuncio": nombre_modulo,
                "codigo": codigo,
                "descripcion": descripcion,
                "width": width,
                "height": height
            }
            
             $.ajax({
                beforeSend: function(){
                    OpenLoad("Editando datos, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=editar_anuncio',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
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
   
    $("body").on("click",".btnEliminarModulo", function(event){
        var codigo=$(this).attr("data-codigo");
       
            var parametros = {
                "codigo": codigo
            }
            
            swal({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Eliminar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
            }).then(function(result) {
              if (result.value) {
                 $.ajax({
                    beforeSend: function(){
                        OpenLoad("Eliminando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=eliminar_modulo',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $("#contMo"+codigo).css("display","none");
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
    
    $("body").on("click",".btnEliminarAnuncio", function(event){
        var codigo=$(this).attr("data-codigo");
       
            var parametros = {
                "codigo": codigo
            }
            
            swal({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Eliminar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
            }).then(function(result) {
              if (result.value) {
                 $.ajax({
                    beforeSend: function(){
                        OpenLoad("Eliminando datos, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=eliminar_anuncio',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $("#contAn"+codigo).css("display","none");
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

    $("body").on("change", ".cmb_pago_permiso", function(){
        //$(this).css("background-color", "green");
    });
    
    
    $("body").on("click", ".btn-verificarToken", function(){
        $( "#txt_serverkey" ).prop( "disabled", true );
        $( "#txt_clientcode" ).prop( "disabled", true );
        $( "#txt_clientkey" ).prop( "disabled", true );
        $("#txt_servercode").prop('disabled',true);
        $("#cmbTipoBP").prop('disabled',true);
        $(".btn-updatePay").prop('disabled',true);
        var cod_empresa = $("#id").val();
        var parametros = {
                    "cod_empresa": cod_empresa,
                }
             $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=verificar_paymentez',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 0 || response['success'] == 2 )
                            {
                                $( "#txt_serverkey" ).prop( "disabled", false );
                                $( "#txt_clientcode" ).prop( "disabled", false );
                                $( "#txt_clientkey" ).prop( "disabled", false );
                                $("#txt_servercode").prop('disabled',false);
                                $("#cmbTipoBP").prop('disabled',false);
                                var mensaje;
                                if(response['success'] == 2)
                                {
                                    mensaje ="Ups, intente verificar mas tarde";
                                }
                                else
                                {
                                    mensaje ="Tokens Invalidos";   
                                }
                                messageDone(mensaje,'error');
                            }
                            else if( response['success'] == 1)
                            {
                                $(".btn-updatePay").prop('disabled',false);
                                messageDone("Tokens Verificados Correctamente",'error');
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
       
    
    $("body").on("click", ".btn-updatePay", function(){
       var servercode=$("#txt_servercode").val();
       var serverkey=$("#txt_serverkey").val();
       var clientcode=$("#txt_clientcode").val();
       var clientkey=$("#txt_clientkey").val();
       var codigo=$(this).attr("data-codigo");
       var cod_empresa=$("#id").val();
       var tipo=$("#cmbTipoBP").val();
       if(cod_empresa == 0)
       {
            messageDone("Debe guardar primero la empresa",'error');
            return;
        }
        if(servercode=="" || serverkey=="" || clientcode=="" || clientkey=="")
        {
            messageDone("Debe completar todos los campos",'error');
        }
        else
        {
                var parametros = {
                    "servercode": servercode,
                    "serverkey": serverkey,
                    "clientcode": clientcode,
                    "clientkey": clientkey,
                    "cod_empresa": cod_empresa,
                    "codigo": codigo,
                    "tipo": tipo
                }
                if(codigo!=0)
                {
                    swal({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Actualizar',
                  cancelButtonText: 'Cancelar',
                  padding: '2em'
                  }).then(function(result) {
                  if (result.value) {
                       $.ajax({
                        beforeSend: function(){
                            OpenLoad("Editando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=edit_paymentez',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                $( "#txt_serverkey" ).prop( "disabled", false );
                                $( "#txt_clientcode" ).prop( "disabled", false );
                                $( "#txt_clientkey" ).prop( "disabled", false );
                                $("#txt_servercode").prop('disabled',false);
                                $("#cmbTipoBP").prop('disabled',false);
                                $(".btn-updatePay").prop('disabled',true);
                                messageDone(response['mensaje'],'success');
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
                }
                else
                {
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Insertando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_configuraciones.php?metodo=insert_paymentez&tipo='+tipo,
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 1)
                            {
                                messageDone(response['mensaje'],'success');
                                $(".btn-updatePay").attr("data-codigo",response['id']);
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
        }
    });
});