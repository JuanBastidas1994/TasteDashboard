$(document).ready(function(){
    var dataFastAmb = $("#cmbAmbienteDatafast").val();
    if(dataFastAmb == 'production'){
        $("#cmbFaseDatafast").attr("disabled", true);
    }
});

$(".btn-getTokens").on("click", function(){
    var clientCode = $("#txt_clientCode_desarrollo").val();
    var clientKey = $("#txt_clientKey_desarrollo").val();
    var serverCode = $("#txt_serverCode_desarrollo").val();
    var serverKey = $("#txt_serverKey_desarrollo").val();
    
    $("#txt_clientcode").val(clientCode);
    $("#txt_clientkey").val(clientKey);
    $("#txt_servercode").val(serverCode);
    $("#txt_serverkey").val(serverKey);
    
    $(".btn-updatePay").removeClass("btn-outline-primary");
    $(".btn-updatePay").addClass("btn-warning");
    $(".btn-updatePay").html("Guardar");
});

$("body").on("click", ".btn-updatePay", function(){
    var servercode=$("#txt_servercode").val();
    var serverkey=$("#txt_serverkey").val();
    var clientcode=$("#txt_clientcode").val();
    var clientkey=$("#txt_clientkey").val();
    var codigo=$(this).attr("data-codigo");
    var cod_empresa=$("#id").val();
    var tipo=$("#cmbTipoBP").val();
    if(cod_empresa == 0)
    {
         messageDone("Debe guardar primero la empresa",'error');
         return;
     }
     if(servercode=="" || serverkey=="" || clientcode=="" || clientkey==""){
         messageDone("Debe completar todos los campos",'error');
     }
     else
     {
             var parametros = {
                 "servercode": servercode,
                 "serverkey": serverkey,
                 "clientcode": clientcode,
                 "clientkey": clientkey,
                 "cod_empresa": cod_empresa,
                 "codigo": codigo,
                 "tipo": tipo
             }
             if(codigo!=0)
             {
                messageConfirm('¿Estas seguro?', 'No se puede revertir los cambios', "warning")
                    .then(function(result) {
                        if (result) {
                            $.ajax({
                                 beforeSend: function(){
                                     OpenLoad("Editando datos, por favor espere...");
                                 },
                                 url: 'controllers/controlador_configuraciones.php?metodo=edit_paymentez',
                                 type: 'GET',
                                 data: parametros,
                                 success: function(response){
                                     console.log(response);
                                     if( response['success'] == 1)
                                     {
                                         $( "#txt_serverkey" ).prop( "disabled", false );
                                         $( "#txt_clientcode" ).prop( "disabled", false );
                                         $( "#txt_clientkey" ).prop( "disabled", false );
                                         $("#txt_servercode").prop('disabled',false);
                                         $("#cmbTipoBP").prop('disabled',false);
                                         $(".btn-updatePay").prop('disabled',true);
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
                        }
                    });
             }
             else
             {
                 $.ajax({
                     beforeSend: function(){
                         OpenLoad("Insertando datos, por favor espere...");
                     },
                     url: 'controllers/controlador_configuraciones.php?metodo=insert_paymentez&tipo='+tipo,
                     type: 'GET',
                     data: parametros,
                     success: function(response){
                         console.log(response);
                         if( response['success'] == 1)
                         {
                             messageDone(response['mensaje'],'success');
                             $(".btn-updatePay").attr("data-codigo",response['id']);
                             $(".btn-updatePay").removeClass("btn-warning");
                             $(".btn-updatePay").addClass("btn-outline-primary");
                             $(".btn-updatePay").html("Actualizar");
                             
                             $(".btn-getTokens").hide();
                             $(".btn-config-estado-P").removeClass("text-danger");
                             $(".btn-config-estado-P").addClass("text-success");
                             $(".btn-config-estado-P").html("<i data-feather=\"check-circle\"></i> Botón configurado");
                             feather.replace();
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
     }
 });

$("body").on("click", ".btn-datafast-desarrollo-tokens", function(){
    var api = $("#txt_api_desarrollo").val();
    var entity = $("#txt_entity_desarrollo").val();
    var mid = $("#txt_mid_desarrollo").val();
    var tid = $("#txt_tid_desarrollo").val();

    $("#txt_api").val(api);
    $("#txt_entityId").val(entity);
    $("#txt_mid").val(mid);
    $("#txt_tid").val(tid);

    $(".btn-updateDataFast").removeClass("btn-outline-primary");
    $(".btn-updateDataFast").addClass("btn-warning");
    $(".btn-updateDataFast").html("Guardar");
});

$("#cmbAmbienteDatafast").on("change", function(){
    var ambiente = $(this).val();
    if(ambiente == "development"){
        $("#cmbFaseDatafast").removeAttr("disabled");
    }
    else{
        $("#cmbFaseDatafast").val("FASE2");
        $("#cmbFaseDatafast").attr("disabled", true);
    }
});

$(".btn-updateDataFast").on("click", function(){
    alert($("#cmbFaseDatafast").val());
});

$("body").on("click", ".btn-updateDataFast", function(){
    var api=$("#txt_api").val();
    var entity=$("#txt_entityId").val();
    var mid=$("#txt_mid").val();
    var tid=$("#txt_tid").val();
    var ambiente=$("#cmbAmbienteDatafast").val();
    var fase=$("#cmbFaseDatafast").val();
    var codigo=$(this).attr("data-codigo");
    var cod_empresa=$("#id").val();
    if(cod_empresa == 0){
         messageDone("Debe guardar primero la empresa",'error');
         return;
     }
     if(api=="" || entity=="" || mid=="" || tid=="" || ambiente=="" || fase==""){
         messageDone("Debe completar todos los campos",'error');
     }
     else{
            var parametros = {
                "api": api,
                "entity": entity,
                "mid": mid,
                "tid": tid,
                "ambiente": ambiente,
                "fase": fase,
                "cod_empresa": cod_empresa,
                "codigo": codigo
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Insertando datos, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=insert_datafast',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
                        $(".btn-updateDataFast").attr("data-codigo",response['id']);
                        $(".btn-updateDataFast").removeClass("btn-warning");
                        $(".btn-updateDataFast").addClass("btn-outline-primary");
                        $(".btn-updateDataFast").html("Actualizar");

                        $(".btn-datafast-desarrollo-tokens").hide();
                        $(".btn-config-estado-D").removeClass("text-danger");
                        $(".btn-config-estado-D").addClass("text-success");
                        $(".btn-config-estado-D").html("<i data-feather=\"check-circle\"></i> Botón configurado");
                        feather.replace();
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

$(".btn-select-boton").on("click", function(event){
    var cod_proveedor_botonpagos = $("#cmbBotonActual").val();
    if(cod_proveedor_botonpagos < 1)
        return;
    
    messageConfirm('¿Deseas continuar?', 'Este será su nuevo botón de pagos', "warning")
    .then(function(result) {
        if (result) {
            selectBoton(event);
        }
    });
})

function selectBoton(e){
    e.preventDefault();
    var cod_proveedor_botonpagos = $("#cmbBotonActual").val();
    if(cod_proveedor_botonpagos < 1)
        return;
    var cod_empresa = $("#id").val();
    var parametros = {
        "cod_proveedor_botonpagos": cod_proveedor_botonpagos,
        "cod_empresa": cod_empresa
    }
    $.ajax({
        beforeSend: function(){
            OpenLoad("Guardando, por favor espere...");
        },
        url:'controllers/controlador_configuraciones.php?metodo=establecerBoton',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response['mensaje'], "success");
                $(".btnActual").html($("#cmbBotonActual option:selected").text());
            }
            else{
                messageDone(response['mensaje'], "error");
            }
        },
        error: function(data){
        },
        complete: function(){
            CloseLoad();
        },
    });
}