<?php
require_once "funciones.php";
require_once "clases/cl_contifico.php";
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
$Clcontifico = new cl_contifico($cod_empresa);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $ruc = $Clcontifico->getRuc($id, $cod_empresa);
    if($ruc){
        $nameRuc = $ruc['razon_social'];
        $rucName = $ruc['ruc'];
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
        
        <!--MODAL PRODUCTOS CONTIFICO -->
        <div class="modal fade bs-example-modal-lg" id="modalProductosContifico" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleProductosModal">Productos Contífico</h5>
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
                                    <td>{{codigo}}</td>
                                    <td>
                                        {{nombre}}
                                        <dl>
                                          <dd>{{id}}</dd>
                                        </dl>
                                    </td>
                                    <td class="text-right">${{pvp1}}</td>
                                    <td class="text-right">{{stock}}</td>
                                    <td class="text-center">
                                        <button class="btn btn-secondary btnSetProducto" data-id="{{id}}" data-name="{{nombre}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-warning btnSetIngrediente" data-id="{{id}}" data-name="{{nombre}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-danger btnImportar" data-id="{{id}}" data-name="{{nombre}}" data-precio="{{pvp1}}">
                                            Importar
                                        </button>
                                        <button class="btn btn-danger btnSaveRecipiente" data-id="{{id}}" data-name="{{nombre}}" data-precio="{{pvp1}}">
                                            Importar
                                        </button>
                                        <button class="btn btn-info btnSetRecipiente" data-id="{{id}}" data-name="{{nombre}}" data-precio="{{pvp1}}">
                                            Escoger
                                        </button>
                                        <button class="btn btn-dark btnSetDomiciliouAdicionales" data-id="{{id}}" data-name="{{nombre}}" data-precio="{{pvp1}}">
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
                                            <th>Stock</th>
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
                        <button type="button" class="btn btn-outline-primary" data-id="">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--MODAL PRODUCTOS CONTIFICO -->

        <!--MODAL CATEGORIAS  CONTIFICO -->
        <div class="modal fade bs-example-modal-lg" id="modalCategoriasContifico" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleProductosModal">Categorias Contífico</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="x_content">     
                            <script id="categorias-contifico-template" type="text/x-handlebars-template">
                                {{#each this}}
                                    <button type="button" class="btn btn-info btnCategory mb-2 ml-2"
                                            style="padding: 5px 10px; font-size: 12px;"
                                            onclick="loadProductosByCategoryContifico('{{id}}', 1)">{{nombre}}</button>
                                {{/each}}
                            </script>
                            <div id="LstCategorias"></div>
                        
                            
                            <!--Table-->
                            <div class="text-right" id="numRows">0</div>
                            <div class="table-responsive mb-4">
                                <table id="table-contifico2" class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Nombre</th>
                                            <th>PVP</th>
                                            <th>Stock</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-products2">
                                    </tbody>
                                </table>
                            </div>
                            
                                    
                        </div>
                    
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        <!--MODAL PRODUCTOS CONTIFICO -->
        
        <!--MODAL BODEGAS CONTIFICO -->
        <div class="modal fade bs-example-modal-lg" id="modalBodegasContifico" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Bodegas Contífico</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="x_content">    
                            
                            <!--Table-->
                            <script id="bodega-contifico-template" type="text/x-handlebars-template">
                            {{#each this}}
                                <tr>
                                    <td>{{codigo}}</td>
                                    <td>
                                        {{nombre}}
                                        <dl>
                                          <dd>{{id}}</dd>
                                        </dl>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-secondary btnSetBodega" data-id="{{id}}" data-name="{{nombre}}">
                                            Escoger
                                        </button>
                                    </td>
                                </tr>
                            {{/each}}
                            </script>
                            <div class="table-responsive mb-4">
                                <table id="table-bodega" class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body-bodega">
                                    </tbody>
                                </table>
                            </div>
                            
                                    
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-id="">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--MODAL PRODUCTOS CONTIFICO -->
        
        <!-- MODAL CREAR TALONARIO -->
        <div class="modal fade bs-example-modal-md" id="modalCrearTalonario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nuevo Talonario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                        <div class="x_content">    
                            <div class="form-group">
                                <!-- API TOKEN -->
                                <div class="col-md-12 col-sm-12 col-xs-12 input-group" style="margin-bottom:10px;">
                                    <label>Api token <span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Este token te lo proporciona contífico"></span>
                                    </label>
    
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i data-feather="anchor"></i></span>
                                        </div>
                                        <input type="text" placeholder="Api Token proporcionado por contífico" name="txt_api_token" id="txt_api_token" class="form-control" required="required" autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <!-- EMISOR -->
                                <div class="col-md-6 col-sm-6 col-6 input-group" style="margin-bottom:10px;">
                                    <label>Emisor <span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Emisor para la factura, por lo general es 001"></span>
                                    </label>
    
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i data-feather="home"></i></span>
                                        </div>
                                        <input type="text" placeholder="Emisor" name="txt_emisor" id="txt_emisor" class="form-control" required="required" autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <!-- PTO EMISION -->
                                <div class="col-md-6 col-sm-6 col-6 input-group" style="margin-bottom:10px;">
                                    <label>Pto. Emisión <span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Pto. de Emisión para la factura, por lo general es 001"></span>
                                    </label>
    
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i data-feather="airplay"></i></span>
                                        </div>
                                        <input type="text" placeholder="Emision" name="txt_emision" id="txt_emision" class="form-control" required="required" autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <!-- SECUENCIAL FAC -->
                                <div class="col-md-6 col-sm-6 col-6 input-group" style="margin-bottom:10px;">
                                    <label>Secuencial Fac<span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Secuencial para la próxima factura en el sistema"></span>
                                    </label>
    
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i data-feather="clipboard"></i></span>
                                        </div>
                                        <input type="text" placeholder="Secuencial" name="txt_sec_fac" id="txt_sec_fac" class="form-control" required="required" autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <!-- SECUENCIAL DNA -->
                                <div class="col-md-6 col-sm-6 col-6 mb-3 input-group">
                                    <label>Secuencial DNA<span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Secuencial para el próximo DNA en el sistema"></span>
                                    </label>
    
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i data-feather="clipboard"></i></span>
                                        </div>
                                        <input type="text" placeholder="Secuencial" name="txt_sec_dna" id="txt_sec_dna" class="form-control" required="required" autocomplete="off"/>
                                    </div>
                                </div>
                                
                                <!-- Sucursales -->
                                <div class="col-md-12 col-sm-12 col-12 mb-3">
                                    <label>Sucursales<span class="asterisco">*</span> 
                                        <span class="far fa-question-circle rounded bs-tooltip" data-placement="top" title="Escoje las sucursales a las que se facturará con este talonario, si son más de una compartirán el secuencial"></span>
                                    </label>
                                    
                                    <div>
                                        <script id="offices-check-template" type="text/x-handlebars-template">
                                            {{#each this}}
                                                {{#eq disable true}}
                                                    <div class="n-chk mb-3">
                                                        <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                                                        <input type="checkbox" name="cmb_offices[]" value="{{cod_sucursal}}" class="new-control-input chkOffices">
                                                        <span class="new-control-indicator"></span> <b class="text-primary">{{nombre}}</b>
                                                        </label>
                                                    </div>
                                                {{else}}
                                                    <div class="n-chk mb-3 disabled">
                                                        <label class="new-control new-checkbox new-checkbox-rounded checkbox-success">
                                                        <input type="checkbox" class="new-control-input" disabled />
                                                        <span class="new-control-indicator"></span> <b class="">{{nombre}}</b> - Ya está configurado con otro postoken
                                                        </label>
                                                    </div>
                                                {{/eq}}
                                            {{/each}}
                                        </script>    
                                        
                                        <div id="officesCheck"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>    
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardar" onclick="saveNewTalonario()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL CREAR TALONARIO -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                
                <div class="row mt-4">
                    <div class="col-9" >
                        <a href="index.php"><span id="btnBack" data-module-back="rucs.php" style="cursor: pointer;color:#888ea8;">
                          <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Mis Rucs</span></span>
                        </a>
                        <h3 id="titulo"><?php echo $nameRuc; ?></h3>
                        <h3 id="titulo"><?php echo $rucName; ?></h3>
                    </div>
                    <div class="col-3">
                        <label for="">Api Version Contifico</label>
                        <select id="cmbVersionContifico" class="form-control">
                            <option value="v1">V1</option>
                            <option value="v2">V2</option>
                        </select>
                    </div>
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
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-bodegas" role="tab" aria-controls="pills-sucursales" aria-selected="true">
                                    <i data-feather="archive"></i> 
                                    <span>Bodegas</span>
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
                                <a class="nav-link" data-toggle="tab" href="#tab-talonarios" role="tab" aria-controls="pills-talonarios" aria-selected="true">
                                    <i data-feather="file"></i> 
                                    <span>Talonarios</span>
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
                                                <td>{{nombre}}</td>
                                                <td class="text-right info-contifico">
                                                    {{name_in_contifico}}
                                                    <dl>
                                                      <dd>{{id}}</dd>
                                                    </dl>
                                                </td>
                                                <td class="text-center">
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
                                                        <th>Id Contífico</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-my-products">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Tab Bodegas -->
                            <div class="tab-pane fade" id="tab-bodegas" role="tabpanel" aria-labelledby="pills-sucursales-tab">
                                <div class="widget-content widget-content-area">
                                    <div class="row">
                                        <!--Table-->
                                        <script id="office-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td>{{nombre}}</td>
                                                <td>{{id_bodega}}</td>
                                                <td>{{name_bodega}}</td>
                                                <td>
                                                    {{#diferent id_bodega null}}
                                                        <label class="switch s-icons s-outline s-outline-success">
                                                            <input type="checkbox" class="chkInventario" data-id="{{cod_contifico_sucursal}}"
                                                                {{#eq inventario "1"}} checked {{/eq}}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    {{/diferent}}
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-secondary btnAsignarBodega" data-id="{{cod_sucursal}}">
                                                        Asignar
                                                    </button>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-office" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Sucursal</th>
                                                        <th>Id Contífico</th>
                                                        <th>Nombre Bodega</th>
                                                        <th>Activar inventario</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-office">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>      
                            </div>
                            
                            <!-- Tab Talonarios -->
                            <div class="tab-pane fade" id="tab-talonarios" role="tabpanel" aria-labelledby="pills-talonarios-tab">
                                <div class="widget-content widget-content-area">
                                    <div class="row">
                                        <div class="col-8 mb-5">
                                            <p>
                                                Los talonarios estan estrictamente relacionados a contífico mediante el "API TOKEN", este token debe ser solicitado a Contífico.
                                            </p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <button class="btn btn-primary" onclick="openNewTalonario()">Nuevo Talonario</button>
                                        </div>
                                        
                                        <!--Table-->
                                        <script id="talonario-template" type="text/x-handlebars-template">
                                        {{#each this}}
                                            <tr>
                                                <td>{{pos}}</td>
                                                <td>{{secuencial_fac}}</td>
                                                <td>{{secuencial_dna}}</td>
                                                <td>{{sucursalesText}}</td>
                                                <td>
                                                    <label class="switch s-icons s-outline s-outline-success">
                                                        <input type="checkbox" class="chkTalonario" data-id="{{cod_postoken}}"
                                                            {{#eq facturar "1"}} checked {{/eq}}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                        <li><a href="javascript:void(0);" data-value="{{cod_postoken}}" class="bs-tooltip btnEditar"   data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="edit-2"></i></a></li>
                                                        <li><a href="javascript:void(0);" data-value="{{cod_postoken}}" class="bs-tooltip btnEliminar" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i data-feather="trash-2"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        {{/each}}
                                        </script>
                                        <div class="table-responsive mb-4">
                                            <table id="table-talonarios" class="table style-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Pos Token</th>
                                                        <th>Prox Factura</th>
                                                        <th>Prox DNA</th>
                                                        <th>Sucursales</th>
                                                        <th>Facturar</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-talonario">
                                                </tbody>
                                            </table>
                                        </div>
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
                                                        <th>Id Contífico</th>
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
                                                        <th>Id Contífico</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-recipientes">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                            
                            <!-- Tab Configuracion -->
                            <div class="tab-pane fade" id="tab-configuracion" role="tabpanel" aria-labelledby="pills-configuracion-tab">
                                    <div>
                                        <h3>Productos Requeridos en contífico</h3>
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
                                                        Para poder facturar correctamente mediante <b>Contífico</b> se necesita tener o crear un producto de <b>Servicio a Domicilio</b> 
                                                        en el mismo <br/> Puedes importarlo si ya lo tienes creado en contífico.
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
                                                        Para poder facturar correctamente mediante <b>Contífico</b> se necesita tener o crear un producto de las <b>opciones adicionales</b> 
                                                        que el usuario escoge. <br/> Puedes importarlo si ya lo tienes creado en contífico.
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
                                        
                                        
                                        
                                    </div>


                                    <div>
                                        <h3>Datos de integración</h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="">Api Key</label>
                                            <p><?= $ruc['api']; ?></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="">Ambiente</label>
                                            <p><?= $ruc['ambiente']; ?></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="">Ambiente</label>
                                            <p><?= $ruc['ambiente']; ?></p>
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
    <script src="assets/js/pages/ruc_detalle.js" type="text/javascript"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> -->
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>