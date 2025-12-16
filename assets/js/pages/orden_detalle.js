$(document).ready(function() {

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

    $("#btnBack").on("click",function(){
        var link = $(this).attr("data-module-back");
        if (typeof link === "undefined") {
          link = "index.php";
        }else{
            window.location.href = link;
        }
      }); 
      
    $(".btnNotificar").on("click",function(){
         var $this = $(this);
        var usuario = $this.data("usuario");
        var texto = $("#demo1").val();
        let tipo = $("#cmbTipoNotificacion");
        let tituloTipo = $("#cmbTipoNotificacion option:selected").text();
        if(texto == "" || texto == null){
          messageDone("La notificación no puede estar vacía", "error");
          return;
        }
        //ENVIAR NOTIFICACION
        $.ajax({
          url: 'controllers/controlador_notificaciones.php?metodo=notificarUsuario&usuario='+usuario+'&texto='+texto+'&tipo='+tipo+'&tituloTipo='+tituloTipo,
          type:'GET',
          success: function(response){
              console.log(response);
              messageDone(response['mensaje'], "success");
              $("#demo1").val("");
              $("#cmbTipoNotificacion").val("");
              $(".emojionearea-editor").html("");
          },
          error: function(data){
            messageDone("Error al enviar la notificacion, intenta nuevamente", "error");
          }
        });

        
      });   

      if($(".btn-anular").length > 0){
        if($("#hdAnulada").length > 0){
          if($("#hdAnulada").val() == 1){
            $(".btn-anular").attr("disabled", true);
          }
        }
        else{
          $(".btn-anular").removeAttr("disabled");
        }
      }
});

$("body").on("click", ".btn-anular", function(){
  let cod_orden = $(this).data("value");
  Swal.fire({
     title: '¿Estas seguro?',
     text: 'Se cambiara el estado de la orden a ANULADA',
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Aceptar',
     cancelButtonText: 'Cancelar',
     padding: '2em'
  }).then(function(result){
     if (result.value) {
      revertirPago(cod_orden);
     }
  }); 
});

function revertirPago(orden){
  $.ajax({
   url: 'controllers/controlador_gestion_ordenes.php?metodo=revertir_pago&cod_orden='+orden,
   type:'GET',
   success: function(response){
       console.log("----- REVERTIR PAGO ------");
       console.log(response);
       if(response['success'] == 1)
         notify(response['mensaje'], "success", 2);
   },
   error: function(data){
       notify("Error al anular el pago de tarjeta de credito, intenta nuevamente", "success", 2);
   }
 });
}

function getDetailPayPaymentez(orden){
    $.ajax({
       url: 'controllers/controlador_gestion_ordenes.php?metodo=getInfoPaymentez&cod_orden='+orden,
       type:'GET',
       success: function(response){
           console.log("----- INFORMACION DEL PAGO ------");
           console.log(response);
           if(response['success'] == 1){
             notify(response['mensaje'], "success", 2);
             $("#detallePaymentezModal").modal();
             let transaction = response['data']['transaction'];
             console.log(transaction);
             $("#pay-status").html(transaction.current_status);
             $("#pay-id").html(transaction.id);
             $("#pay-date").html(transaction.payment_date);
             $("#pay-trace").html(transaction.trace_number);
             $("#pay-lote").html(transaction.lot_number);
             $("#pay-auth").html(transaction.authorization_code);
             
             //Tarjeta
             let card = response['data']['card'];
             $("#card-bin").html(card.bin);
             $("#card-number").html(card.number);
             $("#card-mes").html(card.expiry_month);
             $("#card-year").html(card.expiry_year);
           } 
       },
       error: function(data){
           notify("Error al traer la información del pago de tarjeta de credito, intenta nuevamente", "success", 2);
       }
     });
}