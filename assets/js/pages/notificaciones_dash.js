$(document).ready(function() {
  $(".btnSendNotification").on("click",function(event){
          event.preventDefault();
          
          var form = $("#frmSave");
          form.validate();
          var isForm = form.valid();

          if(isForm ==false)
          {
            notify("Falta llenar informacion", "success", 2);
            return false;
          }
          
          var formData = new FormData($("#frmSave")[0]);
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_notificaciones.php?metodo=notificar_variosAdmins',
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
    crearSelect2($("#cmb_empresas"));
    crearSelect2($("#cmb_usuarios"));
});

function crearSelect2(elemento){
    elemento.select2();
    /*elemento.select2({
      closeOnSelect: false,
      tags: true,
      tokenSeparators: [',']
    });*/
}

function destruirSelect2(elemento){
    elemento.select2("destroy");
}

function des_bloquearSelect2(elemento, cant){
    elemento.select2({
        maximumSelectionLength: cant
    });
}

function limpiarSelect2(elemento){
    elemento.val(null).trigger('change');
}

$("#cmb_empresas").on("change", function(e){
    e.preventDefault();
    
    var combo = $(this);
    
    //$("#optEmpresa").remove();
    //combo.prepend("<option id=\"optEmpresa\" value=\"all\">Todas las empresas</option>");
    
    if(combo.val() == "all"){
        des_bloquearSelect2(combo, 1);
    }
    else if(combo.val() != "" && combo.val() != "all"){
        des_bloquearSelect2(combo, 999999999);
        //$("#optEmpresa").remove();
    }
    
    var formData = new FormData($("#frmSave")[0]);
          $.ajax({
              beforeSend: function(){
                  OpenLoad("Cargando datos, por favor espere...");
               },
              url: 'controllers/controlador_notificaciones.php?metodo=get_admins',
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response){
                  console.log(response);
                  destruirSelect2($("#cmb_usuarios"));
                    $("#cmb_usuarios").html("");
                    $("#cmb_usuarios").html("<option value=\"all\">Todos los usuarios</option> \
                                                <option value=\"rol-2\">Administradores de empresa</option> \
                                                <option value=\"rol-3\">Administradores de sucursal</option>" + response['html']);
                    crearSelect2($("#cmb_usuarios"));
                                           
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

$("#cmb_usuarios").on("change", function(e){
    e.preventDefault();
    
    var combo = $(this);
    if(combo.val() == "all")
        des_bloquearSelect2(combo, 1);
    else
        des_bloquearSelect2(combo, 999999999);
});