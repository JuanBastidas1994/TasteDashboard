$(function() {

});

function getOfficeById(){
    $("#sucursalModal").modal();

    getConfigBySucursal(sucursal_id)
        .then(data => {
            console.log("SUCURSAL INFO", data);
            var template = Handlebars.compile($("#office-detail-template").html());
            $("#sucursal-detail").html(template(data));
            feather.replace();
        })
        .catch(error=>{
            messageDone(error,'error');
        });
}

function closeOffice(){
    messageConfirm("Cerrar sucursal", "¿Está seguro?", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "cod_sucursal": sucursal_id,
                    "tiempo": $("#cmbHoras").val()
                }
                console.log("info", info);
                fetch(`${ApiUrl}/sucursales/crear_restriccion`,{
                        method: 'POST',
                        headers: {
                        'Api-Key':ApiKey
                        },
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log(response);
                        if(response.success == 1){
                            $(".office-status").removeClass("text-success");
                            $(".office-status").addClass("text-danger");
                            $(".office-status").text("Cerrado");
                            getOfficeById();
                            notify(response.mensaje, "success", 2);
                        }else{
                            messageDone(response.mensaje, "error");
                        }
                    })
                    .catch(error=>{
                        console.log(error);
                        messageDone('Ocurrió un error al cerrar la sucursal', "error");
                    });
            }
        });
}

function deleteRestrictionOffice(){
    messageConfirm("Abrir sucursal", "¿Está seguro?", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "cod_sucursal": sucursal_id,
                }
                fetch(`${ApiUrl}/sucursales/eliminar_restriccion`,{
                        method: 'POST',
                        headers: {
                        'Api-Key':ApiKey
                        },
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log(response);
                        if(response.success == 1){
                            $(".office-status").removeClass("text-danger");
                            $(".office-status").addClass("text-success");
                            $(".office-status").text("Abierto");
                            getOfficeById();
                            notify(response.mensaje, "success", 2);
                        }else{
                            messageDone(response.mensaje, "error");
                        }
                    })
                    .catch(error=>{
                        messageDone('Error: Ocurrió un error al cerrar la sucursal', "error");
                    });
            }
        });
}

function storeAltaDemanda(){
    messageConfirm("Activar alta demanda", "¿Está seguro?", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "cod_sucursal": sucursal_id,
                    "tiempo": $("#cmbHorasAltaDemanda").val()
                }
                console.log("info", info);
                fetch(`${ApiUrl}/sucursales/crear_altademanda`,{
                        method: 'POST',
                        headers: {
                        'Api-Key':ApiKey
                        },
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log(response);
                        if(response.success == 1){
                            getOfficeById();
                            notify(response.mensaje, "success", 2);
                        }else{
                            messageDone(response.mensaje, "error");
                        }
                    })
                    .catch(error=>{
                        console.log(error);
                        messageDone('Ocurrió un error al activar la alta demanda', "error");
                    });
            }
        });
}

function deleteAltaDemanda(){
    messageConfirm("Remover alta demanda", "¿Está seguro?", "question")
        .then(function(result) {
            if (result) {
                let info = {
                    "cod_sucursal": sucursal_id,
                }
                fetch(`${ApiUrl}/sucursales/eliminar_altademanda`,{
                        method: 'POST',
                        headers: {
                        'Api-Key':ApiKey
                        },
                        body: JSON.stringify(info)
                    })
                    .then(res => res.json())
                    .then(response => {
                        console.log(response);
                        if(response.success == 1){
                            getOfficeById();
                            notify(response.mensaje, "success", 2);
                        }else{
                            messageDone(response.mensaje, "error");
                        }
                    })
                    .catch(error=>{
                        messageDone('Error: Ocurrió un error al remover la alta demanda', "error");
                    });
            }
        });
}