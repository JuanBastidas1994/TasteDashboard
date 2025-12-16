<?php
require_once "funciones.php";
require_once "clases/cl_timeline.php";

if(!isLogin()){
    header("location:login.php");
}

$Cltimeline = new cl_timeline(NULL);
$session = getSession();

$cod_empresa = $session['cod_empresa']; 
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$imageDefault = $imagen = url_sistema.'/assets/img/200x200.jpg';

$nombre = "";
$estado = "checked";

$cod_timeline = isset($_GET["id"]) ?  $_GET["id"] : 0;
$alias = isset($_GET["alias"]) ?  $_GET["alias"] : "";
if($alias == "")
    header("location:index.php"); //TODO Redirigir bien

if($cod_timeline > 0){
    $timeline = $Cltimeline->getById($cod_timeline);
    if(!$timeline)
        header("location:index.php"); //TODO Redirigir bien
    $nombre = $timeline["nombre"];
    if($timeline["estado"] <> "A")
        $estado = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
    <!--<link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />

    <style>
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
            -webkit-transition: border-color .15s linear;
            transition: border-color .15s linear;
            border: 1px solid #acb0c3;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(false); ?>
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
                    <div class="col-md-8" >
                        <h3 id="titulo">Timeline</h3>
                    </div>
                </div>
                <div class="row layout-top-spacing">
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        
                        <div class="widget-content widget-content-area br-6">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-row mt-3">
                                            <div class="col-8">
                                                <label>Nombre *</label>
                                                <input type="text" id="nombre" class="form-control" placeholder="Nombre" value="<?=$nombre?>">
                                                <input type="hidden" id="id" value="<?=$cod_timeline?>">
                                                <input type="hidden" id="alias" value="<?=$alias?>">
                                            </div>
                                            <div class="col" style="margin-bottom:10px;">
                                                <label>Estado <span class="asterisco">*</span></label>
                                                <div>
                                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                        <input type="checkbox" name="chk_estado" id="chk_estado" <?php echo $estado; ?> />
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row mt-3">
                                            <div class="col-12 text-right">
                                                <button class="btn btn-primary" id="btnGuardar">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row layout-top-spacing d-none timelineDetalles">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6 mt-3">
                            <div class="x_content">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>Detalles</h3>
                                    </div>      
                                </div>
                                <form id="frmTimelineDet" method="POST">
                                    <div class="row lstDetalle connectedSortable">
                                        <div class="itemDetalle col-12">
                                            <div class="form-row mt-3">
                                                <input class="idHidden" type="hidden" name="idDetalle[]" value="0">
                                                <div class="col-1 align-self-center text-center">
                                                    <span style="cursor: move;"><i data-feather="align-justify"></i></span>
                                                </div>
                                                <div class="col-3 divImg">
                                                    
                                                </div>
                                                <div class="col-3">
                                                    <label>Título *</label>
                                                    <input type="text" name="titulo[]" id="titulo0"
                                                    class="form-control txtTitulo" placeholder="Título" required>
                                                </div>
                                                <div class="col-3">
                                                    <label>Subtítulo *</label>
                                                    <input type="text" name="subtitulo[]" id="subtitulo0" class="form-control txtSubtitulo" placeholder="Subtítulo" required>
                                                </div>
                                                <div class="col-2 align-self-center">
                                                    <button class="btn btn-danger btnDel"><i data-feather="trash-2"></i></button>
                                                    <button class="btn btn-success btnAdd"><i data-feather="plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-12 text-right mt-3">
                                        <button class="btn btn-primary" id="btnGuardarDetalles">Guardar</button>
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
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    <?php js_mandatory(); ?>
    <script src="./assets/js/libs/jquery.validate.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="./assets/js/pages/timeline_crear.js"></script>
</body>
</html>