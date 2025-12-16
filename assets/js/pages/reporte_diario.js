//$(document).ready(function() {

	$("body").on('click', ".btnReporte",function (event){
		 event.preventDefault();
		var sucursal=$("#cmb_sucursal").val();
		var f_inicio=$("#fecha_inicio").val();
		var f_fin=$("#fecha_fin").val();
		
		if(f_inicio!="")
		{
				var parametros = {
		            "sucursal": sucursal,
		            "f_inicio": f_inicio
		        }

				$.ajax({
		          beforeSend: function(){
		              OpenLoad("Cargando datos, por favor espere...");
		           },
		          url: 'controllers/controlador_reporte_diario.php?metodo=lista_ordenes',
		          type: 'POST',
		          data: parametros,
		          success: function(response){
		              console.log(response);
		            if( response['success'] == 1)
                    {  
		              $('#Content-tabs').css("display","initial");
		              $('#style-3').DataTable().destroy();
		              $("#bloqueTabla").html(response['tabla']);
		              $("#bloque1").html(response['bloque1']);
		              $("#bloque2").html(response['bloque2']);
		              $("#bloque3").html(response['bloque3']);
		              cargarComponentes();
		            }
		            else
		            {
		            	$('#Content-tabs').css("display","none");
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
        else
        {
        	messageDone("Debe completar todos los campos, intentelo nuevamente",'error');
        }
	});

	/*Combos fecha*/
    var f4 = flatpickr(document.getElementById('fecha_inicio'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });
    
    /*Combos sucursales*/
    var ss = $(".basic").select2({
	    tags: true,
        enableTime: false,
        dateFormat: "Y-m-d"
	});

//});