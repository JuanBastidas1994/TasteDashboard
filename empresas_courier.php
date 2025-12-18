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

$queryCouriers = "SELECT *
                    FROM tb_courier
                    WHERE estado = 'A'";
$couriers = Conexion::buscarVariosRegistro($queryCouriers);

//HTML COURIERS
$htmlCouriers = "";
foreach ($couriers as $courier) {
    $nombreImg = str_replace(" ", "_", $courier['nombre']);
    $nombreTab = str_replace(" ", "-", $courier['nombre']);
    $nombreBoton = $courier['nombre'];
    $htmlCouriers.= '  <li class="nav-item ml-2 mr-2">
                <a class="nav-link mb-2 text-center tabCourier" data-id="'.$courier['cod_courier'].'" id="rounded-pills-icon-home-tab" data-toggle="pill" href="#rounded-pills-icon-'.strtolower($nombreTab).'" role="tab" aria-controls="rounded-pills-icon-'.strtolower($nombreTab).'" aria-selected="true">
                    <img src="assets/img/'.strtolower($nombreImg).'.png" alt="'.$nombreBoton.'" style="height: 30px;"> 
                    <br>
                    '.$nombreBoton.'
                </a>
            </li>';
}

//OBTENER SUCURSALES
$sucursales = $ClSucursales->listaByEmpresa($cod_empresa);

//HTML GACELA
$gacelaSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $infoG = $ClSucursales->getgacelaSucursalAmbiente($cod_sucursal, 'development');
    $api = "";
    $token = "";
    $ver = "";
    $cod_gacela_sucursal = 0;
    if ($infoG) {
        $api = $infoG['api'];
        $token = $infoG['token'];
        $cod_gacela_sucursal = $infoG['cod_gacela_sucursal'];
        $ambiente = ($infoG['ambiente']);
        $ver = '<td  class="text-center"><button type="button" class="btn btn-outline-primary btnVerconfigGacela" data-api="' . $api . '" data-token="' . $token . '"> <i data-feather="eye"></i></button></td>';
    }
    $gacelaSucursales .= '<tr class="">
                                    <td><span>' . $cod_sucursal . '</span></td>
                                    <td><input type="text" id="txt_nombreG' . $cod_sucursal . '" class="form-control " value="' . $nom_sucursal . '" disabled></td>
                                    <td><input type="text" id="txt_empresaG' . $cod_sucursal . '" class="form-control " value="' . $api . '" disabled></td>
                                    <td><input type="text" id="txt_sucursalG' . $cod_sucursal . '" class="form-control " value="' . $token . '" disabled></td>
                                    <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarGacela " id="editar_' . $cod_sucursal . '" data-codigo="' . $cod_sucursal . '" data-id="' . $cod_gacela_sucursal . '">Editar</button></td>
                                    ' . $ver . '
                                </tr>';
}

//LAAR
$laarSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $infoL = $ClSucursales->getLaarSucursal($cod_sucursal);
    $user = "";
    $pass = "";
    $cod_laar_sucursal = 0;
    if ($infoL) {
        $user = $infoL['username'];
        $pass = $infoL['password'];
        $cod_laar_sucursal = $infoL['cod_laar_sucursal'];
    }
    $laarSucursales .= '    <tr id="contAn' . $cod_sucursal . '" >
                                        <td><span>' . $cod_sucursal . '</span></td>
                                        <td><input type="text" id="txt_nombreL' . $cod_sucursal . '" class="form-control " value="' . $nom_sucursal . '" disabled></td>
                                        <td><input type="text" id="txt_userL' . $cod_sucursal . '" class="form-control " value="' . $user . '" disabled></td>
                                        <td><input type="text" id="txt_passL' . $cod_sucursal . '" class="form-control " value="' . $pass . '" disabled></td>
                                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarLaar " id="editar_L' . $cod_sucursal . '" data-codigo="' . $cod_sucursal . '" data-id="' . $cod_laar_sucursal . '">Editar</button></td>
                                    </tr>';
}

