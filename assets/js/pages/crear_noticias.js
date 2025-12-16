$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        
    });

    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
         var isForm = form.valid();

          if(isForm ==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
          
          var formData = new FormData($("#frmSave")[0]);
          var data = CKEDITOR.instances.editor1.getData();
          formData.append('desc_larga', data);
          formData.append('txt_crop', $("#txt_crop").val());
          formData.append('txt_crop_min', $("#txt_crop_min").val());

          var id = parseInt($("#id_noti").val());
          if(id > 0)
              formData.append('cod_noticia', id);

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_noticias.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    $("#id_noti").val(response['id']);
                    $("#titulo").html($("#txt_nombre").val());
                    $(".btnAcciones").show();
                    
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
    CKEDITOR.replace("editor1");

    var selectDropify = "PERFIL";
    //DROPIFY PERFIL
    var resize = null;
    var drEvent = $('#dropifyPerfil').dropify({
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
        'remove':  'X', 
        'replace': 'Sube o Arrastra y suelta'
      },
      error:{
        'imageFormat': 'Solo se adminte imagenes cuadradas.'
      }
    });
    drGaleria.on('dropify.beforeImagePreview', function(event, element){
      if (resize != null)
        resize.destroy();
        
        selectDropify = "GALERIA";

        $("#modalCroppie").modal({
          closeExisting: false,
          backdrop: 'static',
          keyboard: false,
        });
    });

    $('#modalCroppie').on('shown.bs.modal', function() {  
      if(selectDropify == "PERFIL")
          var aux = $(".dropify").get(0);
      else
          var aux = $(".dropify").get(1);
      var file = aux.files[0];
      var reader = new FileReader();
      reader.onload = function (e) { 
        $('#my-image').attr('src', e.target.result);
        
        resize = new Croppie($('#my-image')[0], {
          enableExif: true,
          viewport: { width: 512, height: 288 }, //tamaño de la foto que se va a obtener
          boundary: { width: 600, height: 600 }, //la imagen total
          showZoomer: true, // hacer zoom a la foto
          enableResize: false,
          enableOrientation: true // para q funcione girar la imagen 
        });
        $('#crop-get').on('click', function() { // boton recortar
          resize.result({type: 'base64', size: {width: 1024,height: 576}, format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              console.log("BASE 64");
              console.log(InsertImgBase64);
              if(selectDropify == "PERFIL"){
                  $("#txt_crop").val(InsertImgBase64);
                  var imagen = $(".dropify-render img")[0];
                  $(imagen).attr("src",InsertImgBase64);
              }else{
                  $("#txt_crop_galeria").val(InsertImgBase64);
                  var imagen = $(".dropify-render img")[1];
                  $(imagen).attr("src",InsertImgBase64);
              }
              /*MINIATURA*/
              resize.result({type: 'base64', size: {width: 500,height: 281}, format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
                console.log("IMAGEN MINIATURAAAAA");
                console.log(dataImg);
                $("#txt_crop_min").val(dataImg);
              });
              $("#modalCroppie").modal('hide');
          });
          
          
          
        });
        $('.crop-rotate').on('click', function(ev) {
          resize.rotate(parseInt($(this).data('deg')));
        });

        
      } 
      reader.readAsDataURL(file);
  });


   
    //$("#cmb_productos").select2();
    /*$("#cmb_productos").select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });*/

    $("#btnBack").on("click",function(){
      var link = $(this).attr("data-module-back");
      if (typeof link === "undefined") {
        link = "index.php";
      }
      Swal.fire({
            title: '¿Estas seguro?',
            text: "¡Perderas todos los cambios que no hayas guardado!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Salir',
            cancelButtonText: 'Cancelar',
            padding: '2em'
          }).then(function(result) {
            if (result.value) {
              window.location.href = link;
            }
           });
    }); 

    $("#btnNuevo").on("click",function(){
        Swal.fire({
          title: '¿Estas seguro?',
          text: "¡Perderás todos los cambios que no hayas guardado!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            window.location.href = "crear_noticia.php";
          }
        });
    });

    $("#btnEliminar").on("click",function(){
        //var id = parseInt($("#id").val());
        var id = parseInt($("#id_noti").val());
        if(id <= 0){
            messageDone('Error al eliminar el producto','error');
            return;
        }

        Swal.fire({
          title: '¿Estas seguro?',
          text: "¡Perderás todos los cambios que no hayas guardado!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
              "cod_noticia": id,
              "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_noticias.php?metodo=set_estado',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                      messageDone(response['mensaje'],'success');
                      setTimeout(function(){ 
                        window.location.href="noticias.php"
                      }, 1000);
                      
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
            });//FIN AJAX

          }
        });
    });



     $(".tagging").select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });
    

    $("#cmb_categoria").select2();
});

$("#btnUploadImg").on("click",function(event){
  event.preventDefault();
  var form = $("#frmUploadImg");
  form.validate();
  if(form.valid()==false)
  {
    notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
    return false;
  }

  var formData = new FormData($("#frmUploadImg")[0]);
  var id = parseInt($("#id_noti").val());
  if(id > 0){
      formData.append('cod_noticia', id);
  }

  $.ajax({
      beforeSend: function(){
          OpenLoad("Guardando datos, por favor espere...");
       },
      url: 'controllers/controlador_noticias.php?metodo=upload_img',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response){
          console.log(response);
          
          if( response['success'] == 1)
          {
            messageDone(response['mensaje'],'success');
            $("#frmUploadImg").trigger("reset");
            $(".respGalery").prepend(response['html']);
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

$(".respGalery").on("click",".deleteImg",function(event){
  event.preventDefault();
  var cod_imagen = parseInt($(this).attr("data-value"));
  if(cod_imagen==0){
    alert("No se pudo traer la imagen, por favor intentelo mas tarde");
    return;
  }
  var element = $(this);

  Swal.fire({
    title: '¿Estas seguro?',
    text: "¡No podrás revertir esto!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
    padding: '2em'
  }).then(function(result) {
    if (result.value) {
        
        var parametros = {
          "cod_imagen": cod_imagen,
          "estado": "D"
        }
        $.ajax({
            beforeSend: function(){
                OpenLoad("Eliminando imagen, por favor espere...");
             },
            url: 'controllers/controlador_noticias.php?metodo=delete_img',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {
                  messageDone(response['mensaje'],'success');
                  $(element).parent().remove();
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