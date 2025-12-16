$(document).ready(function(){
    getAllCards();
    function getAllCards(){
        var cod_empresa = $("#cod_empresa").val();
        var parametros = {
            "cod_empresa": cod_empresa
        }
        $.ajax({
            beforeSend: function(){
                //OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=getAllCardsByEmpresa',
            type: 'POST',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    $(".lstCards").html(response['html']);
                    $(".btnActivarCard").hide();
                    $(".btnEliminarCard").hide();
                    feather.replace();
                } 
                else{
                    messageDone(response['mensaje'],'error');
                } 
                                        
            },
            error: function(data){
                console.log(data);
                
            },
            complete: function(resp)
            {
                //CloseLoad();
            }
        });
    }
});

$("body").on("click", ".btnVer", function(e){
    e.preventDefault();
    var cod_log = $(this).data("value");
    var tipo = $(this).data("tipo");
    
    var parametros = {
        "cod_log": cod_log,
        "tipo": tipo
    }
    
    $(".pclean").html("");
    
    $.ajax({
            beforeSend: function(){
                //OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=getMieLogs',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    
                    if(tipo == "S"){
                        $("#p_reference").html(response['datos']['transaction_reference']);
                        $("#p_transaction_id").html(response['datos']['transaction_id']);
                        $("#p_auth").html(response['datos']['transaction_autorizacion']);
                        $("#p_status").html(response['datos']['transaction_status']);
                        $("#divSuccess").show();
                        $("#divError").hide();
                    }
                    else{
                        $("#p_descrp").html(response['datos']['descripcion']);
                        $("#p_json").html(response['datos']['json']);
                        $("#divSuccess").hide();
                        $("#divError").show();
                    }
                    $("#crearModal").modal();
                    
                } 
                else{
                    messageDone(response['mensaje'],'error');
                } 
                                        
            },
            error: function(data){
                console.log(data);
                
            },
            complete: function(resp)
            {
                //CloseLoad();
            }
    });
});