$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#txt_password").attr("required","required");
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });

    $("body").on("click", ".btnEditar",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
          return;
        }

        $("#txt_password").removeAttr("required");

        var parametros = {
          "cod_usuario": id
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
                    $("#id").val(data['cod_usuario']);
                    $("#txt_nombre").val(data['nombre']);
                    $("#txt_apellido").val(data['apellido']);
                    $("#txt_telefono").val(data['telefono']);
                    $("#cmbRol").val(data['cod_rol']);
                    $("#fecha_nacimiento").val(data['fecha_nacimiento']);
                    $("#txt_correo").val(data['correo']);
                    $("#cmbSucursal").val(data['cod_sucursal']);

                    $(".dropify-render img").attr("src",data['imagen']);
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

     $("body").on("click", ".btnEliminar", function(event){
        event.preventDefault();
        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el usuario, por favor intentelo mas tarde");
          return;
        }
        
        messageConfirm("¿Estás seguro de eliminar este usuario?", "¡No podrás revertir esto!", "warning")
        .then(function(result) {
            if (result) {
                eliminar(id);
            }
        });
     }); 

     function eliminar(id){
          var parametros = {
            "cod_usuario": id,
            "estado": "D"
          }
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_usuario.php?metodo=set_estado',
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

    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
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

          if($("#cmbRol").val() == 3 || $("#cmbRol").val() == 5){
            if($("#cmbSucursal").val()==0){
              messageDone("Debes escoger una sucursal para un usuario administrador o Personal",'error');
              return;
            }
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_usuario.php?metodo=crear',
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
    });
    
    
    $("#btnGuardarFlota").on("click",function(event){
          event.preventDefault();
          
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
              url: 'controllers/controlador_usuario.php?metodo=crearFlota',
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
    });
    
    $("body").on("click", ".btnEditarFlota",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
          return;
        }

        $("#txt_password").removeAttr("required");

        var parametros = {
          "cod_usuario": id
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
                    $("#id").val(data['cod_usuario']);
                    $("#txt_nombre").val(data['nombre']);
                    $("#txt_apellido").val(data['apellido']);
                    $("#txt_telefono").val(data['telefono']);
                    $("#cmbRol").val(data['cod_rol']);
                    $("#txt_placa").val(data['placa']);
                    $("#txt_correo").val(data['correo']);
                    $("#cmbSucursal").val(data['cod_sucursal']);

                    $(".dropify-render img").attr("src",data['imagen']);
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
    

    $("#cmbRol").on("change",function(){
      var rol = $(this).val();
      if(rol != 3 && rol != 5)
        $(".onlyAdmin").hide();
      else
        $(".onlyAdmin").show();
    })

    /*time picker*/
    var f4 = flatpickr(document.getElementById('fecha_nacimiento'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });

    $('.dropify').dropify({
      messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

});