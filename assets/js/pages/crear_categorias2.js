$(document).ready(function() {
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
          var data = CKEDITOR.instances.editor1.getData();
          formData.append('desc_larga', data);
          formData.append('txt_crop', $("#txt_crop").val());
          formData.append('txt_crop_min', $("#txt_crop_min").val());
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_producto', id);
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_categorias2.php?metodo=crear',
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
                    $(".btnAcciones").show();
                    window.history.pushState(response, "Crear Categorias", "crear_categorias.php?id="+response['alias']);
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

    $("#cmb_categoria").select2ToTree();
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

        //OBTENER FORMATO
        let formato = "jpeg";
        quality = 0.8;
        fondoImg = "#FFFFFF";
        if (file.type === "image/png") {
            formato = "png";
            fondoImg = "";
            quality = 0.7;
        }

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
            resize.result(
              {
                type: 'base64', 
                size: 'viewport', 
                format : formato, 
                quality: quality, 
                backgroundColor: fondoImg
              }
              ).then(function(dataImg) {
                var InsertImgBase64 = dataImg;
                $("#txt_crop").val(InsertImgBase64);
                var imagen = $(".dropify-render img")[0];
                $(imagen).attr("src",InsertImgBase64);
                $("#modalCroppie").modal('hide');
              });

            resize.result(
              {
                type: 'base64', 
                size: {width: 150,height: 150}, 
                format : formato, 
                quality: quality, 
                backgroundColor: fondoImg
              }).then(function(dataImg) {
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

    $("#btnBack").on("click",function(){
        var link = $(this).attr("data-module-back");
        if (typeof link === "undefined") {
          link = "index.php";
        }
        messageConfirm('¿Estas seguro?', '¡Perderas todos los cambios que no hayas guardado!', "warning")
        .then(function(result) {
            if (result) {
                window.location.href = link;
            }
        });
    });

    $("#btnNuevo").on("click",function(){
        messageConfirm('¿Estas seguro?', '¡Perderas todos los cambios que no hayas guardado!', "warning")
        .then(function(result) {
            if (result) {
                window.location.href = "crear_categorias.php";
            }
        });
    });

    $("#btnEliminar").on("click",function(){
        var id = parseInt($("#id").val());
        if(id <= 0){
            messageDone('Error al eliminar la categoria','error');
            return;
        }
        
        messageConfirm('¿Estas seguro de eliminar esta categoria?', '¡No podrás revertir esto!', "warning")
        .then(function(result) {
            if (result) {
                var parametros = {
                  "cod_categoria": id,
                  "estado": "D"
                }
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Buscando informacion, por favor espere...");
                     },
                    url: 'controllers/controlador_categorias2.php?metodo=set_estado',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                          messageDone(response['mensaje'],'success');
                          setTimeout(function(){ 
                            window.location.href="productos.php"
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

    $("#contentCategorias").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#contentCategorias>tr').each(function() {
                selectedData.push($(this).attr("data-codigo"));
            });
            ordenarItems(selectedData);
        }
    });
    
    function ordenarItems(data){
	  var parametros = {
	      "codigos": data,
	    }
	    console.log(parametros);
	   
	  $.ajax({
	      url:'controllers/controlador_categorias2.php?metodo=actualizar',
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
});