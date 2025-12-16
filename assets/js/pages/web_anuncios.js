$(document).ready(function(){
  $("#cmbModulos").trigger("change");

  $("#lstDisponibles").sortable({
      connectWith: ".connectedSortable"
  });

  //$("#cmbCat").select2();
  $("#cmbCat").select2({
    closeOnSelect: false,
    tags: true,
    tokenSeparators: [',']
  });
});

$("body").on("change","#cmbModulos",function(event){
    var parametros = {
        "cod_modulo": $(this).val()
      }
      
      $.ajax({
          beforeSend: function(){
              OpenLoad("Buscando informacion, por favor espere...");
           },
          url: 'controllers/controlador_web_anuncios.php?metodo=lista',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log(response);
              if( response['success'] == 1)
              {
                  
                $("#widthAnun").val(response['width']);
                $("#heightAnun").val(response['height']);
                $("#lstAgotados").html(response['agotados']);
                feather.replace();

                $("#lstAgotados").sortable({
                    connectWith: ".connectedSortable",
                    update: function (event, ui) {
                        var selectedData = new Array();
                        $('#lstAgotados>tr').each(function() {
                            selectedData.push($(this).attr("data-id"));
                        });
                        ordenarItems(selectedData);
                    }
                });

                $("#cmbCat").html(response['categorias']);
                //$("#cmbCat").select2();
                $("#cmbCat").select2({
                  closeOnSelect: false,
                  tags: true,
                  tokenSeparators: [',']
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

$("body").on("click", ".btnEditarAnuncio",function(event){
    event.preventDefault();
    
    var cod_anuncio_detalle = $(this).data("value");
    var parametros = {
        "cod_anuncio_detalle": cod_anuncio_detalle 
    }
    $.ajax({
      url:'controllers/controlador_web_anuncios.php?metodo=cargar',
      type:'GET',
      data:parametros,
      success:function(response){
        console.log(response);
        if(response['success']==1){
            $("#txt_titulo").val(response['data']['titulo']);
            $("#txt_subtitulo").val(response['data']['subtitulo']);
            $("#txt_descripcion_corta").val(response['data']['descripcion']);
            $("#txt_texto_boton").val(response['data']['text_boton']);
            $(".cmbAccion").val(response['data']['accion_id']);
            showDetalleAccion(response['data']['accion_id']);
            loadDetalleAccion(response['data']['accion_id'], response['data']['url_boton']);
            //$("#nombre_img").val(response['data']['imagen']);
            $(".dropify-render img").attr("src",response['ruta_img']);
            $(".dropify-render img").attr("josue", "hola");
            $("#txt_url_boton").val(response['data']['url_boton']);
            $("#id").val(response['data']['cod_anuncio_detalle']);
            $("#btnGuardar").html("Editar");
            $("#cmbCat").html(response['categorias']);
            $("#cmbCat").select2({
              closeOnSelect: false,
              tags: true,
              tokenSeparators: [',']
            });
        }
          //alert(response['mensaje']);
      },
      error: function(data){
        console.log(data);
      }
    });
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
          //var data = CKEDITOR.instances.editor1.getData();
         /* formData.append('desc_larga', data);*/
          formData.append('txt_crop', $("#txt_crop").val());
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_anuncio_detalle', id);
          }
          formData.append('cod_anuncio_cabecera',$("#cmbModulos").val());

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_web_anuncios.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    //alert(id);
                    messageDone(response['mensaje'],'success');
                    $("#id").val(response['id']);
                    //$("#nombre_img").val(response['img']);
                    if(id > 0){
                        $("#tr"+response['id']).html(response['html']);
                    }
                    else{
                        $("#style-3 tbody").append(response['html']);
                    }
                    $("#btnGuardar").html("Editar");
                    feather.replace();
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
    
    $("#btnLimpiar").on("click", function(){
        $("#frmSave").trigger("reset");
        $("#id").val("");
        $("#btnGuardar").html("Guardar");
        $(".dropify-render img").attr("src", $("#img-ori").val());
    });
    
$("body").on("click", ".btnEliminarAnuncio", function(event){
    event.preventDefault();
    
    var cod_anuncio_detalle = $(this).data("value");
    var parametros = {
        "cod_anuncio_detalle" : cod_anuncio_detalle
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
              
            $.ajax({
              url:'controllers/controlador_web_anuncios.php?metodo=eliminar',
              type:'GET',
              data:parametros,
              success:function(response){
                console.log(response);
                if(response['success']==1){
                    $("#tr"+response['id']).remove();
                    messageDone(response['mensaje'],'success');
                }
                else
                  messageDone(response['mensaje'],'error');
              },
              error: function(data){
                console.log(data); 
              }
            });
          }
        });
});

function ordenarItems(data){
  console.log(data);
  var parametros = {
      "cod_modulo": $("#cmbModulos").val(),
      "productos": data
    }
  $.ajax({
      url:'controllers/controlador_web_anuncios.php?metodo=actualizar',
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

$("body").on("click",".productoAgotar",function(event){
        event.preventDefault();
        var element = $(this);
        var minutos = element.attr("data-minutes");
        var cod_producto = element.attr("data-producto");
        var cod_sucursal = $("#cmbSucursal").val();

        Swal.fire({
          title: '¿Estas seguro?',
          text: "El producto no estara disponible para la venta durante "+minutos+" minutos",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
              "cod_producto": cod_producto,
              "minutos": minutos,
              "cod_sucursal": cod_sucursal
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_disponibilidad_productos.php?metodo=setAgotado',
                type: 'POST',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                      messageDone(response['mensaje'],'success');
                      $("#cmbSucursal").trigger("change");
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
     
 // CKEDITOR.replace("editor1");
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
          
          var widthA = parseInt($("#widthAnun").val());
          var heightA = parseInt($("#heightAnun").val());
          
          //var widthA2 = widthA + parseInt(100);
          //var heightA2 = heightA + parseInt(100);
          var widthA2 = widthA;
          var heightA2 = heightA;
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: widthA, height: heightA }, //tama���o de la foto que se va a obtener
            boundary: { width: widthA2, height: heightA2 }, //la imagen total
            showZoomer: true, // hacer zoom a la foto
            enableResize: false,
            enableOrientation: true, // para q funcione girar la imagen 
            mouseWheelZoom: 'ctrl'
          });
          $('#crop-get').on('click', function() { // boton recortar
            resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 1, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              console.log(dataImg);
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');
            });
            resize.result({type: 'base64', size: {width: 150,height: 150}, format : 'jpeg', quality: 1, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
              console.log("IMAGEN MINIATURA");
              console.log(dataImg);
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

//ACCION 
$(".cmbAccion").on("change", function(){
        showDetalleAccion($(this).val());
});

function showDetalleAccion(accion){
    $(".inputAccion").hide();
    if(accion == "URL" || accion == "FILTER"){
        $("#txt_accion_desc").show();
    }else if(accion == "PRODUCTO"){
        $("#cmbProductos").show();
    }else if(accion == "NOTICIA"){
        $("#cmbNoticias").show();
    }
}

function loadDetalleAccion(accion, val){
    if(accion == "URL" || accion == "FILTER"){
        $("#txt_accion_desc").val(val);
    }else if(accion == "PRODUCTO"){
        $("#cmbProductos").val(val);
    }else if(accion == "NOTICIA"){
        $("#cmbNoticias").val(val);
    }
}
