$(document).ready(function() {

	$("#contentLista").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#contentLista>tr').each(function() {
                selectedData.push($(this).attr("data-name"));
            });
            ordenarItems(selectedData);
        }
    });
    
    $(".divTable").sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            var selectedData = new Array();
            $('#contentLista>tr').each(function() {
                selectedData.push($(this).attr("data-name"));
            });
            ordenarItems(selectedData);
        }
    });
    
    
    //NUEVO CODIGO
    
    $(".precioCheck").on("change",function(event){
        
        var padre = $(this).parents(".itemAdd");
        inpPrecio = padre.find(".txt_precio");
        if($(this).prop("checked"))
            inpPrecio.removeAttr("readonly");
        else
        {
            inpPrecio.attr("readonly", "readonly");
            inpPrecio.val(0);
        }
            
    });
    
    
    $(".btnAddItem").on("click", function(event){
        var padre = $(this).parents(".itemAdd");
        var nombre = padre.find(".txt_nom_item").val();
        var precio = padre.find(".txt_precio").val();
        var check = padre.find(".precioCheck").prop("checked");
        if(check)
        {
            var icono = "check-square";
            var num = 1;
        }
            
        else
        {
            var icono = "square";
            var num = 0;
        }
            
        
        var nuevaLinea = "<div class=\"col-md-12 col-sm-12 col-xs-12 col-12 divItem\" style\"margin-botom: 20px;\"> " +
                                "<div>" +
                                    "<div class=\"col-md-4 col-sm-4 col-xs-4 col-4\"> " +
                                        "<input class=\"form-control\" name=\"txt_nomItemDet[]\" value=\"" + nombre + "\" readonly>" +
                                    "</div> " +
                                    "<div align=\"center\" class=\"col-md-2 col-sm-2 col-xs-2 col-2\"> " +
                                        "<a href=\"javascript:void(0)\"><i data-feather=\"" + icono + "\"></i></a>" +
                                        "<input type=\"hidden\" class=\"form-control\" name=\"txt_check[]\" value=\"" + num + "\" readonly>" +
                                    "</div> " +
                                    "<div class=\"col-md-3 col-sm-3 col-xs-3 col-3\"> " +
                                        "<input class=\"form-control\" name=\"txt_precioItemDet[]\" value=\"" + parseFloat(precio).toFixed(2) + "\" readonly style=\"text-align: right;\">" + 
                                    "</div> " +
                                    "<div align=\"center\" class=\"col-md-3 col-sm-3 col-xs-3 col-3\"> " +
                                        "<a href=\"javascript:void(0)\" class=\"btnDelItem\"><i data-feather=\"trash-2\"></i></a>" +
                                    "</div> " +
                                "</div> " +
                         "</div> ";
        
        var divItems = $(this).parents(".itemAdd").next().prepend(nuevaLinea);
        feather.replace();
    });
    
    
    $("body").on("click", ".btnDelItem", function(event){
        $(this).parents(".divItem").remove();
    });
    
    $(".btnGuardaOpc").on("click", function(event){
        event.preventDefault();
        
        var formData = new FormData($("#frmOpciones")[0]);
        
        $.ajax({
		          beforeSend: function(){
		              OpenLoad("Guardando datos, por favor espere...");
		           },
		          url: 'controllers/controlador_personalizar_productos.php?metodo=guardar_opciones',
		          type: 'POST',
		          data: formData,
		          contentType: false,
                  processData: false,
		          success: function(response){
		              console.log(response);
		              
		              if( response['success'] == 1)
		              {
		                $("#idOpcion").val(response['id']);
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
    
    $(".btnGuardarDet").on("click", function(event){
        
        var formData = new FormData($("#frmOpciones")[0]);
        
        $.ajax({
		          beforeSend: function(){
		              OpenLoad("Guardando datos, por favor espere...");
		           },
		          url: 'controllers/controlador_personalizar_productos.php?metodo=guardar_items',
		          type: 'POST',
		          data: formData,
		          contentType: false,
                  processData: false,
		          success: function(response){
		              console.log(response);
		              
		              if( response['success'] == 1)
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
    //FIN NUEVO CODIGO
    
	$(".selectCheck").on("change",function(event){
	    
		var check=$(this);
		$codigo=$(this).attr("data-codigo");
		var nombre_categoria=$(this).attr("data-name");;
		var cod_producto=$("#nombreProducto").val();
		var cantidad=$("#cantidad"+$codigo).val();
		var codigo_padre=$("#cod_extra"+$codigo).val();
		
		var verificar=$(this).is(":checked");
		
		var parametros = {
            "nombre_categoria": nombre_categoria,
            "cod_producto":  cod_producto,
            "cantidad":  cantidad,
            "codigo_padre":  codigo_padre
        }
       
        if(cantidad<=0)
        {
            check.prop('checked', false);
	    	messageDone("Cantidad no valida, ingrese una nueva cantidad ",'error');
	    }
	    else
	    {
	    	if(verificar==true)
        	{
		        $.ajax({
		          beforeSend: function(){
		              OpenLoad("Guardando datos, por favor espere...");
		           },
		          url: 'controllers/controlador_personalizar_productos.php?metodo=guardar_extra',
		          type: 'POST',
		          data: parametros,
		          success: function(response){
		              console.log(response);
		              
		              if( response['success'] == 1)
		              {
		                $("#tab-"+$codigo).attr("data-toggle","tab");
						$("#tab-"+$codigo).css("color","blue");
						$("#tab-"+$codigo).css("font-weight","900");
						$(".tab-pane").removeClass("active show");
						
						if($codigo!=0){$("#panel-"+$codigo).addClass("active show");}
						
						$("#tab-"+$codigo).attr("codigo_padre",response['id']);
						$("#cod_extra"+$codigo).val(response['id']);
						$(".checkExtra").attr("codigo_padre",response['id']);
						$("#contentLista").append('<tr data-name="'+nombre_categoria+'"><td>'+nombre_categoria+'</td><td class="text-center"><input type="number" name="cantidad" id="lista-'+codigo_padre+'" disabled class="form-control" required="required" autocomplete="off" value="'+cantidad+'"></td</tr>');
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
	        else
	        {
	           
	            console.log(parametros);
	        	Swal.fire({
		          title: '¿Estas seguro de eliminar la categoria y todos sus productos seleccionados?',
		          text: "¡No podrás revertir esto!",
		          icon: 'warning',
		          showCancelButton: true,
		          confirmButtonText: 'Eliminar',
		          cancelButtonText: 'Cancelar',
		          padding: '2em'
		        }).then(function(result) {
		          if (result.value) {
		        	$.ajax({
			          beforeSend: function(){
			              OpenLoad("Guardando datos, por favor espere...");
			           },
			          url: 'controllers/controlador_personalizar_productos.php?metodo=eliminar_extra',
			          type: 'POST',
			          data: parametros,
			          success: function(response){
			              console.log(response);
			              
			              if( response['success'] == 1)
			              {
			              	$("#cantidad"+$codigo).val("");
			              	$("#cod_extra"+$codigo).val(0);
			              	$("#tab-"+$codigo).attr("data-toggle","");
			              	$("#tab-"+$codigo).attr("codigo_padre",0);
			              	$("#tab-"+$codigo).css("color","#515365");
			              	$("#tab-"+$codigo).css("font-weight","200");
			              	$("#panel-"+$codigo).removeClass("active show");
			              	$("#"+nombre_categoria).css("display","none");

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
		        else{
		            //$("#chk_estado"+$codigo).attr("checked");
		            check.prop('checked', true);
		            //check.trigger("change");
		        }

               });
	        }

	    }
	});

	$(".nav-link").on("click",function(event){
		var activo=$(this).attr("codigo_padre");
		var codigo=$(this).attr("data-id");
		
		if(activo!=0){ 
			$("#tab-"+codigo).attr("data-toggle","tab");
			$("#tab-"+codigo).css("color","blue");
			$("#tab-"+codigo).css("font-weight","900");
			$(".tab-pane").removeClass("active show");
			$("#panel-"+codigo).addClass("active show");

			refrescar(codigo,activo,event);
		}
		else
		{messageDone("Debe Activar la categoria para poder seleccionar sus productos",'error');}
    });
    
    $("body").on('click', ".cantChange",function (event){
        var cate=$(this).attr("cod_cate");
        var codigo=$("#cod_extra"+cate).val();
        var cantidad=$(this).val();
       
        var parametros = {
            "codigo": codigo,
            "cantidad": cantidad
        }
        
        if(cantidad<=0)
        {
            notify("Cantidad no valida, ingrese una nueva cantidad ", "success", 2);
        }
        else
        {
           
            if(codigo!=0)
            {
                $.ajax({
    	          beforeSend: function(){
    	              OpenLoad("Editando cantidad, por favor espere...");
    	           },
    	          url: 'controllers/controlador_personalizar_productos.php?metodo=update_cantidad',
    	          type: 'POST',
    	          data: parametros,
    	          success: function(response){
    	              console.log(response);
    	              
    	              if( response['success'] == 1)
    	              {
    	                  notify(response['mensaje'], "success", 2);
    	                  $("#lista-"+codigo).val(cantidad);
    	              }
    	              else
    	              {
    	                   notify(response['mensaje'], "error", 2);
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
        }
        
           
        
    });
    
    $("body").on('click', ".btnEliminar",function (event){
      var codigo=$(this).attr("codigo");
      var categoria=$(this).attr("categoria");
      var codigo_padre=$(this).attr("codigo_padre");
    	
    	swal({
          title: '¿Estas seguro de eliminar todos los productos seleccionados?',
          text: "¡No podrás revertir esto!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {

          	var parametros = {
            "codigo": codigo
            }

          	$.ajax({
	          beforeSend: function(){
	              OpenLoad("Guardando datos, por favor espere...");
	           },
	          url: 'controllers/controlador_personalizar_productos.php?metodo=eliminar_todos',
	          type: 'POST',
	          data: parametros,
	          success: function(response){
	              console.log(response);
	              
	              if( response['success'] == 1)
	              {
	              	notify(response['mensaje'], "success", 2);
	                refrescar(categoria,codigo_padre,event);
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

    $("body").on('click', ".btnEliminarProducto",function (event){
    	 var codigo=$(this).attr("codigo");
    	 var categoria=$(this).attr("categoria");
    	 var codigo_padre=$(this).attr("codigo_padre");
    
    	swal({
          title: '¿Estas seguro de eliminar este producto?',
          text: "¡No podrás revertir esto!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
	        if (result.value) {
		        var parametros = {
	            "codigo": codigo
	            }

	        	$.ajax({
		          beforeSend: function(){
		              OpenLoad("Guardando datos, por favor espere...");
		           },
		          url: 'controllers/controlador_personalizar_productos.php?metodo=eliminar_item',
		          type: 'POST',
		          data: parametros,
		          success: function(response){
		              console.log(response);
		              
		              if( response['success'] == 1)
		              {
		              	notify(response['mensaje'], "success", 2);
		                refrescar(categoria,codigo_padre,event);
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

    $("body").on('click', ".checkInsert",function (event){
        var cod_producto=$(this).attr("cod_producto");
        var codigo_padre=$(this).attr("codigo_padre");
        var categoria=$(this).attr("categoria");
    	
    	    var parametros = {
	            "cod_producto_extra": codigo_padre,
	            "cod_producto": cod_producto
	        }

        	$.ajax({
	          beforeSend: function(){
	              OpenLoad("Guardando datos, por favor espere...");
	           },
	          url: 'controllers/controlador_personalizar_productos.php?metodo=insertar_item',
	          type: 'POST',
	          data: parametros,
	          success: function(response){
	              console.log(response);
	              
	              if( response['success'] == 1)
	              {
	              	notify(response['mensaje'], "success", 2);
	                refrescar(categoria,codigo_padre,event);
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

    $("body").on('click', ".btnAgg",function (event){
      var categoria=$(this).attr("categoria");
      var codigo_padre=$(this).attr("codigo_padre");

       var parametros = {
	            "cod_producto_extra": codigo_padre,
	            "categoria": categoria
	        }

        	$.ajax({
	          beforeSend: function(){
	              OpenLoad("Guardando datos, por favor espere...");
	           },
	          url: 'controllers/controlador_personalizar_productos.php?metodo=insertar_productos',
	          type: 'POST',
	          data: parametros,
	          success: function(response){
	              console.log(response);
	              
	              if( response['success'] == 1)
	              {
	              	notify(response['mensaje'], "success", 2);
	                refrescar(categoria,codigo_padre,event);
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


    function refrescar(codigo,codigo_padre,event){
    	var parametros = {
            "cod_product_extra": codigo_padre,
            "codigo_categoria": codigo
            }
            

			 $.ajax({
	          beforeSend: function(){
	              OpenLoad("Guardando datos, por favor espere...");
	           },
	          url: 'controllers/controlador_personalizar_productos.php?metodo=ver_extra_detalle',
	          type: 'POST',
	          data: parametros,
	          success: function(response){
	              console.log(response);
	              
	              if( response['success'] == 1)
	              {
	              	$("#contentProductos"+codigo).html(response['contenido']);
	                $("#contentTabla"+codigo).html(response['html']);
	                $("#boton"+codigo).html(response['boton']);
	                $("#botonAgg"+codigo).html(response['botonAgg']);
	              }
	              else
	              {
	              	$("#contentProductos"+codigo).html(response['contenido']);
	                $("#contentTabla"+codigo).html(response['html']);
	                $("#botonAgg"+codigo).html(response['botonAgg']);
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

    function ordenarItems(data){
	 var codigo=$("#cod_original").val();
	  var parametros = {
	      "productos": data,
	      "codigo": codigo,
	    }
	    console.log(parametros);
	   
	  $.ajax({
	      url:'controllers/controlador_personalizar_productos.php?metodo=actualizar',
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