//PICKER
$pickerSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $infoP = $ClSucursales->getpickerSucursalAmbiente($cod_sucursal, 'development');
    $api = "";
    $token = "";
    $ver = "";
    $cod_picker_sucursal = 0;
    if ($infoP) {
        $api = $infoP['api'];
        // $token = $infoP['token'];
        $cod_picker_sucursal = $infoP['cod_picker_sucursal'];
        $ambiente = ($infoP['ambiente']);
        $ver = '<td  class="text-center"><button type="button" class="btn btn-outline-primary btnVerconfigPicker" data-api="' . $api . '" data-token="' . $cod_sucursal . '"> <i data-feather="eye"></i></button></td>';
    }
    $pickerSucursales .= 
        '<tr class="">
            <td><span>' . $cod_sucursal . '</span></td>
            <td><input type="text" id="txt_nombreP' . $cod_sucursal . '" class="form-control " value="' . $nom_sucursal . '" disabled></td>
            <td><input type="text" id="txt_empresaP' . $cod_sucursal . '" class="form-control " value="' . $api . '" disabled></td>
            <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarPicker " id="editar_' . $cod_sucursal . '" data-codigo="' . $cod_sucursal . '" data-id="' . $cod_picker_sucursal . '">Editar</button></td>
            ' . $ver . '
        </tr>';
}

//PEDIDOSYA
$pedidosYaSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $infoPYA = $ClSucursales->getPedidosYaSucursalAmbiente($cod_sucursal, 'development');
    $token = "";
    $ver = "";
    $cod_pedidosya_sucursal = 0;
    if ($infoPYA) {
        $token = $infoPYA['token'];
        $cod_pedidosya_sucursal = $infoPYA['cod_pedidosya_sucursal'];
        $ambiente = ($infoPYA['ambiente']);
    }
    $pedidosYaSucursales .= 
        '<tr class="">
            <td><span>' . $cod_sucursal . '</span></td>
            <td><input type="text" id="txt_nombrePYA' . $cod_sucursal . '" class="form-control " value="' . $nom_sucursal . '" disabled></td>
            <td><input type="text" id="txt_empresaPYA' . $cod_sucursal . '" class="form-control " value="' . $token . '" disabled></td>
            <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarPedidosYa " id="editar_ya' . $cod_sucursal . '" data-codigo="' . $cod_sucursal . '" data-id="' . $cod_pedidosya_sucursal . '">Editar</button></td>
        </tr>';
}

//MIS MOTORIZADOS
$misMotorizadosSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $ckMisMotosCheck = "";
    $infoM = $ClSucursales->getCourierOffice($cod_sucursal, 99);
    if($infoM){
        if($infoM["estado"] == "A")
            $ckMisMotosCheck = "checked";
    }
    $misMotorizadosSucursales .= 
        '<tr class="">
            <td><span>' . $cod_sucursal . '</span></td>
            <td>' . $nom_sucursal . '</td>
            <td class="text-center">
                <label class="switch s-icons s-outline s-outline-success">
                    <input type="checkbox" ' . $ckMisMotosCheck . ' data-courier="99" class="ckMisMotos" value="' . $cod_sucursal . '">
                    <span class="slider round"></span>
                </label>
            </td>
        </tr>';
}

