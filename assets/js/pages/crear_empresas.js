$(document).ready(function() {


    getMessagePayment();
    
     $("#cmb_courier").select2();
     $("#cmb_courier").trigger("change");
                    
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
        
    });
    
    $(".btnGenerarDemo").on("click",function(event){
        $("#modalConstuirPagina").modal();
        
    });

    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          /*
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }*/
    
          var formData = new FormData($("#frmSave")[0]);
          
          var formaPago = $("#frmFormaPago").serializeArray();
          var frmweb = $("#frmWeb").serializeArray();
          
          formData.append('txt_crop', $("#txt_crop").val());
          
          for (var i=0; i<formaPago.length; i++)
              formData.append(formaPago[i].name, formaPago[i].value);
          
          for (var i=0; i<frmweb.length; i++)
              formData.append(frmweb[i].name, frmweb[i].value);
          
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_empresa', id);
              
          }
          //alert(id);
            
            //PIXEL DE FACEBOOK
            var pixel = $("#txt_pixel").val();
            var pixel_verify = $("#txt_pixel_verify").val();
            formData.append('pixel', pixel);
            formData.append('pixel_verify', pixel_verify);
            
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_empresa.php?metodo=crear',
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
                    if(id == 0){
                        sendMail(event);
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
        window.location.href = "empresas.php";
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

    $("#cmb_courier").on("change",function(){
        $(".courierGacela").hide();
        $(".courierLaar").hide();
        $("#cmb_courier option:selected").each(function(){
            if($(this).val()==1 )
            {
                $(".courierGacela").show();
            }
            else if($(this).val()==2 )
            {
                $(".courierLaar").show();
            }
        });        
    });
    
    $("#chk_estadoGAmbiente").on("change",function(){
        var id = parseInt($("#id").val());
        var ambiente ="";
            if($(this).is(":checked"))
            {
                ambiente ="production";
            }
            else
            {
                ambiente ="development";
            }
        
        var parametros = {
        "ambiente": ambiente,
        "cod_empresa": id
      }
      $.ajax({
              url: 'controllers/controlador_empresa.php?metodo=AmbienteGacela',
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
    

    $("#formTipoTransporte").on('submit', function(event){
        event.preventDefault()
    
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
            beforeSend: () => {
                OpenLoad("Editando datos, por favor espere...");
            },
            success: (response) => {
                notify(response['mensaje'],'success', 3)

            },
            error: (response) => {
                notify(response['mensaje'],'error', 3)

            },
            complete: () => {
                CloseLoad()
            }
        })
    })
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
                Swal.fire({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  icon: 'warning',
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
                  Swal.fire({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  icon: 'warning',
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
            Swal.fire({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  icon: 'warning',
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
                          
                            if( response['success'] == 1){
                                $(".chkFidelizacion").show();
                                messageDone(response['mensaje'] + ", Recuerde activar la fidelización",'success');
                                $(".btnInsertNiveles").attr("data-registro",1);
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
                Swal.fire({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  icon: 'warning',
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
                Swal.fire({
                  title: '¿Estas seguro?',
                  text: 'No se puede revertir los cambios',
                  icon: 'warning',
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
            
            Swal.fire({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              icon: 'warning',
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
            
            Swal.fire({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              icon: 'warning',
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
    
    $("body").on("change", "#cmbTipoD", function(){
         var parametros = {
                    "id":$("#id").val(),
                    "ambiente": $( "#cmbTipoD").val()
                }
            $.ajax({
                        beforeSend: function(){
                            OpenLoad("Por favor espere...");
                        },
                        url: 'controllers/controlador_empresa.php?metodo=InfoDelivery',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            $("#tablaDelivery").html(response['info']);
                            feather.replace();
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
    
    
    $("body").on("click", ".btnverconfig", function(){
        var api=$(this).attr("data-api"); 
        var token=$(this).attr("data-token"); 
         var parametros = {
                    "api": api,
                    "token": token,
                    "ambiente": $( "#cmbTipoD").val()
                }
             $.ajax({
                        beforeSend: function(){
                            OpenLoad("Por favor espere...");
                        },
                        url: 'controllers/controlador_empresa.php?metodo=viewConfigGacela',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            messageDone(response['info']['status'],'success');
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
    
    $("body").on("click", ".btnEditarGacela", function(){
        
       var codigoSucursal=$(this).attr("data-codigo"); 
       var codigoGacela=$(this).attr("data-id"); 
       if($(this).html()=="Editar")
       {
            $(this).html("Guardar");
            $( "#txt_empresaG"+codigoSucursal).prop( "disabled", false );
            $( "#txt_sucursalG"+codigoSucursal).prop( "disabled", false );
       }
       else
       {
            var parametros = {
                    "api": $( "#txt_empresaG"+codigoSucursal).val(),
                    "token": $( "#txt_sucursalG"+codigoSucursal).val(),
                    "ambiente": $( "#cmbTipoD").val(),
                    "cod_gacela_sucursal":codigoGacela,
                    "cod_sucursal":codigoSucursal,
                    "cod_empresa": $("#id").val()
                }
             $.ajax({
                        beforeSend: function(){
                            OpenLoad("Verificando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_empresa.php?metodo=verificarTokens',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 0 )
                            {
                                messageDone(response['mensaje'],'error');
                            }
                            else if( response['success'] == 1)
                            {
                                $("#editar_"+codigoSucursal).html("Editar");
                                $( "#txt_empresaG"+codigoSucursal).prop( "disabled", true );
                                $( "#txt_sucursalG"+codigoSucursal).prop( "disabled", true );
                                messageDone(response['mensaje'],'success');
                                if(codigoGacela==0){
                                    $("#editar_"+codigoSucursal).attr("data-id",response['idGacela']);
                                }
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
    
    
    
    $("body").on("click", ".btnEditarLaar", function(){
        
       var codigoSucursal=$(this).attr("data-codigo"); 
       var codigoLaar=$(this).attr("data-id"); 
       if($(this).html()=="Editar")
       {
            $(this).html("Guardar");
            $( "#txt_userL"+codigoSucursal).prop( "disabled", false );
            $( "#txt_passL"+codigoSucursal).prop( "disabled", false );
       }
       else
       {
           var user = $("#txt_userL"+codigoSucursal).val();
           var pass = $("#txt_passL"+codigoSucursal).val();
           
           alert(user);
           alert(pass);
           alert(codigoLaar);
           
           if(user == "")
           {
               messageDone("Campo Obligatorio..",'error');
               return;
           }
           
           if(pass == "")
           {
               messageDone("Campo Obligatorio..",'error');
               return;
           }
           
            var parametros = {
                    "user": user,
                    "pass": pass,
                    "cod_laar_sucursal":codigoLaar,
                    "cod_sucursal":codigoSucursal,
                    "cod_empresa":$("#id").val()
                }
             $.ajax({
                        beforeSend: function(){
                            OpenLoad("Verificando datos, por favor espere...");
                        },
                        url: 'controllers/controlador_empresa.php?metodo=SaveTokensLaar',
                        type: 'GET',
                        data: parametros,
                        success: function(response){
                            console.log(response);
                            if( response['success'] == 0 )
                            {
                                messageDone(response['mensaje'],'error');
                            }
                            else if( response['success'] == 1)
                            {
                                $("#editar_L"+codigoSucursal).html("Editar");
                                $( "#txt_userL"+codigoSucursal).prop( "disabled", true );
                                $( "#txt_passL"+codigoSucursal).prop( "disabled", true );
                                messageDone("Token Guardado Correctamente",'success');
                                if(codigoLaar==0){
                                    $("#editar_L"+codigoSucursal).attr("data-id",response['idLaar']);
                                }
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
                        Swal.fire({
                      title: '¿Estas seguro?',
                      text: 'No se puede revertir los cambios',
                      icon: 'warning',
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
                                    $(".btn-updatePay").removeClass("btn-warning");
                                    $(".btn-updatePay").addClass("btn-outline-primary");
                                    $(".btn-updatePay").html("Actualizar");
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
        
    /*time picker*/
    if(document.getElementById('txt_fecha_caducidad'))
        var f4 = flatpickr(document.getElementById('txt_fecha_caducidad'), {
            enableTime: false,
            dateFormat: "Y-m-d"
        });
        
    //$("#cmb_planes").trigger("change");
}); //FIN DOCUMENT ready
    
    //TELEGRAM
    $(".btnIntegrarClickUp").on("click", function(event){
      event.preventDefault();
          
            
          var id = parseInt($("#id").val());
          if(id <= 0){
            messageDone("Debes guardar primero la empresa",'error');
            return;
          }
          
            var parametros = {
                cod_empresa: id,
                alias: $("#alias").val(),
                nombre: $("#txt_nombre").val()
            };

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Creando ambiente en clickup, por favor espere...");
               },
              url: 'controllers/controlador_clickup.php?metodo=crearLista',
              type: 'POST',
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
    });
    
    $("body").on("click", ".btn_notificar", function(e){
        e.preventDefault();
        var cod_usuario = $(this).data("value");
        $("#cod_usuario").val(cod_usuario);
        $("#modalNotificacion").modal();
    });
    
    $("body").on("click", ".btn_editar_pass", function(e){
        e.preventDefault();
        var cod_usuario = $(this).data("value");
        $("#cod_usuario2").val(cod_usuario);
        
        var parametros = {
          "cod_usuario": cod_usuario
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_usuario.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#txt_usuario").val(data['usuario']);

                    $("#modalEditarPass").modal();
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
    
    $(".btnSendNotification").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmNotificar");
          form.validate();
          var isForm = form.valid();

          if(isForm ==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
          
          var formData = new FormData($("#frmNotificar")[0]);
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_notificaciones.php?metodo=notificar_admins',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    $("#frmNotificar").trigger("reset");
                    $("#modalNotificacion").modal("hide");
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
    
    $(".btnRestablecer").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmEditarPass");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
    
          var formData = new FormData($("#frmEditarPass")[0]);
          var id = parseInt($("#cod_usuario2").val());
          if(id > 0){
              formData.append('cod_usuario', id);
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_usuario.php?metodo=restablecer',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    $("#frmEditarPass").trigger("reset"); 
                    $("#modalEditarPass").modal("hide");
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
    
    $(".btn-getTokens").on("click", function(){
        var clientCode = $("#txt_clientCode_desarrollo").val();
        var clientKey = $("#txt_clientKey_desarrollo").val();
        var serverCode = $("#txt_serverCode_desarrollo").val();
        var serverKey = $("#txt_serverKey_desarrollo").val();
        
        $("#txt_clientcode").val(clientCode);
        $("#txt_clientkey").val(clientKey);
        $("#txt_servercode").val(serverCode);
        $("#txt_serverkey").val(serverKey);
        
        $(".btn-updatePay").removeClass("btn-outline-primary");
        $(".btn-updatePay").addClass("btn-warning");
        $(".btn-updatePay").html("Guardar");
    });
    
    /*$("#cmb_planes").on("change", function(){
        var combo = $(this);
        var precio = $("#txt_valor_cobrar").val();
        var value = $("#cmb_planes").data("value");
        
        var valor = combo.find("option").attr("selected").val();
        alert(valor);
        if(precio == "")
            $("#txt_valor_cobrar").val(valor);
    });*/

    $("#chk_programar").on("change", function(){
        let checkb = $(this);
        let cod_empresa = checkb.data("empresa");
        let programa = 0;
        if(checkb.is(":checked"))
            programa = 1;
        else
            programa = 0;
        
        var parametros = {
            "cod_empresa": cod_empresa,
            "programa": programa
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=setProgramarPedido',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){
                  messageDone(response['mensaje'], "success");
                }
                else{
                  messageDone(response['mensaje'], "error");
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    });

    $("#chk_envioIva").on("change", function(){
        let checkb = $(this);
        let cod_empresa = checkb.data("empresa");
        let grava = 0;
        if(checkb.is(":checked"))
            grava = 1;
        else
            grava = 0;
        
        var parametros = {
            "cod_empresa": cod_empresa,
            "grava": grava
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=setGravaIva',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){
                  messageDone(response['mensaje'], "success");
                }
                else{
                  messageDone(response['mensaje'], "error");
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    });


    $("#chk_fidelizacion").on("change", function(){
        var id = parseInt($("#id").val());
        if(id == 0){
            messageDone("Primero debes crear la empresa para activar el permiso", "error");
            return;
        }

        if($("#chk_fidelizacion").is(":checked")){
            /*VALIDAR QUE NO ESTEN VACIOS LOS CAMPOS */
            // let nomNivel2 = $(".txt_nombre2").val();
            // let nomNivel3 = $(".txt_nombre3").val();
            // let nomNivel1 = $(".txt_nombre1").val();
            // if(nomNivel1 == "" | nomNivel2 == "" | nomNivel3 == ""){
            //     alert("Por favor llenar los datos de los niveles primero");
            //     $("#chk_fidelizacion").prop("checked", false);
            //     return;
            // }

            let divisor = $("#txt_divisor_puntos").val();
            let monto = $("#txt_monto_puntos").val();

            if(divisor == "" | monto == ""){
                alert("Por favor llenar esquema primero");
                $("#chk_fidelizacion").prop("checked", false);
                return;
            }
        }

        let checkb = $(this);
        let estado = 0;
        if(checkb.is(":checked"))
            estado = 1;
    
        var parametros = {
            "cod_empresa": id,
            "estado": estado
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=setPermisoFidelizacion',
           data: parametros,
           type: "GET",
           beforeSend: function(){
                OpenLoad("Actualizando estado, por favor espere...");
            },
           success: function(response){
                console.log(response);
                if(response['success']==1){
                    messageDone(response['mensaje'], "success");
                    }
                    else{
                    messageDone(response['mensaje'], "error");
                }
           },
           error: function(data){
                console.log(data);
           },
           complete: function(){
                CloseLoad();
           },
        });
    });

    $("#chk_produccion").on("change", function(){
        let checkb = $(this);

        var id = parseInt($("#id").val());
        if(id == 0){
            checkb.prop("checked", false);
            notify("Primero debes crear la empresa para activar cambiar el ambiente", "error", 2);
            return;
        }

        let cod_empresa = checkb.data("empresa");
        let ambiente = "development";
        if(checkb.is(":checked"))
            ambiente = "production";
        let parametros = {
            "cod_empresa": cod_empresa,
            "ambiente": ambiente
        }
        $.ajax({
            url:'controllers/controlador_empresa.php?metodo=setAmbienteEmpresa',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    notify(response['mensaje'], "success", 2);
                }
                else{
                    notify(response['mensaje'], "error", 2);
                }
            },
            error: function(data){
            },
            complete: function(){
            },
        });
    });

    if($(".btnCopiar").length > 0){
        var clipboard = new Clipboard('.btnCopiar');
        clipboard.on('success', function(e) {
            notify('Copiado:'+e.text, 'success', 2);

            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);

            e.clearSelection();
        });
    }

    $(".flLogos").change(function(){
        let inputfile = this.files[0];
        //let img = $(this).next('img');
        let imgSubida = $(this).data("titulo");
        let nomImage = $(this).data("name");
        let formData = new FormData($("#frmLogos")[0]);
        formData.append("nomImage", nomImage);
        formData.append("inputFile", inputfile);

        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=subirLogos',
           data: formData,
           type: "POST",
           contentType: false,
           processData: false,
           success: function(response){
              console.log(response);
              if(response['success']==1){
                notify(imgSubida + " subida con éxito", "success");
                  //img.attr("src", response['rutaImage']);
              }
              else{
                notify(response['mensaje'], "error");
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    });
    
    $(".btnEliminarLogo").on('click', function(e){
        e.preventDefault();
        var id = parseInt($("#id").val());
        if(id == 0){
            messageDone("Primero debes crear la empresa para activar el permiso", "error");
            return;
        }
        
        let {name} = $(this).data();
        console.log($(this).data(), name);

        Swal.fire({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Eliminar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
        }).then(function(result) {
              if (result.value) {
                 $.ajax({
                   url:'controllers/controlador_empresa.php?metodo=eliminarLogos',
                   data: {
                       'image': name,
                       'business_id': id
                   },
                   type: "GET",
                   success: function(response){
                      console.log(response);
                      if(response['success']==1){
                        notify(imgSubida + " subida con éxito", "success");
                          //img.attr("src", response['rutaImage']);
                      }
                      else{
                        notify(response['mensaje'], "error");
                      }
                   },
                   error: function(data){
                   },
                   complete: function(){
                   },
                });
                
              }
        });
            
        
    });

    $("body").on("click", ".LoginAdmins", function(){
        let alias = $(this).data("value");
        let user = $(this).data("user");
        Swal.fire({
           title: 'Se cerrará la sesión actual',
           text: '¿Continuar?',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Aceptar',
           cancelButtonText: 'Cancelar',
           padding: '2em'
        }).then(function(result){
           if (result.value) {
              cerrarActualLogin(alias, user);
           }
        }); 
    });

    function cerrarActualLogin(newAlias, user) {
        const alias = Cookies.get('alias');
        console.log(alias);

        $.ajax({
            url:'controllers/controlador_usuario.php?metodo=logout',
            type: "POST",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    notify(response['mensaje'], "success", 2);
                    unSubscribeTokenToTopic("general");
                    unSubscribeTokenToTopic(response['alias']);
                    unSubscribeTokenToTopic("usuario"+response['id']);
                    
                    buscarAdmin(newAlias, user);
                }else{
                    messageDone(response['mensaje'],'error');
                }
            },
            error: function(data){
              console.log(data);  
            },
            complete: function()
            {
              
            }
        });
    }

    function buscarAdmin(alias, user){
        let parametros = {
            "alias": alias,
            "user": user
        }
        $.ajax({
           url:'controllers/controlador_usuario.php?metodo=getUserAdmin',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){ 
                  loginNuevo(response['usuario'], response['pass']);  
              }
              else{
                  messageDone(response['mensaje'], "error");
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    }

    function loginNuevo(user, pass){
        var parametros = {
            "username": user,
            "password": pass
        }
        $.ajax({
            url:'controllers/controlador_usuario.php?metodo=loginAutomatico',
            data: parametros,
            type: "POST",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    notify(response['mensaje'], "success", 2);
                    subscribeTokenToTopic("general");
                    subscribeTokenToTopic(response['alias']);
                    subscribeTokenToTopic("usuario"+response['id']);
                    
                    setTimeout(function () {
                      window.location.href = './index.php';
                    }, 1200);
                }else{
                    messageDone(response['mensaje'],'error');
                }
            },
            error: function(data){
              console.log(data);  
            },
            complete: function()
            {
              
            }
        });
    }

    $(".btnActLogosPagina").on("click", function(e){
        e.preventDefault();
        Swal.fire({
           title: 'Los cambios podrían ser irreversibles',
           text: '¿Continuar?',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Aceptar',
           cancelButtonText: 'Cancelar',
           padding: '2em'
        }).then(function(result){
           if (result.value) {
                actualizarLogos();
           }
        }); 
    });

    function actualizarLogos() {
        let cod_empresa = $("#id").val();
        let url = "/home1/digitalmind/" + $("#urlFolder").val();
        let parametros = {
            "id": cod_empresa,
            "url": url
        }
        $.ajax({
           url:'https://dashboard.mie-commerce.com/replicador/iconos.php',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){
                notify(response['mensaje'], "success", 2);
              }
              else{
                notify(response['mensaje'], "error", 2);
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    }

    $("body").on("click", ".btnConfig", function(){
        let alias = $(this).data("value");
        let parametros = {
            "alias": alias
            }
        $.ajax({
            url:'controllers/controlador_empresa.php?metodo=getConfigs',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    $("#divCouriers").html(response['htmlCouriers']);
                    $("#divBotonPagos").html(response['htmlBotonPago']);
                    $("#divOtros").html(response['htmlOtros']);
                    feather.replace();
                    $("#modalConfig").modal();
                }
                else{

                }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    });
    
    
    $("body").on("click", ".btnMore", function(){
        let alias = $(this).data("value");
        let web = $(this).data("web");
        let folder = $(this).data("folder");
        
        $("#alias-mas-funciones").val(alias);
        var parametros = {
            "alias": alias,
            "web": web,
            "folder": folder,
        }
        var template = Handlebars.compile($("#lista-funciones").html());
        $(".lista-funciones").html(template(parametros));
        feather.replace();
        $("#modalFunciones").modal();
    });

    function updateEmprendedor(cod_empresa, isEmprendedor){
        let link = 'https://dashboard.mie-commerce.com/replicador/emprendedores_delete.php?id=' + cod_empresa;
        if(1 == isEmprendedor){
            link = 'https://dashboard.mie-commerce.com/replicador/emprendedores.php?id=' + cod_empresa;
        }
        $.ajax({url: link, success: function(result){
            console.log(result);
        }});
    }
    
    //Permisos
    $(".chk_permiso").on("change", function(){
        let checkb = $(this);
        let cod_empresa = checkb.data("empresa");
        let permiso = checkb.data("status");
        
        let status = 0;
        if(checkb.is(":checked"))
            status = 1;
        
        var parametros = {
            "cod_empresa": cod_empresa,
            "permiso": permiso,
            "status": status
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=setPermisos',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){
                  notify(response['mensaje'], "success", 2);
                }
                else{
                  notify(response['mensaje'], "error", 2);
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    });
    
    //Actualizar el impuesto
    function actualizarImpuesto(){
        let id = parseInt($("#id").val());
        let impuesto = parseInt($("#txt_impuesto").val());
        let tipo = $("#cmb_tipo").val()
        swal.fire({
            title: '¿Estas seguro de actualizar el impuesto?',
            text: "¡No podrás revertir esto!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {

                var parametros = {
                    "cod_empresa": id,
                    "impuesto": impuesto,
                    "tipo": tipo
                }
                $.ajax({
                    beforeSend: function () {
                        OpenLoad("Actualizando impuesto, por favor espere...");
                    },
                    url: 'controllers/controlador_empresa.php?metodo=actualizarImpuesto',
                    type: 'GET',
                    data: parametros,
                    success: function (response) {
                        console.log(response);
                        if (response['success'] == 1) {
                            messageDone(response['mensaje'], 'success');
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
    }

    /* FALTAS DE PAGO */
    function getMessagePayment() {
        const id = $("#id").val();
        if(id === 0)
            return;

        fetch(`controllers/controlador_empresa.php?metodo=getFaltaPagos&id=${id}`,{
            method: 'GET'
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1) {
                $("#fp-title").val(response.data.titulo);
                $("#fp-message").val(response.data.mensaje);
                $("#removeMessagePayment").removeClass("d-none");
            }
        })
        .catch(error=>{
            console.log(error);
        });
    }

    function saveMessagePayment() {
        const id = $("#id").val();
        const title = $("#fp-title").val();
        const message = $("#fp-message").val();

        if(title == '' || message == '') {
            notify("Ingrese título o mensaje", "error", 2);
            return;
        }

        const info = {
            id,
            title,
            message
        }

        OpenLoad("Guardando mensaje...");
        fetch(`controllers/controlador_empresa.php?metodo=guardarFaltaPagos`,{
            method: 'POST',
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if(response.success == 1){
                notify(response.mensaje, "success", 2);
            }
            else{
                notify(response.mensaje, "error", 2);
            }
        })
        .catch(error=>{
            CloseLoad();
            notify('Ocurrió un error', "error", 2);
            console.log(error);
        });
    }

    function removeMessagePayment() {
        const id = $("#id").val();
        if(id === 0)
            return;

        OpenLoad("Eliminando...");
        fetch(`controllers/controlador_empresa.php?metodo=removeFaltaPagos&id=${id}`,{
            method: 'GET'
        })
        .then(res => res.json())
        .then(response => {
            CloseLoad();
            if(response.success == 1) {
                $("#removeMessagePayment").addClass("d-none");
                $("#fp-title").val("");
                $("#fp-message").val("");
                notify(response.mensaje, "success", 2);
            }
            else {
                notify(response.mensaje, "error", 2);
            }
        })
        .catch(error=>{
            CloseLoad();
            notify('Ocurrió un error', "error", 2);
            console.log(error);
        });
    }
    
    
     $(".btnCrearDemo").on("click", function(){
         let id = $("#id").val();
        let data = $(this).data();
        let url = $("#folder_demo").val();
        
        console.log(data, url);
        Swal.fire({
           title: 'Los cambios podrían ser irreversibles',
           text: '¿Continuar?',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Aceptar',
           cancelButtonText: 'Cancelar',
           padding: '2em'
        }).then(function(result){
           if (result.value) {

                $.ajax({
                    url:'https://dashboard.mie-commerce.com/replicador/replicar.php',
                    data: {
                        id,
                        template: data.file,
                        url
                    },
                    type: "GET",
                    success: function(response){
                        console.log(response);
                        if(response['success']==1){
                            $("#pdetalle").html(response['detalle']);
                            notify(response['mensaje'], "success", 2);
                        }
                        else{
                            notify(response['mensaje'], "error", 2);
                        }
                    },
                    error: function(data){
                    },
                    complete: function(){
                    },
                });
           }
        }); 
    });
    
     $(".btnCrearDemoZip").on("click", function(){
        let data = $(this).data();
        
        let params = {
            id: $("#id").val(),
            template: data.file,
            url: `/home1/digitalmind/dashboard.mie-commerce.com/replicador/tempPageforDownload`,
            download: 1
        };
        
        let openUrl = 'https://dashboard.mie-commerce.com/replicador/replicar.php';
        let queryString = new URLSearchParams(params).toString();
        let finalUrl = `${openUrl}?${queryString}`;
        window.open(finalUrl, '_blank');
    });
    
    $(".btnSendOtherServer").on("click", function(){
        OpenLoad("Creando web...");
        let data = $(this).data();
        console.log(data);
        
        let params = {
            id: $("#id").val(),
            template: data.file,
            url: `/home1/digitalmind/dashboard.mie-commerce.com/replicador/tempPageforDownload`,
            compress: 1
        };
        
        $.ajax({
            url:'https://dashboard.mie-commerce.com/replicador/replicar.php',
            data: params,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response.success==1){
                    
                    OpenLoad("Enviando pagina a otro servidor...");
                    fetch(`controllers/controlador_empresa.php?metodo=replicarWebHostingExterno`,{
                        method: 'POST',
                        body: JSON.stringify({
                            cod_empresa: $("#id").val(),
                            zip: response.zipfile 
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        CloseLoad();
                        if(response.success == 1) {
                            notify(response.mensaje, "success", 2);
                        }
                        else {
                            notify(response.mensaje, "error", 2);
                        }
                    })
                    .catch(error=>{
                        CloseLoad();
                        notify('Ocurrió un error', "error", 2);
                        console.log(error);
                    });
                    
                }
                else{
                    notify(response['mensaje'], "error", 2);
                }
            },
            complete: function(){
                CloseLoad();
            },
        });
    });
    
    
    
    