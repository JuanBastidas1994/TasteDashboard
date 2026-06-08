$(document).ready(function() {
    getSucursalesCourier();
});

$("#btnActualizarInfo").on("click",function(event){
    event.preventDefault();
    var codigo=$(this).attr("data-id");
    var direccion=$("#txt_direccion").val();
    var telefono=$("#txt_telefono").val();
    var correo=$("#txt_correo").val();
    
    if(direccion=="" || telefono == "" || correo == "" ){
            alert("Debes llenar los campos obligatorios");
            return;
        }
        
        swal.fire({
              title: '¿Desea Confirmar Datos?',
              text: 'No se puede revertir los cambios',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Actualizar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
            }).then(function(result) {
              if (result.value) {
                
                var parametros = {
                    "direccion": direccion,
                    "telefono":telefono,
                    "correo": correo,
                    "codigo":codigo
                }
                console.log(parametros);
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=update_Info',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            messageDone(response['mensaje'],'success');
                            $("input[name='txt_redes[]']").each(function(indice, elemento) {
                                if($(elemento).val() != "")
                                {
                                    UpdateRedes(this.id,$(elemento).val(),codigo);    
                                }
                            });
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
});

function UpdateRedes(id,text,codigo)
{
    var parametros = {
                    "id": id,
                    "text":text,
                    "codigo":codigo
                }
                console.log(parametros);
    $.ajax({
                    url: 'controllers/controlador_configuraciones.php?metodo=update_Redes',
                    type: 'POST',
                    data: parametros,
                    headers: { 
    Accept : "application/json"
},
                    success: function(response){
                        console.log(response);
                    },
                    error: function(data){
                        console.log(data);
                        
                    }
                });
}
    

 $("#btnActualizarCostoEnvio").on("click",function(event){
        event.preventDefault();
        var codigo=$(this).attr("data-id");
        var base_dinero=$("#base_dinero").val()
        var base_km=$("#base_km").val()
        var adicional_km=$("#adicional_km").val()

        if($("#base_dinero").val().trim().length == 0 || $("#base_km").val().trim().length == 0 || $("#adicional_km").val().trim().length == 0){
            alert("Debes llenar todos los campos");
            return;
        }

        if(base_dinero<=0 || base_km<=0 || adicional_km<=0)
        {
            messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
        }
        else
        {
            swal.fire({
              title: '¿Estas seguro?',
              text: 'No se puede revertir los cambios',
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Actualizar',
              cancelButtonText: 'Cancelar',
              padding: '2em'
            }).then(function(result) {
              if (result.value) {
                
                var parametros = {
                    "base_dinero": $("#base_dinero").val().trim(),
                    "base_km": $("#base_km").val().trim(),
                    "codigo": codigo,
                    "adicional_km": $("#adicional_km").val().trim()
                }
                console.log(parametros);
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Buscando informacion, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=update_costo_envio',
                    type: 'GET',
                    data: parametros,
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


              }
            });
        }


     });

$(".btnFidelizacion").on("click",function(event){
    var codigo=$(this).attr("data-id");
    var divisor=$("#txt_divisor_puntos").val();
    var puntos=$("#txt_monto_puntos").val();

    if(divisor<=0 || puntos<=0)
    {
        messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
    }
    else
    {

     swal.fire({
          title: '¿Estas seguro?',
          text: 'No se puede revertir los cambios',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Actualizar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
                "codigo": codigo,
                "divisor": divisor,
                "puntos": puntos
            }
          
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=update_fidelizacion',
                type: 'GET',
                data: parametros,
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


          }
        });
    }
});

$(".btnNiveles").on("click",function(event){
    var codigo=$(this).attr("data-id");
    var nombre=$("#txt_nombre"+codigo).val();
    var inicio=$("#txt_inicio"+codigo).val();
    var fin=$("#txt_fin"+codigo).val();
    var monto=$("#txt_monto"+codigo).val();

    if(inicio<0 || fin<=0  || monto<=0)
    {
        messageDone("Los valores no son los correctos, vuelva a ingresarlos",'error');
    }
    else
    {

     swal.fire({
          title: '¿Estas seguro?',
          text: 'No se puede revertir los cambios',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Actualizar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
                "codigo": codigo,
                "nombre": nombre,
                "inicio": inicio,
                "fin": fin,
                "monto": monto
            }
          
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                },
                url: 'controllers/controlador_configuraciones.php?metodo=update_niveles',
                type: 'GET',
                data: parametros,
                success: function(response){
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


          }
        });
    }
});


    $("#chk_envioIva").on("change", function(){
        let checkb = $(this);
        let cod_empresa = checkb.data("empresa");
        let grava = 0;
        if(checkb.is(":checked"))
            grava = 1;
        else
            grava = 0;
        
        var parametros = {
            "cod_empresa": cod_empresa,
            "grava": grava
        }
        $.ajax({
           url:'controllers/controlador_empresa.php?metodo=setGravaIva',
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
    });
    
    
/*FUNCIONES CUMPLE*/
var resize = null;
$("#image_cumple").on("change", function(){
    if (resize != null)
        resize.destroy();

    if(this.files.length > 0){
        $("#modalCroppie").modal({
            closeExisting: false,
            backdrop: 'static',
            keyboard: false,
        });
    }
});

$('#modalCroppie').on('shown.bs.modal', function() {
    var aux = $("#image_cumple").get(0);
    var file = aux.files[0];
    var reader = new FileReader();
    reader.onload = function (e) { 
      $('#my-image').attr('src', e.target.result);
      
      resize = new Croppie($('#my-image')[0], {
        viewport: { width: 500, height: 500 }, //tamaño de la foto que se va a obtener
        boundary: { width: 600, height: 600 }, //la imagen total
        showZoomer: true, // hacer zoom a la foto
        enableResize: false,
        enableOrientation: true, // para q funcione girar la imagen 
        mouseWheelZoom: 'ctrl'
      });
      $('#crop-get').on('click', function() { // boton recortar
        resize.result({type: 'base64', size: 'viewport', format : 'jpeg', quality: 0.8, backgroundColor: '#FFFFFF'}).then(function(dataImg) {
          uploadImageCumple(dataImg);
        });
      });
      $('.crop-rotate').on('click', function(ev) {
        resize.rotate(parseInt($(this).data('deg')));
      });

      
    } 
    reader.readAsDataURL(file);
});

function uploadImageCumple(base64){
    let parametros = {
        crop: base64
    }
    console.log(parametros);
    $.ajax({
        beforeSend: function(){
            OpenLoad("Subiendo Imagen Cumpleaños, por favor espere...");
         },
        url: 'controllers/controlador_configuraciones.php?metodo=UploadImageCumple',
        type: 'POST',
        data: parametros,
        success: function(response){
            console.log(response);
            if( response['success'] == 1){
                $("#imgCumple").attr("src",base64);
                $("#modalCroppie").modal('hide');
                messageDone(response['mensaje'],'success');
            }
            else{
              messageDone(response['mensaje'],'error');
            }                  
        },
        error: function(data){
          console.log(data);
        },
        complete: function(){
          CloseLoad();
        }
    });
}

$(".actualizarCumple").on("click",function(event){
    swal.fire({
        title: '¿Estas seguro?',
        text: 'No se puede revertir los cambios',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        padding: '2em'
      }).then(function(result) {
            if (result.value) {
                var parametros = {
                    "monto": $("#txt_monto_cumple").val(),
                    "dias": $("#txt_dias_cumple").val(),
                    "restriccion": $("#txt_restriccion_cumple").val()
                }
                
                $.ajax({
                    beforeSend: function(){
                        OpenLoad("Buscando informacion, por favor espere...");
                    },
                    url: 'controllers/controlador_configuraciones.php?metodo=updateFidelizacionCumple',
                    type: 'GET',
                    data: parametros,
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
            }
        });
});


$("#chk_img_cumple").on("change", function(){
    let checkb = $(this);
    let estado = 'I';
    if(checkb.is(":checked"))
        estado = 'A';

    var parametros = {
        "estado": estado
    }
    $.ajax({
       url:'controllers/controlador_configuraciones.php?metodo=setEstadoImgCumple',
       data: parametros,
       type: "GET",
       beforeSend: function(){
            OpenLoad("Actualizando estado, por favor espere...");
        },
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
            console.log(data);
       },
       complete: function(){
            CloseLoad();
       },
    });
});

$("body").on("click", ".btnCaducidad", function(){
    let cod_empresa = $(this).data("empresa");
    let fechaPuntos = $("#txt_cdPuntos").val();
    let fechaDinero = $("#txt_cdDinero").val();
    let fechaSaldo = $("#txt_cdSaldo").val();
    
    if(fechaPuntos == ""){
        messageDone("Ingrese el tiempo de caducidad de los puntos", "error");
        return;
    }
    if(fechaDinero == ""){
        messageDone("Ingrese el tiempo de caducidad del dinero", "error");
        return;
    }
    if(fechaSaldo == ""){
        messageDone("Ingrese el tiempo de caducidad del saldo", "error");
        return;
    }
    swal.fire({
       title: 'Actualizar los tiempos',
       text: '¿Desea continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          actualizarFechasCaducidad(cod_empresa);
       }
    }); 
});

function actualizarFechasCaducidad(cod_empresa){
    let fechaPuntos = $("#txt_cdPuntos").val();
    let fechaDinero = $("#txt_cdDinero").val();
    let fechaSaldo = $("#txt_cdSaldo").val();
    let parametros = {
        "cod_empresa": cod_empresa,
        "fechaPuntos": fechaPuntos,
        "fechaDinero": fechaDinero,
        "fechaSaldo": fechaSaldo
    }
    
    $.ajax({
       url:'controllers/controlador_configuraciones.php?metodo=actualizarFechasCaducidad',
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

$("#btnPermisoTienda").on("click", function(){
    swal.fire({
       title: 'Cambiar permiso',
       text: '¿Desea continuar?',
       type: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Aceptar',
       cancelButtonText: 'Cancelar',
       padding: '2em'
    }).then(function(result){
       if (result.value) {
          permisoTienda();
       }
    }); 
});

function permisoTienda(){
    let checkT = $("#ck_permisoTienda");
    let encender = 0;

    if(checkT.is(":checked"))
        encender = 1;
    
    let parametros = {
    "encender": encender
    }
    $.ajax({
       url:'controllers/controlador_configuraciones.php?metodo=permisoTienda',
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

$("#frmConfigTransporte").on('submit', function(event){
    event.preventDefault()
    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: $(this).serialize(),
        beforeSend: () => {
            OpenLoad("Editando datos, por favor espere...");
        },
        success: (response) => {
            notify(response['mensaje'],'success', 3)

        },
        error: (response) => {
            notify(response['mensaje'],'error', 3)

        },
        complete: () => {
            CloseLoad()
        }
    })
});




$("body").on("click", "#btnActualizarImpuesto", function () {
    var cod_empresa = $("#cod_empresa_imp").val();
    var impuesto    = parseInt($("#txt_impuesto_conf").val());
    var tipo        = $("#cmb_tipo_impuesto").val();

    if (isNaN(impuesto) || impuesto < 0 || impuesto > 100) {
        messageDone("Ingrese un porcentaje válido entre 0 y 100", "error");
        return;
    }

    swal.fire({
        title: "¿Actualizar el impuesto?",
        text: "Esta acción actualizará el impuesto y recalculará los precios de todos los productos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, actualizar",
        cancelButtonText: "Cancelar"
    }).then(function (result) {
        if (!result.value) return;
        $.ajax({
            beforeSend: function () { OpenLoad("Actualizando impuesto..."); },
            url: "controllers/controlador_empresa.php?metodo=actualizarImpuesto",
            type: "GET",
            data: { cod_empresa: cod_empresa, impuesto: impuesto, tipo: tipo },
            success: function (response) {
                messageDone(response["mensaje"], response["success"] == 1 ? "success" : "error");
            },
            error: function () { messageDone("Error al conectar con el servidor", "error"); },
            complete: function () { CloseLoad(); }
        });
    });
});

$("body").on("click", "#btnGuardarCartConfig", function () {
    var cart_minutes       = parseInt($("#txt_cart_minutes").val());
    var cart_expiry        = parseInt($("#txt_cart_expiry").val());
    var campaigns_enabled  = $("#chk_campaigns_enabled").is(":checked") ? 1 : 0;
    var campaign_interval  = parseInt($("#txt_campaign_interval").val());
    var campaign_max       = parseInt($("#txt_campaign_max").val());
    var recovery_email     = $("#chk_recovery_email").is(":checked") ? 1 : 0;
    var recovery_push      = $("#chk_recovery_push").is(":checked") ? 1 : 0;
    var recovery_whatsapp  = $("#chk_recovery_whatsapp").is(":checked") ? 1 : 0;

    if (isNaN(cart_minutes) || cart_minutes <= 0) {
        messageDone("Ingrese un tiempo de abandono válido (mayor a 0)", "error");
        return;
    }
    if (isNaN(cart_expiry) || cart_expiry <= 0) {
        messageDone("Ingrese un tiempo de expiración válido (mayor a 0)", "error");
        return;
    }

    swal.fire({
        title: "¿Guardar configuración de carrito?",
        text: "Se actualizarán los tiempos y canales de recuperación.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, guardar",
        cancelButtonText: "Cancelar"
    }).then(function (result) {
        if (!result.value) return;
        $.ajax({
            beforeSend: function () { OpenLoad("Guardando..."); },
            url: "controllers/controlador_empresa.php?metodo=saveCartConfig",
            type: "GET",
            data: {
                cart_minutes:      cart_minutes,
                cart_expiry:       cart_expiry,
                campaigns_enabled: campaigns_enabled,
                campaign_interval: isNaN(campaign_interval) ? 60 : campaign_interval,
                campaign_max:      isNaN(campaign_max)      ? 3  : campaign_max,
                recovery_email:    recovery_email,
                recovery_push:     recovery_push,
                recovery_whatsapp: recovery_whatsapp
            },
            success: function (response) {
                messageDone(response["mensaje"], response["success"] == 1 ? "success" : "error");
            },
            error: function () { messageDone("Error al conectar con el servidor", "error"); },
            complete: function () { CloseLoad(); }
        });
    });
});

$("body").on("click", ".btnEditarNombreFP", function () {
    $(this).hide();
    $(this).parents(".rowNombreFP").find(".nombreFP").attr("disabled", false);
    $(this).next(".btnGuardarNombreFP").show();
});

$("body").on("click", ".btnGuardarNombreFP", function () {
    let nombre = $(this).parents(".rowNombreFP").find(".nombreFP").val();
    let btnData = $(this).data("fp");

    if (nombre == "") {
        notify("Ingrese nombre de la forma de pago", "error", 2);
        return;
    }

    let data = {
        cod_empresa_forma_pago: btnData.id,
        nombre
    }
    
    $.ajax({
        url: 'controllers/controlador_configuraciones.php?metodo=setNombreFormaPago',
        data,
        type: "POST",
        headers: {
            Accept: 'application/json'
        },
        success: function (response) {
            console.log(response);
            if (response['success'] == 1) {
                messageDone(response.mensaje, "success");
            }
            else {
                messageDone(response.mensaje, "error");
            }
        },
        error: function (data) {
            console.log(data);
        },
        complete: function () {
        },
    });
});

// ── Tab Histórico: colapso anual de información ───────────────────────────────
function cargarEstadoColapso() {
    $.ajax({
        url: "controllers/controlador_colapso_historico.php?metodo=getEstadoColapso",
        type: "POST",
        success: function (response) {
            if (response["success"] == 1) {
                var d = response["data"];
                $("#aniosMensual").text(d.mensual.length    ? d.mensual.join(", ")    : "Sin datos");
                $("#aniosAnual").text(d.anual.length        ? d.anual.join(", ")      : "Sin datos");
                $("#aniosPendientes").text(d.pendientes.length ? d.pendientes.join(", ") : "Ninguno");
            }
        },
        error: function () { messageDone("Error al conectar con el servidor", "error"); }
    });
}

$(document).on("shown.bs.tab", 'a[href="#tab-historico"]', function () {
    cargarEstadoColapso();
});

$("#btnEjecutarColapso").on("click", function () {
    swal.fire({
        title: "¿Ejecutar colapso anual?",
        text: "Se generará el resumen anual de los años pendientes a partir de la información detallada. Esta acción puede tardar unos segundos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, ejecutar",
        cancelButtonText: "Cancelar"
    }).then(function (result) {
        if (!result.value) return;

        $.ajax({
            beforeSend: function () { OpenLoad("Ejecutando colapso anual..."); },
            url: "controllers/controlador_colapso_historico.php?metodo=ejecutarColapso",
            type: "POST",
            success: function (response) {
                messageDone(response["mensaje"], response["success"] == 1 ? "success" : "error");
                if (response["success"] == 1) cargarEstadoColapso();
            },
            error: function () { messageDone("Error al conectar con el servidor", "error"); },
            complete: function () { CloseLoad(); }
        });
    });
});

// ── Tab Mesa: tipo de identificación de mesa ───────────────────────────────
$("#btnGuardarMesaTipo").on("click", function () {
    var mesa_tipo = $("#sel_mesa_tipo").val();
    OpenLoad();
    $.ajax({
        url: "controllers/controlador_configuraciones.php?metodo=guardarMesaTipo",
        type: "POST",
        data: { mesa_tipo: mesa_tipo },
        success: function (response) {
            messageDone(response["mensaje"], response["success"] == 1 ? "success" : "error");
        },
        error: function () { messageDone("Error al conectar con el servidor", "error"); },
        complete: function () { CloseLoad(); }
    });
});
