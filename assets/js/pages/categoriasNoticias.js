$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#cod_categoria_noticia").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });

    $("body").on("click",".btnEditar",function(event){
        event.preventDefault();

        var cod_categoria_noticia = parseInt($(this).attr("data-value"));
        if(cod_categoria_noticia==0){
          alert("No se pudo traer la sucursal, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_categoria_noticia": cod_categoria_noticia
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_categorias_noticias.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#cod_categoria_noticia").val(data['cod_categorias_noticias']);
                    $("#txt_nombre").val(data['nombre']);
                    $("#cmb_categoria").val(data['cod_categoria_padre']);
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
        var cod_categoria_noticia = parseInt($(this).attr("data-value"));
        if(cod_categoria_noticia==0){
          alert("No se pudo Eliminar la noticia, por favor intentelo mas tarde");
          return;
        }
        var element = $(this);
        
        messageConfirm('¿Estas seguro?', 'No se puede revertir los cambios', "warning")
            .then(function(result) {
                if (result) {
                    var parametros = {
                      "cod_categoria_noticia": cod_categoria_noticia,
                      "estado": "D"
                    }
                    $.ajax({
                        beforeSend: function(){
                            OpenLoad("Buscando informacion, por favor espere...");
                         },
                        url: 'controllers/controlador_categorias_noticias.php?metodo=set_estado',
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
          var cod_categoria_noticia = parseInt($("#cod_categoria_noticia").val());
          if(cod_categoria_noticia > 0){
              formData.append('cod_categoria_noticia', cod_categoria_noticia);
              tipo = "U";
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_categorias_noticias.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if(response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    $("#cod_categoria_noticia").val(response['id']);
                     changesTable(tipo, response['id']);
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

    function changesTable(tipo, codigo){
        var data = new Array();
        data[0] = $("#txt_nombre").val().trim();
        data[1] = $("#cmb_categoria option:selected").text();
        data[2] = tableEstado('A');
        data[3] = tableAcciones(codigo);

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

    
    
    

});