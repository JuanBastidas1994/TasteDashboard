$(document).ready(function(){
  $("#cmbSucursal").trigger("change");
});

$("body").on("change","#cmbSucursal",function(event){
    var parametros = {
        "cod_sucursal": $(this).val()
      }
      $.ajax({
          beforeSend: function(){
              OpenLoad("Buscando informacion, por favor espere...");
           },
          url: 'controllers/controlador_disponibilidad_productos.php?metodo=lista',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log(response);
              if( response['success'] == 1)
              {
                $("#lstDisponibles").html(response['disponibles']);
                $("#lstAgotados").html(response['agotados']);
                feather.replace();
                
                
                
                if ( $.fn.dataTable.isDataTable( '.cl-disponibles' ) ) {
                    var myTable = $('.cl-disponibles').DataTable();
                }
                else {
                    var myTable = $('.cl-disponibles').DataTable( {
                        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                        buttons: {
                            buttons: []
                        },
                        "oLanguage": {
                            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                            "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                            "sInfoEmpty": "",
                            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                            "sSearchPlaceholder": "Buscar...",
                           "sLengthMenu": "Resultados :  _MENU_",
                           "sEmptyTable": "No se encontraron resultados",
                           "sZeroRecords": "No se encontraron resultados",
                           "buttons": {}
                        },
                        "stripeClasses": [],
                        "lengthMenu": [7, 10, 20, 50],
                        "pageLength": 10,
                        "bPaginate": false, //Ocultar paginación
                    } );
                }
                
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

$("body").on("click",".productoAgotar",function(event){
    event.preventDefault();
    var element = $(this);
    var minutos = element.attr("data-minutes");
    var cod_producto = element.attr("data-producto");
    var cod_sucursal = $("#cmbSucursal").val();
    const texttime = convertirMinutos(parseInt(minutos));

    swal.fire({
      title: '¿Estas seguro?',
      text: "El producto no estara disponible para la venta durante "+texttime,
      type: 'warning',
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
 
 function convertirMinutos(minutos) {
     if(minutos > 5000)
        return "tiempo indefinido";
  // Calcular cuántas horas y minutos
  const horas = Math.floor(minutos / 60);
  const minutosRestantes = minutos % 60;
  console.log(minutosRestantes);
  
  // Construir el texto a devolver
  let resultado = '';
  
  if (horas > 0) {
    resultado += horas + ' hora' + (horas > 1 ? 's' : '');
  }
  
  if (minutosRestantes > 0) {
    if (resultado) {
      resultado += ' ';
    }
    resultado += minutosRestantes + ' minuto' + (minutosRestantes > 1 ? 's' : '');
  }
  
  // Si el número de minutos es 0
  if (minutos === 0) {
    resultado = '0 minutos';
  }
  
  return resultado;
}


$("body").on("click",".deleteProductoAgotar",function(event){
    event.preventDefault();
    var element = $(this);
    var cod_producto = element.attr("data-producto");
    var cod_sucursal = $("#cmbSucursal").val();

    swal.fire({
      title: '¿Estas seguro?',
      text: "El producto regresará a la venta",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
      padding: '2em'
    }).then(function(result) {
      if (result.value) {
        
        var parametros = {
          "cod_producto": cod_producto,
          "cod_sucursal": cod_sucursal
        }
        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando informacion, por favor espere...");
             },
            url: 'controllers/controlador_disponibilidad_productos.php?metodo=deleteAgotado',
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
 

