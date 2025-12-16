$("body").on("click",".btnEliminar",function(event){
        event.preventDefault();
        var cod_programa = parseInt($(this).attr("data-value"));
        if(cod_programa==0){
          alert("No se pudo traer el producto, por favor intentelo mas tarde");
          return;
        }
        var element = $(this);

        swal({
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
              "cod_programa": cod_programa,
              "estado": "D"
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                 },
                url: 'controllers/controlador_programas.php?metodo=set_estado',
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

$("body").on("click", ".btnEditar", function(){
    let cod_programa = $(this).data("value");
    let parametros = {
        "cod_programa": cod_programa
    }
    $.ajax({
        url:'controllers/controlador_programas.php?metodo=get',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                let data = response['data'];
                $("#txtNombre").val(data[0]['nombre']);
                $("#txtDesc").val(data[0]['descripcion']);
                $("#txtPrecio").val(data[0]['precio']);
                $("#cmbEstado").val(data[0]['estado']);
                $("#btnGuardar").attr("data-value", data[0]['cod_programa'])
                $("#modalPrograma").modal();
            }
            else{
                messageDone(response['mensaje'], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
});

$("#btnGuardar").on("click", function(){
    let cod_programa = $(this).data("value");

    if("" == $("#txtTitulo").val()){
        messageDone("Ingrese título", "error");
        return;
    }
    if("" == $("#txtPrecio").val()){
        messageDone("Ingrese precio", "error");
        return;
    }
    
    let parametros = {
        "cod_programa": cod_programa,
        "txtNombre": $("#txtNombre").val(),
        "txtPrecio": $("#txtPrecio").val(),
        "txtDesc": $("#txtDesc").val(),
        "cmbEstado": $("#cmbEstado").val(),
    }

    console.log(parametros);
    $.ajax({
        url:'controllers/controlador_programas.php?metodo=crear',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response['mensaje'], "success");
                $("#modalPrograma").modal("hide");
            }
            else{
                messageDone(response['mensaje'], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}); 

$(".btnNuevoPrograma").on("click", function(){
    $("#btnGuardar").attr("data-value", 0);
    $("#modalPrograma").modal();
});

$("body").on("click", ".btnAceptarPrograma", function(){
    let boton = $(this);
    let aprobar = $(this).data("aprobar");
    let cod_programa_usuario = $(this).data("value");
    let precio = $("#textPrecio-"+cod_programa_usuario).val();

    let parametros = {
        "cod_programa_usuario": cod_programa_usuario,
        "precio": precio,
        "estado": aprobar
    }
    $.ajax({
        url:'controllers/controlador_programas.php?metodo=aceptarPrograma',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                let tdd = boton.parents('td');
                boton.remove();
                if("A" == aprobar){
                    tdd.html(`<span class="text-success">Aceptado</span>`);
                    correoAceptar(cod_programa_usuario, 1);
                }
                else{
                    tdd.html(`<span class="text-danger">Rechazado</span>`);
                    correoAceptar(cod_programa_usuario, 0);
                }
            }
            else{
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
});

function correoAceptar(cod_programa_usuario, isAceptado){
    let parametros = {
        "alias": $("#txtAlias").val(),
        "cod_programa_usuario": cod_programa_usuario,
        "isAceptado": isAceptado
    }
    console.log(parametros);
    $.ajax({
        url:'correosFront/semillitas/programaAceptado.php',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response['mensaje'], "success");
            }
            else{
                messageDone(response['mensaje'], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}