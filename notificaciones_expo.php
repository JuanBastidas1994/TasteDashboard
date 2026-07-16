<?php
require_once "funciones.php";
require_once "clases/cl_notificaciones_expo.php";

if(!isLogin()){
    header("location:login.php");
}

$ClNotificaciones = new cl_notificaciones_expo(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$lista = $ClNotificaciones->lista();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
</head>
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                        <span id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                            <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span>
                        </span>
                        <h3 id="titulo">Notificaciones</h3>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-4 text-right">
                        <a href="crear_notificacion_expo.php?tipo=evento" class="btn btn-primary">Crear notificación general</a>
                    </div>
                </div>
                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>&Uacute;ltimas notificaciones</h4></div>
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>T&iacute;tulo</th>
                                                <th>mensaje</th>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Total enviados</th>
                                                <th>Usuario</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($lista as $items) {
                                                echo '<tr>
                                                    <td>'.$items['titulo'].'</td>
                                                    <td>'.$items['mensaje'].'</td>
                                                    <td>'.$items['tipo'].'</td>
                                                    <td>'.fechaLatinoShort($items['fecha']).'</td>
                                                    <td>'.$items['total_enviados'].'</td>
                                                    <td>'.$items['cod_usuario_admin'].'</td>
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
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <?php js_mandatory(); ?>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>