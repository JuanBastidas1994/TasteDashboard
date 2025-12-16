$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#cod_sucursal").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });
    
    $("#btnCrearG").on("click",function(event){
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearGiftcards").modal();
    });
    
    
    $("body").on("click",".btnEditarG",function(event){
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        var cod_giftcard = parseInt($(this).attr("data-value"));
        if(cod_giftcard==0){
          alert("No se pudo traer la giftcard, por favor intentelo mas tarde");
          return;
        }
        var info = ($(this).attr("data-info"));
        $("#id").val(cod_giftcard);
       // $("#crearGiftcards").modal();
        
         var parametros = {
          "cod_giftcard": cod_giftcard
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_giftcard.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#txt_nombre").val(data['nombre']);
                    if(data['estado']=="A")
                    {
                        var chk = true;
                    }
                    else
                    {
                        var chk = false;
                    }
                    $("#chk_estado").attr("checked",chk);
                    $('#cmb_montos').select2('destroy');
                    $("#cmb_montos").html(data['htmlMontos']);
                    $("#cmb_montos").select2({
                      closeOnSelect: false,
                      tags: true,
                      tokenSeparators: [',']
                    });
                    console.log(data['imagen']);
                    $(".dropify-render img").attr("src",data['image']);
                  //  $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
                    $(".gllpUpdateButton").trigger("click");
                    $("#crearGiftcards").modal();
                    
                    
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
    

     $("body").on("click",".btnEliminar",function(event){
        event.preventDefault();
        var cod = parseInt($(this).attr("data-value"));
        if(cod==0){
          alert("No se pudo traer la giftcard, por favor intentelo mas tarde");
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
              "cod_giftcard": cod,
              "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_giftcard.php?metodo=set_estado',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                      messageDone(response['mensaje'],'success');
                      var myTable = $('#style-3').DataTable();
                      var tr = $(element).parents("tr");
                      myTable.row(tr[0]).remove().draw();
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
    
    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
          if(form.valid()==false)
          {
            notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
            return false;
          }
        
          var tipo = "I";
          var formData = new FormData($("#frmSave")[0]);
          formData.append('txt_crop', $("#txt_crop").val());
          var cod_giftcard = parseInt($("#id").val());
          if(cod_giftcard > 0){
              formData.append('cod_giftcard', cod_giftcard);
              tipo = "U";
          }
          
          //DISPONIBILIDAD
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_giftcard.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
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
    });
    
     $("#moveCategorias").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#moveCategorias>tr').each(function() {
                selectedData.push($(this).attr("data-id"));
            });
            ordenarItems(selectedData,"opciones");
        }
    });
    
    
function ordenarItems(data,tipo){
    
  var parametros = {
      "datos": data,
      "tipo":tipo
    }
    console.log(parametros);
  $.ajax({
      url:'controllers/controlador_giftcard.php?metodo=actualizar',
      type:'POST',
      data:parametros,
      success:function(response){
        console.log(response);
        if(response['success']==1){
          notify("Actualizado correctamente", "success", 2);
        }
      },
      error: function(data){
        console.log(data);
      }
  });
}

    function changesTable(tipo, codigo, ruta_imagen){
        var data = new Array();
        data[0] = '<img src="'+ruta_imagen+'" class="profile-img" alt="Imagen">';
        data[1] = $("#txt_nombre").val().trim();
        data[2] = $("#txt_direccion").val().trim();
        data[3] = $("#hora_ini").val().trim();
        data[4] = $("#hora_fin").val().trim();
        data[5] = $("#txt_emisor").val().trim();
        data[6] = tableEstado('A');
        data[7] = tableAcciones(codigo);

        var myTable = $('#style-3').DataTable();
        if(tipo == "I"){  //INSERTAR
            myTable.row.add(data).draw();
        }else{ //EDITAR
            var tr = $('#style-3').find("[data-value='"+codigo+"']");
            //var data = myTable.row(tr[0]).data();
            myTable.row(tr[0]).data(data).draw();
        }
        feather.replace();
    }

    /*time picker*/
    /*
    var f4 = flatpickr(document.getElementById('hora_ini'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    var f5 = flatpickr(document.getElementById('hora_fin'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });*/
    
    /*--NUEVO--*/
    /*time picker*/
    flatpickr('.hora_iniD', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    flatpickr('.hora_finD', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
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
         $("#crearGiftcards").modal('hide');
         $("#modalCroppie").css("overflow","scroll");
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) { 
          $('#my-image').attr('src', e.target.result);
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: 400, height: 200 }, //tamaño de la foto que se va a obtener
            boundary: { width: 400, height: 200 }, //la imagen total
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
                $("#crearGiftcards").modal();
                $("#crearGiftcards").css("overflow","scroll");
            });
          });
          $('.crop-rotate').on('click', function(ev) {
            resize.rotate(parseInt($(this).data('deg')));
          });

          
        } 
        reader.readAsDataURL(file);
    });
    /*IMAGEN*/
    
    
 $(".tagging").select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });
    

    $("#cmb_montos").select2();
    //$("#cmb_productos").select2();
    $("#cmb_montos").select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });
});