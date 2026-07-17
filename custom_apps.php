<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$session = getSession();

if(!isset($_GET['id']) || trim($_GET['id']) == ""){
    header("location: ./index.php");
    exit;
}

$alias = $_GET['id'];
$empresa = $Clempresas->getByAlias($alias);
if(!$empresa){
    header("location: ./index.php");
    exit;
}

$cod_empresa = $empresa['cod_empresa'];
$alias = $empresa['alias'];
$nombre = $empresa['nombre'];
$folder = $empresa['folder'];
$urlAndroid = $empresa['url_android'];
$urlIos = $empresa['url_ios'];

$filesActuales = url_sistema.'assets/empresas/'.$empresa['alias'].'/';
$filesActualesUp = url_upload.'assets/empresas/'.$empresa['alias'].'/';

$disabledBtnLogos = "disabled";
if($folder <> ""){
    $disabledBtnLogos = "";
}

$imgPdf = "";
$urlPDF = "";
if(file_exists($filesActualesUp."menu.pdf")){
    $imgPdf = url_sistema.'/assets/img/logoPDF.png';
    $urlPDF = $filesActuales."menu.pdf";
}

/*TABS MOVIBLES DEL BOTTOM BAR*/
$tabsValidos = [
    'menu'      => 'Men&uacute;',
    'orders'    => 'Pedidos',
    'wallet'    => 'Billetera',
    'giftcards' => 'Giftcards',
    'profile'   => 'Perfil',
];
$tabsDefaultOrder = ['menu', 'orders', 'wallet', 'profile'];

$currentOrder = [];
if(!empty($empresa['tabs_order'])){
    $currentOrder = array_filter(array_map('trim', explode(',', $empresa['tabs_order'])));
    $currentOrder = array_values(array_intersect($currentOrder, array_keys($tabsValidos)));
}
if(count($currentOrder) == 0){
    $currentOrder = $tabsDefaultOrder;
}
$availableOrder = array_values(array_diff(array_keys($tabsValidos), $currentOrder));

$htmlSeleccionados = "";
foreach($currentOrder as $key){
    $htmlSeleccionados .= '<li class="list-group-item tab-order-item" data-id="'.$key.'"><i data-feather="move" class="mr-2"></i>'.$tabsValidos[$key].'</li>';
}

