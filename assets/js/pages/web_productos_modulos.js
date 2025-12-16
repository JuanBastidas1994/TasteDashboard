$(document).ready(function(){
  $("#cmbModulos").trigger("change");

  $("#lstDisponibles").sortable({
      connectWith: ".connectedSortable"
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
          url: 'controllers/controlador_web_productos_modulos.php?metodo=lista',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log(response);
              if( response['success'] == 1)
              {
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

function ordenarItems(data){
  console.log(data);
  var parametros = {
      "cod_modulo": $("#cmbModulos").val(),
      "productos": data
    }
  $.ajax({
      url:'controllers/controlador_web_productos_modulos.php?metodo=actualizar',
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