//WHATSAPP
$whatsappSucursales = "";
foreach ($sucursales as $sucursal) {
    $cod_sucursal = $sucursal['cod_sucursal'];
    $nom_sucursal = $sucursal['nombre'];
    $ckMisMotosCheck = "";
    $infoM = $ClSucursales->getCourierOffice($cod_sucursal, 100);
    if($infoM){
        if($infoM["estado"] == "A")
            $ckMisMotosCheck = "checked";
    }
    $whatsappSucursales .= 
        '<tr class="">
            <td><span>' . $cod_sucursal . '</span></td>
            <td>' . $nom_sucursal . '</td>
            <td class="text-center">
                <label class="switch s-icons s-outline s-outline-success">
                    <input type="checkbox" ' . $ckMisMotosCheck . ' data-courier="100" class="ckMisMotos" value="' . $cod_sucursal . '">
                    <span class="slider round"></span>
                </label>
            </td>
        </tr>';
}
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

                    <div class="col-xl-8 col-lg-8 col-sm-12  layout-spacing">
                        <input type="hidden" name="id" id="id" value="<?php echo $cod_empresa; ?>">
                        <div class="widget-content widget-content-area br-6">


                            <div class="widget-content widget-content-area rounded-pills-icon">

                                <ul class="nav nav-pills mb-4 mt-3  justify-content-center" id="rounded-pills-icon-tab" role="tablist">
                                    <?=$htmlCouriers?>
                                </ul>


                                <div class="tab-content" id="rounded-pills-icon-tabContent">
                                    
                                    
                                    <!-- TAB GACELA -->
                                    <div class="tab-pane fade show active" id="rounded-pills-icon-gacela" role="tabpanel" aria-labelledby="rounded-pills-icon-home-tab">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>GACELA - Sucursales</h4>
                                        </div>
                                        <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                            <label>Ambiente <span class="asterisco">*</span></label>
                                            <select class="form-control" id="cmbAmbienteGacela" name="cmbAmbienteGacela" style="margin-bottom: 15px;">
                                                <option value="development">Desarrollo</option>
                                                <option value="production">Produccion</option>
                                            </select>
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
                                                <?= $gacelaSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- TAB LAAR -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-laar" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>LAAR - Sucursales</h4>
                                        </div>
                                        <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                            <label>Ambiente <span class="asterisco">*</span></label>
                                            <select class="form-control" id="cmbAmbienteLaar" name="cmbAmbienteLaar" style="margin-bottom: 15px;">
                                                <option value="development">Desarrollo</option>
                                                <option value="production">Produccion</option>
                                            </select>
                                        </div>

                                        <table id="style-5" class="table style-3">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th>Nombre</th>
                                                    <th class="text-center">Usuario</th>
                                                    <th class="text-center">Contrase&ntilde;a</th>
                                                    <th colspan="2" class="text-center">Acci&oacute;n</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?= $laarSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- TAB PICKER -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-picker" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>PICKER - Sucursales</h4>
                                        </div>

                                        <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                            <label>Ambiente <span class="asterisco">*</span></label>
                                            <select class="form-control" id="cmbAmbientePicker" name="cmbAmbientePicker" style="margin-bottom: 15px;">
                                                <option value="development">Desarrollo</option>
                                                <option value="production">Produccion</option>
                                            </select>
                                        </div>

                                        <table id="style-6" class="table style-3">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th>Nombre</th>
                                                    <th class="text-center">Token Sucursal</th>
                                                    <th colspan="2" class="text-center">Acci&oacute;n</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaDeliveryPicker">
                                                <?= $pickerSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- PEDIDOS YA -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-pedidosya" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>PedidosYa - Sucursales</h4>
                                        </div>

                                        <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                            <label>Ambiente <span class="asterisco">*</span></label>
                                            <select class="form-control" id="cmbAmbientePedidosYa" name="cmbAmbientePedidosYa" style="margin-bottom: 15px;">
                                                <option value="development">Desarrollo</option>
                                                <option value="production">Produccion</option>
                                            </select>
                                        </div>

                                        <table id="style-6" class="table style-3">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th>Nombre</th>
                                                    <th class="text-center">Token Sucursal</th>
                                                    <th class="text-center">Estado</th>
                                                    <th colspan="2" class="text-center">Acci&oacute;n</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaDeliveryPedidosYa">
                                                <?= $pedidosYaSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- MIS MOTORIZADOS -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-mis-motorizados" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>Mis Motos - Sucursales</h4>
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
                                                <?= $misMotorizadosSucursales ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- WHATSAPP -->
                                    <div class="tab-pane fade" id="rounded-pills-icon-whatsapp" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
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

                    <div class="col-xl-4 col-lg-4 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            Bloque sucursales
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
    <script src="assets/js/pages/empresas_courier.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- HANDLEBARS -->
    <script src="./assets/js/libs/handlebars/handlebars.js"></script>
    <script src="./assets/js/libs/handlebars/helpers.js"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="plugins/select2/select2totree.js"></script>
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <!-- TEMPLATES HANDLEBARS -->
    <script id="pedidosya-template" type="text/x-handlebars-template">
        {{#each this}}
            <tr class="">
                <td>
                    <span>{{cod_sucursal}}</span>
                </td>
                <td>
                    <input type="text" id="txt_nombrePYA{{cod_sucursal}}" class="form-control " value="{{nombre}}" disabled>
                </td>
                <td>
                    <input type="text" id="txt_empresaPYA{{cod_sucursal}}" class="form-control " value="{{token}}" disabled>
                </td>
                <td  class="text-center align-self-center">
                    <label class="switch s-icons s-outline s-outline-success">
                        <input type="checkbox" class="estadoToken"
                            {{#eq tokenEstado "A"}}
                                checked
                            {{/eq}}
                        >
                        <span class="slider round"></span>
                    </label>
                </td>
                <td  class="text-center">
                    <button type="button" class="btn btn-outline-primary btnEditarPedidosYa" id="editar_ya{{cod_sucursal}}" data-codigo="{{cod_sucursal}}" data-id="{{cod_pedidosya_sucursal}}">Editar</button>
                </td>
            </tr>
        {{/each}}
    </script>
</body>

</html>