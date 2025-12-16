$(document).ready(function(){

  $("#cmbTipo").trigger("change");
  
});


$("body").on("change","#cmbTipo",function(event){
    var tipo=$(this).val();
    SelectView(tipo);
    LoadList(event);
});

function LoadList(event)
{
    event.preventDefault();
    var idEmpresa = $("#idEmpresa").val();
    var plataforma = $("#cmbPlataforma").val();
    var parametros = {
        "idEmpresa" : idEmpresa,
        "plataforma": plataforma
    }
    
    
    
    $.ajax({
          beforeSend: function(){
              OpenLoad("Buscando informacion, por favor espere...");
           },
          url: 'controllers/controlador_web_esquema.php?metodo=lista',
          type: 'GET',
          data:parametros,
          success: function(response){
              console.log("Lista"+response);
              if( response['success'] == 1)
              {
                $("#lstEsquema").html(response['esquema']);
              $("#lstEsquema").sortable({
                    connectWith: ".connectedSortable",
                    update: function (event, ui) {
                        var selectedData = new Array();
                        $('#lstEsquema>tr').each(function() {
                            selectedData.push($(this).attr("data-id"));
                        });
                      ordenarItems(selectedData);
                    }
                });
                
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
}

function SelectView(tipo)
{
    if (tipo =="ordenar")
   {
       $("#cmbModulos").css("display","");
       $("#cmbAnuncios").css("display","none");
   }
   else
   {
       $("#cmbModulos").css("display","none");
       $("#cmbAnuncios").css("display","");
   }
}

$("#btnBack").on("click",function(event){
    window.location.href = "empresas.php";
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
          formData.append('textForma', $("#cmbForma option:selected").text());
          formData.append('textModulos', $("#cmbModulos option:selected").text());
          formData.append('textAnuncios', $("#cmbAnuncios option:selected").text());
          formData.append('idEmpresa',$("#idEmpresa").val());
          formData.append('plataforma',$("#cmbPlataforma").val());

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_web_esquema.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                        $("#style-3").append(response['html']);
                    $("#frmSave").trigger("reset");
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
    
});
    
$("body").on("click", ".btnEliminarEsquema", function(event){
    event.preventDefault();
    
    var cod_esquema = $(this).data("value");
    var parametros = {
        "cod_esquema" : cod_esquema
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
              url:'controllers/controlador_web_esquema.php?metodo=eliminar',
              type:'GET',
              data:parametros,
              success:function(response){
                console.log(response);
                if(response['success']==1){
                    $("#tr"+response['id']).remove();
                    messageDone(response['mensaje'],'success');
                }
                else
                {
                  messageDone(response['mensaje'],'error');
                }
              },
              error: function(data){
                console.log(data); 
              }
            });
          }
        });
});

$("body").on("click", ".btnEditarEsquema", function(event){
  event.preventDefault();
  
  var cod_esquema = $(this).data("value");
  var parametros = {
      "cod_esquema" : cod_esquema
  }

  $.ajax({
    url:'controllers/controlador_web_esquema.php?metodo=get',
    type:'GET',
    data:parametros,
    beforeSend: function(){
      OpenLoad("Buscando informacion, por favor espere...");
   },
    success:function(response){
      console.log(response);
      if(response['success']==1){
          let data = response['data'];
          $("#uid").val(data.cod_web_esquema);
          $("#utxt_titulo").val(data.titulo);
          $("#ucmbForma").val(data.forma);
          $("#ucmbNumColumnas").val(data.num_columnas);
          $("#editarEsquemaModal").modal();
      }
      else{
        messageDone(response['mensaje'],'error');
      }
    },
    error: function(data){
      console.log(data); 
    },
    complete: function(){
      CloseLoad();
    }
  });
});

$("body").on("click", "#btnActualizarEsquema", function(event){
  event.preventDefault();
  
  var parametros = {
      "cod_esquema" : $("#uid").val(),
      "titulo": $("#utxt_titulo").val(),
      "forma": $("#ucmbForma").val(),
      "columnas": $("#ucmbNumColumnas").val()
  }
  
  Swal.fire({
        title: '¿Estas seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
      }).then(function(result) {
        if (result.value) {
            
          $.ajax({
            url:'controllers/controlador_web_esquema.php?metodo=editar',
            type:'GET',
            data:parametros,
            success:function(response){
              console.log(response);
              if(response['success']==1){
                $("#editarEsquemaModal").modal('hide');
                  messageDone(response['mensaje'],'success');
              }
              else
              {
                messageDone(response['mensaje'],'error');
              }
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
      "esquemas": data
    }
  $.ajax({
      url:'controllers/controlador_web_esquema.php?metodo=actualizar',
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
          
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: 400, height: 400 }, //tama���o de la foto que se va a obtener
            boundary: { width: 500, height: 500 }, //la imagen total
            showZoomer: true, // hacer zoom a la foto
            enableResize: false,
            enableOrientation: true, // para q funcione girar la imagen 
            mouseWheelZoom: 'ctrl'
          });
          $('#crop-get').on('click', function() { // boton recortar
            resize.result({type: 'base64', size: 'viewport', format : 'png', quality: 1}).then(function(dataImg) {
              var InsertImgBase64 = dataImg;
              console.log(dataImg);
              $("#txt_crop").val(InsertImgBase64);
              var imagen = $(".dropify-render img")[0];
              $(imagen).attr("src",InsertImgBase64);
              $("#modalCroppie").modal('hide');
            });
            resize.result({type: 'base64', size: {width: 150,height: 150}, format : 'png', quality: 1}).then(function(dataImg) {
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

    $("body").on("change", "#cmbPlataforma", function(e){
      var plataforma = $(this).val();
      LoadList(e);
      if(plataforma == "APP")
        $("#optionBanner").show();
      else
        $("#optionBanner").hide();
    });

    $("#cmbForma").on("change", function(){
      var tipoForma = $(this).val();
      if(tipoForma == "slide_4")
        $("#bloqueNumColumnas").show();
      else
        $("#bloqueNumColumnas").hide();
    });