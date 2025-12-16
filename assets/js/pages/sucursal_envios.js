$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });

    $(".btnEditar").on("click",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer informacion de la promocion, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_promocion": id
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_marketing_envios.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#id").val(data['cod_producto_descuento']);
                    $("#hora_ini").val(data['fecha_inicio']);
                    $("#hora_fin").val(data['fecha_fin']);
                    $("#txt_valor").val(data['valor']);
                    $("#cmb_productos").val(data['cod_producto']);
                    $('#cmb_productos').trigger('change');
                    $("#cmb_sucursales").val(data['cod_sucursal']);
                    $('#cmb_sucursales').trigger('change');
                    //$("#cmbRol").val(data['cod_rol']);
                    //$("#fecha_nacimiento").val(data['fecha_nacimiento']);
                    //$("#txt_correo").val(data['correo']);

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

     $(".btnEliminar").on("click",function(event){
        event.preventDefault();
        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer la disponibilidad, por favor intentelo mas tarde");
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
                "cod_sucursal_disponibilidad": id,
                "estado": "D"
              }
              $.ajax({
                  beforeSend: function(){
                      OpenLoad("Buscando informacion, por favor espere...");
                   },
                  url: 'controllers/controlador_sucursal.php?metodo=eliminar_disponibilidad',
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
    
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_sucursal', id);
          }

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_sucursal.php?metodo=crear_disponibilidad',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    //$("#id").val(response['id']);
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
    
    $("#cmb_sucursales").select2();
    
    /*time picker*/
    $('#crearModal').removeAttr('tabindex');
    var picker = document.getElementById("hora_ini");
    flatpickr(picker, {
      noCalendar: true,
      enableTime: true,
      dateFormat: "H:i"
    });

    var picker2 = document.getElementById("hora_fin");
    flatpickr(picker2, {
      noCalendar: true,
      enableTime: true,
      dateFormat: "H:i"
    }); 

    var picker3 = document.getElementById("fecha_inicio");
    flatpickr(picker3, {
      enableTime: false,
      dateFormat: "Y-m-d"
    });

    $('.dropify').dropify({
      messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

});