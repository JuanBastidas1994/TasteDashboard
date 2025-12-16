$(document).ready(function(){
    getAllCards();
    var submitButton = $("#btnGuardarTarjeta");
    var submitInitialText = submitButton.text();
    
    //Payment.init('stg', 'TPP3-EC-CLIENT', 'ZfapAKOk4QFXheRNvndVib9XU3szzg');
    Payment.init('stg', 'NUVEISTG-EC-CLIENT', 'rvpKAv2tc49x6YL38fvtv5jJxRRiPs');
    
    var successHandler = function(cardResponse) {
      console.log(cardResponse);
      console.log(cardResponse.card);
      if(cardResponse.card.status === 'valid'){
        $('#messages').html('Card Successfully Added<br>'+
                      'status: ' + cardResponse.card.status + '<br>' +
                      "Card Token: " + cardResponse.card.token + "<br>" +
                      "transaction_reference: " + cardResponse.card.transaction_reference
                    );    
        saveCard(cardResponse.card);     
      }else if(cardResponse.card.status === 'review'){
        $('#messages').html('Card Under Review<br>'+
                      'status: ' + cardResponse.card.status + '<br>' +
                      "Card Token: " + cardResponse.card.token + "<br>" +
                      "transaction_reference: " + cardResponse.card.transaction_reference
                    );
        console.log(cardResponse.card);
        saveCard(cardResponse.card);
      }else{
        $('#messages').html('Error<br>'+
                      'status: ' + cardResponse.card.status + '<br>' +
                      "message Token: " + cardResponse.card.message + "<br>"
                    ); 
      }
      submitButton.removeAttr("disabled");
      submitButton.text(submitInitialText);
    };
    
    var errorHandler = function(err) {
      console.log(err.error);
      $('#messages').html(err.error.type);    
      submitButton.removeAttr("disabled");
      submitButton.text(submitInitialText);
      
      messageDone(err.error.help,'error');
    };
    
    function saveCard(info){
        $.ajax({
            beforeSend: function(){
                //OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=addCard',
            type: 'POST',
            data: info,
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    messageDone(response['mensaje'],'success');
                    getAllCards();
                } 
                else{
                    messageDone(response['mensaje'],'error');
                    getAllCards();
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
    
    function getAllCards(){
        $.ajax({
            beforeSend: function(){
                //OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=getAllCards',
            type: 'POST',
            success: function(response){
                console.log(response);
                if( response['success'] == 1){
                    $(".lstCards").html(response['html']);
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
    
    
    $("#btnGuardarTarjeta").on("click",function(){
        var myCard = $('#my-card');
        var cardToSave = myCard.PaymentForm('card');
        if(cardToSave == null){
          notify("Datos de tarjeta no válidos", "error", 2);
        }else{
            submitButton.attr("disabled", "disabled").text("Procesando Tarjeta...");
            
            let uid = $("#empId").val();
            let email = $("#correo").val();
            Payment.addCard(uid, email, cardToSave, successHandler, errorHandler);
        }
    });
    
    $("body").on("click", ".btnEliminarCard", function(){
        var token = $(this).data("value");
        Swal.fire({
          title: '¿Estas seguro de eliminar esta tarjeta?',
          text: '',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Proceder',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
                token: token,
            }
            console.log(parametros);
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Eliminando tarjeta, por favor espere...");
                },
                url: 'controllers/controlador_empresa.php?metodo=deleteCard',
                type: 'GET',
                data: parametros,
                success: function(response){
                    console.log(response);
                    if( response['success'] == 1)
                    {
                        messageDone(response['mensaje'],'success');
                        getAllCards();
                    } 
                    else
                    {
                        messageDone(response['mensaje'],'error');
                        getAllCards();
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
});


/*ACTUALIZAR CLAVE*/
$("#btnActualizarPassword").on("click",function(event){
    event.preventDefault();

    if($("#txt_pass").val().trim().length == 0){
        alert("Debe proporcionar una contraseña");
        return;
    }
    
    if($("#txt_pass").val().trim() != $("#txt_pass2").val().trim()){
        alert("las contraseñas no coinciden, por favor verificar");
        return;
    }

    swal({
      title: '¿Estas seguro de cambiar tu password?',
      text: 'La proxima vez que inicies sesión deberas usar tu nueva password',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Proceder',
      cancelButtonText: 'Cancelar',
      padding: '2em'
    }).then(function(result) {
      if (result.value) {
        
        var parametros = {
            "password": $("#txt_pass").val().trim(),
        }
        console.log(parametros);
        $.ajax({
            beforeSend: function(){
                OpenLoad("Buscando informacion, por favor espere...");
            },
            url: 'controllers/controlador_usuario.php?metodo=set_password',
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
 
 $(".btnAddTarjeta").on("click", function(e){
     e.preventDefault();
     $("#modalAddTarjetas").modal();
 });
 
 $(".btnTarjetas").on("click", function(e){
     e.preventDefault();
     $("#modalTarjetas").modal();
 });
 
 $("body").on("change", ".chkEstado", function(e){
     e.preventDefault();
     var token = $(this).val();
     swal({
      title: '¿Estas seguro de cambiar tu tarjeta?',
      text: 'Esta será tu nueva forma de pago',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Proceder',
      cancelButtonText: 'Cancelar',
      padding: '2em'
    }).then(function(result) {
      if (result.value) {
        actualizarFormaPago(token);
      }
      else{
          $("#modalTarjetas").modal("hide");
      }
    });
     
 });
 
 function actualizarFormaPago(token){
     var parametros = {
         "token": token
     }
    $.ajax({
            beforeSend: function(){
                OpenLoad("Actualizando informacion, por favor espere...");
            },
            url: 'controllers/controlador_empresa.php?metodo=set_tarjeta_actual',
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