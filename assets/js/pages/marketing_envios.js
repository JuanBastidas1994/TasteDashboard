$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });

    $(".btnEditar").on("click",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer informacion de la promocion, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_promocion": id
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#id").val(data['cod_producto_descuento']);
                    $("#hora_ini").val(data['fecha_inicio']);
                    $("#hora_fin").val(data['fecha_fin']);
                    $("#txt_valor").val(data['valor']);
                    $("#cmb_productos").val(data['cod_producto']);
                    $('#cmb_productos').trigger('change');
                    $("#cmb_sucursales").val(data['cod_sucursal']);
                    $('#cmb_sucursales').trigger('change');
                    //$("#cmbRol").val(data['cod_rol']);
                    //$("#fecha_nacimiento").val(data['fecha_nacimiento']);
                    //$("#txt_correo").val(data['correo']);

                    $("#crearModal").modal();
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

     $(".btnEliminar").on("click",function(event){
        event.preventDefault();
        var btn = $(this);
        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer la promocion, por favor intentelo mas tarde");
          return;
        }

        Swal.fire({
          title: '¿Estas seguro?',
          text: "¡No podrás revertir esto!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            eliminar(id, btn);
          }
        });
     }); 

     function eliminar(id, btn){
          var parametros = {
            "cod_promocion": id,
            "estado": "D"
          }
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=eliminar',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                      btn.parents('tr').remove();
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
    
    function guardarPromo(eliminar){
        event.preventDefault();

          let days = [];
          if($("#ckHours").is(":checked")) {
            $(".ckDays").each(function() {
              let ckDay = $(this);
              if(ckDay.is(":checked"))
                days.push(ckDay.val());
            });

            if(days.length == 0) {
              messageDone("Escoja días para la promoción",'error');
              return;
            }
          }
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
    
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_usuario', id);
          }
            formData.append('eliminar', eliminar);
            formData.append('dias', days);
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=crear',
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

    $("body").on("change", ".ckHours", function(){
      if($(this).is(":checked"))
        $(".lstDays").show();
      else
        $(".lstDays").hide();
    });
    
    $("#btnGuardar").on("click",function(event){
        /*event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
    
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_usuario', id);
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=crear',
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
          });*/
        
          event.preventDefault();

          let days = [];
          if($("#ckHours").is(":checked")) {
            $(".ckDays").each(function() {
              let ckDay = $(this);
              if(ckDay.is(":checked"))
                days.push(ckDay.val());
            });

            if(days.length == 0) {
              messageDone("Escoja días para la promoción",'error');
              return;
            }
          }
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
    
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_usuario', id);
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=consultarPromoExistente',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var eliminar = false;
                    $("#id").val(response['id']);
                    if(confirm("Ya hay una promo existente ¿Desea reemplazarla?")){
                        eliminar = true;
                        guardarPromo(eliminar);
                    }
                    else
                        return;
                  } 
                  else
                  {
                      var eliminar = false;
                      guardarPromo(eliminar);
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
    
    $("#cmb_sucursales").select2();
    
    /*time picker*/
    $('#crearModal').removeAttr('tabindex');
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

    $('.dropify').dropify({
      messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

});