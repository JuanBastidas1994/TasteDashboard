/*TEMPLATES EN EL MISMO ARCHIVO*/
var templateloading = Handlebars.compile($("#loading-data").html()); //DEBEN SER CREADOS CON LA ETIQUETA SCRIPT
var templateEspera = Handlebars.compile($("#wait-for-data").html());
var templatenodata = Handlebars.compile($("#no-data").html());
var templateCliente = Handlebars.compile($("#cliente-info").html());

$(document).ready(function() {
    $(".infoClienteFidelizacion").html(templateEspera());
});

$(".cedulaFidelizacion").on("search", function(e){
    e.preventDefault();
    $(".cedulaFidelizacion").attr("readonly","readonly");
    getClientePuntos();
});

$(".fdBtnAgregar").on("click", function(){
    acumularFidelizacionOrden();
});

$("body").on("click", ".fdBtnCancelar", function(){
    $(".infoClienteFidelizacion").html(templateEspera());
    $(".cedulaFidelizacion").val("");
    $(".cedulaFidelizacion").removeAttr("readonly");
    limpiarFormularioFidelizacion();
});

$("body").on("click", ".tabLista", function(){
    getOrdenesRunfood();
});

function getClientePuntos(){
    $(".infoClienteFidelizacion").html(templateloading());
    let cedula = $(".cedulaFidelizacion").val();
    fetch(`https://api.mie-commerce.com/pos/v2/puntos/calcular/${cedula}`,{
            method: 'GET',
            headers: {
            'Api-Key':$("#apikey_empresa").val()
            },
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            fetch(`https://api.mie-commerce.com/pos/v2/puntos/${cedula}`,{
                    method: 'GET',
                    headers: {
                    'Api-Key':$("#apikey_empresa").val()
                    },
                })
                .then(res => res.json())
                .then(response => {
                    console.log(response);
                    if(response.success == 1){
                        $(".infoClienteFidelizacion").html(templateCliente(response));
                        $(".fdBtnAgregar").removeAttr("disabled");
                        $(".fdCed").val(response.cliente.num_documento);
                        $(".fdNombres").val(response.cliente.nombre);
                    }else{
                        if(response.errorCode == "CODIGO_NO_ASIGNADO"){
                            $(".infoClienteFidelizacion").html(templatenodata({mensaje: response.mensaje}));
                            $(".fdBtnAgregar").removeAttr("disabled");
                            $(".fdCed").removeAttr("disabled");
                            $(".fdNombres").removeAttr("disabled");
                            $(".fdCed").val("");
                            $(".fdNombres").val("");
                        }else{
                            messageDone(response.mensaje,'error');
                            $(".infoClienteFidelizacion").html(templatenodata({mensaje: response.mensaje}));
                            $(".cedulaFidelizacion").val("");
                            $(".cedulaFidelizacion").removeAttr("readonly");
                        }
                    }
                })
                .catch(error=>{
                    console.log(error);
                });

        })
        .catch(error=>{
            console.log(error);
        });
}

