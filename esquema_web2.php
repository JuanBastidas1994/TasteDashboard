<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_web_anuncios.php";
require_once "clases/cl_web_modulos.php";
if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clempresas = new cl_empresas(NULL);
$ClWebAnuncios = new cl_web_anuncios(NULL);
$ClWebModulos = new cl_web_modulos(NULL);
$session = getSession();
$nombre = "";
if(isset($_GET['id'])){
    $cod_empresa=($_GET['id']);
    $empresa = $Clempresas->get($cod_empresa);
    $nombre = $empresa['nombre'];
}
else{
header("location: ./index.php");
}

//$files = url_sistema.'assets/empresas/'.$alias.'/';
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
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="editarEsquemaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CREAR CUPON</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" placeholder="" name="id" id="id" class="form-control" required="required" autocomplete="off" value="0"/>
                <form id="frmModalEsquema" name="frmModalEsquema" class="form-horizontal form-label-left">    
                    <div class="x_content">    
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:15px;">
                                <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                <input type="text" id="utxt_titulo" class="form-control" required="required" autocomplete="off" value="">
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label>Forma </label>
                                <select class="form-control" id="ucmbForma">
                                    <option value="lista_4">Lista de 4 Columnas</option>
                                    <option value="slide_4">Slide de 4</option>
                                    <option value="banner">Banner</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label>Número de columnas </label>
                                <select class="form-control" id="ucmbNumColumnas">
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <input type="hidden" id="uid" value="">

                            </div>
                        </div>    
                    </div>
                </form>    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnActualizarEsquema">Guardar</button>
                </div>
            </div>
        </div>
    </div>

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
                
                <div class="row layout-top-spacing" style="display: block;">
                    <div><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Empresas</span></span>
                    </div>
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-7">
                            <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : ""; ?></h3>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <div class="col-6" style="display: flex; align-items: center;">
                                    <label style="margin: 0; text-align: right; width: 100%;">Plataforma </label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control" id="cmbPlataforma" name="cmbPlataforma">
                                        <option value="WEB">WEB</option>
                                        <option value="APP">APP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DISPONIBLES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Esquema Web</h4>
                                </div>
                            </div>
                            <form name="frmSave" id="frmSave" autocomplete="off">    
                            <input type="hidden" id="idEmpresa" name="idEmpresa" value="<?php echo $cod_empresa ?>">
                            <div id="" class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                     <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-top: 20px;">
                                        <label for="">Tipo de esquema</label>
                                        <select class="form-control" id="cmbTipo" name="cmbTipo">
                                            <option value="ordenar">Ordenar Items</option>
                                            <option value="anuncios">Anuncions Web</option>
                                            <option value="youtube">Youtube</option>
                                            <option value="iframe">Iframe</option>
                                            <option value="blog">Blog</option>
                                            <option value="html">Html</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12" style="margin-top: 20px;">
                                        <label for="">Sección</label>
                                        <select class="form-control" id="cmbModulos" name="cmbModulos" style="display:none">
                                        <?php
                                        $modulos = $ClWebModulos->listabyempresa($cod_empresa);
                                        foreach ($modulos as $modulo) {
                                            echo '<option value="'.$modulo['cod_web_modulos_producto'].'">'.$modulo['nombre'].'</option>';
                                        }    
                                        ?>
                                        </select>
                                        
                                        <select class="form-control" id="cmbAnuncios" name="cmbAnuncios"  style="display:none">
                                        <?php
                                        $modulos = $ClWebAnuncios->listabyempresa($cod_empresa);
                                        foreach ($modulos as $modulo) {
                                            echo '<option value="'.$modulo['cod_anuncio_cabecera'].'">'.$modulo['nombre'].'</option>';
                                        }    
                                        ?>
                                        </select>
                                    </div>
                                
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                    <input type="text" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off" value="">
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <label>Forma </label>
                                    <select class="form-control" id="cmbForma" name="cmbForma">
                                        <option value="lista_4">Lista de 4 Columnas</option>
                                        <option value="slide_4">Slide de 4</option>
                                        <option id="optionBanner" value="banner" style="display: none;">Banner</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12" id="bloqueNumColumnas" style="display: none;">
                                    <label>Número de columnas </label>
                                    <select class="form-control" id="cmbNumColumnas" name="cmbNumColumnas">
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                

                            </div>
                            <div class="form-row">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align:right;">
                                        <button type="button" class="btn btn-danger" id="btnLimpiar" wfd-id="137">Limpiar</button>
                                       <button type="button" class="btn btn-primary" id="btnGuardar" wfd-id="137">Guardar</button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>

                    <!-- AGOTADOS O PENDIENTES -->
                    <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Lista - Orden</h4>
                                </div>
                            </div> 
                            <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                <table id="style-3" class="table style-3">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Titulo</th>
                                                <th>Forma</th>
                                                <th class="text-center">Detalle</th>
                                                <th class="text-center">Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lstEsquema" class="connectedSortable">
                                            
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
    <script src="assets/js/pages/web_esquema.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>