<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_categorias.php";

if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);
$Clcategorias = new cl_categorias(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$alias = $_GET['id'];
$Clproductos->getArrayByAlias($alias,$array);
$nom_product=$array['nombre'];
$cod_product=$array['cod_producto'];
$estado = "";
$cantidad ="";
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
                
                <div class="col-md-8" >
                    <a href="crear_productos.php?id=<?php echo $alias?>"><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Regresar al producto</span></span>
                    </a>
                    <h3 id="titulo">Detalle del producto</h3>
                    <input type="hidden" id="cod_original" value="<?php echo $cod_product?>">
                </div>
            
                <div class="row layout-top-spacing" style="display: block;">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6" style="padding-bottom: 30px;">
                            
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <?php
                                    echo'<h4>'.$nom_product.'</h4>';
                                ?>
                                </div>
                            </div>
                           
                               
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12 widget-content widget-content-area">
                                <form id="frmOpciones" name="frmOpciones">
                                    <?php
                                        echo'<input type="hidden" value="'.$cod_product.'" id="nombreProducto" name="nombreProducto">';
                                    ?>
                                  <div class="br-6">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="25">
                                        <h4>Categorias</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-12" >
                                        <div class="form-group" style="margin-top: 20px;">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                                <label>Nombre</label>
                                                <input type="text" class="form-control" name="txt_nombre_opc">
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12 form-group" style="margin-top: 20px;">
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                min<input type="number" class="form-control" name="txt_min">
                                            </div>
                                            
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                max<input type="number" class="form-control" name="txt_max">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-12" >
                                    
                                        <div class="row" style="margin-top: 30px;">
                                            <div class="col-xl-2 col-md-2 col-sm-2 col-2" >
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                      <input class="ck_isCheck" type="checkbox" name="ck_isCheck" data-name="" data-codigo=""/>
                                                      <span class="slider round"></span>
                                                </label>
                                                
                                            </div>
                                            <div class="col-xl-10 col-md-10 col-sm-10 col-10" >
                                                <span>Is Check</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xl-2 col-md-2 col-sm-2 col-2" >
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                      <input class="ck_isDB" type="checkbox" name="ck_isDB" data-name="" data-codigo=""/>
                                                      <span class="slider round"></span>
                                                </label>
                                                
                                            </div>
                                            <div class="col-xl-10 col-md-10 col-sm-10 col-10" >
                                                <span>Is Database</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div align="center" class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                                <button class="btnGuardaOpc btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="margin-top: 20px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                            <h4>Items</h4>
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                            <hr>
                                        </div>
                                       
                                        
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12 itemAdd" >
                                            <div class="col-xl-4 col-md-4 col-sm-12 col-12" >
                                                <input type="hidden" id="idOpcion" name="idOpcion">
                                                <label>Nombre Detalle</label>
                                                <input type="text" class="form-control txt_nom_item" placeholder="Nombre Item" name="txt_nom_item[]">
                                            </div>
                                            <div align="center" class="col-xl-2 col-md-2 col-sm-12 col-12" >
                                                <label>Agregar Precio</label>
                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                      <input class="precioCheck" type="checkbox" name="precioCheck[]"/>
                                                      <span class="slider round"></span>
                                                </label>
                                            </div>
                                            <div align="right" class="col-xl-3 col-md-3 col-sm-12 col-12" >
                                                <label>Precio ($)</label>
                                                <input type="number" class="form-control txt_precio" name="txt_precio[]" placeholder="precio" readonly value="0" style="text-align: right;">
                                            </div>
                                            <div align="center" class="col-xl-3 col-md-3 col-sm-12 col-12" >
                                                <label>Acciones</label><br>
                                                <button type="button" class="btn btn-primary btnAddItem">+</button>
                                            </div>
                                        </div>
                                        
                                        <div class="divTable connectedSortable ui-sortable">
                                            
                                        </div>
                                        <div align="right" class="col-xl-12 col-md-12 col-sm-12 col-12" >
                                            <button type="button" class="btn btn-primary btnGuardarDet">Guardar</button>
                                        </div> 
                                    </div> 
                                    
                                  </div>
                                </form>
                            </div>


                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" style="margin-top: 50px;">
                              <div class="widget-content widget-content-area br-6">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="25">
                                    <h4>Ordenar</h4>
                                </div>
                                <table id="style-3" class="table style-3">
                                    <thead>
                                        <tr>
                                            <th>Categoria</th>
                                            <th class="text-center">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contentLista" class="connectedSortable">
                                        <?php
                                     $resp = $Clproductos->lista_productos_extras($cod_product);
                                    foreach ($resp as $productosExtra) {
                                     echo'
                                        <tr  data-name="'.$productosExtra['titulo'].'" id="'.$productosExtra['titulo'].'">
                                            <td>'.$productosExtra['titulo'].'</td>
                                            <td class="text-center">
                                              <input type="number" name="cantidad" id="lista-'.$productosExtra['cod_producto_extra'].'" disabled class="form-control" required="required" autocomplete="off" value="'.$productosExtra['cantidad'].'">
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
            </div> 

          
                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing" style="margin-top: 30px">
                    <div class="widget-content widget-content-area br-6">
                        <ul class="nav nav-tabs  mb-3 mt-3" id="simpletab" role="tablist">
                            <?php
                                $resp = $Clcategorias->lista_extras();
                                foreach ($resp as $categoriaExtra) 
                                {
                                  $temp1 = $Clproductos->coincidencia_extras($cod_product,$categoriaExtra['categoria']);
                                   if($temp1){$cod_extra =$temp1['cod_producto_extra'];}else{$cod_extra =0;}
                                    echo'
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="" id="tab-'.$categoriaExtra['cod_categoria'].'" data-id="'.$categoriaExtra['cod_categoria'].'" role="tab" aria-controls="home" codigo_padre="'.$cod_extra.'" aria-selected="true" href="#panel-'.$categoriaExtra['cod_categoria'].'">'.$categoriaExtra['categoria'].'</a>
                                    </li>';
                                }
                            $temp1 = $Clproductos->coincidencia_extras($cod_product,"Extra");
                            if($temp1){$cod_extra =$temp1['cod_producto_extra'];}else{$cod_extra =0;}
                            echo'
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="" id="tab-0" data-id="0" role="tab" aria-controls="home" codigo_padre="'.$cod_extra.'" href="#panel-0"aria-selected="true">Extras</a>
                            </li>';
                            ?>
                        </ul>

                        <div class="tab-content" id="simpletabContent">
                            <?php
                                $resp = $Clcategorias->lista_extras();
                                foreach ($resp as $categoria) 
                                {
                                echo'
                                    <div class="tab-pane fade "  id="panel-'.$categoria['cod_categoria'].'" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="col-xl-6 col-lg-6 col-sm-12 " style="background-color: #fff;">
                                            <div class="widget-content widget-content-area br-6">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                        <h4>Productos '.$categoria['categoria'].'</h4>
                                                    </div>
                                                </div> 
                                                
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="contentProductos'.$categoria['cod_categoria'].'">
                                               </div>
                                            </div>
                                             
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: center;" id="botonAgg'.$categoria['cod_categoria'].'">
                                            </div>
                                        </div>

                                        
                                        <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6" style="padding-bottom: 50px;">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                        <h4>Seleccionados</h4>
                                                    </div>
                                                </div> 
                                                <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                                    <table id="style-3" class="table style-3">
                                                            <thead>
                                                                <tr>
                                                                    <th>Producto</th>
                                                                    <th class="text-center">Eliminar</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="contentTabla'.$categoria['cod_categoria'].'">
                                                           
                                                             
                                                            </tbody>
                                                    </table>
                                                </div>
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: center;" id="boton'.$categoria['cod_categoria'].'">
                                                </div>
                                            </div>
                                           

                                        </div>

                                    </div>';
                                }
                            ?>
                            <!--EXTRA-->
                            <div class="tab-pane fade "  id="panel-0" role="tabpanel" aria-labelledby="home-tab">
                                <div class="col-xl-6 col-lg-6 col-sm-12 " style="background-color: #fff;">
                                    <div class="widget-content widget-content-area br-6">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <h4>Productos Extra</h4>
                                            </div>
                                        </div> 
                                        
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" id="contentProductos0">
                                            <?php
                                                $resp = $Clproductos->lista_extras();
                                                foreach ($resp as $extra) 
                                                {
                                                  $padre = $Clproductos->verificar_extra($cod_product);
                                                  $verificar = $Clproductos->verificar_registro($extra['cod_producto'],$padre['cod_producto_extra']);
                                                  if(!$verificar){
                                                    echo'<div class="col-xl-8 col-md-8 col-sm-8 col-8">
                                                      <label>'.$extra['nombre'].'</label>
                                                  </div>
                                                  <div class="col-xl-4 col-md-4 col-sm-4 col-4">
                                                     <input class="checkInsert checkExtra" id="check0" type="checkbox" id="" cod_producto="'.$extra['cod_producto'].'" codigo_padre="'.$padre['cod_producto_extra'].'" categoria="0">
                                                  </div>';
                                                  }
                                                
                                                }
                                            ?>
                                       </div>
                                       
                                    </div>
                                     
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: center;" id="botonAgg0">    
                                     
                                    </div>
                                </div>

                                        
                                <div class="col-xl-6 col-lg-6 col-sm-12  layout-spacing">
                                    <div class="widget-content widget-content-area br-6" style="padding-bottom: 50px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <h4>Seleccionados</h4>
                                            </div>
                                        </div> 
                                        <div class="table-responsive mb-4 mt-4" style="max-height: 500px;">
                                            <table id="style-3" class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th class="text-center">Eliminar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="contentTabla0">
                                                   
                                                     
                                                    </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: center;" id="boton0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--EXTRA-->
                        </div>
                    </div>    
                </div>
             

            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
     <script src="assets/js/pages/personalizar_productos.js" type="text/javascript"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
 
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>