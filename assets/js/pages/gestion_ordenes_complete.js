$(document).ready(function() {
  getFestivoHoy();
    const alias = Cookies.get('alias');
    console.log(alias);
    var sonido = {};
    var recordatorio = {};
    var sounds = [
        {name: "sirena1"},
        {name: "gallo"},
        {name: "dragon-ball"},
        {name: "latigazo"},
        {name: "vida-mario-bros"},
        {name: "sonido-whatsapp"},
        {name: "correcaminos"}
      ];
    var printer = {};

    for(var x=0; x<sounds.length; x++){
      $("#cmbSonido").append('<option value="'+sounds[x]['name']+'">'+sounds[x]['name']+'</option>');
    }
    //console.log(Cookies.get());

    /*CONFIG SONIDO*/
    if (JSON.parse(localStorage.getItem('sonido')) === null) {
      sonido.sirena = "sirena1";
      sonido.repeat = true;
      localStorage.setItem('sonido', JSON.stringify(sonido));
    }else{
      sonido = JSON.parse(localStorage.getItem('sonido'));
    }

    /*CONFIG PUERTO IMPRESORA*/
    if (JSON.parse(localStorage.getItem('printer')) === null) {
      printer.puerto = "8890";
      localStorage.setItem('printer', JSON.stringify(printer));
    }
    else{
      printer = JSON.parse(localStorage.getItem('printer'));
      $("#txtPuerto").val(printer.puerto);
    }

    /*CONFIG RECORDATORIO*/
    if (JSON.parse(localStorage.getItem('recordatorio')) === null) {
      recordatorio.tiempo = 5;
      recordatorio.permiso = $("#permisoRecordarOrdenes").val();
      recordatorio.asignacion = 0;
      localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
    }else{
      recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    }

    /*
    const ps = new PerfectScrollbar('.message-box-scroll');
    const mailScroll = new PerfectScrollbar('.mail-sidebar-scroll', {
      suppressScrollX : true
    });

    function mailInboxScroll() {
      $('.detalleOrden').each(function(){ 
        console.log($(this)[0]);
        const mailContainerScroll = new PerfectScrollbar($(this)[0], {
        suppressScrollX : true
        }); 
      });
    }
    mailInboxScroll();*/

    // Open Mail Sidebar on resolution below or equal to 991px.
    $('.mail-menu').on('click', function(e){
      $(this).parents('.mail-box-container').children('.tab-title').addClass('mail-menu-show')
      $(this).parents('.mail-box-container').children('.mail-overlay').addClass('mail-overlay-show')
    });

    // Close sidebar when clicked on ovelay ( and ovelay itself ).

    $('.mail-overlay').on('click', function(e){
      $(this).parents('.mail-box-container').children('.tab-title').removeClass('mail-menu-show')
      $(this).removeClass('mail-overlay-show')
    });

    /*CLICK EN LOS FILTROS POR ESTADO*/
    $(".list-actions").on("click", function(){
      //$('.content-box .collapse').collapse('hide');
      var estado = this.id;
      var tipo = $("#is_envio").val();
      $(".list-actions").removeClass("active");
      $(this).addClass("active");
        var parametros = {
          "estado": estado,
          "tipo": tipo,
          "cod_sucursal": $("#cmbSucursal").val()
        }
        console.log(parametros);
        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando ordenes, por favor espere...");
             },
            url: 'controllers/controlador_gestion_ordenes.php?metodo=lista',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {
                    $('.content-box').css({
                      width: '0',
                      left: 'auto',
                      right: '-46px'
                    });
                  $("#lista_ordenes").html(response['html']);
                } 
                else
                {
                  $('.content-box').css({
                      width: '0',
                      left: 'auto',
                      right: '-46px'
                    });
                  $("#lista_ordenes").html('');
                  //messageDone(response['mensaje'],'error');
                } 
                feather.replace();
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
    
    var tipo = $(".changeTipo").attr("data-value");
    showEstados(tipo);  

    /*CAMBIAR DE TIPO - ENVIOS y PICKUP*/
    $(".changeTipo").on("click", function(){
        
        $(".changeTipo").removeClass("active");
        $(this).addClass("active");
        var tipo = $(this).attr("data-value");
          showEstados(tipo);  
        $(".list-actions#ENTRANTE").trigger('click');
    });
    
    function showEstados(tipo)
    {
        if(tipo == "E"){
            $("#is_envio").val(1);
            $("#PREPARANDO").hide();
            $("#ASIGNADA").show();
            $("#ENVIANDO").show();
            $("#NO_ENTREGADA").show();
        }else if(tipo == "P")
        {
            $("#is_envio").val(0);
            $("#PREPARANDO").show();
            $("#ASIGNADA").hide();
            $("#ENVIANDO").hide();
            $("#NO_ENTREGADA").hide();
        }
        else{
          $("#is_envio").val(2);
            $("#PREPARANDO").show();
            $("#ASIGNADA").show();
            $("#ENVIANDO").show();
            $("#NO_ENTREGADA").show();
        }
    }

    /*CLICK EN LAS ORDENES - ABRIR UNA ORDEN*/
    $('body').on('click',".mail-item", function(event) {
      var id = $(this).attr("data-value");
      $(this).removeClass("unread-mail");
      openOrdenDetalle(id);
    });

    function openOrdenDetalle(id){
      let d = new Date();
      fecha = new Date(d.getFullYear(), d.getMonth(), d.getDate());
      console.log("fecha js", fecha);
      var parametros = {
        "id": id
      }
      $.ajax({
          beforeSend: function(){
              OpenLoad("Buscando ordenes, por favor espere...");
           },
          url: 'controllers/controlador_gestion_ordenes.php?metodo=get_orden',
          type: 'GET',
          data: parametros,
          success: function(response){
              console.log("JS",response);
              if( response['success'] == 1)
              {
                $("#orden").html(response['html']);
                //$("#ordenV2").html(response['htmlV2']);
                
                let tipoEmpresa = $("#tipo_empresa").val();
                if(tipoEmpresa == 1){
                    let fechaOrden = response['fecha'];
                    fechaOrden = new Date(fechaOrden.replace('-', ','));
                    if(fechaOrden < fecha){
                      let infoAnular = $(".infoAnular");
                      $(".formaAsignacion").html("");
                      $(".formaAsignacion").html(infoAnular);
                      $(".pedidoAntiguo").show();
                    }
                }
                
                
                feather.replace();
                if($("#hora_ini").length){
                  var picker = document.getElementById("hora_ini");
                  flatpickr(picker, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i"
                  });
                }

                if($("#mapa").length){
                  var mapa = document.getElementById("mapa");
                  var latitud = parseFloat(mapa.getAttribute("data-latitud"));
                  var longitud = parseFloat(mapa.getAttribute("data-longitud"));
                  pos = {lat: latitud, lng: longitud};
                  var map = new google.maps.Map(mapa, {
                    zoom: 14,
                    center: pos
                  });

                  marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    id: 15
                  });
                }
                

                //mailInboxScroll();
                $('.content-box').css({
                  width: '100%',
                  left: '0',
                  right: '100%'
                });

                if($("#modalBusqueda").length > 0){
                  $("#modalBusqueda").modal("hide");
                }
              } 
              else
              {
                //$("#lista_ordenes").html('');
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

    /*CERRAR LA ORDEN ABIERTA*/
    $('.close-message').on('click', function(event) {
      event.preventDefault();
      //$('.content-box .collapse').collapse('hide');
      $('.content-box').css({
        width: '0',
        left: 'auto',
        right: '-46px'
      });
    });

   
    $(".content-box").on("click",".btn-asignaciongacela",function(){
        var $this = $(this);
        var id = $this.attr("data-value");
        var parametros = {
          id: id
        }
        
        swal({
          title: 'Estas seguro?',
          text: "Desea confirmar el metodo de asignacion con Gacela",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirmar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
            if (result.value) {
            
                 $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_gestion_ordenes.php?metodo=asignarGacela',
              type: 'POST',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    $("#lbl-g").html("Cancelar");
                    $("#cmbForma").attr("disabled","disabled");
                    $("#asignaciongacela").hide();
                    $("#cancelargacela").show();
                    
                    $(".infoAsignacion").hide();
                    $(".detailAction").html("<h3 class='alert-heading'>Asignaci&oacute;n Realizada</h3>");
                    $(".detailAction").show();
                    
                    notify(response['mensaje'], "success", 2);
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
    
    //NUEVA VERSION: ya no es por campo "is_gacela" sera por "cod_courier"
    $(".content-box").on("click",".btn-asignacioncourier",function(){
        var $this = $(this);
        ion.sound.stop();
        var id = $this.attr("data-value");
        var courier = $("#cmbFormaCourier").val();
        var textCourier = $("#cmbFormaCourier option:selected").html();
        var metodo = "";
        var parametros = {
            id: id
        };
        
        /*-----------------------*/
        if(courier == 0){
            messageDone("Debe seleccionar un motorizado",'error');
            return;
        }
        else{
            if(courier == 1){
                metodo='asignarGacelaV2';
            }else if(courier == 2){
                metodo='asignarLaar';
            }else if(courier == 3){
              metodo='asignarPicker';
            }else if(courier == 4){
              metodo='asignarInlog';
            }
        }
        
        if(metodo == ""){
            messageDone("Metodo no configurado..",'error');
            return;
        }
        
        /*-----------------------*/
        
        swal({
          title: 'Estas seguro?',
          text: "Desea confirmar el metodo de asignacion con "+textCourier,
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirmar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
            if (result.value) {
            
                 $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: "controllers/controlador_gestion_ordenes.php?metodo="+metodo,
              type: 'POST',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    $("#lbl-g").html("Cancelar");
                    $("#cmbFormaCourier").attr("disabled","disabled");
                    $("#asignacioncourier").hide();
                    $("#cancelarcourier").show();
                    
                    $(".infoAsignacion").hide();
                    $(".detailAction").html("<h3 class='alert-heading'>Asignaci&oacute;n Realizada</h3>");
                    $(".detailAction").show();
                    
                    notify(response['mensaje'], "success", 2);
                    
                    if(metodo=='asignarLaar'){
                      enviarCorreoLaarAsignacion(id);
                    }
                    sendFacturaElectronica(id);
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
    
    $(".content-box").on("click",".btn-cancelarcourier",function(){
        var $this = $(this);
        var id = $this.attr("data-value");
        var estado = "CANCELADA";
        var parametros = {
            cod_orden: id,
            estado: estado,
        }
        var textCourier = $("#cmbFormaCourier option:selected").html();
        
            swal({
          title: 'Estas seguro?',
          text: "Desea cancelar el metodo de asignacion con "+textCourier,
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirmar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
            if (result.value) {
            
                 $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_gestion_ordenes.php?metodo=set_estado',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    db.ref('ordenes/'+alias+'/'+id).set({
                      id: id,
                      estado: estado,
                      sucursal: 0
                    });
                    
                    $(".infoAsignacion").hide();
                    $(".detailAction").html("<h3 class='alert-heading'>Asignaci&oacute;n Cancelada</h3>");
                    $(".detailAction").show();
                    
                    notify(response['mensaje'], "success", 2);
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
    
     $(".content-box").on("click",".btn-cancelargacela",function(){
        var $this = $(this);
        var id = $this.attr("data-value");
        var estado = "CANCELADA";
        var parametros = {
            cod_orden: id,
            estado: estado,
        }
        
            swal({
          title: 'Estas seguro?',
          text: "Desea cancelar el metodo de asignacion con Gacela",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Confirmar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
            if (result.value) {
            
                 $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_gestion_ordenes.php?metodo=set_estado',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    db.ref('ordenes/'+alias+'/'+id).set({
                      id: id,
                      estado: estado,
                      sucursal: 0
                    });
                    
                    $(".infoAsignacion").hide();
                    $(".detailAction").html("<h3 class='alert-heading'>Asignaci&oacute;n Cancelada</h3>");
                    $(".detailAction").show();
                    
                    notify(response['mensaje'], "success", 2);
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

    $(".content-box").on("click",".btn-asignacion",function(){
        var $this = $(this);
        ion.sound.stop();
        var info = $this.parents(".infoAsignacion");
        var id = $this.attr("data-value");
        var motorizado = info.find(".cmbMotorizado").val();
        var hora = info.find(".hora_partida").val();

        var parametros = {
          id: id,
          motorizado: motorizado,
          hora: hora
        }
        console.log(parametros);
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_gestion_ordenes.php?metodo=asignar',
              type: 'POST',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    console.log('ordenes/'+alias+'/'+id);
                    
                    //ENVIAR NOTIFICACION
                    $.ajax({
                      url: 'controllers/controlador_notificaciones.php?metodo=notificarOrden&orden='+id,
                      type:'GET',
                    });
                    
                    db.ref('ordenes/'+alias+'/'+id).set({
                      id: id,
                      estado: "ASIGNADA",
                      sucursal: 0
                    });
                    
                    $(".infoAsignacion").hide();
                    $(".detailAction").html("<h3 class='alert-heading'>Asignaci&oacute;n Realizada</h3>");
                    $(".detailAction").show();
                    
                    notify(response['mensaje'], "success", 2);
                    sendFacturaElectronica(id);
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

    $(".content-box").on("click",".btn-asignacion-pickup",function(){
      
        var $this = $(this);
        var id = $this.attr("data-value");
        var estado = $this.attr("data-estado");
        let cod_usuario = $this.attr("data-usuario");
        console.log("El usuario es", cod_usuario);
        
        if($("#txt_comentario_cambio_estado").val() == "" && estado == "CANCELADA"){
            messageDone("Agregue un motivo de anulación", "error");
            return;
        }
        
        var titulo = "¿Estas seguro?";
        var texto = "Se cambiara el estado de la orden a " + estado;
        if(estado == "CANCELADA" || estado == "ANULADA"){
            titulo = "¿Estás seguro de Cancelar la orden?";
            texto = "Si el pago fue con tarjeta se intentará revertir el pago y se revertirán los puntos en caso haber obtenido por esta compra";
        }
        
        swal({
          title: titulo,
          text: texto,
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
            if (result.value) {
              /*AJAX*/
              var parametros = {
                cod_orden: id,
                estado: estado,
                comentario: $("#txt_comentario_cambio_estado").val()
              }
              $.ajax({
                    beforeSend: function(){
                        OpenLoad("Guardando datos, por favor espere...");
                     },
                    url: 'controllers/controlador_gestion_ordenes.php?metodo=set_estado',
                    type: 'GET',
                    data: parametros,
                    success: function(response){
                        console.log(response);
                        if( response['success'] == 1)
                        {
                            db.ref('ordenes/'+alias+'/'+id).set({
                                id: id,
                                estado: estado,
                                sucursal: 0
                            });
                          
                            //ENVIAR NOTIFICACION
                            $.ajax({
                              url: 'controllers/controlador_notificaciones.php?metodo=notificarOrden&orden='+id,
                              type:'GET',
                              success: function(data){
                                  console.log(data);
                              },
                              error: function(data){
                                  console.log(data);
                              }
                            });
                          
                            $(".infoAsignacion").hide();
                            $(".detailAction").html("<h3 class='alert-heading'>ORDEN "+estado+"</h3>");
                            $(".detailAction").show();
                            notify(response['mensaje'], "success", 2);
                            if(estado == "CANCELADA" || estado == "ANULADA"){
                                revertirPago(id, cod_usuario);
                                revertirFactura(id);
                            }
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
              /*AJAX*/
            }  
        });
        
     }); 
     
     
     function revertirPago(orden, cod_usuario){
       console.log("fue a revertir");
       let motivo = $("#txt_comentario_cambio_estado").val();
         $.ajax({
          url: 'controllers/controlador_gestion_ordenes.php?metodo=revertir_pago&cod_orden='+orden,
          type:'GET',
          success: function(response){
              console.log("----- REVERTIR PAGO ------");
              console.log(response);
              let alias_empresa = $("#alias_empresa").val();
              console.log(alias_empresa, cod_usuario, motivo);
              $.ajax({
                  url:'https://api.mie-commerce.com/v2/correos/orden_anulada.php?alias='+alias_empresa+'&id='+cod_usuario+'&motivo='+motivo,
                  type: "GET"
              });
              if(response['success'] == 1)
                notify(response['mensaje'], "success", 2);
              else
                notify(response['mensaje'], "error", 2);
          },
          error: function(data){
              notify("Error al anular el pago de tarjeta de credito, intenta nuevamente", "error", 2);
          }
        });
     }

     function revertirFactura(cod_orden){
          if($("#fact_electronica").length){
              if($("#fact_electronica").val() == 0){
                  return false;
              }
          }else{
              return false;
          }
          
          var urlController = "";
          var fact_electronica = $("#fact_electronica").val();
          if(fact_electronica == 1){      //CONTIFICO
            urlController = 'controllers/controlador_contifico.php?metodo=anularFactura&id='+cod_orden;
          }if(fact_electronica == 2){     //FACT. MOVIL
              urlController = 'controllers/controlador_fact_movil.php?metodo=anularFactura&id='+cod_orden;
          }
          
          console.log("ANULAR FACTURA ELECTRONICA");
              $.ajax({
                  url: urlController,
                  type:'GET',
                  beforeSend: function(){
                      notify('Anulando factura electronica', "warning", 2);
                  },success: function(response){
                      console.log(response);
                      if(response['success']==1)
                        notify(response['mensaje'], "success", 2);
                      else
                        notify(response['mensaje'], "error", 2);
                  },error: function(data){
                      notify(response['mensaje'], "error", 2);
                      console.log(data);
                  }
              });
      }

    
    /*NOTIFICAR A MOTORIZADO*/
    $(".content-box").on("click",".btnNotifOrdenLista",function(){
        var $this = $(this);
        var orden = $this.data("value");

        //ENVIAR NOTIFICACION
        $.ajax({
          url: 'controllers/controlador_notificaciones.php?metodo=notificarPedidoListo&orden='+orden,
          type:'GET',
          success: function(response){
              console.log(response);
              notify(response['mensaje'], "success", 2);
          },
          error: function(data){
              notify("Error al enviar la notificacion, intenta nuevamente", "error", 2);
          }
        });

    });

    /*NOTIFICAR A CLIENTE*/
    $("body").on("click",".btnEnviarRecordatorio",function(event){    
        var $this = $(this);
        var usuario = $this.data("usuario");
        var texto = $(".textRecordatorio").val();
        var tipo = $("#cmbTipoNotificacion").val();
        var tituloTipo = $("#cmbTipoNotificacion option:selected").text();
        if(texto == "" || texto == null){
          messageDone("La notificación no puede estar vacía", "error");
          return;
        }
        notifyClient(usuario,texto,tipo,tituloTipo);   
    });
    
    function notifyClient(usuario,texto,tipo,tituloTipo)    
    {
        //ENVIAR NOTIFICACION
        $.ajax({
          url: 'controllers/controlador_notificaciones.php?metodo=notificarUsuario&usuario='+usuario+'&texto='+texto+'&tipo='+tipo+'&tituloTipo='+tituloTipo,
          type:'GET',
          success: function(response){
              console.log(response);
              messageDone(response['mensaje'], "success");
              $("#modalNotificar").modal("hide");
              $("#txt_descripcion").val("");
              $("#cmbTipoNotificacion").val("");
              $(".emojionearea-editor").html("");
          },
          error: function(data){
              messageDone("Error al enviar la notificacion, intenta nuevamente", "error");
          }
        });
    }

    /*CAMBIAR ESTADO A LA ORDEN MANUALMENTE*/
    $(".content-box").on("click",".btn-estado",function(){
        var $this = $(this);
        var id = $this.data("value");
        var estado = $this.data("estado");
        
        /*BUSCAR UNA SOLA VEZ
        db.ref('ordenes/'+id).on('value', function(data){
          console.log("PREGUNTANDO POR UNA ORDEN");
          console.log(data.val);
        });*/

        var parametros = {
          cod_orden: id,
          estado: estado
        }
        console.log(parametros);
        $.ajax({
              beforeSend: function(){
                  OpenLoad("Guardando datos, por favor espere...");
               },
              url: 'controllers/controlador_gestion_ordenes.php?metodo=set_estado',
              type: 'GET',
              data: parametros,
              success: function(response){
                  console.log(response);
                  if( response['success'] == 1)
                  {
                    db.ref('ordenes/'+alias+'/'+id).set({
                      id: id,
                      estado: estado,
                      sucursal: 0
                    });
                    
                    if(estado == "ENTREGADA")
                    {
                        $(".btn-estado").hide();
                    }
                    else
                    {
                        $this.hide();
                    }
                    $(".detailAction").html("<h3 class='alert-heading'>"+$this.html()+"</h3>");
                    $(".detailAction").show();
                    notify(response['mensaje'], "success", 2);

                    //ENVIAR NOTIFICACION
                    $.ajax({
                      url: 'controllers/controlador_notificaciones.php?metodo=notificarOrden&orden='+id,
                      type:'GET',
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
    });

    setTimeout(function() {
        $(".list-actions#ENTRANTE").trigger('click');
    },10);

    /*IMPRIMIR*/
    $("#btnPrinterConf").on("click", function(){
      let puerto = $("#txtPuerto").val();
      if(puerto == ""){
        messageDone("Debe agregar un puerto", "error");
        return;
      }
      if(!Number.isInteger(puerto) && puerto < 0){
        messageDone("Debe agregar un puerto válido entero positivo", "error");
        return;
      }
      let printer = {};
      printer.puerto = puerto;
      localStorage.setItem('printer', JSON.stringify(printer));
      messageDone("Puerto cambiado correctamente", "success");
    });
    
    function printOrden(id){
        let printer = JSON.parse(localStorage.getItem('printer'));
       var parametros = {
          "id": id
        }
        $.ajax({
            beforeSend: function(){
                //OpenLoad("Buscando ordenes, por favor espere...");
             },
            url: 'controllers/controlador_gestion_ordenes.php?metodo=get',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                if( response['success'] == 1)
                {
                  console.log(JSON.stringify(response['data']));
                  $.ajax({ 
                    type: "POST",
                    url: "http://localhost:"+printer.puerto+"/print",
                    dataType: "json",
                    data: JSON.stringify(response['data']),
                    success: function(response){
                      console.log(response);
                    },error: function(data){
                      console.log("Servicio de impresion apagado");
                      notify("Error: Verifica el servicio de impresion", "error", 2);
                      console.log(data);
                    }
                  });
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
    
    $("body").on("click", ".printOrden", function(){
      printOrden($(this).data("value"));
    });
    
    /*UPDATE NEW ORDERS*/
    var newItems = false;
    db.ref('ordenes/'+alias).limitToLast(10).on('child_added', function(data){
        if (!newItems) return;

        var info = data.val();
        console.log("ORDENES NUEVAS");
        console.log(info);
        
        var notificarSucursal = $("#notificarSucursal").val();
        if(notificarSucursal != 0){
            if(notificarSucursal != info.sucursal){
                console.log("Llego un pedido de otra sucursal");
                return;
            }
        }
        
        var data = JSON.parse(localStorage.getItem('sonido'));
        ion.sound.stop();
        ion.sound.play(data.sirena);
        
        var estado = info.estado;
        var id = info.id;
        var sucursal = info.sucursal;

        var badge = $(".badge-"+estado);
        var count = badge.html();
        if(count=="")
          count=0;
        var count = parseInt(count) + 1;
        badge.html(count);

        if($("#"+estado).hasClass("active")){
            addOrden(id);
        }
    });

    db.ref('ordenes/'+alias).limitToLast(1).on('child_changed', function(data){
        console.log("Se ejecuto Update Firebase");
        console.log(data.val());

        var info = data.val();
        var estado = info.estado;
        var id = info.id;
        var sucursal = info.sucursal;

        var badge = $(".badge-"+estado);
        var count = badge.html();
        if(count=="")
          count=0;
        var count = parseInt(count) + 1;
        badge.html(count);

        if($("#orden"+id).length){
            $("#orden"+id).remove();
        }

        if($("#"+estado).hasClass("active")){
            addOrden(id);
        }
    });

    db.ref('ordenes/'+alias).once('value', function(messages) {
      newItems = true;
      console.log("TERMINO LA LISTA DE ORDENES");
    });

    ion.sound({
        sounds: sounds,
        path: "assets/sounds/",
        preload: true,
        volume: 1.0,
        loop: sonido.repeat,
        multiplay: true,
        ready_callback: function (obj) {
            /*
            obj.name;     // File name
            obj.alias;    // Alias (if set)
            obj.ext;      // File .ext
            obj.duration; // Seconds*/
            //console.log("SOUND READY " + obj.name);
        },
    }); 
    

    $("#btn-compose-mail").on("click",function(){
      var data = JSON.parse(localStorage.getItem('sonido'));
      ion.sound.pause(data.sirena);
    });
    
    $("#btn-pause-sound").on("click",function(){
      var data = JSON.parse(localStorage.getItem('sonido'));
      ion.sound.pause(data.sirena);
    });

    $(".activarSound").on("click",function(){
      var data = JSON.parse(localStorage.getItem('sonido'));
      ion.sound.play(data.sirena);
      $(".contenedorActivarSound").hide();
    });

    $(".btnTestSonido").on("click",function(){
      ion.sound.stop();
      ion.sound.play($("#cmbSonido").val());
    });

    $("#btnGuardarSonido").on("click",function(){
      ion.sound.stop();

      var sonido = {};
      sonido.sirena = $("#cmbSonido").val();

      if($("#cmbRepeat").val() == 1)
        sonido.repeat = true;
      else
        sonido.repeat = false;

      localStorage.setItem('sonido', JSON.stringify(sonido));
      messageDone("Sonido configurado con exito",'success');

      // let parametros = {
      //   "recordar_ordenes": recordar
      // }
      // $.ajax({
      //    url:'controllers/controlador_gestion_ordenes_19ene2022.php?metodo=setPermisoRecordatorio',
      //    data: parametros,
      //    type: "GET",
      //    success: function(response){
      //       console.log(response);
      //       if(response['success']==1){
      //         notify(response['mensaje'], "success", 2);
      //         location.reload();
      //       }
      //       else{
      //         notify(response['mensaje'], "error", 2);
      //       }
      //    },
      //    error: function(data){
      //    },
      //    complete: function(){
      //    },
      // });
    });
   
   $("#cmbSucursal").on("change", function(){
          var estado = $(".list-actions.active").attr("id");
          
          var tipo = $("#is_envio").val();
         
            var parametros = {
              "estado": estado,
              "tipo": tipo,
              "cod_sucursal": $("#cmbSucursal").val()
            }
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando ordenes, por favor espere...");
                 },
                url: 'controllers/controlador_gestion_ordenes.php?metodo=lista',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        $('.content-box').css({
                          width: '0',
                          left: 'auto',
                          right: '-46px'
                        });
                      $("#lista_ordenes").html(response['html']);
                    } 
                    else
                    {
                      $('.content-box').css({
                          width: '0',
                          left: 'auto',
                          right: '-46px'
                        });
                      $("#lista_ordenes").html('');
                      //messageDone(response['mensaje'],'error');
                    } 
                    feather.replace();
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

    /*RECORDAR ÓRDENES*/
    let alerta = JSON.parse(localStorage.getItem('recordatorio'));
    let tiempo = alerta.tiempo;
    let permisoRecordatorio = alerta.permiso;
    let permisoAutoAsignacion = alerta.asignacion;
    //MINUTOS A MILISEGUNDOS 60000
    $milisegundos = tiempo * 60000
    $("#chkRecordatorios").attr("checked", false);
    $(".div-recordar").hide();
    $("#cmbRecordar").val(tiempo);
    if(1 == permisoRecordatorio){
      $("#chkRecordatorios").attr("checked", true);
      $(".div-recordar").show();
      $(".div-recordar").css("justify-content", "flex-start");
      setInterval(recordarOrdenPendiente, $milisegundos, 'alerta');
      console.log('Recordatorios encendidos');
    }
    else{
      console.log('Recordatorios apagados');
    }

    if(1 == permisoAutoAsignacion){
      $("#chkAsignar").attr("checked", true);
      $(".div-recordar").show();
      $(".div-recordar").css("justify-content", "flex-end");
      setInterval(recordarOrdenPendiente, $milisegundos, 'asignar');
      console.log('Asignación automática encendida');
    }
    else{
      console.log('Asignación automática apagada');
    }
    $(".sidebarCollapse").trigger("click");
});


$("body").on("change","#cmbForma",function(event){
   var forma =$(this).val();
        console.log(forma);
        if(forma == "0"){
            $(".infoGacela").show();
            $(".infoAsignacion").hide();
      }
      else
      {
           $(".infoGacela").hide();
           $(".infoAsignacion").show();
      }
});

$("body").on("change","#cmbFormaCourier",function(event){
   var forma =$(this).val();
        console.log(forma);
        if(forma == "0"){
           $(".infoCourier").hide();
           $(".infoAsignacion").show();
      }
      else
      {
          $(".infoCourier").show();
            $(".infoAsignacion").hide();
         
      }
});

$("body").on("click","#btnModalNotify",function(event){    
  event.preventDefault();
  $(".nameUser").html($(".mail-usr-name").html());
  $("#frmNotificar").trigger("reset");
  $("#modalNotificar").modal();
  var id=$(".codusuarioOrden").val();
  $(".btn-modal").html('<button type="button" class="btn btn-outline-primary btnEnviarRecordatorio" data-usuario="'+id+'">Enviar Notificac&oacute;n</button>');
  
});


function sendFacturaElectronica(cod_orden){
    if($("#fact_electronica").length){
        if($("#fact_electronica").val() == 0){
            return false;
        }
    }else{
        return false;
    }
    
    var urlController = "";
    var fact_electronica = $("#fact_electronica").val();
    if(fact_electronica == 1){      //CONTIFICO
      urlController = 'controllers/controlador_contifico.php?metodo=crearFactura&id='+cod_orden;
    }if(fact_electronica == 2){     //FACT. MOVIL
        urlController = 'controllers/controlador_fact_movil.php?metodo=crearFactura&id='+cod_orden;
    }
    
    console.log("ENVIAR FACTURA ELECTRONICA");
        $.ajax({
            url: urlController,
            type:'GET',
            beforeSend: function(){
                notify('Enviando factura electronica', "warning", 2);
            },success: function(response){
                console.log(response);
                if(response['success']==1)
                  notify(response['mensaje'], "success", 2);
                else
                  notify(response['mensaje'], "error", 2);
            },error: function(data){
                notify(response['mensaje'], "error", 2);
                console.log(data);
            }
        });
}

$("#txtBusqueda").on("click", function(){
  $("#modalBusqueda").modal();
});

$("#txtBuscar").keyup(function(){
  let busqueda = $(this).val();
  let tipo_busqueda = $("#cmbBusqueda").val();
  let cod_sucursal = $("#cmbSucursal").val();

  if(busqueda == ""){
    let html = '  <tr> \
                    <td colspan="5"> \
                      Sin resultados \
                    </td> \
                  <tr>';
    $(".busquedaResultados").html(html);
    return;
  }

  let parametros = {
    "busqueda": busqueda,
    "tipo_busqueda": tipo_busqueda,
    "cod_sucursal": cod_sucursal
  }
  $.ajax({
     url:'controllers/controlador_gestion_ordenes.php?metodo=getBusquedaOrdenes',
     data: parametros,
     type: "GET",
     success: function(response){
        console.log(response);
        if(response['success']==1){
          $(".busquedaResultados").html(response['html']);
        }
        else{
          $(".busquedaResultados").html(response['html']);
        }
     },
     error: function(data){
     },
     complete: function(){
     },
  });
});

$("#cmbBusqueda").on("change", function(){
  let valor = $(this).val();
  $("#txtBuscar").val("");
  if(valor == 1)
    $("#txtBuscar").attr("placeholder", "Buscar por número de orden...");
  else if(valor == 2)
    $("#txtBuscar").attr("placeholder", "Buscar por número de cédula...");
  else if(valor == 3)
    $("#txtBuscar").attr("placeholder", "Buscar por nombre de cliente...");
});

$(".btnAbrirTienda").on("click", function(){
  $("#modalAbrirTienda").modal();
});

$("#cmbSucFestivo").on("change", function(){
  getFestivoHoy();
});

function getFestivoHoy() {
  var parametros = {
    "cod_sucursal": $("#cmbSucFestivo").val()
  }
  $.ajax({
     url:'controllers/controlador_sucursal.php?metodo=getFestivos',
     data: parametros,
     type: "GET",
     success: function(response){
        console.log(response);
        if(response['success']==1){
          $("#bloqueEstadoAbierto").html(response['html']);
        }
        else{
          $("#bloqueEstadoAbierto").html(response['html']);
        }
     },
     error: function(data){
     },
     complete: function(){
     },
  });
}

$("body").on("click", ".btnGuardarFestivo", function(e){
  e.preventDefault();
  swal({
     title: 'Cerrar Sucursal',
     text: '¿Esta seguro?',
     type: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Aceptar',
     cancelButtonText: 'Cancelar',
     padding: '2em'
  }).then(function(result){
     if (result.value) {
        GuardarFestivo();
     }
  }); 
});

$("body").on("click", ".btnQuitarFestivo", function(e){
  e.preventDefault();
  let cod_sucursal_festivos = $(this).data("value");

  swal({
     title: 'Abrir Sucursal',
     text: '¿Está seguro?',
     type: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Aceptar',
     cancelButtonText: 'Cancelar',
     padding: '2em'
  }).then(function(result){
     if (result.value) {
        QuitarFestivo(cod_sucursal_festivos);
     }
  }); 
});

function GuardarFestivo(){
  var parametros = {
    "cod_sucursal": $("#cmbSucFestivo").val(),
    "tiempo": $("#cmbHoras").val()
  }
  $.ajax({
     url:'controllers/controlador_sucursal.php?metodo=guardarCierreFestivo',
     data: parametros,
     type: "GET",
     success: function(response){
        console.log(response);
        if(response['success']==1){
          $("#bloqueEstadoAbierto").html(response['html']);
          messageDone(response['mensaje'], "success");
          $("#modalAbrirTienda").modal("hide");
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

function QuitarFestivo(cod_sucursal_festivos){
  
  let parametros = {
    "cod_sucursal": $("#cmbSucFestivo").val(),
    "cod_sucursal_festivos": cod_sucursal_festivos
  }
  $.ajax({
     url:'controllers/controlador_sucursal.php?metodo=quitarCierreFestivos',
     data: parametros,
     type: "GET",
     success: function(response){
        console.log(response);
        if(response['success']==1){
          $("#bloqueEstadoAbierto").html(response['html']);
          messageDone(response['mensaje'], "success");
          $("#modalAbrirTienda").modal("hide");
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

function recordarOrdenPendiente(tipo){
    var sonido = JSON.parse(localStorage.getItem('sonido'));
    var recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    let tiempo = recordatorio.tiempo;
    let parametros = {
        "tiempo": tiempo
    }
    $.ajax({
       url:'controllers/controlador_gestion_ordenes.php?metodo=recordarOrdenPendiente',
       data: parametros,
       type: "GET",
       success: function(response){
          console.log(response);
            if(response['success']==1){
                notify(response['mensaje'], "success", 2);
                let ordenes = response['ordenes'];
                for (let i = 0; i < ordenes.length; i++) {
                  if(tipo == "alerta"){
                    $("#orden"+ordenes[i]['cod_orden']).remove();                 
                    addOrden(ordenes[i]['cod_orden']);
                  }
                  else{
                    autoAsignacion(ordenes[i]['cod_orden'], ordenes[i]['courier']);
                  }                
                }
                if(tipo == "alerta"){
                  ion.sound.stop();
                  ion.sound.play(sonido.sirena);
                }
            }
            else{
                notify(response['mensaje'], "error", 2);
            }
       },
       error: function(data){
       },
       complete: function(){
       },
    });
}

$("body").on("change", "#chkRecordatorios", function(){
  let chk = $(this);
  if(chk.is(":checked")){
    $(".div-recordar").show();
    $(".div-recordar").css("justify-content", "flex-start");
    $("#chkAsignar").prop("checked", false);
  }
  else{
    if(!$("#chkAsignar").is(":checked")){
      $(".div-recordar").hide();
    }
  }
});

$("body").on("change", "#chkAsignar", function(){
  let chk = $(this);
  if(chk.is(":checked")){
    $(".div-recordar").show();
    $(".div-recordar").css("justify-content", "flex-end");
    $("#chkRecordatorios").prop("checked", false);
    $("#chkRecordatorios").trigger("change");
  }
  else{
    if(!$("#chkRecordatorios").is(":checked")){
      $(".div-recordar").hide();
    }
  }
});

$("body").on("click", ".btnGuardarRecodatorios", function(){
  /*CONFIGURAR TIEMPO*/
  let recordatorio = {};
  let recordar = 0;
  let asignacion = 0;
  if($("#chkRecordatorios").is(":checked"))
    recordar = 1;
  if($("#chkAsignar").is(":checked"))
    asignacion = 1;
  recordatorio.permiso = recordar;
  recordatorio.asignacion = asignacion;
  recordatorio.tiempo = $("#cmbRecordar").val();
  localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
  location.reload();
});

function autoAsignacion(cod_orden, courier){
    var metodo = "";
    var parametros = {
        id: cod_orden
    };
    
    /*-----------------------*/
    if(courier == 0){
        notify("No tienes un courier configurado", "error" , 2);
        return;
    }
    else{
        if(courier == 1){
            metodo='asignarGacelaV2';
        }else if(courier == 2){
            metodo='asignarLaar';
        }else if(courier == 3){
          metodo='asignarPicker';
        }
    }
    
    if(metodo == ""){
        messageDone("Metodo no configurado..",'error');
        return;
    }
    
    /*-----------------------*/
    
    $.ajax({
      beforeSend: function(){
          OpenLoad("Guardando datos, por favor espere...");
       },
      url: "controllers/controlador_gestion_ordenes.php?metodo="+metodo,
      type: 'POST',
      data: parametros,
      success: function(response){
          console.log(response);
          if( response['success'] == 1){        
            notify("Asignación Automática: "+response['mensaje'], "success", 2);
            sendFacturaElectronica(cod_orden);
          } 
          else{
            messageDone("Asignación Automática: "+response['mensaje'],'error');
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

function addOrden(cod_orden){
  var parametros = {
    "id": cod_orden
  }
  $.ajax({
      beforeSend: function(){
          //OpenLoad("Buscando ordenes, por favor espere...");
       },
      url: 'controllers/controlador_gestion_ordenes.php?metodo=item',
      type: 'GET',
      data: parametros,
      success: function(response){
          console.log(response);
          if( response['success'] == 1)
          {
            $("#lista_ordenes").prepend(response['html']);
            feather.replace();
          } 
          else{
            window.location.reload();
          }
      },
      error: function(data){
        console.log(data);
        window.location.reload();
      },
      complete: function(resp)
      {
        //CloseLoad();
      }
  });
}

function enviarCorreoLaarAsignacion(cod_orden){
  if(cod_orden <= 0){
    notify("Orden inválida", "error", 2);
    return;
  }
  $.ajax({
     url:'https://dashboard.mie-commerce.com/correosFront/asignacionLaar.php?cod_orden='+cod_orden,
     data: parametros,
     type: "GET",
     success: function(response){
        console.log(response);
        if(response['success']==1){
          notify(response['mensaje'], "success", 2);
        }
        else{
          notify(response['mensaje'], "error", 2);
        }
     },
     error: function(data){
     },
     complete: function(){
     },
  });
}