$(document).ready(function() {
    CKEDITOR.replace("editor1");
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
            boundary: { width: 500, height: 500 }, //la imagen total
            showZoomer: true, // hacer zoom a la foto
            enableResize: false,
            enableOrientation: true, // para q funcione girar la imagen 
            mouseWheelZoom: 'ctrl'
          });
          $('#crop-get').on('click', function() { // boton recortar
            resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              console.log(dataImg);
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');
            });
            resize.result({type: 'base64', size: {width: 150,height: 150}, format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              console.log("IMAGEN MINIATURA");
              console.log(dataImg);
              $("#txt_crop_min").val(dataImg);
              /*
              var InsertImgBase64 = dataImg;
              console.log(dataImg);
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');
              */
            });
          });
          $('.crop-rotate').on('click', function(ev) {
            resize.rotate(parseInt($(this).data('deg')));
          });

          
        } 
        reader.readAsDataURL(file);
    });
    
    $("#cmb_empresas").select2();
});

$("#cmb_tipos").on("change", function(e){
    e.preventDefault();
    var cod_tipo_empresa = $(this).val();
    alert(cod_tipo_empresa);
    var parametros = {
        "cod_tipo_empresa": cod_tipo_empresa
    }
    
    $.ajax({
          beforeSend: function(){
              OpenLoad("Buscando datos, por favor espere...");
           },
          url: 'controllers/controlador_updates.php?metodo=tipos',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log(response);
              
              if( response['success'] == 1){
                $("#cmb_empresas").html(response['html']);
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

$("#btnGuardar").on("click", function(e){
    e.preventDefault();
    var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
          var formData = new FormData($("#frmSave")[0]);
          var data = CKEDITOR.instances.editor1.getData();
          formData.append('desc_larga', data);
    
    $.ajax({
          beforeSend: function(){
              OpenLoad("Guardando datos, por favor espere...");
           },
          url: 'controllers/controlador_updates.php?metodo=crear',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response){
              console.log(response);
              
              if( response['success'] == 1){
                messageDone(response['mensaje'],'success');
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