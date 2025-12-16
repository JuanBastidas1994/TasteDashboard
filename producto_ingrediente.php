<?php
require_once "funciones.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clproductos = new cl_productos(NULL);
$session = getSession();
$cod_empresa = $session['cod_empresa'];

$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $producto = NULL;
  if ($Clproductos->getArrayByAlias($alias, $producto)) {
    $cod_producto = $producto['cod_producto'];
    $imagen = $files.$producto['image_min'];
    $nombre = $producto['nombre'];
    $opciones = $Clproductos->opciones($cod_producto);
  }else{
    header("location: ./index.php");
  }
}else{
    header("location: ./index.php");
}

$empresa = $Clempresas->get($session['cod_empresa']);
$api = $empresa['api_key'];

?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
</head>
<body>
    <!-- MODAL OPCIONES INGREDIENTES -->
    <div class="modal" tabindex="-1" role="dialog" id="modalIngredientes">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agrega ingredientes</h5>
                    <button type="button" class="close">
                        <span aria-hidden="true" data-dismiss="modal" aria-label="Close">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="lstIngredientes">
                        <div class="row align-items-center">
                        <script id="lista-ingredientes-template" type="text/x-handlebars-template">
                            {{#each this}}
                                <option value="{{cod_ingrediente}}" 
                                    data-id="{{cod_ingrediente}}"
                                    data-unidad_medida="{{cod_unidad_medida}}">{{ingrediente}}</option>
                            {{/each}}
                        </script>
                            <div class="col-12 text-right mb-3">
                                <button class="btn btn-outline-primary" onclick="getAllIngredientes()">Refrescar</button>
                            </div>
                            <div class="col-12 mb-3">
                                <select class="form-control" id="cmbIngredientes"></select>                         
                            </div>
                            <div class="col-9 mb-3">
                                <input type="number" class="form-control" value="1" placeholder="1" id="cantidadIngrediente">                         
                            </div>
                            <div class="col-3">
                                <p id="unidadMedidaOpciones"></p>                         
                            </div>
                            <div class="col-12 mb-3">
                                <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
                                <button class="btn btn-primary" onclick="setIngredienteToOpcion()">Añadir</button>
                            </div>
                        </div>
                    </div>
                    <!-- LISTA DE INGREDIENTES -->
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL OPCIONES INGREDIENTES -->

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true,"crear_productos.php?id=".$alias); ?>
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
                    <div><span id="btnBack" data-module-back="crear_productos.php?id=<?= $alias ?>" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Regresar al detalle</span></span>
                    </div>
                    <h3 id="titulo">Administrar Ingredientes</h3>
                </div>

                <div class="row layout-top-spacing">
                
                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing">
                        <!-- BLOQUE PRODUCTO INGREDIENTES -->
                        <div class="widget-content widget-content-area br-6">                            
                            <input type="hidden"  id="alias" value="<?= $api; ?>"/>
                            <input type="hidden"  id="productId" value="<?= $cod_producto; ?>"/>
                            <script id="product-ingredientes-template" type="text/x-handlebars-template">
                                <div class="">

                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{producto.image_min}}" alt="" class="mr-2" style="width: 70px;">
                                        <h3>{{producto.nombre}}</h3>
                                        <div><button class="btn btn-primary ml-3" onclick="openModalIngredientes({{producto.cod_producto}}, 'PRODUCTO')">Agregar</button></div>
                                    </div>
                                    <hr style="margin: 0; border-top: 1px solid #cdcdcd;">
                                </div>
                                <div class="mt-4">
                                    {{#each producto.ingredientes}}
                                    <div class="d-flex align-items-center justify-content-end mb-2 item-ingrediente">
                                        <div class="mr-4">
                                            {{ingrediente}}
                                        </div>
                                        <div class="d-flex align-items-center mr-4">
                                            <input type="number" class="form-control mr-2" value="{{valor}}" style="max-width: 120px;">
                                            <span style="width: 20px;">{{cod_unidad_medida}}</span>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary btnEditarProductoIngrediente" data-id="{{cod_producto_ingrediente}}" data-type="PRODUCTO">
                                                <i data-feather="edit-2"></i>
                                            </button>
                                            <button class="btn btn-danger btnDeleteProductoIngrediente" data-id="{{cod_producto_ingrediente}}" data-type="PRODUCTO">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    {{/each}}
                                </div>
                            </script>
                            <div class="content" id="productInformation">
                                <h4>Cargando...</h4>
                            </div>
                        </div>

                        <div class="mt-4 mb-4">
                            <h3>Opciones</h3>
                        </div>
                        <div id="contentOpciones"></div>
                        <script id="product-data-template" type="text/x-handlebars-template">
                            {{#each opciones}}
                            <!-- BLOQUE OPCIONES INGREDIENTES -->
                            <div class="widget-content widget-content-area br-6 mt-4">
                                <div class="content">
                                    <div class="">
                                        <div class="d-flex">
                                            <div><h5>{{titulo}}</h5></div>
                                            {{#eq isDatabase "1"}}
                                                <div class="ml-auto text-primary"><b>Son Productos</b></div>
                                            {{else}}
                                                <div class="ml-auto text-danger"><b>Opciones abiertas</b></div>  
                                            {{/eq}}
                                        </div>
                                        {{#eq isDatabase "1"}}
                                        <div>
                                            Las opciones que ya son productos, se darán de baja del inventario automaticamente siempre que el check de inventario esté encendido <br/>
                                            Puedes controlar debitar el producto o solo los ingredientes escogidos por ti <br/>
                                        </div>
                                        {{/eq}}
                                        <hr style="margin: 0; border-top: 1px solid #cdcdcd;">
                                    </div>
                                    {{#each items}}
                                    <!-- ITEM OPCION -->
                                    <div class="ml-5 mt-3 mb-5" style="border: 1px solid; padding: 15px;">
                                        <div class="d-flex align-items-center">
                                            <div style="font-size: 16px;"><b>- {{item}}</b></div>
                                            {{#eq ../isDatabase "1"}}
                                                <div class="d-flex flex-column ml-4">
                                                    <span style="font-size: 11px;">Inventario</span>
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" class="chkIsInventario" data-id="{{cod_producto_opciones_detalle}}"
                                                            {{#eq debitInventario "1"}} checked {{/eq}}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            {{else}}
                                                <div class="ml-2" style="display: none;">No son bases de datos</div>    
                                            {{/eq}}
                                            <div class="ml-auto">
                                                <button class="btn btn-outline-primary" onclick="openModalIngredientes({{cod_producto_opciones_detalle}}, 'OPCIONES')">
                                                    <i data-feather="plus-circle"></i> Agregar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="ml-5">
                                            {{#each ingredientes}}
                                            <div class="d-flex align-items-center justify-content-end mt-2 item-ingrediente">
                                                <div class="mr-4">
                                                    {{ingrediente}}
                                                </div>
                                                <div class="d-flex align-items-center mr-4">
                                                    <input type="number" class="form-control mr-2" value="{{valor}}" style="max-width: 120px;">
                                                    <span style="width: 20px;">{{cod_unidad_medida}}</span>
                                                </div>
                                                <div>
                                                    <button class="btn btn-primary btnEditarProductoIngrediente" data-id="{{cod_producto_opcion_ingrediente}}" data-type="OPCIONES">
                                                        <i data-feather="edit-2"></i>
                                                    </button>
                                                    <button class="btn btn-danger btnDeleteProductoIngrediente" data-id="{{cod_producto_opcion_ingrediente}}" data-type="OPCIONES">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            {{/each}}
                                        </div>
                                        
                                    </div>
                                    {{/each}}
                                </div>
                            </div>
                            {{/each}}
                        </script>
                    </div>

                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                                              
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Productos en esta categor&iacute;a</h4></div>
                            <div class="row"> 
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
    <script>
        Handlebars.registerHelper('eq', function(arg1, arg2, options) {
            return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('diferent', function(arg1, arg2, options) {
            return (arg1 !== arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('mayor', function(arg1, arg2, options) {
            return (arg1 > arg2) ? options.fn(this) : options.inverse(this);
        });
        Handlebars.registerHelper('decimal', function(number) {
            return parseFloat(number).toFixed(2);
        });
        Handlebars.registerHelper('colorStatus', function(status) {
            if(status == "ENTRANTE")
                return "primary";
            else if(status == "ASIGNADA")
                return "warning";
            else if(status == "ENVIANDO")
                return "secondary";
            else if(status == "ENTREGADA")
                return "success";
            else if(status == "ANULADA")
                return "danger";
            else if(status == "PUNTO_RECOGIDA")
                return "info";
            else if(status == "PUNTO_ENTREGA")
                return "dark";
            else
                return "info";
        });
        Handlebars.registerHelper('select', function( value, options ){
            var $el = $('<select />').html( options.fn(this) );
            $el.find('[value="' + value + '"]').attr({'selected':'selected'});
            return $el.html();
        });
        Handlebars.registerHelper('times', function(block) {
            var accum = '';
            for(var i = 1; i <= 15; ++i)
                accum += block.fn(i);
            return accum;
        });
        Handlebars.registerHelper('ifIn', function(elem, list, options) {
            if(list.indexOf(elem) > -1) {
                return options.fn(this);
            }
            return options.inverse(this);
        });
        Handlebars.registerHelper('array', function() {
            return Array.prototype.slice.call(arguments, 0, -1);
        });
        Handlebars.registerHelper('reverse', function(arreglo) {
            return arreglo.reverse();
        });
        Handlebars.registerHelper('count', function (arrayElement) {
            return arrayElement.length;
        });
        Handlebars.registerHelper('in_array', function(arg1, arg2, options) {
            for(var x=0; x<arg1.length; x++){
                if(arg1[x] === arg2){
                    return options.fn(this);
                }
            }
            return options.inverse(this);
        });
    </script>

    <?php js_mandatory(); ?>
    <script src="./assets/js/pages/producto_ingrediente.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="plugins/select2/select2totree.js"></script>
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>