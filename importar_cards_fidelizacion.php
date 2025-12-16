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
                                    <h4>Importar Tarjetas de Fidelizaci&oacute;n</h4>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <hr/>
                                </div>
                            </div> 
                            
                            <div class="">
                                <form name="frmImportar" id="frmImportar">
                                                                        
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <h3>Subir archivo (Excel)</h3>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-4 col-6">
                                        <label>Acción</label>
                                        <select name="cmbAccion" class="form-control" required="required">
                                            <option value="INSERTAR">Subir C&oacute;digos</option>
                                            <!-- <option value="VERIFICAR">Verificar Estado</option>
                                            <option value="ACTUALIZAR">Actualizar información</option> -->
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-4 col-4">
                                        <label>Archivo</label>
                                        <input class="input" type="file" name="excel" id="excel">
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-sm-4 col-4">
                                        <button type="button" class="btn btn-primary" id="btn_importar" name="btn_importar">Importar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive mb-4 mt-4" id="divDatos" style="display:none;">
                                <h3 style="margin-top: 50px;">Datos Importados</h3>
                                <table id="style-3" class="table style-3  table-hover">
                                    <thead>
                                        <tr>
                                            <th>C&oacute;digos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datos">
                                        
                                    </tbody>
                                </table>
                                <script id="itemTabla" type="text/x-handlebars-template">
                                {{#each this}}
                                    <tr>
                                        <td>{{codigo}}</td>
                                        {{#eq importado true}}
                                            <td style="color:green;">{{importado}}</td>
                                            <td style="color:green;">{{motivo}}</td>
                                        {{else}}
                                            <td style="color:red;">{{importado}}</td>
                                            <td style="color:red;">{{motivo}}</td>
                                        {{/eq}}    
                                    </tr>
                                {{/each}}    
                                </script>
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
    <?php js_mandatory(); ?>

    <script src="assets/js/pages/importar_cards_fidelizacion.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>