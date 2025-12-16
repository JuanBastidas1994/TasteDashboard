$("body").on("click", ".btnVaciarCard", function(){
	let btn = $(this);
	let cod_tarjeta = btn.data("tarjeta");
	Swal.fire({
	   title: 'Vaciar tarjeta',
	   text: '¿Desea continuar?',
	   icon: 'warning',
	   showCancelButton: true,
	   confirmButtonText: 'Aceptar',
	   cancelButtonText: 'Cancelar',
	   padding: '2em'
	}).then(function(result){
	   if (result.value) {
		  vaciarTarjeta(cod_tarjeta, btn);
	   }
	}); 
});

function vaciarTarjeta(cod_tarjeta, btn) {
	let parametros = {
		"cod_tarjeta": cod_tarjeta
	}
	$.ajax({
		url:'controllers/controlador_importar_cards_fidelizacion.php?metodo=vaciarTarjeta',
		data: parametros,
		type: "GET",
		success: function(response){
			console.log(response);
			if(response['success']==1){
				let nombreCliente = btn.parents('tr').find('.campoNombre');
				nombreCliente.html("Sin aginar");
				nombreCliente.removeClass("text-success");
				nombreCliente.addClass("text-info");
				btn.parents('ul').html('');
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

Handlebars.registerHelper('eq', function(arg1, arg2, options) {
    return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
});
Handlebars.registerHelper('decimal', function(number) {
  return parseFloat(number).toFixed(2);
});

var templateItemTabla = Handlebars.compile($("#itemTabla").html()); //DEBEN SER CREADOS CON LA ETIQUETA SCRIPT
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
        	      url:'controllers/controlador_importar_cards_fidelizacion.php?metodo=importar',
        	      type:'POST',
        	      data:formData,
        	      contentType: false,
                  processData: false,
        	      success:function(response){
        	        console.log(response);
        	        if(response['success']==1){
        	            $("#datos").html(templateItemTabla(response['data']));
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