<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_sucursales.php";

$Clempresas = new cl_empresas(NULL);
$ClSucursales = new cl_sucursales(NULL);
$session = getSession();
$files = url_sistema . 'assets/empresas/' . $session['alias'] . '/';

if (!isLogin()) {
    header("location:login.php");
}

if (isset($_GET['id'])) {
    $alias = $_GET['id'];
    $empresa = $Clempresas->getByAlias($alias);
    if ($empresa) {
        $cod_empresa = $empresa['cod_empresa'];
        $imagen = url_sistema . 'assets/empresas/' . $empresa['alias'] . '/' . $empresa['logo'];
        $nombre = $empresa['nombre'];
        $alias = $empresa['alias'];
        $api = $empresa['api_key'];
    } else {
        header("location: ./index.php");
    }
} else {
    header("location: ./crear_empresa.php");
}

$queryCouriers = "SELECT * FROM tb_empresas WHERE estado = 'A' AND cod_tipo_empresa = 4";
$couriers = Conexion::buscarVariosRegistro($queryCouriers);
foreach($couriers as $key => $courier){
    $querySucursal = "SELECT sf.cod_sucursal 
        FROM tb_sucursal_flota sf
        INNER JOIN tb_sucursales s ON sf.cod_sucursal = s.cod_sucursal AND s.cod_empresa = $cod_empresa
        WHERE sf.cod_flota = ".$courier['cod_empresa'];
    $sucursalesCourier = Conexion::buscarVariosRegistro($querySucursal);
    $codSucursales = array_map(function($item) { return (int)$item['cod_sucursal']; }, $sucursalesCourier);
    $couriers[$key]['sucursales'] = $codSucursales;
}


//OBTENER SUCURSALES
$sucursales = $ClSucursales->listaByEmpresa($cod_empresa);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
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

        .respGalery>div {
            margin-top: 15px;
        }

        .croppie-container .cr-boundary {
            background-image: url(assets/img/transparent.jpg);
            background-position: center;
            background-size: cover;
        }

        .rounded-pills-icon .nav-pills .nav-link.active,
        .rounded-pills-icon .nav-pills .show>.nav-link {
            box-shadow: 0px 5px 15px 0px rgb(0 0 0 / 30%) !important;
            background-color: #f1f2f3 !important;
            border: solid 1px #009688;
            color: #000 !important;
        }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/select2/select2totree.css" rel="stylesheet">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true, "categorias.php"); ?>
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
                    <h3 id="titulo">Configurar Couriers</h3>
                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_producto != 0) ? "" : "display: none;";  ?>">
                        <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                            <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Categor&iacute;a</span>
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
                        <input type="hidden" name="id" id="id" value="<?php echo $cod_empresa; ?>">
                        <div class="widget-content widget-content-area br-6">


                            <div class="widget-content widget-content-area rounded-pills-icon">

                                <ul class="nav nav-pills mb-4 mt-3  justify-content-center" id="rounded-pills-icon-tab" role="tablist">
                                    <?php
                                    foreach ($couriers as $courier) {
                                        $imagen = url_sistema.'assets/empresas/'.$courier['alias'].'/'.$courier['logo'];
                                        $nombreTab = str_replace(" ", "-", $courier['nombre']);
                                        $nombreBoton = $courier['nombre'];
                                        echo '<li class="nav-item ml-2 mr-2">
                                                    <a class="nav-link mb-2 text-center tabCourier" data-id="'.$courier['cod_courier'].'" id="rounded-pills-icon-home-tab" data-toggle="pill" href="#rounded-pills-icon-'.strtolower($nombreTab).'" role="tab" aria-controls="rounded-pills-icon-'.strtolower($nombreTab).'" aria-selected="true">
                                                        <img src="'.$imagen.'" alt="'.$nombreBoton.'" style="height: 30px;"> 
                                                        <br>
                                                        '.$nombreBoton.'
                                                    </a>
                                                </li>';
                                    }
                                    ?>
                                </ul>


                                <div class="tab-content" id="rounded-pills-icon-tabContent">
                                    <?php
                                    foreach ($couriers as $courier) {
                                        $nombreTab = str_replace(" ", "-", $courier['nombre']);
                                        $courierName = $courier['nombre'];
                                        $sucursalCourier = $courier['sucursales'];
                                        
                                        $tablaSucursales = '';
                                        foreach ($sucursales as $sucursal) {
                                            $cod_sucursal = $sucursal['cod_sucursal'];
                                            $nom_sucursal = $sucursal['nombre'];
                                            $ckMisMotosCheck = "";
                                            if(in_array($cod_sucursal, $sucursalCourier)){
                                                $ckMisMotosCheck = "checked";
                                            }
                                            $tablaSucursales .= 
                                                '<tr class="">
                                                    <td><span>' . $cod_sucursal . '</span></td>
                                                    <td>' . $nom_sucursal . '</td>
                                                    <td class="text-center">
                                                        <label class="switch s-icons s-outline s-outline-success">
                                                            <input type="checkbox" ' . $ckMisMotosCheck . ' data-courier="'.$courier['cod_empresa'].'" class="ckMiFlota" value="' . $cod_sucursal . '">
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                </tr>';
                                        }
                                        
                                        
                                        echo '<div class="tab-pane fade show" id="rounded-pills-icon-'.strtolower($nombreTab).'" role="tabpanel" aria-labelledby="rounded-pills-icon-home-tab">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>'.$courierName.'</h4>
                                            </div>
                                            <table id="style-4" class="table style-3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Id</th>
                                                        <th>Nombre</th>
                                                        <th class="text-center">Token Empresa</th>
                                                        <th class="text-center">Token Sucursal</th>
                                                        <th colspan="2" class="text-center">Acci&oacute;n</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tablaDelivery">
                                                    '.$tablaSucursales.'
                                                </tbody>
                                            </table>
                                        </div>';
                                    }
                                        
                                    
                                    ?>
                                    
                                    <!-- WHATSAPP -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-whatsappasda" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>Whatsapp - Sucursales</h4>
                                        </div>

                                        <table id="style-6" class="table style-3">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th>Nombre</th>
                                                    <th colspan="2" class="text-center">Act/Desact mis motorizados</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbMisMotos">
                                                <?= $whatsappSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    
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
    <script src="assets/js/pages/empresas_courier.js?v=1" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="plugins/select2/select2totree.js"></script>
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>

</html>