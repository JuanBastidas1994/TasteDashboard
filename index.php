<?php
require_once "funciones.php";

//Clases
require_once "clases/cl_ordenes.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_updates.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clcategorias = new cl_categorias(NULL);
$Clordenes = new cl_ordenes(NULL);
$Clsucursales = new cl_sucursales(NULL);
$Clupdates = new cl_updates(NULL);
$Clempresa = new cl_empresas(null);

$session = getSession();
$cod_usuario = $session['cod_usuario'];
$cod_rol = $session['cod_rol'];
$cod_empresa = $session['cod_empresa'];

if($cod_rol == 19) {
    header('location:reporte_delivery.php');
}

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_update = "";
$titulo = "";
$detalle = "";
$multimedia = "";
$mostrarModal = "";
if(isset($session['cod_usuario'])){
    $visualizado = $Clupdates->mostrarUpdate($session['cod_usuario']);
    if(!$visualizado){
        $hayUpdate = $Clupdates->getLastUpdate($session['cod_empresa'], $update);
        if($hayUpdate){
            $mostrarModal = "Si";
            $cod_update = $update['cod_update'];
            $titulo = $update['titulo'];
            $detalle = editor_decode($update['detalle']);
            $url = editor_decode($update['url']);
            if($url <> "" && $url <> null){
                if($update['tipo_multimedia'] == 1){
                    $multimedia = '<img style="height: 300px;" src="'.$url.'">';    
                }
                else if($update['tipo_multimedia'] == 2){
                    $multimedia = '<iframe src="'.$url.'"></iframe>';
                }
                else{

                    $multimedia = '<lottie-player src="'.$url.'"  background="transparent"  speed="1"  style="height: 300px;"  loop  autoplay></lottie-player>';
                }
            } 
        }    
        else{
            $mostrarModal = "No";
        }
    }
}

/* ALERTAS DE PAGO */
$query = "SELECT * FROM tb_empresa_pagos WHERE cod_empresa = $cod_empresa";
$faltaPagos = Conexion::buscarRegistro($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
    <link href="assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">
    <link rel="stylesheet" href="plugins/font-icons/fontawesome/css/fontawesome.css">
    <link href="assets/css/elements/avatar.css" rel="stylesheet" type="text/css" />
    <style>
        .card-funciones{
            text-align:center;
            border: 1px solid #1b55e2;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
        }
        
        #tab-revenueYearly .apexcharts-legend-text {
            font-size: 10px !important;
        }
    </style>
</head>
<body>
        <input type="hidden" id="id" value="<?= $cod_empresa ?>">

    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

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

        
        <!-- Modal -->
        <div class="modal fade" id="videoMedia1" tabindex="-1" role="dialog" aria-labelledby="videoMedia1Label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" id="videoMedia1Label">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="video-container">
                            <input type="hidden" id="mostrarModal" value="<?= $mostrarModal?>">
                            <input type="hidden" id="codUsuario" value="<?= $cod_usuario?>">
                            <input type="hidden" id="codUpdate" value="<?= $cod_update?>">
                            <div class="row">
                                <div class="col-12" style="text-align: center;">
                                    <?= $multimedia;?>
                                </div>
                                <div class="col-12">
                                    <h3 style="text-align: center; margin-top: 25px; margin-bottom: 25px;"> <?= $titulo;?> </h3>
                                </div>
                                <div class="col-12">
                                    <div class="offset-1 col-10">
                                        <?= $detalle;?>
                                    </div>
                                </div>
                                <div class="offset-9 col-3" style="margin-top: 25px; margin-bottom: 25px;">
                                    <a class="no-mostrar" style="cursor: pointer;">No volver a mostrar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        
        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <?php
                    $allowedRoles = [1,2];
                    if($faltaPagos && in_array($cod_rol, $allowedRoles)) {
                        $tituloFp = $faltaPagos['titulo'];
                        $mensajeFp = $faltaPagos['mensaje'];
                        echo '
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info" role="alert">
                                        <h4 class="alert-heading">'.$tituloFp.'</h4>
                                        <p>
                                            '.$mensajeFp.'
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ';
                    }
                ?>

                <div class="row layout-top-spacing">
                    <?php
                        if($cod_rol == 1){
                            include "content_index/index_super_admin.php";
                        }
                        else if($cod_rol == 3)
                            include "content_index/index_admin_sucursal.php";
                        else if($cod_rol == 2)
                            include "content_index/index_admin_empresa.php";
                    ?>
                </div>
            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="plugins/apex/apexcharts.min.js"></script>
    <script src="assets/js/dashboard/dash_1.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script src="plugins/momentjs/moment.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    <script src="assets/js/dashboard/homeReports.js?v=1" type="text/javascript"></script>
    <script src="assets/js/pages/crear_empresas.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script>
        $(document).ready(function(){
            if($("#mostrarModal").val() == "Si")
                $("#videoMedia1").modal();


            const bc = new BroadcastChannel("my-awesome-site");
            bc.onmessage = (event) => {
                if (event.data === `Am I the first?`) {
                    bc.postMessage(`No you're not.`);
                    console.log(`Another tab of this site just got opened`);
                    
                }
                if (event.data === `No you're not.`) {
                    console.log(`An instance of this site is already running`);
                }
            };
            bc.postMessage(`Am I the first?`);
        });

        $(".no-mostrar").on("click", function(){
            var cod_usuario = $("#codUsuario").val();
            var cod_update = $("#codUpdate").val();
            var parametros = {
                "cod_usuario": cod_usuario,
                "cod_update": cod_update
            }
            $.ajax({
               url:'controllers/controlador_updates.php?metodo=marcarLeido',
               data: parametros,
               type: "GET",
               success: function(response){
                  console.log(response);
                  if(response['success']==1){
                    $("#videoMedia1").modal("hide");    
                    messageDone(response['mensaje'], 'success');
                  }
                  else{
                    messageDone(response['mensaje'], 'error');
                  }
               },
               error: function(data){
               },
               complete: function(){
               },
            });
        });
    </script>
    <script>
        if($('#style-3').length > 0){
            var myTable = $('#style-3').DataTable( {
                dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
                buttons: {
                    buttons: [
                        { extend: 'copy', className: 'btn' },
                        { extend: 'csv', className: 'btn' },
                        { extend: 'excel', className: 'btn' },
                        { extend: 'print', className: 'btn' }
                    ]
                },
                "oLanguage": {
                    "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                    "sInfo": "Showing page _PAGE_ of _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
                },
                "stripeClasses": [],
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 9 
            } );
        }
    </script>
    <script>
        topMenosVentas();
        function topMenosVentas(){
            let parametros = {}
            $.ajax({
                url:'controllers/controlador_reporte_ventas.php?metodo=topMenosVentas',
                data: parametros,
                type: "GET",
                success: function(response){
                    console.log(response);
                    if(response['success']==1){
                        $(".top-menos-ventas").html(response['html']);
                    }
                    else{
                        notify(response['mensaje'], "error");
                    }
                },
                error: function(data){
                },
                complete: function(){
                },
            });
        }
    </script>
</body>
</html>