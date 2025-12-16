$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#cod_sucursal").val(0);
        $("#frmSave").trigger("reset");
        $(".dropify-render img").attr("src",'assets/img/200x200.jpg');
        $("#crearModal").modal();
    });
    
    $("body").on("change","#cmb_sucursales",function(event){
        event.preventDefault();
         
        var cod_sucursal = $(this).val();
        if (cod_sucursal == 0){
            $(".infoTabla").html("");
            return;
        }
        var parametros = {
          "cod_sucursal": cod_sucursal
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_stock.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var table = $('#style-3').DataTable();
                    table.destroy();
                    $(".infoTabla").html(response['html']);
                    cargarTable();
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

    $("body").on("click",".btnEditar",function(event){
        event.preventDefault();

        var cod_sucursal = parseInt($(this).attr("data-value"));
        if(cod_sucursal==0){
          alert("No se pudo traer la sucursal, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_sucursal": cod_sucursal
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_sucursal.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#cod_sucursal").val(data['cod_sucursal']);
                    $("#txt_nombre").val(data['nombre']);
                    $("#txt_emisor").val(data['emisor']);
                    $("#txt_cobertura").val(data['distancia_km']);
                    $("#timeFlatpickr").val(data['hora_ini']);
                    $("#timeFlatpickr2").val(data['hora_fin']);
                    $("#txt_direccion").val(data['direccion']);
                    $("#txt_latitud").val(data['latitud']);
                    $("#txt_longitud").val(data['longitud']);
                   
                    console.log(data['image']);
                    $(".dropify-render img").attr("src",data['image']);
                    $(".gllpUpdateButton").trigger("click");
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

     $("body").on("click",".btnEliminar",function(event){
        event.preventDefault();
        var cod_sucursal = parseInt($(this).attr("data-value"));
        if(cod_sucursal==0){
          alert("No se pudo traer la sucursal, por favor intentelo mas tarde");
          return;
        }
        var element = $(this);

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
              "cod_sucursal": cod_sucursal,
              "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_sucursal.php?metodo=set_estado',
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
          
          var cod_sucursal = $("#cmb_sucursales").val();
          var form = $("#frmStock").val();
          
          if(cod_sucursal == 0){
              messageDone("Por favor escoja una sucursal",'error');
              return;
          }
          
          var formData = new FormData($("#frmStock")[0]);

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_stock.php?metodo=crear',
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
    var f4 = flatpickr(document.getElementById('hora_ini'), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i"
    });

    var f5 = flatpickr(document.getElementById('hora_fin'), {
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
         $("#crearModal").modal('hide');
         $("#modalCroppie").css("overflow","scroll");
        var aux = $(".dropify").get(0);
        var file = aux.files[0];
        var reader = new FileReader();
        reader.onload = function (e) { 
          $('#my-image').attr('src', e.target.result);
          resize = new Croppie($('#my-image')[0], {
            viewport: { width: 600, height: 200 }, //tamaño de la foto que se va a obtener
            boundary: { width: 600, height: 200 }, //la imagen total
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
    /*IMAGEN*/
    
    

});