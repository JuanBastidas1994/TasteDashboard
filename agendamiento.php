<?php
require_once "funciones.php";
require_once "clases/cl_agendamiento.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_productos.php";
if(!isLogin()){
    header("location:login.php");
}



if(isset($_GET['cod_usuario'])){
    $ClUsuario = new cl_usuarios();
    $Clproductos= new cl_productos();
    $ClSucursales = new cl_sucursales(null);
    $Clagendamiento = new cl_agendamiento();
    $ClUsuario->cod_usuario = $_GET['cod_usuario'];
    if($ClUsuario->GetDatos()){
        $Clagendamiento->cod_usuario = $_GET['cod_usuario'];
        $serviciosUsuario = $Clagendamiento->listarServiciosUsuarios();
        $sucursales = $ClSucursales->listaByEmpresa($ClUsuario->cod_empresa);
        $productos = $Clproductos->lista();
    }else{
        //Redireccionar a otra pagina        
    }
}else{
    //Redireccionar a otra pagina
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
    <link href="plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css">
    <link href="plugins/fullcalendar/custom-fullcalendar.advance.css" rel="stylesheet" type="text/css">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css">
    <style type="text/css">
      .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
      }
      
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

      .respGalery > div {
          margin-top: 15px;
      }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>
<body>
    <!-- Modal -->
    <div class="modal fade" id="formSucursal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post"  id="addUserAvailable" action="controllers/controlador_agendamiento.php?metodo=crear">
                    <input type="hidden" id="userId" name="cod_usuario" value="<?php echo $ClUsuario->cod_usuario?>">
                    <input type="hidden" id="diaId" name="dia">
                    <input type="hidden" id="horaInicioId" name="hora_inicio">
                    <input type="hidden" id="horaFinalId" name="hora_final">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Verifica la Información</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mx-1">
                            <div class="form-group col-md-6 col-sm-6 col-xs-6">
                                <label id="lblInicio"></label>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 col-xs-6">
                                <label id="lblFin"></label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-6">
                            <label id="lblDia"></label>
                        </div>
                        <div class="form-group col-md-6 col-sm-12 col-xs-12">
                            <label>Seleccionar Sucursal <span class="asterisco">*</span></label>
                            <select name="cod_sucursal" id="sucursalId" class="form-control" required="required">
                                <!-- <option>Selecciona una sucursal</option> -->
                                <?php
                                    foreach($sucursales as $s){
                                        echo '<option value="'.$s['cod_sucursal'].'">'. $s['nombre'].'</option>';
                                    };
                                ?>
                            </select>
                        </div>
                    </div>
                   
                </form>
                <div class="modal-footer">
                        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i>
                            Descartar</button>
                        <input type="submit" class="btn btn-primary" form="addUserAvailable" value="Guardar">
                    </div>
            </div>
            
        </div>
    </div>

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true,"categorias.php"); ?>
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
                <div class="col-md-12" style="margin-top:25px; ">
                    
                    <h3 id="titulo"><?php echo $ClUsuario->nombre;?></h3>
                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_noticia != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Noticia</span>
                      </span>

                      <span style="cursor: pointer;margin-right: 15px;display: none;">
                        <i class="feather-16" data-feather="copy"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Duplicar</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                      </span>
                    </div>
                </div>
                                      
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                        <h4 class="ml-2 ">Servicios</h4>
                            <div class="row ml-3 mt-3">
                                <?php
                                    foreach($productos as $p){
                                        $checked = '';
                                        foreach($serviciosUsuario as $su){
                                            if($su['cod_producto'] == $p['cod_producto'])
                                                $checked = 'checked';
                                        }
                                        echo ' 
                                        <div class="col-3">
                                            <div class="n-chk">
                                                <label class="new-control new-checkbox checkbox-primary">
                                                    <input type="checkbox" class="new-control-input event-check" id="check-'.$p['cod_producto'].  '" data-service="'.$p['cod_producto'].'" '  .$checked.' >
                                                    <span class="new-control-indicator"></span>'.$p['nombre'].'</label></div></div>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>         
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="row">
                                <?php
                                    foreach($sucursales as $s){
                                        echo '
                                            <div class="col-4 d-flex align-items-center">
                                                <div style="background: red; width: 10px; height: 10px; border-radius:2px; "
                                                    class="mb-2" id="office_color_'.$s['cod_sucursal'].'">
                                                </div>
                                                <p class="ml-2">'.$s['nombre'].'</p>
                                            </div>';
                                    };            
                                ?>
                            </div>
                            <div id="calendar"></div>
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
    <script src="plugins/fullcalendar/moment.min.js"></script>
    <script src="plugins/fullcalendar/fullcalendar.min.js"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>                                     
    <script src="assets/js/pages/agendamiento.js" type="text/javascript"></script>

</body>
</html>