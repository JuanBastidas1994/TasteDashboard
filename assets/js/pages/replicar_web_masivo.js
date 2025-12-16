$(document).ready(function() {
    
    function getBusiness(){
        // Crear un arreglo vacío para almacenar los objetos
        const datos = [];
        
        // Seleccionar y recorrer los checkboxes marcados con la clase 'chk_replicar'
        $('.chk_replicar:checked').each(function() {
          // Crear un objeto con los valores de los atributos data-id, data-folder y data-name
          const item = {
            id: $(this).data('id'),
            folder: $(this).data('folder'),
            alias: $(this).data('alias')
          };
        
          // Agregar el objeto al arreglo
          datos.push(item);
        });
        
        console.log(datos);
        return datos;
    }
    

    $(".btnReplicar").on("click", function(){
        
        Swal.fire({
           title: 'Los cambios podrían ser irreversibles',
           text: '¿Continuar?',
           icon: 'warning',
           showCancelButton: true,
           confirmButtonText: 'Aceptar',
           cancelButtonText: 'Cancelar',
           padding: '2em'
        }).then(function(result){
           if (result.value) {
                let datos = getBusiness();
                console.log(datos);
                replicarSecuencialmente(datos);
           }
        }); 
    });
    
    // Función principal para llamar a replicarWeb de manera secuencial
    async function replicarSecuencialmente(datos) {
      for (const dato of datos) {
        try {
          await replicarWeb(dato.id, dato.folder); // Espera a que termine la llamada antes de continuar
          console.log(`Replicación completada para ID: ${dato.id}`);
        } catch (error) {
          console.error(`Error replicando ID: ${dato.id}`, error);
        }
      }
    }

    function replicarWeb(id, folder) {
      return new Promise((resolve, reject) => {
        let url = "/home1/digitalmind/" + folder;
        let template = $("#cmbVersion").val();
    
        $.ajax({
          url: 'https://dashboard.mie-commerce.com/replicador/replicar.php',
          data: { id, url, template },
          type: "GET",
          success: function(response) {
            console.log(response);
            if (response['success'] == 1) {
              $("#pdetalle").html(response['detalle']);
              notify(response['mensaje'], "success", 2);
            } else {
              notify(response['mensaje'], "error", 2);
            }
            resolve(); // Llamar resolve cuando termina exitosamente
          },
          error: function(data) {
            reject(data); // Llamar reject si ocurre un error
          }
        });
      });
    }

    function actualizarFolder(cod_empresa, folder) {
        let parametros = {
            "cod_empresa": cod_empresa,
            "folder": folder
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=updateFolder',
           data: parametros,
           type: "GET",
           success: function(response){
              console.log(response);
              if(response['success']==1){
                notify(response['mensaje'], "success", 2);
              }
              else{
                notify(response['mensaje'], "error", 2);
              }
           },
           error: function(data){
           },
           complete: function(){
           },
        });
    }

});
