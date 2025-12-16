<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
$ClEmpresas = new cl_empresas();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $empresa = $ClEmpresas->getByAlias($alias);
  if($empresa){
      $nombre = $empresa['nombre'];
      $cod_empresa = $empresa['cod_empresa'];
  }else
  {
      header("location: ./index.php");
  }
}
else{
    header("location: ./index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <link href="assets/css/users/user-profile.css" rel="stylesheet" type="text/css" />
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
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
    </style>
</head>
<body>
    
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(); ?>
    <!--  END NAVBAR  -->
    
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR USUARIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                    <div class="x_content" id="divSuccess" style="display: none;">    
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Transaction Reference </label>
                              <p id="p_reference" class="pclean"></p>
                          </div>
                          
                           <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Transaction Id</label>
                              <p id="p_transaction_id" class="pclean"></p>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Authorization Code</label>
                              <p id="p_auth" class="pclean"></p>
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Transaction Status</label>
                              <p id="p_status" class="pclean"></p>
                          </div>
                      </div>
                    </div>
                    
                    <div class="x_content" id="divError" style="display: none;">    
                      <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>Descripci&oacute;n </label>
                              <p id="p_descrp" class="pclean"></p>
                          </div>
                          
                           <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                              <label>JSON</label>
                              <p id="p_json" class="pclean"></p>
                          </div>
                      </div>
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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
                    <div class="col-xl-7 col-lg-6 col-md-7 col-sm-12 layout-top-spacing">

                        <div class="layout-spacing">
                            <div class="widget-content widget-content-area">
                                <div class="d-flex justify-content-between">
                                    <div class="row">
                                    <div class="col-md-12"><h3 class=""><?php echo $nombre;?></h3></div>
                                        <div class="col-md-12"><h4 class="">Intentos de Pago</h4></div>
                                        <div class="col-md-12">
                                            <input id="cod_empresa" type="hidden" value="<?php echo $cod_empresa;?>">
                                           <table class="table style-3 table-hover">
                                                <thead>
                                                  <tr>
                                                    <th>Fecha</th>
                                                    <th>Tarjeta</th>
                                                    <th>Monto</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    $logs = $ClEmpresas->getLogsPagos($cod_empresa);
                                                    foreach($logs as $log){
                                                       $tipo = "E";
                                                       $badge="danger";
                                                       if($log['estado'] == "SUCCESS"){
                                                            $tipo = "S";
                                                            $badge = "primary";
                                                       }
                                                       echo'<tr>
                                                                <td>'.$log['fecha'].'</td>
                                                                <td><img src="/assets/img/cards/'.$log['card_type'].'.svg" style="width: 35px;"/> •••• •••• •••• '.$log['card_number'].'</td>
                                                                <td>$'.$log['monto'].'</td>
                                                                <td><span class="shadow-none badge badge-'.$badge.'">'.$log['estado'].'</span></td>
                                                                <td><button class="btn btn-primary btnVer" data-tipo="'.$tipo.'" data-value="'.$log['cod_mie_log_pago'].'">Ver</button></td>
                                                            </tr>';
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-5 col-lg-6 col-md-5 col-sm-12 layout-top-spacing">

                        <div class="layout-spacing ">
                            <div class="widget-content widget-content-area">
                                <h4 class="">Formas de Pago</h4>
                                <div class="container lstCards"></div>
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
    <script src="assets/js/pages/pagos.js"></script>
    <script>
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


     $("#btnActualizarCostoEnvio").on("click",function(event){
        event.preventDefault();

        if($("#base_dinero").val().trim().length == 0 || $("#base_km").val().trim().length == 0 || $("#adicional_km").val().trim().length == 0){
            alert("Debes llenar todos los campos");
            return;
        }

        swal({
          title: '¿Estas seguro?',
          text: 'No se puede revertir los cambios',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Actualizar Costo',
          cancelButtonText: 'Cancelar',
          padding: '2em'
        }).then(function(result) {
          if (result.value) {
            
            var parametros = {
                "base_dinero": $("#base_dinero").val().trim(),
                "base_km": $("#base_km").val().trim(),
                "adicional_km": $("#adicional_km").val().trim()
            }
            console.log(parametros);
            $.ajax({
                beforeSend: function(){
                    OpenLoad("Buscando informacion, por favor espere...");
                },
                url: 'controllers/controlador_empresa.php?metodo=update_costo_envio',
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