<?php
require_once "funciones.php";
require_once "clases/cl_app_versiones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$ClappVersiones = new cl_app_versiones(NULL);
$Clempresas = new cl_empresas(NULL);

$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(isset($_GET['id'])){
    $alias = $_GET['id'];
    $empresa = $Clempresas->getByAlias($alias);
    if($empresa){
        $cod_empresa = $empresa['cod_empresa'];
        $lista = $ClappVersiones->lista($cod_empresa);
    }
    else{
        header("location:index.php");
    }
}
else{
    header("location:index.php");
}
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
                    <div><span id="btnBack" data-module-back="index.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </div>
                    <h3 id="titulo">Crear Versión</h3>
                </div>
                <div class="row layout-top-spacing">
                
                    <div class="col-xl-5 col-lg-5 col-sm-12  layout-spacing">
                      <!-- Datos de Facturacion -->
                      <div class="widget-content widget-content-area br-6">
                            <div><h4>Crear nueva versi&oacute;n</h4></div>
                            <form name="frmSave" id="frmSave" autocomplete="off">
                                <input type="hidden" value="<?= $cod_empresa?>" name="cod_empresa" id="cod_empresa">
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Name <span class="asterisco">*</span></label>
                                            <input class="form-control" type="text" id="txt_name" name="txt_name" placeholder="Name" required>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Code <span class="asterisco">*</span></label>
                                            <input class="form-control" type="number" id="txt_code" name="txt_code" placeholder="Code" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Obligatorio <span class="asterisco">*</span></label>
                                            <input class="form-control" type="number" id="txt_obligatorio" required name="txt_obligatorio" min="0" max="1" placeholder="Obligatorio" value="0">
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Aplicación <span class="asterisco">*</span></label>
                                            <select class="form-control" name="cmb_aplicacion" id="cmb_aplicacion" required>
                                                <option value="ANDROID">ANDROID</option>
                                                <option value="iOS">iOS</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Texto <span class="asterisco">*</span></label>
                                            <textarea class="form-control" name="txt_texto" id="txt_texto" required cols="2" rows="2" maxlength="200" placeholder="Pequeño detalle de la nueva APP"></textarea>
                                        </div>

                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <label for="">Descripción</label>
                                            <textarea class="form-control" name="txt_descripcion" id="txt_descripcion" cols="2" rows="2" placeholder="Descripción"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                            <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </form>
                      </div>
                    </div>

                    <div class="col-xl-7 col-lg-7 col-sm-12  layout-spacing ">
                        <div class="widget-content widget-content-area br-6">
                            <div><h4>&Uacute;ltimas Versiones</h4></div>
                            <div class="table-responsive mb-4 mt-4">
                                <table id="style-3" class="table style-3  table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Code</th>
                                                <th>Obligatorio</th>
                                                <th>Aplicación</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($lista as $version) {
                                                echo '<tr>
                                                    <td>'.$version['cod_empresa_version'].'</td>
                                                    <td>'.$version['name'].'</td>
                                                    <td>'.$version['code'].'</td>
                                                    <td>'.$version['obligatorio'].'</td>
                                                    <td>'.$version['aplicacion'].'</td>
                                                    <td>'.$version['fecha_modificacion'].'</td>
                                                    <td class="text-center">
                                                        <ul class="table-controls">
                                                            <li><a href="javascript:void(0);" data-value="'.$version['cod_empresa_version'].'" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a></li>
                                                        </ul>
                                                    </td>
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
    <script src="assets/js/pages/app_versiones.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>