function acumularFidelizacionOrden(){
    let cedula = $(".cedulaFidelizacion").val();
    let metodosPago = [];

    if($(".fdId").val().trim() === ""){
        messageDone('El identificador de la factura no puede ir vacío','error');
        return;
    }
    if($(".fdTotal").val().trim() === ""){
        messageDone('El total de la factura no puede ir vacío','error');
        return;
    }
    if($("#cod_sucursal").val()>0){
        messageDone('Debes escoger una sucursal','error');
        return;
    }
    if($(".fdCed").val().trim() === ""){
        messageDone('La cédula del cliente no puede ir vacía','error');
        return;
    }
    if($(".fdNombres").val().trim() === ""){
        messageDone('Los nombres del cliente no pueden ir vacíos','error');
        return;
    }

    let total = $(".fdTotal").val().trim();
    let acu = 0;
    if($(".fdEfectivo").val() > 0){
        acu = acu + parseFloat($(".fdEfectivo").val());
        metodosPago.push({
            "tipo":"E",
            "monto": $(".fdEfectivo").val(),
            "observacion": ""
        })
    }
    if($(".fdTarjeta").val() > 0){
        acu = acu + parseFloat($(".fdTarjeta").val());
        metodosPago.push({
            "tipo":"T",
            "monto": $(".fdTarjeta").val(),
            "observacion": ""
        })
    }
    if($(".fdPuntos").val() > 0){
        acu = acu + parseFloat($(".fdPuntos").val());
        metodosPago.push({
            "tipo":"P",
            "monto": $(".fdPuntos").val(),
            "observacion": ""
        })
    }

    let info = {
        "id": $(".fdId").val(),
        "scanner": cedula,
        "cod_sucursal": $("#cod_sucursal").val(),
        "subtotal": 0,
        "descuento":0,
        "envio": 0,
        "iva": 0,
        "total": $(".fdTotal").val(),
        "cliente": {
            "num_documento": $(".fdCed").val(),
            "nombres": $(".fdNombres").val()
        },
        "metodoPago":metodosPago
    };
    console.log(JSON.stringify(info));

    fetch(`https://api.mie-commerce.com/pos/v2/ordenes`,{
            method: 'POST',
            headers: {
            'Api-Key':$("#apikey_empresa").val()
            },
            body: JSON.stringify(info)
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.success == 1){
                $(".cedulaFidelizacion").removeAttr("readonly");
                //getClientePuntos();
                limpiarFormularioFidelizacion();
                messageDone(response.mensaje,'success');
            }else{
                messageDone(response.mensaje,'error');
            }
        })
        .catch(error=>{
            console.log(error);
        });
}

function limpiarFormularioFidelizacion(){
    $(".cedulaFidelizacion").removeAttr("readonly");
    $(".fdBtnAgregar").attr("disabled","disabled");
    $(".fdCed").attr("disabled","disabled");
    $(".fdNombres").attr("disabled","disabled");
    $(".fdId").val("");
    $(".fdTotal").val("");
    $(".fdEfectivo").val("");
    $(".fdTarjeta").val("");
    $(".fdPuntos").val("");
    $(".fdCed").val("");
    $(".fdNombres").val("");
}

function getOrdenesRunfood(){
    $.ajax({
       url:'controllers/controlador_gestion_ordenes.php?metodo=getOrdenesRunfood',
       type: "GET",
       success: function(response){
          console.log(response);
          if(response['success']==1){
              let data = response['data'];
              let html = "";
              for(i=0; i<data.length; i++){
                let estado = "Activo";
                if("CREADA" != data[i]['estado'])
                    estado = "ANULADA";
                html+=` <tr>
                            <td>${data[i]['id']}</td>        
                            <td>${data[i]['nombre']}</td>        
                            <td>$${data[i]['total']}</td>        
                            <td>${estado}</td>        
                            <td><a href="javascript:void(0);" class="btnAnularOrdenRunfood" data-id="${data[i]['id']}"><i data-feather="trash"></i></a></td>        
                        </tr>`;
              }
              $('.bodyOrdenesRunfood').html(html);
          }
          else{
            $('.bodyOrdenesRunfood').html('<tr><td colspan="3">No hay órdenes</td></tr>');
          }
          feather.replace();
       },
       error: function(data){
       },
       complete: function(){
       },
    });
}

$("body").on("click", ".btnAnularOrdenRunfood", function(){
    let id = $(this).data("id");
    Swal.fire({
       title: 'Anular orden',
       text: '¿Está seguro?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       confirmButtonColor: '#EB6341',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          anularOrdenRunfood(id);
       }
    }); 
});

function anularOrdenRunfood(id) {
    fetch(`https://api.mie-commerce.com/pos/v2/ordenes/anular/`+id,{
        method: 'GET',
        headers: {
            'Api-Key': $("#apikey_empresa").val()
        },
    })
    .then(res => res.json())
    .then(response => {
        console.log(response);
        if(response.success == 1){
            messageDone(response.mensaje,'success');
        }
        else{
            messageDone(response.mensaje,'error');
        }
    })
    .catch(error=>{
        console.log(error);
    });
}