$htmlDisponibles = "";
foreach($availableOrder as $key){
    $htmlDisponibles .= '<li class="list-group-item tab-order-item" data-id="'.$key.'"><i data-feather="move" class="mr-2"></i>'.$tabsValidos[$key].'</li>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .tab-order-item{
            cursor: move;
            background-color: #fff;
        }
        .tab-order-list{
            min-height: 220px;
            background-color: #f1f2f3;
            border-radius: 6px;
            padding: 10px;
        }
        #frmLogos img{
            background-color: #c3c3c3;
        }
    </style>
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true); ?>
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
                    <div><span id="btnBack" data-module-back="crear_empresa.php?id=<?= $alias?>" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"><?= $nombre?></span></span>
                    </div>
                    <h3 id="titulo">Configuraci&oacute;n aplicaciones m&oacute;viles</h3>
                    <h5 style="color:#888ea8;"><?= $nombre?></h5>
                </div>

                <div class="row layout-top-spacing">

                    <input type="hidden" name="id" id="id" value="<?= $cod_empresa?>">
                    <input type="hidden" name="alias" id="alias" value="<?= $alias?>">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                        <div class="widget-content widget-content-area br-6">
                            <ul class="nav nav-tabs  mb-3 mt-3" id="lineTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true"><i data-feather="info"></i> Informaci&oacute;n</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="menu-tab" data-toggle="tab" href="#menu" role="tab" aria-controls="menu" aria-selected="false"><i data-feather="menu"></i> Men&uacute;</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="logos-tab" data-toggle="tab" href="#tab-logos" role="tab" aria-controls="logos" aria-selected="false"><i data-feather="image"></i> Logos</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="simpletabContent">

                                <!--CONTENIDO INFO-->
                                <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing" style="margin-top: 15px;">
                                        <div class="widget-content widget-content-area br-6">
                                            <h4>Enlaces de la aplicaci&oacute;n</h4>
                                            <form id="frmCustomAppInfo" name="frmCustomAppInfo" autocomplete="off">
                                                <div class="form-row">
                                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                        <label>URL Android (Play Store)</label>
                                                        <input type="text" placeholder="Ej: https://play.google.com/store/apps/details?id=..." name="txt_url_android" id="txt_url_android" class="form-control" autocomplete="off" value="<?= $urlAndroid?>">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                        <label>URL iOS (App Store)</label>
                                                        <input type="text" placeholder="Ej: https://apps.apple.com/app/..." name="txt_url_ios" id="txt_url_ios" class="form-control" autocomplete="off" value="<?= $urlIos?>">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                        <button type="button" class="btn btn-primary" id="btnGuardarInfoApp">Guardar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin Contenido Info -->

                                <!--CONTENIDO MENU-->
                                <div class="tab-pane fade" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                                    <div class="col-md-12" style="margin-top: 15px;">
                                        <h4>Orden de los tabs del bottom bar</h4>
                                        <p>Arrastra los elementos entre las columnas para activarlos o desactivarlos, y cambia el orden dentro de "Orden actual".</p>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h5>Disponibles</h5>
                                            <ul id="lstTabsDisponibles" class="tab-order-list connectedTabsSortable list-unstyled" style="min-height: 220px;">
                                                <?= $htmlDisponibles?>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Orden actual</h5>
                                            <ul id="lstTabsSeleccionados" class="tab-order-list connectedTabsSortable list-unstyled" style="min-height: 220px;">
                                                <?= $htmlSeleccionados?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="text-align: right;">
                                            <button type="button" class="btn btn-primary" id="btnGuardarTabsOrder">Guardar orden</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin Contenido Menu -->

                                <!--CONTENIDO LOGOS-->
                                <div class="tab-pane fade" id="tab-logos" role="tabpanel" aria-labelledby="logos-tab">
                                    <div class="mb-4 mt-4">
                                         <form id="frmLogos" name="frmLogos">
                                             <input type="hidden" id="hdIdLogo" name="hdIdLogo" value="<?= $cod_empresa?>">
                                             <div class="widget-content widget-content-area">
                                                <h3 class="">Logos</h3>
                                                <br>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo principal 512 <small>(nueva versón: 500 x 350px)</small></label>
                                                        <input class="form-control flLogos" type="file" data-name="logo.png" data-titulo="Logo principal">
                                                        <img src="<?= $filesActuales."logo.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo Footer 512</label>
                                                        <input class="form-control flLogos" type="file" data-name="logo-footer.png" data-titulo="Logo Footer">
                                                        <img src="<?= $filesActuales."logo-footer.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo resposive 512x512</label>
                                                        <input class="form-control flLogos" type="file" data-name="logo-xs.png" data-titulo="Logo resposive">
                                                        <img src="<?= $filesActuales."logo-xs.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono PWA 192x192</label>
                                                        <input class="form-control flLogos" type="file" data-name="icon-192x192.png" data-titulo="Icono PWA 192x192">
                                                        <img src="<?= $filesActuales."icon-192x192.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono PWA 512x512</label>
                                                        <input class="form-control flLogos" type="file" data-name="icon-512x512.png" data-titulo="Icono PWA 512x512">
                                                        <img src="<?= $filesActuales."icon-512x512.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Favicon 16x16</label>
                                                        <input class="form-control flLogos" type="file" data-name="favicon.png" data-titulo="Favicon">
                                                        <img src="<?= $filesActuales."favicon.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo Compartir 512x512 (.jpg con fondo blanco)</label>
                                                        <input class="form-control flLogos" type="file" data-name="compartir.jpg" data-titulo="Logo Compartir 512x512 (.jpg con fondo blanco)">
                                                        <img src="<?= $filesActuales."compartir.jpg"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono App Perfil</label>
                                                        <input class="form-control flLogos" type="file" data-name="profile.png" data-titulo="Icono App Perfil">
                                                        <img src="<?= $filesActuales."profile.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Men&uacute; PDF</label>
                                                        <input class="form-control flLogos" type="file" data-name="menu.pdf" data-titulo="Men&uacute; PDF subido">
                                                        <a href="<?= $urlPDF?>" target="_blank">
                                                            <img src="<?= $imgPdf?>" alt="" style="height: 50px;">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-6 col-12 mb-3" style="text-align: right;">
                                                        <input type="hidden" id="urlFolder" value="<?= $folder?>">
                                                        <button class="btn btn-primary btnActLogosPagina" <?= $disabledBtnLogos?>>Actualizar Logos en la p&aacute;gina</button>
                                                    </div>
                                                </div>
                                             </div>
                                         </form>
                                    </div>
                                </div>
                                <!-- Fin Contenido Logos -->

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
    <script src="assets/js/pages/custom_apps.js?v=1" type="text/javascript"></script>

    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>
