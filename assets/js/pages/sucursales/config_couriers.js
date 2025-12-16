let selectedData = [];
let cod_sucursal = 0;

$(function() {
    if($("#cod_sucursal").length) {
        cod_sucursal = $("#cod_sucursal").val();
        if(cod_sucursal > 0)
            getSucursalesCourier();
    }
    else {
        getSucursalesCourier();  
    }
});

function getSucursalesCourier() {
    let paramSucursal = "";
    if(cod_sucursal > 0) {
        paramSucursal = {cod_sucursal};
    }

    $.ajax({
        url: `controllers/controlador_configuraciones.php?metodo=getSucursalesCourier`,
        type: "GET",
        data: paramSucursal,
        success: function(response){
            console.log(response);
            if(response['success']==1){
                let htmlSucursales = "";
                let sucursales = response.data;
                sucursales.forEach(sucursal => {
                    let htmlCouriers = "";
                    let couriers = sucursal.couriers;
                    couriers.forEach(courier => {
                        let checked = "";
                        if(courier.validar_cobertura == 1)
                            checked = "checked";
                        htmlCouriers+= `    <tr class="trCouriers" id="${courier.cod_courier}" data-row="${courier.cod_courier}">
                                                <td class="tempElement" style="cursor: move; display:none;"><i data-feather="align-justify"></i></td>
                                                <td>
                                                    <img src="${courier.imagen}" alt="alt" style="width: 50px; border-radius: 50%;">
                                                </td>
                                                <td>
                                                    <p>${courier.nombre}</p>
                                                </td>
                                                <td>
                                                    <p>${courier.tipo}</p>
                                                </td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline  s-outline-success  mb-0">
                                                        <input type="checkbox" data-courier="${courier.cod_courier}" data-sucursal="${sucursal.cod_sucursal}" class="ckValidarCobertura" ${checked}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                            </tr>`;
                    });
                    htmlSucursales+= `  <div class="accordion mb-3" id="sucursal${sucursal.cod_sucursal}">
                                            <div class="card">
                                                <div class="card-header" id="headingOne">
                                                    <div class="align-items-center d-flex justify-content-between mb-0">
                                                        <a href="#!" data-toggle="collapse" data-target="#collapse${sucursal.cod_sucursal}" aria-expanded="true" aria-controls="collapseOne">
                                                        ${sucursal.nombre}
                                                        </a>
                                                        <button class="btn btn-primary btnOrdenar" ${sucursal.couriers.length > 1 ? "" : "style=\"display:none;\""}>Cambiar prioridad</button>
                                                        <button class="btn btn-success btnOrdenarSuccess" data-id="${sucursal.cod_sucursal}" style="display: none;">Hecho</button>
                                                    </div>
                                                </div>

                                                <div id="collapse${sucursal.cod_sucursal}" class="collapse show" aria-labelledby="headingOne" data-parent="#sucursal${sucursal.cod_sucursal}">
                                                    
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="tempElement" style="display:none;">Orden</th>
                                                                                <th>Imagen</th>
                                                                                <th>Nombre</th>
                                                                                <th>Tipo</th>
                                                                                <th>Validar cobertura <span class="bs-popover" data-container="body" data-content="Lorem ipsum"><i data-feather="info"></i></span></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody data-tbody="sortSucursal${sucursal.cod_sucursal}" class="connectedSortable sortSucursal${sucursal.cod_sucursal}">
                                                                            ${htmlCouriers}
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>`;
                });
                $("#acordeones").html(htmlSucursales);
                feather.replace();
                $(".bs-popover").popover();
            }
            else{
                messageDone(response.mensaje);
            }
        },
        error: function(data){
        },
        complete: function(){
        },
    });
}

$("body").on("change", ".ckValidarCobertura", function(){
    let courier = $(this).data("courier");
    let sucursal = $(this).data("sucursal");

    let estado = 0;
    if($(this).is(":checked"))
        estado = 1;

    let parametros = {
        courier,
        sucursal,
        estado
    }

    console.log(parametros);

    $.ajax({
        beforeSend: function(){
            OpenLoad("Actualizando, por favor espere...");
        },
        url:'controllers/controlador_configuraciones.php?metodo=courierValidarCobertura',
        data: parametros,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response['success']==1){
                messageDone(response.mensaje, "success");
            }
            else{
                messageDone(response.mensaje, "error");
            }
        },
        error: function(data){
        },
        complete: function(){
            CloseLoad();
        },
    });
});

$("body").on("click", ".btnOrdenar", function(){
    let btn = $(this);
    btn.hide();
    $(".btnOrdenar").prop("disabled", true);
    
    let accordion = btn.parents(".accordion");
    accordion.find(".btnOrdenarSuccess").show();

    let table = accordion.find("table");
    let tbody = table.find("tbody");

    table.find(".tempElement").show();

    let selector = tbody.data("tbody");
    $(tbody).sortable({
        connectWith: ".connectedSortable",
        update: function (event, ui) {
            selectedData = new Array();
            $("."+selector+">tr").each(function() {
                selectedData.push($(this).attr("data-row"));
                console.log("selectedData", selectedData);
            });
        }
    });
});

$("body").on("click", ".btnOrdenarSuccess", function(){
    let btn = $(this);
    let cod_sucursal = btn.data("id");
    btn.hide();
    
    let accordion = btn.parents(".accordion");
    accordion.find(".btnOrdenar").show();

    accordion.find(".tempElement").hide();
    accordion.find("tbody").sortable("destroy");

    $(".btnOrdenar").prop("disabled", false);

    gurdarPosiciones(cod_sucursal);
});

function gurdarPosiciones(cod_sucursal){
    if(selectedData.length <= 0){
        console.log("No hay cambios por guardar");
        $(".btnOrdenar").prop("disabled", false);
        return;
    }

    let parametros = {
        cod_sucursal,
        couriers: selectedData
    }
    
    $.ajax({
        url:'controllers/controlador_configuraciones.php?metodo=gurdarPosicionesCouriers',
        data: parametros,
        type: "POST",
        headers: { 
            Accept : "application/json"
        },
        success: function(response){
            console.log(response);
            if(response['success']==1){
                selectedData = new Array();
                messageDone(response.mensaje, "success");
            }
            else{
                messageDone(response.mensaje, "error");
            }
        },
        error: function(data){
            console.log(data);
        },
        complete: function(){
        },
    });
}