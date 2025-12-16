<?php
    require_once "funciones.php";
    require_once "clases/cl_empresas.php";
    require_once "clases/cl_menu_digital.php";

    $Clempresas = new cl_empresas(null);
    $ClmenuDigital = new cl_menu_digital(null);
    if(!isLogin()){
        header("location:login.php");
    }

    $session = getSession();

    $cod_empresa = $session['cod_empresa'];
    $alias = $session['alias'];

    if(isset($_GET['id'])){
        $alias = $_GET['id'];
        $empresa = $Clempresas->getByAlias($alias);
        if($empresa){
            $cod_empresa = $empresa['cod_empresa'];
        }
        else{
            header("location:crear_menu_digital.php");
        }
    }
    $files = url_sistema.'assets/empresas/'.$alias.'/';

    /*OBTENER MENUs*/
    $menus = $ClmenuDigital->getMenusByEmpresa($cod_empresa);
    $optionsMenu = '<option value="">Seleccione</option>';
    foreach ($menus as $menu) {
        $optionsMenu.= '<option value="'.$menu['cod_menu_digital'].'">'.$menu['titulo'].'</option>';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php css_mandatory(); ?>
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
                
                <div class="col-md-12">
                    <div class="col-md-8" >
                        <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                          <i data-feather="chevron-left"></i><span style="font-size: 16px;vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                        </a>
                        <h3 id="titulo">Men&uacute; Digital</h3>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" id="cod_empresa" value="<?=$cod_empresa?>">
                        <input type="hidden" id="alias" value="<?=$alias?>">
                        <select class="form-control" id="cmbMenus">
                            <?= $optionsMenu?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    
                    <!-- SUBIR IMAGEN -->
                   <div class="col-md-4 col-xs-12 widget-content widget-content-area br-6 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <div class="x_content">   
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="mt-1 pr-md-1">
                                            <label>Subir Imagen (peso m&aacute;x. 1MB)</label>
                                            <input class="form-control" type="file" name="img_menu" id="img_menu"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12 text-right">
                                        <button class="btn btn-primary mt-4 btnSubirImg">Subir</button>
                                    </div>
                                </div>  
                            </form>
                        </div>
                    </div>

                    <!-- LISTA IMÁGENES -->
                    <div class="col-md-8 col-xs-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Im&aacute;genes</h4>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Imagen</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstImagenes" class="connectedSortable">
                                            
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
    <script src="assets/js/pages/menu-digital.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>