<?php
require_once "funciones.php";
require_once "clases/cl_runfood.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_empresas.php";
if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clsucursales = new cl_sucursales(NULL);
$Clempresas = new cl_empresas();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_empresa = $session['cod_empresa'];
$empresa = $Clempresas->get($cod_empresa);
if(!$empresa){
    header("location:index.php");
}
$api = $empresa['api_key'];
$Clrunfood = new cl_runfood(NULL);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $ruc = $Clrunfood->getSucursal($id);
    if($ruc){
        $nameRuc = $ruc['nombre'];
        $rucName = $ruc['dominio'];
    }else{
        header("location:index.php");
    }
}else{
    header("location:index.php");
}

//Permisos
$permisos = $Clempresas->getIdPermisionByBusiness($cod_empresa);
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .dropdown-menu{
            z-index: 999999999999 !important;
        }
        
        table.dataTable{
            margin-top:5px !important;
        }
        
        .table dd {
            font-size: 9px;
            color: #8d8d8d;
            font-weight: bolder;
            margin-left: 15px;
        }
        
        .n-chk.disabled b {
            text-decoration: line-through;
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
        
        <!--MODAL OPCIONES PRODUCTO -->
        <div class="modal fade bs-example-modal-lg" id="modalOpcionesProducto" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Opciones producto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="x_content">    
                            
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
                                        <div class="ml-5 mt-3" style="border: 1px solid; padding: 15px;">
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
                                                    <button class="btn btn-outline-primary btnAsignarOpciones" data-id="{{cod_producto_opciones_detalle}}">
                                                        <i data-feather="plus-circle"></i> Asignar
                                                    </button>
                                                </div>
                                            </div>
                                            {{#diferent facturacion false}}
                                            <div class="ml-3">
                                                <div class="d-flex align-items-center justify-content-start mt-2 item-ingrediente">
                                                    <div class="mr-4">
                                                        {{facturacion.id_runfood}}
                                                        {{facturacion.nombre_runfood}}
                                                        {{#if facturacion.sku}}
                                                            <span class="badge badge-info ml-2">SKU: {{facturacion.sku}}</span>
                                                        {{/if}}
                                                        {{#eq facturacion.es_principal "1"}}
                                                            <span class="badge badge-success ml-2">Principal (reemplaza al producto)</span>
                                                        {{else}}
                                                            <span class="badge badge-secondary ml-2">Adicional</span>
                                                        {{/eq}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{/diferent}}
                                            
                                        </div>
                                        {{/each}}
                                    </div>
                                </div>
                                {{/each}}
                            </script>
                            
                                    
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!--MODAL OPCIONES PRODUCTO -->

        <!--MODAL PRODUCTOS CONTIFICO -->
        <div class="modal fade bs-example-modal-lg" id="modalProductosContifico" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleProductosModal">Productos Runfood</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="x_content">    
                            
                            <!--Table-->
                            <script id="product-contifico-template" type="text/x-handlebars-template">
                            {{#each this}}
                                <tr>
                                    <td>{{sku}}</td>
                                    <td>
                                        {{name}}
                                        <dl>
                                          <dd>{{#if active}}Activo{{else}}Inactivo{{/if}}{{#if tax_applicable}} - Grava IVA{{/if}}</dd>
                                        </dl>
                                    </td>
                                    <td class="text-right">${{price}}</td>
                                    <td class="text-right">{{#if active}}Sí{{else}}No{{/if}}</td>
                                    <td class="text-center">
                                        <button class="btn btn-secondary btnSetProducto" data-id="{{sku}}" data-name="{{name}}" data-sku="{{sku}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-warning btnSetIngrediente" data-id="{{sku}}" data-name="{{name}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-danger btnImportar" data-id="{{sku}}" data-name="{{name}}" data-precio="{{price}}">
                                            Importar
                                        </button>
                                        <button class="btn btn-danger btnSaveRecipiente" data-id="{{sku}}" data-name="{{name}}" data-precio="{{price}}">
                                            Importar
                                        </button>
                                        <button class="btn btn-info btnSetRecipiente" data-id="{{sku}}" data-name="{{name}}" data-precio="{{price}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-dark btnSetDomiciliouAdicionales" data-id="{{sku}}" data-name="{{name}}" data-precio="{{price}}">
                                            Seleccionar
                                        </button>

                                        <button class="btn btn-dark btnSetOpciones" data-id="{{sku}}" data-name="{{name}}" data-precio="{{price}}" data-sku="{{sku}}">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            {{/each}}
                            </script>
                            <div class="table-responsive mb-4">
                                <table id="table-contifico" class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Nombre</th>
                                            <th>PVP</th>
                                            <th>Activo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-products">
                                    </tbody>
                                </table>
                            </div>
                            
                                    
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!--MODAL PRODUCTOS CONTIFICO -->
        
        <!--MODAL FORMAS DE PAGO CONTIFICO -->
        <div class="modal fade bs-example-modal-lg" id="modalFormasPagoContifico" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Formas de Pago Runfood</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="x_content">    
                            
                            <!--Table-->
                            <script id="formaspago-contifico-template" type="text/x-handlebars-template">
                            {{#each this}}
                                <tr>
                                    <td>{{id}}</td>
                                    <td>
                                        {{name}}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-success btnSetFormaPago" data-id="{{id}}" data-name="{{name}}">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            {{/each}}
                            </script>
                            <div class="table-responsive mb-4">
                                <table id="table-formaspago-contifico" class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body-formaspago-contifico">
                                    </tbody>
                                </table>
                            </div>
                            
                                    
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!--MODAL FORMAS DE PAGO CONTIFICO -->
        
        

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                
                <div class="col-md-8" >
                    <a href="index.php"><span id="btnBack" data-module-back="rucs.php" style="cursor: pointer;color:#888ea8;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Mis Rucs</span></span>
                    </a>
                    <h3 id="titulo"><?php echo $nameRuc; ?></h3>
                    <h4 id="titulo"><?php echo $rucName; ?></h4>
                     <input type="hidden"  id="api" value="<?= $api; ?>"/>
                </div>
                <div class="col-md-4 text-right">
                    <button type="button" class="btn btn-outline-primary" id="btnVerificarProductos">
                        <i data-feather="refresh-cw"></i> <span class="btn-text-inner ms-3">Verificar</span>
                    </button>
                </div>


                <div class="row layout-top-spacing" style="display: block;">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                      <div class="widget-content widget-content-area br-6">
                        <ul class="nav nav-tabs mb-3" id="lineTab" role="tablist">
                            <!-- Tabs -->
                            <li class="nav-item">
                                <a  class="nav-link active" data-toggle="tab" href="#tab-info" role="tab" aria-controls="pills-info" aria-selected="true">
                                    <i data-feather="coffee"></i> 
                                    <span>Productos</span>
                                </a>
                            </li>
                            
                            <?php if(in_array("PRODUCTO_INGREDIENTES", $permisos)){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-ingredientes" role="tab" aria-controls="pills-ingredientes" aria-selected="true">
                                    <i data-feather="droplet"></i> 
                                    <span>Ingredientes</span>
                                </a>
                            </li>
                            <?php } ?>
                            
                            <?php if(in_array("GESTIONAR_RECIPIENTES", $permisos)){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-recipientes" role="tab" aria-controls="pills-recipientes" aria-selected="true">
                                    <i data-feather="shopping-bag"></i> 
                                    <span>Recipientes</span>
                                </a>
                            </li>
                            <?php } ?>
                            
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-formaspago" role="tab" aria-controls="pills-recipientes" aria-selected="true">
                                    <i data-feather="credit-card"></i> 
                                    <span>Formas de Pago</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-configuracion" role="tab" aria-controls="pills-recipientes" aria-selected="true">
                                    <i data-feather="settings"></i> 
                                    <span>Configuración</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            
                            <!-- Tab Productos -->
                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="pills-info-tab">
                                    <div class="row">
                                        <!--Table-->
                                        <script id="my-product-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td><img src="{{image_min}}" style="width: 50px;"/></td>
                                                <td><a href="crear_productos.php?id={{alias}}" target="_blank">{{nombre}}</a></td>
                                                <td class="text-right info-contifico">
                                                    {{#if id}}
                                                    {{name_in_contifico}}
                                                    <dl>
                                                      <dd>{{id}}</dd>
                                                      {{#if sku}}<dd>SKU: {{sku}}</dd>{{/if}}
                                                      <dd><a href="javascript:void(0);" class="btnDesasignarProducto" data-id="{{cod_producto}}">Desasignar</a></dd>
                                                    </dl>
                                                    {{/if}}
                                                </td>
                                                <td class="text-center">
                                                    {{#if tiene_opciones}}
                                                    <button class="btn btn-primary btnVerOpciones" data-id="{{cod_producto}}">
                                                        Opciones
                                                    </button>
                                                    {{/if}}
                                                    <button class="btn btn-secondary btnAsignarProducto" data-id="{{cod_producto}}">
                                                        Asignar
                                                    </button>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-my-products" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>Nombre</th>
                                                        <th>Id Runfood</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-my-products">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Tab Ingredientes -->
                            <div class="tab-pane fade" id="tab-ingredientes" role="tabpanel" aria-labelledby="pills-ingredientes-tab">
                                    <div class="w-100 text-right mb-3">
                                        <button class="btn btn-danger btnImportarIngredientes">Importar</button>
                                    </div>
                                    <div class="row">
                                        <!--Table-->
                                        <script id="ingredientes-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td>{{nombre}}</td>
                                                <td>{{cod_unidad_medida}}</td>
                                                <td>${{precio}}</td>
                                                <td class="text-right info-contifico-ingredientes">
                                                    {{name_in_contifico}}
                                                    <dl>
                                                      <dd>{{id}}</dd>
                                                    </dl>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btnAsignarIngrediente" data-id="{{cod_ingrediente}}">
                                                        Asignar
                                                    </button>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-ingredientes" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Unidad</th>
                                                        <th>Precio</th>
                                                        <th>Id Runfood</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-ingredientes">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Tab Recipientes -->
                            <div class="tab-pane fade" id="tab-recipientes" role="tabpanel" aria-labelledby="pills-recipientes-tab">
                                    <div class="w-100 text-right mb-3">
                                        <button class="btn btn-danger btnImportarRecipientes">Importar</button>
                                    </div>
                                    <div class="row">
                                        <!--Table-->
                                        <script id="recipientes-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td>{{nombre}}</td>
                                                <td>${{precio}}</td>
                                                <td class="text-right info-contifico-recipientes">
                                                    {{name_in_contifico}}
                                                    <dl>
                                                      <dd>{{id}}</dd>
                                                    </dl>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btnAsignarRecipiente" data-id="{{cod_recipiente}}">
                                                        Asignar
                                                    </button>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-recipientes" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Precio</th>
                                                        <th>Id Runfood</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-recipientes">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            
                            <!-- Tab Formas de Pago -->
                            <div class="tab-pane fade" id="tab-formaspago" role="tabpanel" aria-labelledby="pills-recipientes-tab">
                                    <div class="row">
                                        <!--Table-->
                                        <script id="formaspago-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td>{{descripcion}}</td>
                                                <td>{{cod_forma_pago}}</td>
                                                <td class="text-right info-contifico-formaspago">
                                                    {{name_in_contifico}}
                                                    <dl>
                                                      <dd>{{id}}</dd>
                                                    </dl>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-success btnAsignarFormaPago" data-id="{{cod_forma_pago}}">
                                                        Escoger de Runfood
                                                    </button>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-formaspago" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>ID</th>
                                                        <th>Id Runfood</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-formaspago">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Tab Configuracion -->
                            <div class="tab-pane fade" id="tab-configuracion" role="tabpanel" aria-labelledby="pills-configuracion-tab">
                                    <div>
                                        <h3>Productos Requeridos en Runfood</h3>
                                    </div>
                                    <div class="row">
                                        <!-- Servicio a domicilio -->
                                        <div class="col-4">
                                           <div class="card style-4 ">
                                               <div class="card-body pt-3">
                                                    <div class="media mt-0 mb-3">
                                                        <div class="media-body">
                                                            <h4 class="media-heading mb-0">Servicio a Domicilio</h4>
                                                            <p class="media-text">Producto</p>
                                                        </div>
                                                    </div>
                                                    <p class="card-text mt-4 mb-0">
                                                        Para poder facturar correctamente mediante <b>Runfood</b> se necesita tener o crear un producto de <b>Servicio a Domicilio</b> 
                                                        en el mismo <br/> Puedes importarlo si ya lo tienes creado en Runfood.
                                                    </p>
                                                </div>
                                                <div class="card-footer pt-0 border-0 text-center">
                                                    <a href="javascript:void(0);" class="btn btn-secondary w-40 btnAsignarDomiciliouAdicionales" data-tipo="DOMICILIO">
                                                        <i data-feather="download-cloud"></i> <span class="btn-text-inner ms-3">Importar</span>
                                                    </a>
                                                    <!--<a href="javascript:void(0);" class="btn btn-danger w-40">
                                                        <i data-feather="upload-cloud"></i> <span class="btn-text-inner ms-3">Crear</span>
                                                    </a>-->
                                                </div>
                                            </div> 
                                        </div>
                                        
                                        <!-- Productos adicionales -->
                                        <div class="col-4">
                                           <div class="card style-4 ">
                                               <div class="card-body pt-3">
                                                    <div class="media mt-0 mb-3">
                                                        <div class="media-body">
                                                            <h4 class="media-heading mb-0">Productos Adicionales</h4>
                                                            <p class="media-text">Producto</p>
                                                        </div>
                                                    </div>
                                                    <p class="card-text mt-4 mb-0">
                                                        Para poder facturar correctamente mediante <b>Runfood</b> se necesita tener o crear un producto de las <b>opciones adicionales</b> 
                                                        que el usuario escoge. <br/> Puedes importarlo si ya lo tienes creado en Runfood.
                                                    </p>
                                                </div>
                                                <div class="card-footer pt-0 border-0 text-center">
                                                    <a href="javascript:void(0);" class="btn btn-secondary w-40 btnAsignarDomiciliouAdicionales" data-tipo="ADICIONALES">
                                                        <i data-feather="download-cloud"></i> <span class="btn-text-inner ms-3">Importar</span>
                                                    </a>
                                                    <!--<a href="javascript:void(0);" class="btn btn-danger w-40">
                                                        <i data-feather="upload-cloud"></i> <span class="btn-text-inner ms-3">Crear</span>
                                                    </a>-->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Credenciales API -->
                                        <div class="col-4">
                                           <div class="card style-4 ">
                                               <div class="card-body pt-3">
                                                    <div class="media mt-0 mb-3">
                                                        <div class="media-body">
                                                            <h4 class="media-heading mb-0">Credenciales API</h4>
                                                            <p class="media-text">Configuración</p>
                                                        </div>
                                                    </div>
                                                    <p class="card-text mt-4 mb-0">
                                                        API Key (header <b>X-Api-Key</b>) entregada por Runfood para esta sucursal.
                                                    </p>
                                                    <input type="text" id="inputApiKey" class="form-control mt-2" placeholder="API Key de Runfood" value="<?= htmlspecialchars($ruc['api_key'] ?? '', ENT_QUOTES); ?>"/>
                                                </div>
                                                <div class="card-footer pt-0 border-0 text-center">
                                                    <button type="button" class="btn btn-secondary w-40" id="btnGuardarApiKey">
                                                        <i data-feather="save"></i> <span class="btn-text-inner ms-3">Guardar</span>
                                                    </button>
                                                </div>
                                            </div>
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
    </script>
    <?php js_mandatory(); ?>
     <script src="assets/js/scrollspyNav.js"></script>
    <script src="assets/js/pages/ruc_detalle_runfood.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>