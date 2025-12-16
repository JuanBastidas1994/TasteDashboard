$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });

    $(".btnEditar").on("click",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_banner": id
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_banner.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#id").val(data['cod_banner']);
                    $("#txt_titulo").val(data['titulo']);
                    $("#txt_subtitulo").val(data['subtitulo']);
                    $("#txt_descuento").val(data['descuento']);
                    $("#txt_text_boton").val(data['text_boton']);
                    $("#txt_url").val(data['url_boton']);

                    console.log(data['image_min']);
                    $(".dropify-render img").attr("src",data['image_min']);
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
        var cod_usuario = parseInt($(this).attr("data-usuario"));
        var cod_usuario_telegram = parseInt($(this).attr("data-telegram"));

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
            
            var parametros = {
              "cod_usuario": cod_usuario,
              "cod_usuario_telegram": cod_usuario_telegram
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_telegram.php?metodo=set_estado',
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
     }); 

    $(".btnAsignar").on("click",function(event){
          event.preventDefault();
          
          var parametros = {
            "cod_usuario": $("#cmb_mis_usuarios").val(),
            "cod_usuario_telegram": $("#cmb_usuarios_telegram").val()
          }

          console.log(parametros);

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_telegram.php?metodo=asignar',
              type: 'GET',
              data: parametros,
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

    $(".chkGrupos").on("change",function(){
        var parametros = {
          "cod_chat": $(this).val(),
          "activo": $(this).is(":checked"),
        }

        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando Informacion");
             },
            url:'controllers/controlador_telegram.php?metodo=set_estado_chat',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    messageDone(response['mensaje'],'success');
                } 
                else
                {
                    $(this).attr("checked", $(this).is(":checked"));
                    messageDone(response['mensaje'],'success');
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

    $('.dropify').dropify({
      messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

});