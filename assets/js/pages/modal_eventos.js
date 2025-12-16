$(document).ready(function() {
    
    $("#btnOpenModal").on("click",function(event){
        $("#cod_sucursal").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });
    
    /*IMAGEN*/
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
         $("#crearModal").modal('hide');
         $("#modalCroppie").css("overflow","scroll");
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) { 
          $('#my-image').attr('src', e.target.result);
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: 512, height: 512 }, //tamaño de la foto que se va a obtener
            boundary: { width: 512, height: 512 }, //la imagen total
            showZoomer: true, // hacer zoom a la foto
            enableResize: false,
            enableOrientation: true // para q funcione girar la imagen 
            
          });
          $('#crop-get').on('click', function() { // boton recortar
            resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 0.8}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');
                $("#crearModal").modal();
                $("#crearModal").css("overflow","scroll");
            });
            /*MINIATURA*/
            resize.result({type: 'base64', size: {width: 256,height: 256}, format : 'jpeg', quality: 0.8}).then(function(dataImg) {
                console.log("IMAGEN MINIATURAAAAA");
                console.log(dataImg);
                $("#txt_crop_min").val(dataImg);
            });
          });
          $('.crop-rotate').on('click', function(ev) {
            resize.rotate(parseInt($(this).data('deg')));
          });

          
        } 
        reader.readAsDataURL(file);
    });
    /*IMAGEN*/

    $("#btnGuardar").on("click", function(){
        let formData = new FormData($("#frmSave")[0]);
        formData.append('txt_crop', $("#txt_crop").val());
        formData.append('cod_modal_evento', $("#cod_modal_evento").val());
        $.ajax({
           url:'controllers/controlador_modal_eventos.php?metodo=crear',
           data: formData,
           type: "POST",
           contentType: false,
           processData: false,
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

    $("body").on("click", ".btnEliminar", function(){
      let cod_modal_evento = $(this).data("value");
      swal({
         title: 'Eliminar',
         text: '¿Está seguro?',
         type: 'warning',
         showCancelButton: true,
         confirmButtonText: 'Aceptar',
         cancelButtonText: 'Cancelar',
         padding: '2em'
      }).then(function(result){
         if (result.value) {
          eliminar(cod_modal_evento);
         }
      }); 
    });

    function eliminar(cod_modal_evento){
      let parametros = {
        "cod_modal_evento": cod_modal_evento,
        "estado": "D"
      }
      $.ajax({
         url:'controllers/controlador_modal_eventos.php?metodo=set_estado',
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
    }

    var f4 = flatpickr(document.getElementById('txt_fecha_ini'), {
      enableTime: true,
      dateFormat: "Y-m-d H:i"
    });

    var f5 = flatpickr(document.getElementById('txt_fecha_fin'), {
      enableTime: true,
      dateFormat: "Y-m-d H:i"
    });
});
