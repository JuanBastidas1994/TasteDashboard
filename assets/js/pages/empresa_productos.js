$("body").on("change", "#cmbSucursales", function(){
    let cod_sucursal = $(this).val();
    let cod_empresa = $(this).data("empresa");
    let alias = $(this).data("alias");
    $("#bodyAsignados").html("");
    $("#bodyNoAsignados").html("");
    $("#cantAsignados").html("0");
    $("#cantNoAsignados").html("0");
    if(cod_sucursal <= 0){
        return;
    }
    
    let parametros = {
        "cod_empresa": cod_empresa,
        "cod_sucursal": cod_sucursal,
        "alias": alias
    }
    $.ajax({
        url:'controllers/controlador_productos.php?metodo=getProdSucursal',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                $("#bodyAsignados").html(response['htmlAsignados']);
                $("#bodyNoAsignados").html(response['htmlNoAsignados']);
                $("#cantAsignados").html(response['cantAsignados']);
                $("#cantNoAsignados").html(response['cantNoAsignados']);
                feather.replace();
            }
            else{
                notify(response['mensaje'], "error")
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
});