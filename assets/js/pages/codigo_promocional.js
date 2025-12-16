$(document).ready(function() {
    $("#btnOpenModal").on("click",function(event){
        $("#id").val(0);
        $("#frmSave").trigger("reset");
        $("#crearModal").modal();
    });

    $("body").on("click", ".btnEditar",function(event){
        event.preventDefault();

        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
          return;
        }
        var parametros = {
          "cod_codigo_promocional": id
        }
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Buscando informacion, por favor espere...");
               },
              url: 'controllers/controlador_codigo_promociona.php?metodo=get',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    var data = response['data'];
                    $("#id").val(data['cod_codigo_promocional']); 
                    $("#cmbTipo").val(data['por_o_din']);
                    $("#txt_codigo").val(data['codigo']);
                    $("#txt_monto").val(data['monto']);
                    $("#txt_cantidad").val(data['cantidad']);
                    $("#txt_restriccion").val(data['restriccion']);
                    $("#fecha_expiracion").val(data['fecha_expiracion']);

                    $(".ckIlimitado").prop("checked", false);
                    if(data['ilimitado'] == 1) {
                        $(".ckIlimitado").prop("checked", true);
                    }

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

     $("body").on("click", ".btnEliminar", function(event){
        event.preventDefault();
        var id = parseInt($(this).attr("data-value"));
        if(id==0){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
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
            "cod_codigo_promocional": id,
            "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_codigo_promociona.php?metodo=set_estado',
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
     
     $("body").on("click", ".btnVentas",function(event){
        event.preventDefault();

        var codigo = $(this).attr("data-codigo");
        if(codigo==""){
          alert("No se pudo traer el codigo promocional, por favor intentelo mas tarde");
          return;
        }
        
        $("#cuponfilterText").html(codigo);
        $("#cuponfilter").val(codigo);
        $("#ventasModal").modal();
        console.log(codigo);
        console.log(tableProcessing);
        if(!tableProcessing)
            loadDatatable();
        else
            tableProcessing.ajax.reload();
        // var parametros = {
        //   "id": id
        // }
        // $.ajax({
        //       beforeSend: function(){
        //           OpenLoad("Buscando informacion, por favor espere...");
        //       },
        //       url: 'controllers/controlador_codigo_promociona.php?metodo=getCantUse',
        //       type: 'GET',
        //       data: parametros,
        //       success: function(response){
        //           console.log(response);
        //           if( response['success'] == 1){
        //             $("#ventasModal").modal();
        //           } 
        //           else{
        //             messageDone(response['mensaje'],'error');
        //           } 
        //       },
        //       error: function(data){
        //         console.log(data);
                 
        //       },
        //       complete: function(resp)
        //       {
        //         CloseLoad();
        //       }
        //   });
     });


    $("#btnGuardar").on("click",function(event){
          event.preventDefault();
          
          // var form = $("#frmSave");
          // form.validate();
          // if(form.valid()==false)
          // {
          //   notify("Informacion", "Campos obligatorios vacios", "info", 2, true);
          //   return false;
          // }
          

          if("" == $("#txt_monto").val()){
            notify("Ingrese el monto", "info", 2, true);
            return;
          }
          
          if("" == $("#txt_cantidad").val()){
            notify("Ingrese la cantidad", "info", 2, true);
            return;
          }

          if("" == $("#txt_codigo").val()){
            notify("Ingrese el código", "info", 2, true);
            return;
          }
          
          if("" == $("#fecha_expiracion").val()){
            notify("Ingrese la fecha", "info", 2, true);
            return;
          }

          if("" == $("#txt_restriccion").val()){
            notify("Ingrese la restricción", "info", 2, true);
            return;
          }

          let usoIlimitado = 0;
          if ($(".ckIlimitado").is(":checked"))
              usoIlimitado = 1;

          var tipo = "I";
          var formData = new FormData($("#frmSave")[0]);
          var id = parseInt($("#id").val());
          if(id > 0){
              formData.append('cod_codigo_promocional', id);
              tipo = "U";
          }

          formData.append('usoIlimitado', usoIlimitado);

          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_codigo_promociona.php?metodo=crear',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  
                  if( response['success'] == 1)
                  {
                    messageDone(response['mensaje'],'success');
                    $("#id").val(response['id']);
                    $("#crearModal").modal('hide');
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
        data[0] = $("#txt_codigo").val().trim();
        data[1] = $("#cmbTipo option:selected").text().trim();
        data[2] = $("#txt_monto").val().trim();
        data[3] = $("#txt_cantidad").val().trim();
        data[4] = $("#txt_cantidad").val().trim();
        data[5] = "> $"+$("#txt_restriccion").val().trim();
        data[6] = $("#fecha_expiracion").val().trim();
        data[7] = tableEstado('A');
        data[8] = tableAcciones(codigo);

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

    /*time picker*/
    var f4 = flatpickr(document.getElementById('fecha_expiracion'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });
    var clipboard = new Clipboard('.btnCopiar');

    clipboard.on('success', function(e) {
        notify('Copiado:'+e.text, 'success', 2);

        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
    });

});