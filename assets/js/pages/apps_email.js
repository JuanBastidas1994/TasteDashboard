Handlebars.registerHelper('iconAttachment', function(texto) {
    if(texto === "JPEG" || texto === "JPG" || texto === "PNG")
        return "image";
    else if(texto === "RAR" || texto === "ZIP" || texto === "ISO")
        return "box";
    else
        return "file-text";
});

$(document).ready(function() {
    const alias = Cookies.get('alias');
    console.log(alias);

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
    mailInboxScroll();

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
      var folder = this.id;
      $(".list-actions").removeClass("active");
      $(this).addClass("active");
        var parametros = {
          "folder": folder
        }
        console.log(parametros);
        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando correos, por favor espere...");
             },
            url: 'controllers/controlador_email.php?metodo=getEmails',
            type: 'GET',
            data: parametros,
            success: function(response){
                console.log(response);
                $("#lista_ordenes").html("");
                if( response.success == 1){
                    var template = Handlebars.compile($("#email-item").html());
                    $('.content-box').css({
                      width: '0',
                      left: 'auto',
                      right: '-46px'
                    });
                    
                    let emails = response.emails;
                    for(var x=0; x<emails.length; x++){
                        var item = emails[x];
                        $("#lista_ordenes").append(template(item));
                    }
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

    /*CLICK EN LAS ORDENES - ABRIR UNA ORDEN*/
    $('body').on('click',".mail-item", function(event) {
      var id = $(this).attr("data-value");
      var folder = $(this).attr("data-folder");
      $(this).removeClass("unread-mail");
      openMailItem(folder, id);
    });

    function openMailItem(folder, id){
      var parametros = {
        "folder": folder,
        "id": id
      }
      $.ajax({
          beforeSend: function(){
              OpenLoad("Abriendo mensaje, por favor espere...");
           },
          url: 'controllers/controlador_email.php?metodo=getEmailDetail',
          type: 'GET',
          data: parametros,
        success: function(response){
            console.log(response);
            if( response.success == 1){
                let email = response.email;
                $(".title-open-mail").html(email.subject);
                
                var template = Handlebars.compile($("#email-item-expanded").html());
                $("#orden").html(template(email));
                feather.replace();

                mailInboxScroll();
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

    setTimeout(function() {
        $(".list-actions").first().trigger('click');
    },10);
    

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

        var data = JSON.parse(localStorage.getItem('sonido'));
        ion.sound.play(data.sirena);
    });

    db.ref('ordenes/'+alias).limitToLast(10).on('child_changed', function(data){
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

    db.ref('ordenes').once('value', function(messages) {
      newItems = true;
      console.log("TERMINO LA LISTA DE ORDENES");
    });
});


$("#txtBusqueda").on("click", function(){
  $("#modalBusqueda").modal();
});

$("#txtBuscar").keyup(function(){
  let busqueda = $(this).val();
  let tipo_busqueda = $("#cmbBusqueda").val();

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