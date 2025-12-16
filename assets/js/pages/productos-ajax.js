  $("body").on("click",".btnEliminar",function(event){
        event.preventDefault();
        var cod_producto = parseInt($(this).attr("data-value"));
        if(cod_producto==0){
          alert("No se pudo traer el producto, por favor intentelo mas tarde");
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
              "cod_producto": cod_producto,
              "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_productos.php?metodo=set_estado',
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
     
     
$(document).ready(function(){
    
    var table = $('#table-products').dataTable( {
                processing: true,
                serverSide: true,
                "language": {
                    "emptyTable": "No se encuentran resultados para "
                },

                ajax: {
                    url:'ajax-productos.php',
                    type:'GET',

                    /*success: function(response){
                      console.log(response);  
                    },*/
                    error: function(e){
                       console.log(e);
                    }
                }


            } );
})