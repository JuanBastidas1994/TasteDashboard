<?php
	require_once "funciones.php";
	require_once "clases/cl_ordenes.php";
?>

<!DOCTYPE html>
<html>
<head>
	<?php css_mandatory(); ?>
	<title></title>
</head>
<body>
	<input type="text" name="txt_orden" id="txt_orden">
	<button class="revert">Revertir</button>

	<?php js_mandatory(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 

	<script type="text/javascript">
		$(".revert").on("click", function(e){
			e.preventDefault();
			var cod_orden = $("#txt_orden").val();
			//alert(cod_orden);
			var parametros = {
				"cod_orden": cod_orden
			}

			$.ajax({
                  beforeSend: function(){
                      //OpenLoad("Eliminando imagen, por favor espere...");
                   },
                  url: 'controllers/controlador_gestion_ordenes.php?metodo=revertir_pago',
                  type: 'GET',
                  data: parametros,
                  success: function(response){
                      console.log(response);
                      if( response['success'] == 1)
                      {
                        alert(response['mensaje']);
                      } 
                      else
                      {
                        alert(response['mensaje']);
                      } 
                                               
                  },
                  error: function(data){
                    console.log(data);
                     
                  },
                  complete: function(resp)
                  {
                    //();
                  }
              });
		});
	</script>
</body>
</html>