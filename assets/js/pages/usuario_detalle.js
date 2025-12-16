$(document).ready(function(){
    /*time picker*/
    var f4 = flatpickr(document.getElementById('fecha_nacimiento'), {
        enableTime: false,
        dateFormat: "Y-m-d"
    });
});

$("#btnEditarInfo").on("click", function(){
    $("#binfo").hide();
    $("#bloqueInfo").show();
});

$("#btnCancelarInfo").on("click", function(e){
    e.preventDefault();
    $("#bloqueInfo").hide();
    $("#binfo").show();
});

$("#btnAbrirBloqueo").on("click", function(){
    $(this).hide();
    $("#bloqueo").show();
    $("#bloqueInfo").hide();
});

$("#btnCancelarEstado").on("click", function(){
    $("#bloqueo").hide();
    $("#btnAbrirBloqueo").show();
});

$("#btnGuardarInfo").on("click", function(e){
    e.preventDefault();
    Swal.fire({
       title: 'Guardar Información',
       text: 'La actualización de estos datos son bajo su responsabilidad ¿Continuar?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          guardarInfo();
       }
    }); 
});

$("#btnEditarEstado").on("click", function(e){
    e.preventDefault();
    Swal.fire({
       title: 'Eliminar Cliente',
       text: '¿Está seguro?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          editarEstado();
       }
    }); 
});

function guardarInfo() {
    let form = $("#frmInfo");
    form.validate();
    if(form.valid()==false){
        messageDone('Debes llenar todos los campos','error');
    return false;
    }

    let formData = new FormData($("#frmInfo")[0]);
    $.ajax({
       url:'controllers/controlador_usuario.php?metodo=editarCliente',
       data: formData,
       type: "POST",
       contentType: false,
       processData: false,
       success: function(response){
          console.log(response);
          if(response['success']==1){
              messageDone(response['mensaje'], "success");
              location.reload();
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

/* function guardarInfo() {
    var parametros = {
            "cod_usuario": $("#cod_usuario").val(),
            "num_documento": $("#txt_cedula").val()
        }

    $.ajax({
       url:'controllers/controlador_usuario.php?metodo=editarCedula',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
              messageDone(response['mensaje'], "success");
              $("#cedulaNueva").html($("#txt_cedula").val());
              $("#bloqueInfo").hide();
              location.href = '#';
          }
          else{
            messageDone(response['mensaje'], "success");
          }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
} */

function editarEstado() {
    var parametros = {
        "cod_usuario": $("#cod_usuario").val(),
        "motivo": $("#txt_motivo").val()
    }

    $.ajax({
    url:'controllers/controlador_usuario.php?metodo=set_estado_cliente',
    data: parametros,
    type: "GET",
    success: function(response){
        console.log(response);
        if(response['success']==1){
            messageDone(response['mensaje'], "success");
            $("#bloqueInfo").hide();
            location.href = '#';
        }
        else{
            messageDone(response['mensaje'], "success");
        }
    },
    error: function(data){
    },
    complete: function(){
    },
    });
}

$("body").on("click", ".btnEliminarCliente", function(e){
    e.preventDefault();
    let cod_usuario = $(this).data("value");
    Swal.fire({
       title: 'Eliminar Cliente',
       text: '¿Está seguro?',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          eliminarCliente(cod_usuario);
       }
    }); 
});

function eliminarCliente(cod_usuario) {
    var parametros = {
        "cod_usuario": cod_usuario
    }

    $.ajax({
    url:'controllers/controlador_usuario.php?metodo=set_estado_cliente',
    data: parametros,
    type: "GET",
    success: function(response){
        console.log(response);
        if(response['success']==1){
            messageDone(response['mensaje'], "success");
        }
        else{
            messageDone(response['mensaje'], "success");
        }
    },
    error: function(data){
    },
    complete: function(){
    },
    });
}

$(".btnNotificarMoto").on("click", function(){
    let cod_motorizado = $(this).data("usuario");
    let parametros = {
        "metodo": "notificarMotorizadoCustom",
        "cod_motorizado": cod_motorizado,
        "mensaje": $("#txt").val()
    }
    
    $.ajax({
       url:'https://dashboard.mie-commerce.com/controllers/controlador_notificaciones.php',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
            $("#txt").val("");
          }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
});