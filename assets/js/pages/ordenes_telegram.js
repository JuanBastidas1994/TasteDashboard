var clipboard
$(function(){
    let clipboard2 = new Clipboard('.btnCopiar');
    clipboard2.on('success', function(e) {
        console.log('clipboard', e);
        notify('Copiado:'+e.text, 'success', 2);

        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
    });
    getListSucursales();
});

function getListSucursales(){

    fetch(`controllers/controlador_telegram_sucursales.php?metodo=getList`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            $("#lstSucursales").html("");
            response.data.forEach(sucursal => {
                let configuracion = `<a class="btnTelegramCrear btn btn-primary" data-id="${sucursal.cod_sucursal}" data-name="${sucursal.nombre}" href="javascript:void(0);">Configurar</a>`;
                let code = "NO CONFIGURADO";
                let estado = "NO CONFIGURADO";
                let badge = "danger";
                if(sucursal.config_telegram != null){
                    configuracion = `<a class="btnTelegramVer btn btn-outline-primary" data-id="${sucursal.config_telegram.code}" data-name="${sucursal.nombre}" href="javascript:void(0);">Ver configuración</i></a>`;
                    code = `<span class="idCode">${sucursal.config_telegram.code}</span>
                            <span class="btnCopiar" style="cursor: pointer;" data-clipboard-text="${sucursal.config_telegram.code}">
                                <i data-feather="copy"></i>
                            </span>`;
                    estado = sucursal.config_telegram.estado;
                    badge = "success";
                    if(estado == "PENDIENTE")
                        badge = "warning";
                }
                $("#lstSucursales").append(`    <tr>
                                                    <td>${sucursal.cod_sucursal}</td>
                                                    <td>${sucursal.nombre}</td>
                                                    <td>
                                                        ${code}
                                                    </td>
                                                    <td class="text-center"><span class="badge badge-${badge}">${estado}</span> </td>
                                                    <td class="text-center">${configuracion}</td>
                                                </tr>`);
            });
            feather.replace();
        }
        else{
        }
    })
    .catch(error=>{
        console.log(error);
    });
}

$("body").on("click", ".btnTelegramCrear", function(){
    let cod_sucursal = $(this).data("id");
    let nombre = $(this).data("name");

    fetch(`controllers/controlador_telegram_sucursales.php?metodo=crear&cod_sucursal=${cod_sucursal}`,{
        method: 'GET',
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            getListSucursales();
            let data = {
                id: response.code,
                nombre
            }
            verConfigTelegram(data);
        }
        else{
            messageDone(response.mensaje, "error");
        }
    })
    .catch(error=>{
        console.log(error);
    });
});

$("body").on("click", ".btnTelegramVer", function(){
    let data = {
        id: $(this).data("id"),
        nombre: $(this).data("name")
    }
    verConfigTelegram(data);
});

function verConfigTelegram(data){
    $("#idTelegram").html(data.id);
    $("#idNomSucursal").text(data.nombre);
    feather.replace();
    $("#modalVerConfig").modal();
}