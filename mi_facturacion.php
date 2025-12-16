<?php
require_once "funciones.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_paymentez.php";

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
$Clusuarios = new cl_usuarios($session['cod_usuario']);
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imagen = $files.$Clusuarios->imagen;
$nombres = $Clusuarios->nombre.' '.$Clusuarios->apellido;
$cod_empresa = $session['cod_empresa'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/users/user-profile.css" rel="stylesheet" type="text/css" />
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
      .dropify-wrapper {
          display: block;
          position: relative;
          cursor: pointer;
          overflow: hidden;
          width: 100%;
          max-width: 100%;
          height: 110px !important;
          padding: 5px 10px;
          font-size: 14px;
          line-height: 22px;
          color: #777;
          background-color: #fff;
          background-image: none;
          text-align: center;
          border: 0 !important;
          -webkit-transition: border-color .15s linear;
          transition: border-color .15s linear;
      }
      
      #my-card input {
        height: 50px !important;
    }
    </style>
</head>
<body>
    
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(); ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <?php echo sidebar(); ?>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-spacing">

                    <!-- Content -->
                    

                    <div class="col-xl-8 col-lg-6 col-md-7 col-sm-12 layout-top-spacing">
                        
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h3 class="">Lista de Tarjetas</h3>
                                <input type="password" style="display:none;"/>
                                <div class="container">
                                <?php
                                    $uid = "uid1234";
                                    //$uid = $cod_empresa;
                                    $tarjetas = listCardsById($uid);
                                    if(isset($tarjetas['cards'])){
                                        $tarj = $tarjetas['cards'];
                                        foreach($tarj as $card){
                                            echo '
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-12" style="padding: 15px 0px; border: 1px solid #dedede;  margin-bottom: 5px; margin-bottom: 5px;">
                                                    <div class="col-md-6 col-sm-6 col-9">
                                                        <img src="/assets/img/cards/'.$card['type'].'.svg" style="width: 35px;"/> •••• •••• •••• '.$card['number'].'
                                                    </div>
                                                    <div class="col-md-3 col-sm-3 col-3">
                                                       '.$card['expiry_month'].'/'.$card['expiry_year'].'
                                                    </div>
                                                    <div class="col-md-3 col-sm-3 col-12" style="text-align: right;">
                                                        <a href="crear_productos.php?id='.$card['token'].'" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="check-square"></i></a>
                                                        <a href="javascript:void(0);" data-value="'.$card['token'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i data-feather="trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>';
                                        }
                                    }
                                    
                                    //var_dump($tarjetas);
                                ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12 layout-top-spacing">
                        <div class="skills layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h3 class="">Agregar nueva tarjeta</h3>
                                <div class="row">
                                    <div class="payment-form" id="my-card" data-capture-name="true"></div>
                                </div>
                                <div class="row"> 
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                        <button type="button" class="btn btn-outline-primary" id="btnGuardarTarjeta">Guardar tarjeta</button>
                                    </div>
                                </div> 
                                <div class="row">
                                    <span id="messages"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.js" charset="UTF-8"></script>
    
    <script>
    $(document).ready(function(){
        var submitButton = $("#btnGuardarTarjeta");
        var submitInitialText = submitButton.text();
        
        Payment.init('stg', 'TPP3-EC-CLIENT', 'ZfapAKOk4QFXheRNvndVib9XU3szzg');
        
        var successHandler = function(cardResponse) {
          console.log(cardResponse);
          console.log(cardResponse.card);
          if(cardResponse.card.status === 'valid'){
            $('#messages').html('Card Successfully Added<br>'+
                          'status: ' + cardResponse.card.status + '<br>' +
                          "Card Token: " + cardResponse.card.token + "<br>" +
                          "transaction_reference: " + cardResponse.card.transaction_reference
                        );    
          }else if(cardResponse.card.status === 'review'){
            $('#messages').html('Card Under Review<br>'+
                          'status: ' + cardResponse.card.status + '<br>' +
                          "Card Token: " + cardResponse.card.token + "<br>" +
                          "transaction_reference: " + cardResponse.card.transaction_reference
                        ); 
            console.log(cardResponse.card);            
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
        };
        
        $("#btnGuardarTarjeta").on("click",function(){
            var myCard = $('#my-card');
            var cardToSave = myCard.PaymentForm('card');
            if(cardToSave == null){
              alert("Invalid Card Data");
            }else{
                submitButton.attr("disabled", "disabled").text("Procesando Tarjeta...");
                
                let uid = "uid1234";
                let email = "jhon@doe.com";
                Payment.addCard(uid, email, cardToSave, successHandler, errorHandler);
            }
        });
    });
    
    $('.dropify').dropify({
        messages: { 'default': 'Click to Upload or Drag n Drop', 'remove':  '<i class="flaticon-close-fill"></i>', 'replace': 'Upload or Drag n Drop' }
    });

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
    </script>
</body>
</html>