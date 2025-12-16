$("#btnGuardar").on("click", function(){
    var form = $("#frmSave");
    form.validate();
    if(form.valid()==false){
        messageDone('Debes llenar todos los campos','error');
        return false;
    }

    var formData = new FormData($("#frmSave")[0]);
    $.ajax({
       url:'controllers/controlador_app_versiones.php?metodo=crear',
       data: formData,
       type: "POST",
       contentType: false,
       processData: false,
       success: function(response){
          console.log(response);
          if(response['success']==1){
              $("#frmSave").trigger("reset");
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
});

$(".btnEliminar").on("click", function(){
    let cod_empresa_version = $(this).data("value");
    messageConfirm("Eliminar versión", "¿Está seguro?", "warning")
        .then(function(result) {
            if (result) {
                eliminarVersion(cod_empresa_version);
            }
        });
});

function eliminarVersion(cod_empresa_version){
    var parametros = {
        "cod_empresa_version": cod_empresa_version
    }
    $.ajax({
       url:'controllers/controlador_app_versiones.php?metodo=eliminar',
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