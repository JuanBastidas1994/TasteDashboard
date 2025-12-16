$(document).ready(function() {
    $("body").on("click",".btnEliminar",function(event){
        event.preventDefault();
        var cod_categoria = parseInt($(this).attr("data-value"));
        if(cod_categoria==0){
          alert("No se pudo traer la categoria, por favor intentelo mas tarde");
          return;
        }
        var element = $(this);

        swal.fire({
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
              "cod_categoria": cod_categoria,
              "estado": "I"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_categorias.php?metodo=set_estado',
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
     
    $("#btnOpenModal").on("click",function(event){
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });
    
    $("#moveCategorias").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#moveCategorias>tr').each(function() {
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
	      url:'controllers/controlador_categorias.php?metodo=actualizarCategorias',
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