<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

//CLASES
$Clempresas = new cl_empresas();
$empresa = $Clempresas->get($session['cod_empresa']);
$cod_tipo_empresa = $empresa['cod_tipo_empresa'];

if($cod_tipo_empresa == 1){
    $linkImagen = 'importar_productos/formato_excelR.jpg';
    $linkExcel = 'importar_productos/formato_excelR.xlsx';
}
else if($cod_tipo_empresa == 2){
    $linkImagen = 'importar_productos/formato_excelC.jpg';
    $linkExcel = 'importar_productos/formato_excelC.xlsx';
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
                
                <div class="row layout-top-spacing">
                    <div style="background-color: white; border-radius: 10px;">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                    <h4>Importar Productos</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="">
                                <form name="frmImportar" id="frmImportar">
                                    <div class="col-xl-6 col-md-6 col-sm-12 col-12" style="margin-bottom: 20px;">
                                        <a href="<?= $linkImagen?>" target="_blank"><img src="<?= $linkImagen?>" alt="ejemplo excel" style="width: 500px;"></a>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="margin-bottom: 20px;">
                                        <a href="<?= $linkExcel?>" target="_blank">Descargar Ejemplo</a>
                                    </div>
                                    
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <label>Subir archivo (Excel)</label>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <input class="input" type="file" name="excel" id="excel">
                                        <button type="button" class="btn btn-primary" id="btn_importar" name="btn_importar">Importar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive mb-4 mt-4" id="divDatos" style="display:none;">
                                <h3 style="margin-top: 50px;">Datos Importados</h3>
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="datos">
                                            
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

    <script src="assets/js/pages/importar_productos.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>