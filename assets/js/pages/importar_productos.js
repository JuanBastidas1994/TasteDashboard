$(document).ready(function() {
    $("#btn_importar").on("click", function(){
        
        let excel = document.getElementById("excel");
        
        if( excel.files.length == 0){
            messageDone("Asegúrese de escoger el archivo",'error');
            return;
        }
        
        if(confirm("Se importaran los datos ¿Desea continuar?")){
            var formData = new FormData($("#frmImportar")[0]);
        	  $.ajax({
        	      url:'controllers/controlador_importar_productos.php?metodo=importar',
        	      type:'POST',
        	      data:formData,
        	      contentType: false,
                  processData: false,
        	      success:function(response){
        	        console.log(response);
        	        if(response['success']==1){
        	            $("#datos").html(response['html']); 
        	            $("#divDatos").show();
        	          messageDone(response['mensaje'],'success');
        	        }
        	        else{
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