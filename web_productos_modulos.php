<?php
require_once "funciones.php";
require_once "clases/cl_web_modulos.php";
require_once "clases/cl_productos.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$ClWebModulos = new cl_web_modulos(NULL);
$Clproductos = new cl_productos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
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
                
                <div class="col-md-8" >
                    <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px;vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </a>
                    <h3 id="titulo">M&oacute;dulos Web</h3>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="cmbModulos">
                    <?php
                    $modulos = $ClWebModulos->lista();
                    foreach ($modulos as $modulo) {
                        echo '<option value="'.$modulo['cod_web_modulos_producto'].'">'.$modulo['nombre'].'</option>';
                    }    
                    ?>
                    </select>
                </div>

                <div class="row layout-top-spacing" style="display: block;">
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Productos</h4>
                                </div>
                            </div>

                            <div id="" class="table-responsive mb-4 mt-4" style="max-height: 500px;">

                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th>Precio</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstDisponibles" class="connectedSortable">
                                        <?php
                                        $htmlDisponibles = "";
                                        $resp = $Clproductos->lista();
                                        if(!$resp)
                                            $htmlDisponibles = '<tr><td colspan="4">No hay registros</td></tr>';
                                        foreach ($resp as $productos) {
                                            $code = ($productos['sku']!=="") ? $productos['sku'] : $productos['cod_producto'];
                                            $code .= '<br/>'; 
                                            $imagen = $files.$productos['image_min'];
                                            $badge='primary';
                                            if($productos['estado'] == 'I')
                                                $badge='danger';
                                            $htmlDisponibles .= '<tr data-id="'.$productos['cod_producto'].'">
                                                <td class="text-center">
                                                    <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                </td>
                                                <td>'.$code.$productos['nombre'].'</td>
                                                <td>$'.number_format($productos['precio'],2).'</td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">'.getEstado($productos['estado']).'</span></td>
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
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Orden</h4>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Image</th>
                                                <th>Nombre</th>
                                                <th class="text-center">Precio</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstAgotados" class="connectedSortable">
                                            
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
    <script src="assets/js/pages/web_productos_modulos.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>