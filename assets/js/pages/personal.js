$(document).ready(function(){
    /*INICIO CROPPIE*/
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
               $("#crearModal").modal();
               $("#crearModal").css("overflow","scroll");
           });
         });
         $('.crop-rotate').on('click', function(ev) {
           resize.rotate(parseInt($(this).data('deg')));
         });

         
       } 
       reader.readAsDataURL(file);
    });
    /*FIN CROPPIE*/

    /*FLAT PICKER*/
    var f4 = flatpickr(document.getElementById('txt_fecha_nac'), {
      enableTime: false,
      dateFormat: "Y-m-d"
  });
});

$("#btnGuardar").on("click", function(){
    var formData = new FormData($("#frmSave")[0]);
    var cod_usuario = $("#cod_usuario").val();
    if(cod_usuario != ""){
        formData.append("cod_usuario", cod_usuario);
    }

    formData.append('txt_crop', $("#txt_crop").val());

    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando datos, por favor espere...");
         },
        url: 'controllers/controlador_personal.php?metodo=crear',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            
            if(response['success'] == 1)
            {
              messageDone(response['mensaje'],'success');
              window.location.href = "?id=" + response['id'];
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

$("body").on("click", ".btnEditar", function(){
    var cod_usuario = $(this).data("value");
    window.location.href = "crear_personal.php?id=" + cod_usuario;
});

$("body").on("click", ".btnEliminar", function(e){
    e.preventDefault();
    var cod_usuario = $(this).data("value");
    swal({
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
            "cod_usuario": cod_usuario,
            "estado": "D"
          }        
          eliminarPesonal(parametros);
        }
      });
});

function eliminarPesonal(parametros){
    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando datos, por favor espere...");
         },
        url: 'controllers/controlador_personal.php?metodo=set_estado',
        type: 'GET',
        data: parametros,
        success: function(response){
            console.log(response);
            
            if(response['success'] == 1)
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