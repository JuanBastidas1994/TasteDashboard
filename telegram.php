<?php
require_once "funciones.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_telegram.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$ClUsuarios = new cl_usuarios(NULL);
$ClTelegram = new cl_telegram(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$botname = "No tienes configurado un bot";
$token = "No tienes configurado un bot";
$bot = $ClTelegram->get();
if($bot){
    $token = $bot['token'];
    $botname = $bot['botname'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
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
                
                <div class="col-md-12" >
                    <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px;vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </a>
                    <h3 id="titulo">Telegram</h3>
                </div>
               
                <div class="row layout-top-spacing" style="display: block;">
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-5 col-lg-5 col-sm-12  layout-spacing">

                        <div class="widget-content widget-content-area br-6" style="height: 200px; margin-bottom: 15px;">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>ChatBot Telegram</h4>
                                </div>
                            </div>
                            <div class="mb-4 mt-4">

                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="border-bottom: 1px solid #e8e8e8;">
                                        <label>Nombre</label>
                                        <div><?php echo $botname; ?></div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="margin-top: 10px;">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <label>Token</label>
                                        <div><?php echo $token; ?></div>
                                    </div>
                                </div>

                            </div>    
                            
                        </div>

                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Grupos</h4>
                                    <p>Recuerda que adem&aacute;s de activar el grupo debes poner el bot como <b>administrador</b> en cada grupo que desees que capture informaci&oacute;n para que el sistema pueda recibir y enviar mensajes.</p>
                                </div>
                            </div>

                            <div id="" class="table-responsive mb-4 mt-4" style="max-height: 500px;">

                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="">
                                        <?php
                                        $htmlDisponibles = "";
                                        $resp = $ClTelegram->lista_grupos();
                                        if(!$resp)
                                            $htmlDisponibles = '<tr><td colspan="2">No hay registros</td></tr>';
                                        foreach ($resp as $grupos) {
                                            $checked='checked';
                                            if($grupos['estado'] != 'A')
                                                $checked='';
                                            $htmlDisponibles .= '<tr data-id="'.$grupos['cod_chat'].'">
                                                <td>'.$grupos['nombre'].'
                                                    <dl>
                                                        <dt style="font-size:12px;">'.$grupos['cod_chat'].'</dt>
                                                    </dl>
                                                </td>
                                                <td class="text-center">
                                                    <label class="switch s-icons s-outline  s-outline-success mb-1 mr-1">
                                                      <input type="checkbox" name="chk_estado" id="chk_estado" '.$checked.' value="'.$grupos['cod_chat'].'" class="chkGrupos">
                                                      <span class="slider round"></span>
                                                  </label>
                                                </td>
                                            </tr>';
                                        }  
                                        echo $htmlDisponibles;  
                                        ?>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>

                    <!-- AGOTADOS O PENDIENTES -->
                    <div class="col-xl-7 col-lg-7 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Usuarios</h4>
                                </div>
                            </div> 
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div  class="col-xl-6 col-md-6 col-sm-6 col-12">
                                    <label for="cmb_usuarios_telegram">Usuarios Telegram</label>
                                    <select class="form-control" id="cmb_usuarios_telegram">
                                    <?php
                                    $userTelegram = $ClTelegram->lista_usuarios();
                                    if($userTelegram){
                                        foreach ($userTelegram as $aux) {
                                            echo '<option value="'.$aux['cod_telegram_usuario'].'">'.$aux['nombre'].' '.$aux['apellido'].'</option>';
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div  class="col-xl-6 col-md-6 col-sm-6 col-12">
                                    <label for="cmb_mis_usuarios">Mis Usuarios</label>
                                    <select class="form-control" id="cmb_mis_usuarios">
                                    <?php
                                    $motorizados = $ClUsuarios->lista_motorizados();
                                    if($motorizados){
                                        foreach ($motorizados as $aux) {
                                            echo '<option value="'.$aux['cod_usuario'].'">'.$aux['nombre'].' '.$aux['apellido'].'</option>';
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div  class="col-xl-12 col-md-12 col-sm-6 col-12" style="padding: 15px; text-align: right;">
                                    <button class="btn btn-primary btnAsignar">Asignar</button>
                                </div>    
                            </div>
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px; margin-bottom: 15px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th class="text-center">Asignar</th>
                                                <th class="text-center">Acci&oacute;n</th>
                                            </tr>
                                        </thead>
                                        <tbody id="">
                                        <?php
                                        $htmlDisponibles = "";
                                        $resp = $ClTelegram->lista_usuarios();
                                        if(!$resp)
                                            $htmlDisponibles = '<tr><td colspan="2">No hay registros</td></tr>';
                                        foreach ($resp as $usuario) {
                                            $detalle = "";
                                            $accion = "";
                                            if($usuario['cod_usuario'] != ""){
                                                $info = $ClUsuarios->get($usuario['cod_usuario']);
                                                if($info){
                                                    $detalle = "Asignado a ".$info['nombre']." ".$info['apellido'];
                                                    $accion = '<a href="javascript:void(0);" data-usuario="'.$info['cod_usuario'].'" data-telegram="'.$usuario['cod_telegram_usuario'].'"  class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Quitar asignaci&oacute;n"><i data-feather="x"></i></a>';
                                                }
                                            }

                                            $htmlDisponibles .= '<tr data-id="'.$usuario['cod_telegram_usuario'].'">
                                                <td>'.$usuario['nombre'].' '.$usuario['apellido'].'
                                                    <dl>
                                                        <dt style="font-size:12px;">'.$usuario['cod_telegram_usuario'].'</dt>
                                                    </dl>
                                                </td>  
                                                <td class="text-center">
                                                    '.$detalle.'
                                                </td>
                                                <td class="text-center">
                                                    '.$accion.'
                                                </td>
                                            </tr>';
                                        }  
                                        echo $htmlDisponibles;  
                                        ?>
                                        </tbody>
                                    </table>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="assets/js/pages/telegram.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>