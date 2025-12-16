<?php
require_once "funciones.php";
require_once "clases/cl_categorias.php";
require_once "clases/cl_personalizar_web.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_noticias.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clcategorias = new cl_categorias();
$Clpersonalizarweb = new cl_personalizar_web();
$Clnoticias = new cl_noticias(NULL);
$Clproductos = new cl_productos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$query = "SELECT * from tb_empresas where cod_empresa = ".$session['cod_empresa'];
$row = Conexion::buscarRegistro($query, NULL);
$direccion=$row['direccion'];
$telefono=$row['telefono'];
$correo=$row['correo'];

?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
        }

        .inputAccion{
            display:none;
        }
    </style>
</head>
<body>

    <div class="modal fade bs-example-modal-lg" id="modalCroppie" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RECORTADOR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                        
                          <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                            <input type="hidden" id="txt_crop" name="txt_crop" value="" />
                            <input type="hidden" id="txt_crop_min" name="txt_crop_min" value="" />
                             <img id="my-image" src="#" style="width: 100%; max-height: 400px;"/>
                             <input type="hidden" id="img-ori" name="img-ori" value="<?php echo $imagen;?>" />
                          </div>
                         
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
                    <button class="btn btn-dark crop-rotate" data-deg="-90">Rotate Left</button>
                    <button class="btn btn-dark crop-rotate" data-deg="90">Rotate Right</button>
                    <button type="button" class="btn btn-primary" id="crop-get">Recortar</button>
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
        
        <!--MODAL DESCRIPCION -->
    <div class="modal fade bs-example-modal-lg" id="modalDescripcion" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Adicionales</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                        <form id="frmDescripcion" method="POST" action="#">
                                <div class="row">
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                    <label>Categor&iacute;as</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    <input type="hidden" id="id" name="id">
                                                    <select id="cmb_categorias" name="cmb_categorias" class="form-control">
                                                        <?php
                                                            $categorias = $Clcategorias->lista();
                                                            foreach($categorias as $cat){
                                                                echo'<option value="'.$cat['cod_categoria'].'">'.$cat['categoria'].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-6">
                                                    <button id="btn_anadir" name="btn_anadir" class="btn btn-primary">A&ntilde;adir</button>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                <hr>
                                            </div>
                                            
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                <label>Items</label>
                                                <table class="table style-3 table-hover">
                                                    <thead>
                                                      <tr>
                                                        <th>Item</th>
                                                        <th>T&iacute;tulo</th>
                                                        <th>Quitar</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody id="bloque_items" class="connectedSortable">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                
                                            </div>
                                </div> 
                              </div>
                        </form>         
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnSaveDesc" data-id="">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!--MODAL DESCRIPCION -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                
                <div class="col-md-8" >
                    <a href="index.php"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Dashboard</span></span>
                    </a>
                    <h3 id="titulo">Personalizar Web</h3>
                </div>
                

                <div class="row layout-top-spacing" style="display: block;">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content"">
                      <div class="widget-content widget-content-area br-6">
                        <ul class="nav nav-tabs mb-3 mt-3" id="lineTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab-info" role="tab" aria-controls="pills-info" aria-selected="false">
                                    <i data-feather="coffee"></i> 
                                    <span>Adicionales</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tabModalesEventos" data-toggle="tab" href="#tab-modales-eventos" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="award"></i> 
                                    <span>Modales</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">

                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="pills-info-tab">
                                
                                <br>
                                    <div class="row">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <h3 class="">Adicionales</h3>
                                            
                                            <div class="table-responsive mb-4 mt-4">
                                                <table id="style-3" class="table style-3 table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>&nbsp;</th>
                                                                <th>Categor&iacute;a</th>
                                                                <th>Items</th>
                                                                <th class="text-center">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            
                                                            $resp = $Clcategorias->lista();
                                                            foreach ($resp as $personalizar) {
                                                                $x=0;
                                                               if($Clpersonalizarweb->listaByCategoria($personalizar['cod_categoria'], $adicionales)){
                                                                    $x = count($adicionales);
                                                                }
                                                                echo '<tr>
                                                                    <td>'.$personalizar['cod_categoria'].'</td>
                                                                    <td class="nom_cat">'.$personalizar['categoria'].'</td>
                                                                    <td>'.$x.' item(s)</td>
                                                                    <td class="text-center">
                                                                        <ul class="table-controls">
                                                                            <li><a href="javascript:void(0);" data-value="'.$personalizar['cod_categoria'].'" class="bs-tooltip btnEditar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Detalles"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a></li>
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
                                    <br/>
                            </div>

                            <div class="tab-pane fade" id="tab-modales-eventos" role="tabpanel" aria-labelledby="pills-info-tab">
                                <div class="row">
                                    <div class="form-group col-md-5 col-sm-5 col-xs-12">
                                        <form name="frmSave" id="frmSave" autocomplete="off">
                                            <input type="hidden" id="uid" name="uid" require="required"/>
                                            <input type="hidden" id="crop" name="crop" require="required"/>
                                            <h3 class="">Modales para el usuario</h3>
                                            <p>Modal Informativo que le aparecerá al usuario al iniciar la aplicación</p>
                                            <hr/>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label>Cambiar Imagen </label>
                                                <img src="" class="imgEvento" style="width: 100px;"/>
                                                <input type="file" name="image_evento" id="image_evento">
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                                <input type="text" name="txt_titulo" id="txt_titulo" class="form-control" required="required" autocomplete="off" value="">
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                                    <label>Acci&oacute;n</label>
                                                    <select name="cmbAccion" id="cmbAccion" class="form-control cmbAccion">
                                                        <option value="INFO">Informativo (No hace nada)</option>
                                                        <option value="URL">Ir a una URL</option>
                                                        <option value="PRODUCTO">Ir a un Producto</option>
                                                        <option value="NOTICIA">Ir a una noticia</option>
                                                        <option value="FILTER">Filtrar Productos</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                                    <label>Detalle Acci&oacute;n</label>
                                                    <input type="text" placeholder="" name="txt_accion_desc" id="txt_accion_desc" class="form-control inputAccion" autocomplete="off" value="">
                                                    <select id="cmbNoticias" name="cmbNoticias" class="form-control inputAccion">
                                                        <?php 
                                                            $resp = $Clnoticias->lista();
                                                            foreach ($resp as $noticia) {
                                                                echo '<option value="'.$noticia['alias'].'">'.$noticia['titulo'].'</option>';
                                                            }    
                                                        ?>
                                                    </select>
                                                    <select id="cmbProductos" name="cmbProductos" class="form-control inputAccion">
                                                        <?php 
                                                            $resp = $Clproductos->lista();
                                                            foreach ($resp as $productos) {
                                                                echo '<option value="'.$productos['alias'].'">'.$productos['nombre'].'</option>';
                                                            }    
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label>Inicio <span class="asterisco">*</span></label>
                                                    <input name="fecha_inicio" id="hora_ini" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="">
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label>Fin <span class="asterisco">*</span></label>
                                                    <input name="fecha_fin" id="hora_fin" class="form-control flatpickr-input active" type="text" placeholder="Seleccione hora" value="">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                <button class="btn btn-primary btnGuardar">Guardar</button>
                                            </div>
                                        </form>    
                                    </div>

                                    <div class="form-group col-md-7 col-sm-7 col-xs-12">
                                        <h3 class="">Lista</h3>
                                        <table id="style-3" class="table style-3  table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Img</th>
                                                    <th>Titulo</th>
                                                    <th>Tiempo</th>
                                                    <th>Accion</th>
                                                    <th>Detalle</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody id="datos">
                                            </tbody>
                                        </table>
                                        <script id="itemTabla" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td><img src="{{imagen}}" style="width: 80px;"/></td>
                                                <td>{{titulo}}</td>
                                                <td>{{fecha_inicio}} <br>{{fecha_fin}}</td>
                                                <td>{{accion_id}}</td>
                                                <td>{{accion_desc}}</td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                        <li><a href="javascript:void(0);" data-value="{{cod_modal_evento}}" class="btnEditarAnuncio" title="Editar"><i data-feather="edit-2"></i></a></li>
                                                        <li><a href="javascript:void(0);" data-value="{{cod_modal_evento}}"  class="btnEliminarAnuncio" title="Borrar"><i data-feather="trash"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        {{/each}}    
                                        </script>


                                    </div>
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
    <?php js_mandatory(); ?>
     <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script src="assets/js/pages/personalizar_web.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>