<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
}
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);
$Clempresas = new cl_empresas(NULL);

$session = getSession();
// PERMISOS EMPRESA
$permisos = $Clempresas->getIdPermisionByBusiness($session['cod_empresa']);
/*
if(!userGrant()){
    header("location:index.php");
}*/

$iddia = array("0","1","2","3","4","5","6");
$dias = array("Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo");
$disponibilidadDay = array("checked","checked","checked","checked","checked","checked","checked");
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';
$cod_sucursal = 0;
//$imagen = url_sistema.'/assets/img/200x200.jpg';
$imagen="";
$nombre = "";
$direccion="";
$latitud = "";
$longitud="";
$emisor = "";
$distancia="";
$intervalo = 0;
$estado = "A";
$telefono = "";
$correo = "";
$chkSucursal = "checked ";
$dChecked = "checked ";
$pChecked = "checked ";
$egiChecked = "checked ";
$chkProgramar = "";
$empresa = $Clempresas->get($session['cod_empresa']);
$tipo_empresa =$empresa['cod_tipo_empresa'];
$programar_pedido = $empresa['programar_pedido'];
$displayRetail ="display:none";
if($tipo_empresa==2){
$displayRetail ="";}

$ProvinciaSelect="";
//echo $tipo_empresa; 
if(isset($_GET['id'])){
    $cod_sucursal = $_GET['id'];
    
    if($Clsucursales->isMySucursal($cod_sucursal)) {
        if($Clsucursales->getArray($cod_sucursal, $sucursal)){
            $disponibilidadDay = array();
            $imagen = $files.$sucursal['image'];
            $nombre = $sucursal['nombre'];
            $direccion = $sucursal['direccion'];
            $latitud = $sucursal['latitud'];
            $longitud = $sucursal['longitud'];
            $emisor = $sucursal['emisor'];
            $intervalo = $sucursal['intervalo'];
            $distancia = $sucursal['distancia_km'];
            $estado = $sucursal['estado'];
            $correo = $sucursal['correo'];
            $telefono = $sucursal['telefono'];
            $cod_ciudad = $sucursal['cod_ciudad'];
            $InfoProv = $Clsucursales->getInfoByCiudad($cod_ciudad);
            $ProvinciaSelect=$InfoProv['provincia'];
            $cant_dias_programar = $empresa['cant_dias_programar_pedido'];
            $transferencia_img = $files.$sucursal['transferencia_img'];
            $banner_xl = $files.$sucursal['banner_xl'];
            
            $chkSucursal = "";
            if($estado == "A")
                $chkSucursal = "checked";
            
            if($sucursal['programar_pedido'] == 1)
                $chkProgramar = "checked";
            
            $dChecked = ($sucursal['delivery'] == 1) ? "checked" : "";
            $pChecked = ($sucursal['pickup'] == 1) ? "checked" : "";
            $iChecked = ($sucursal['insite'] == 1) ? "checked" : "";
            $egiChecked = ($sucursal['envio_grava_iva'] == 1) ? "checked" : "";
        }
        else{
            header("location: ./index.php");
        }
    }
    else{
        header("location: ./index.php");
    }
}
$listaProductos = $Clproductos->listaProductBySucursal($cod_sucursal); 
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="gb18030">
    <?php css_mandatory(); ?>
      <style type="text/css">
      .dropify-wrapper {
          display: block;
          position: relative;
          cursor: pointer;
          overflow: hidden;
          width: 100%;
          max-width: 100%;
          height: 110px !important;
          padding: 5px 10px;
          font-size: 14px;
          line-height: 22px;
          color: #777;
          background-color: #fff;
          background-image: none;
          text-align: center;
          border: 0 !important;
          -webkit-transition: border-color .15s linear;
          transition: border-color .15s linear;
      }

      .respGalery > div {
          margin-top: 15px;
      }

      .croppie-container .cr-boundary{
          background-image: url(assets/img/transparent.jpg);
          background-position: center;
          background-size: cover;
      }
    </style>

    <!-- mapa lalitud -->
    <link rel="stylesheet" type="text/css" href="plugins/maps-latlon/jquery-gmaps-latlon-picker.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/elements/alert.css">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
</head>
<body>
     <!-- Modal Recortador-->
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
     <!-- End Modal Recortador-->

    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true); ?>
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
                
                <div class="layout-top-spacing">

                    <div class="col-md-12" style="margin-top:25px; ">
                        <div><span id="btnBack" data-module-back="sucursales.php" style="cursor: pointer;">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Sucursales</span></span>
                        </div>
                        <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Sucursal"; ?></h3>
                    </div>
                    
                    
                    
                    <div class="layout-top-spacing" style="display: block;">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                      <div class="widget-content widget-content-area br-6">
                        <ul class="nav nav-tabs mb-3 mt-3" id="lineTab" role="tablist">
                            <li class="nav-item">
                                <a  class="nav-link active" data-toggle="tab" href="#tab-info" role="tab" aria-controls="pills-info" aria-selected="true">
                                    <i data-feather="home"></i> 
                                    <span>Informaci&oacute;n</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-costo-envio" role="tab" aria-controls="pills-home" aria-selected="true">
                                    <i data-feather="truck"></i> 
                                    <span>Costo de env&iacute;o</span>
                                </a>
                            </li>
                         

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-formaspago" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="coffee"></i> 
                                    <span>Productos</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-transferencias" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="credit-card"></i> 
                                    <span>Imagenes adicionales</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
    
                            <!-- TAB INFO -->
                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                                <div class="row">
                                    
                                    <div class="col-xl-7 col-lg-12 col-sm-12">
                                        <div class="">
                                            <input type="hidden" placeholder="" name="cod_sucursal" id="cod_sucursal" class="form-control" required="required" autocomplete="off" value="<?php echo $cod_sucursal; ?>"/>
                                            <form id="frmSave" name="frmSave" class="form-horizontal form-label-left">    
                                                <div class="col-lg-12 col-ms-12 col-xs-12">    
                                                    <div class="form-group">
                                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                                            <div class="upload mt-1 pr-md-1">
                                                                <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="6M" data-allowed-file-extensions="jpeg jpg png"/>
                                                                <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Imagen</p>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Nombre <span class="asterisco">*</span></label>
                                                            <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control maxlength" required="required" autocomplete="off" maxlength="50" value="<?php echo $nombre; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="col-md-2 col-sm-2 col-xs-12" style="margin-bottom:10px; display: none;">
                                                            <label>Emisor</label>
                                                            <input type="text" placeholder="Ej. 001" name="txt_emisor" id="txt_emisor" class="form-control maxlength" autocomplete="off" maxlength="3" value="<?php echo $emisor; ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Cobertura:</label>
                                                            <input type="text" placeholder="Ej 10" name="txt_cobertura" id="txt_cobertura" class="gllpRadius form-control maxlength" required="required" autocomplete="off" maxlength="2" value="<?php echo $distancia; ?>"/>
                                                        </div>
                                                        <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Intervalo:</label>
                                                            <input type="text" placeholder="Ej 10" name="txt_intervalo" id="txt_intervalo" class="form-control maxlength" required="required" autocomplete="off" maxlength="2" value="<?php echo $intervalo; ?>"/>
                                                        </div>
                                                        
                                                        <div class="col-md-2 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Estado:</label>
                                                            <div>
                                                                <label class="switch s-icons s-outline s-outline-success mb-4">
                                                                    <input type="checkbox" class="chkEstadoSuc" name="chkEstadoSuc" id="chkEstadoSuc" value="" <?php echo $chkSucursal; ?>>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                            <input type="hidden" name="cmbEstado" id="cmbEstado" value="<?php echo $estado; ?>">
                                                        </div>
                                                    </div>
                            
                                                    <div class="form-group">
                                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Tel&eacute;fono <span class="asterisco">*</span></label>
                                                            <input type="text" placeholder="Tel&eacute;fono" name="txt_telefono" id="txt_telefono" class="form-control" autocomplete="off" value="<?php echo $telefono; ?>" required/>
                                                        </div>
                
                                                        <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Correo</label>
                                                            <input type="text" placeholder="Correo" name="txt_correo" id="txt_correo" class="form-control" autocomplete="off" value="<?php echo $correo; ?>"/>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            
                                                            <div class="d-flex mt-3">
                                                                <div class="mr-5">
                                                                    <label>Delivery</label>
                                                                    <div>
                                                                        <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                            <input type="checkbox" name="chk_delivery" id="chk_delivery" <?= $dChecked?>>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mr-5">
                                                                    <label>Pickup</label>
                                                                    <div>
                                                                        <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                            <input type="checkbox" name="chk_pickup" id="chk_pickup" <?= $pChecked?>>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mr-5">
                                                                    <label>Envío grava IVA</label>
                                                                    <div>
                                                                        <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                            <input type="checkbox" name="chk_envio_grava_iva" id="chk_envio_grava_iva" <?= $egiChecked?>>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mr-5" style="display: <?php echo in_array("OFFICE_INSITE", $permisos) ? 'initial' : 'none'; ?>;">
                                                                    <label>En Mesa</label>
                                                                    <div>
                                                                        <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                            <input type="checkbox" name="chk_insite" id="chk_insite" <?= $iChecked?>>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Direcci&oacute;n <span class="asterisco">*</span></label>
                                                            <input type="text" placeholder="Direccion del establecimiento" name="txt_direccion" id="txt_direccion" class="form-control" required="required" autocomplete="off" value="<?php echo $direccion; ?>"/>
                                                        </div>                                                                                                
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <div class="form-group col-md-6">
                                                        <label>Provincia</label>
                                                        <select id="cmbProvincias" name="cmbProvincias" class="form-control">
                                                            <?php
                                                            $resp = $Clsucursales->getProvincias();
                                                            $nomProvincia ="";
                                                            $x=0;
                                                            if($resp){
                                                                
                                                                foreach($resp as $prov)
                                                                {
                                                                    $select = "";
                                                                    if($ProvinciaSelect == $prov['provincia'])
                                                                    {$select = "selected";}
                                                                    if($x==0 && $ProvinciaSelect ==""){$nomProvincia=$prov['provincia'];}
                                                                    else{$nomProvincia = $ProvinciaSelect;}
                                                                    echo '<option value="'.$prov['provincia'].'" '.$select.'>'.$prov['provincia'].'</option>';
                                                                    $x++;
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>Ciudad</label>
                                                        <select id="cmbCiudades" name="cmbCiudades" class="form-control">
                                                        <?php
                                                            $resp = $Clsucursales->getCiudades($nomProvincia);
                                                            if($resp){
                                                                
                                                                foreach($resp as $ciud)
                                                                {
                                                                    $select = "";
                                                                    if($cod_ciudad == $ciud['cod_ciudad'])
                                                                    {$select = "selected";}
                                                                    echo '<option value='.$ciud['cod_ciudad'].' '.$select.'>'.$ciud['nombre'].'</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    </div>
                            
                                                    
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Ubicaci&oacute;n <span class="asterisco">*</span></label>
                                                        </div>
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                                        <fieldset class="gllpLatlonPicker" >
                                                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                                                <input type="text" class="gllpSearchField form-control" placeholder="Direcci&oacute;n de busqueda">
                                                            </div> 
                                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                                <button type="button" class="gllpSearchButton btn btn-primary form-control"><i data-feather="search"></i> Buscar</button>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px; margin-bottom: 15px;">
                                                            <div class="gllpMap" style="margin-left: 0; width: 100%;">Google Maps</div> 
                                                            </div>  
                            
                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                                <label>Latitud:</label>
                                                                <input type="text" class="gllpLatitude form-control" id="txt_latitud" name="txt_latitud" value="<?php echo $latitud; ?>"/>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                                <label>Longitud:</label>
                                                                <input type="text" class="gllpLongitude form-control" id="txt_longitud" name="txt_longitud" value="<?php echo $longitud; ?>"/>
                                                            </div>
                                                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                                <label>Zoom:</label>
                                                                <input type="number" class="gllpZoom form-control" value="15"/>
                                                            </div> 
                                                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                                <label>&nbsp;</label>
                                                                <input type="button" class="gllpUpdateButton btn btn-primary"  value="Actualizar">
                                                            </div>
                                                        </fieldset>
                                                        </div>
                                                    </div>
                            
                                                
                                                </div>
                                            </form>
                                                        
                                        </div>
                                    </div>

                                    <div class="col-xl-5 col-lg-12 col-sm-12 ">
                                        <?php if($programar_pedido == 1){ ?>
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="widget-content widget-content-area br-6">
                                                <div style="margin-top: 15px;">
                                                    <div><h4>Programar pedido</h4></div>
                                                    <div class="row">
                                                    
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                                <label>Programar pedido</label>
                                                                <div>
                                                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                        <input type="checkbox" name="chk_programar" id="chk_programar" <?= $chkProgramar?>>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                
                                                            <div class="col-md-9 col-sm-9 col-xs-12" style="margin-bottom:10px;">
                                                                <label>Cantidad de d&iacute;as en los que se puede programar<a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Al modificar este campo se modificará en todas las sucursales"><i data-feather="help-circle"></i></a></label>
                                                                <div>
                                                                    <input class="form-control" type="number" id="txt_cant_dias_programar" name="txt_cant_dias_programar" value="<?=$cant_dias_programar?>" placeholder="Cantidad de d&iacute;as">
                                                                </div>
                                                            </div>
                
                                                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px; text-align: right;">
                                                                <button class="btn btn-primary btnGuardarProgramar" data-sucursal="<?= $cod_sucursal?>"> Guardar</button>
                                                            </div>
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?> 
                
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="">
                                                    <div style="margin-top: 15px;">
                                                          <div><h4>Disponibilidad</h4></div>
                                                          <form id="frmDisponibilidad" method="POST" action="#">
                                                            <div class="x_content">
                                                                      <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;display:none">
                                                                        <input id="hora_ini" name="hora_ini" class="form-control flatpickr flatpickr-input active hora_iniD" type="hidden" placeholder="Seleccione hora" value="08:30" readonly="readonly">
                                                                      </div>
                                                                       <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;display:none">
                                                                          <input id="hora_fin" name="hora_fin" class="form-control flatpickr flatpickr-input active hora_finD" type="hidden" placeholder="Seleccione hora" value="17:30" readonly="readonly">
                                                                        </div>
                                                                <?php
                                                                $sucSelectDia = array();
                                                                for($i=0; $i< count($dias); $i++){
                                                                    if($cod_sucursal == 0){
                                                                    $disponibilidadDay[$i] ="checked";
                                                                    $sucSelectDia[$i] = 1;
                                                                    $horaI="08:30";
                                                                    $horaF="17:30";
                                                                    }else{
                                                                        $r=$Clsucursales->getDisponibilidadDay($cod_sucursal,$iddia[$i]);
                                                                        if($r){
                                                                        $disponibilidadDay[$i]="checked";
                                                                        $sucSelectDia[$i] = 1;
                                        
                                                                        $horaI = $r['hora_ini'];
                                                                        $horaF=$r['hora_fin'];
                                                                        }
                                                                        else
                                                                        {
                                                                        $disponibilidadDay[$i] ="";
                                                                        $sucSelectDia[$i] = 0;
                                                                        $horaI="08:30";
                                                                        $horaF="17:30";
                                                                        }
                                                                    }
                                                                ?>
                                        
                                                                  <div class="form-group itemDisponibilidad">
                                                                      <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                                          <label><?php echo $dias[$i]?></label>
                                                                          <input type="hidden" class="txtdia" name="txtdia[]" id="txtdia" value="<?php echo $iddia[$i]?>">
                                        
                                                                      </div>
                                                                      <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                                        <input name="hora_iniD[]" id="hora_iniD<?php echo $iddia[$i]?>" class="form-control flatpickr flatpickr-input active hora_iniD" type="text" placeholder="Seleccione hora" value="<?php echo $horaI?>" readonly="readonly">
                                                                      </div>
                                                                       <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                                          <input name="hora_finD[]" id="hora_finD<?php echo $iddia[$i]?>" class="form-control flatpickr flatpickr-input active hora_finD" type="text" placeholder="Seleccione hora" value="<?php echo $horaF?>" readonly="readonly">
                                                                        </div>
                                                                       <div class="col-md-3 col-sm-3 col-xs-12" style="margin-bottom:10px;">
                                                                          <h6>Activar</h6>
                                                                          <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                                              <input type="checkbox" class="chkDia" name="chkDia[]" id="chkDia" value="" <?php echo $disponibilidadDay[$i]?>>
                                                                              <span class="slider round"></span>
                                                                          </label>
                                        
                                                                        </div>
                                                                         <input type="hidden" name="selectDia[]" class="sucSelectDia" value="<?php echo $sucSelectDia[$i]?>"/>
                                                                   </div>
                                                                <?php
                                                                }
                                                                ?>
                                        
                                                            </div>
                                        
                                        
                                                          </form>
                                                          <input type="text" style="border:none">
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                            <!-- Tab costo de Envio -->
                            <div class="tab-pane fade" id="tab-costo-envio" role="tabpanel">
                                
                                <div>
                                    <div><h4>Costo de envío</h4></div>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5>Precios por rangos</h5>
                                                </div>
                                                <div class="col-12">
                                                    <button class="btn btn-success" onclick="addRango()">Agregar rango</button>
                                                    <button class="btn btn-primary" onclick="saveRangos()">Guardar</button>
                                                </div>
                                                <div class="col-12 lst-rangos">
                                                    <!-- TEMPLATE ID: rango-template -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-xl-6 col-lg-6 col-sm-12">
                                            <h5>xxxxx</h5>
                                        </div> -->
                                    </div>     
                                </div>
                            </div>

                            
                            <!-- Tab Productos -->
                            <div class="tab-pane fade" id="tab-formaspago" role="tabpanel" >
                                
                                <div class="">
                                    <div><h4>Disponibilidad de Productos</h4></div>
                                    <div class="col-md-12 col-sm-12" style="text-align:right;">
                                        <button type="button" class="btn btn-primary btnGuardarDisProduct" id="btnGuardarDisProduct">Actualizar Disponibilidad</button>    
                                    </div>
                                    
                                    <form id="frmProductos" method="POST" action="#">
                                        <div class="table-responsive mb-4 mt-4">
                                            <table id="style-3" class="table style-3  table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Estado</th>
                                                            <th>Precio</th>
                                                            <th class="text-center">Precio de Venta</th>
                                                            <th class="text-center">Precio Comparaci&oacute;n</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            if ($listaProductos)
                                                            {
                                                                foreach ($listaProductos as $productos) {
                                                                      $precioDisponibilidad = floatval(0);
                                                                      $precioAnteriorDisponibilidad = floatval(0);
                                                                      $estado = '';
                                                                      $chkprecio = "";
                                                                      $style = "disabled";
                                                                      $replace = 0;
                                                                      $selecreplace = 0;
                                                                    if($cod_sucursal > 0){
                                                                          $precioDisponibilidad = floatval($productos['precio']);
                                                                          $precioAnteriorDisponibilidad = floatval($productos['precio_anterior']);
                                                                          $disponibilidad = $Clproductos->getdisponibilidad($productos['cod_producto'], $cod_sucursal);
                                                                          if($disponibilidad){
                                                                              $precioDisponibilidad = floatval($disponibilidad['precio']);
                                                                              $precioAnteriorDisponibilidad = floatval($disponibilidad['precio_anterior']);
                                                                              $estado = $disponibilidad['estado'];
                                                                              $replace = $disponibilidad['replacePrice'];
                                                                          }
                                                                      }
                                                                      
                                                                      $checked = "checked";
                                                                      $seleccionado = 1;
                                                                      
                                                                      if($estado == 'A'){
                                                                        $checked = 'checked';
                                                                        $seleccionado = 1;
                                                                      }
                                                                      else if($estado == 'I')
                                                                      {
                                                                         $checked = ""; 
                                                                         $seleccionado = 0;
                                                                      }
                                                                      
                                                                      if ($replace == 1)
                                                                      {
                                                                          $selecreplace = 1;
                                                                          $chkprecio = "checked";
                                                                          $style = "";
                                                                      }
    
                                                                    echo '
                                                                    <tr id='.$productos['cod_producto'].' class="itemProductos">
                                                                    <td>'.$productos['nombre'].'</td>
                                                                    <td>
                                                                        <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                                          <input type="checkbox" class="chkEstado" name="chkEstado[]" id="chkEstado" value="" '.$checked.'>
                                                                          <span class="slider round"></span>
                                                                        </label> 
                                                                    </td>
                                                                    <td>
                                                                        <label class="switch s-icons s-outline s-outline-success mb-4 mr-2">
                                                                          <input type="checkbox" class="chkPrecio" name="chkPrecio[]" id="chkPrecio" value="" '.$chkprecio.' >
                                                                          <span class="slider round"></span>
                                                                        </label> 
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" placeholder="0.00" name="txt_precio_sucursal[]" class="form-control txt_precio_sucursal" required="required" autocomplete="off" value="'.$precioDisponibilidad.'" >
                                                                        <input type="hidden" name="select[]" class="sucSelect" value="'.$seleccionado.'"/>
                                                                        <input type="hidden" name="precioR[]" class="sucPrecio" value="'.$selecreplace.'"/>
                                                                        <input type="hidden" name="txt_producto[]" class="txt_producto" value="'.$productos['cod_producto'].'"/>
                                                                    </td>
                                                                    <td><input type="number" placeholder="0.00" name="txt_precio_anterior_sucursal[]" class="form-control txt_precio_anterior_sucursal" autocomplete="off" value="'.$precioAnteriorDisponibilidad.'" ></td>
                                                                    </tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                echo '<td colspan="5">Sin lista de productos creados..</td>';
                                                            }
                                                        ?>
                                                    </tbody> 
                                            </table>    
                                        </div>    
                                    </form>
                                    <div class="row col-md-12 col-sm-12">
                                        <button type="button" class="btn btn-primary btnGuardarDisProduct col-md-2 col-offset-10" id="btnGuardarDisProduct">Actualizar Disponibilidad</button>    
                                    </div>
                                        
                                </div>
                              
                            </div>
                            
                            
                            <!-- Tab Imagenes -->
                            <div class="tab-pane fade" id="tab-transferencias" role="tabpanel">
                                
                                <div>
                                    <div><h4>Transferencias</h4></div>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    
                                                    <form id="frmLogos" name="frmLogos">
                                                        <label for="">Imagen para transferencias bancarias</label>
                                                        <input class="form-control flLogos" type="file" data-image="transferencia_img">
                                                        <img class="mt-3" src="<?= $transferencia_img ?>" alt="" >
                                                    </form>
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div> 
                                    <hr/>
                                    
                                    <div><h4>Banner XL (solo para web) (1920x300)</h4></div>
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    
                                                    <form id="frmLogos" name="frmLogos">
                                                        <label for="">Banner para web</label>
                                                        <input class="form-control flLogos" type="file" data-image="banner_xl">
                                                        <img class="mt-3" src="<?= $banner_xl ?>" alt="" style="max-width:80%" >
                                                    </form>
                                                    
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
                    
                    
                    
                    
                    
                    
                    
                    

                    
                    
                    
                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
    <script type="text/javascript" src="templates/templates.js"></script>
    
    <?php js_mandatory(); ?>
    <!-- Mapas -->
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAWo6DXlAmrqEiKiaEe9UyOGl3NJ208lI8&libraries=places"></script>
    <script src="plugins/maps-latlon/jquery-gmaps-latlon-picker.js"></script>
    <script src="assets/js/pages/sucursales.js?v=1" type="text/javascript"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script>
        var myTable = $('#style-3').DataTable( {
            dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'pdf', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando pag. _PAGE_ de _PAGES_",
                "sInfoEmpty": "Mostrando pag. 1",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
               "sEmptyTable": "No se encontraron resultados",
               "sZeroRecords": "No se encontraron resultados",
               "buttons": {
                    "copy": "Copiar",
                    "csv": "CSV",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir",
                    "create": "Crear",
                    "edit": "Editar",
                    "remove": "Remover",
                    "upload": "Subir"
                }
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 10
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script id="rango-template" type="text/x-handlebars-template">
        <div class="row mt-3 rango rango-id-{{ id }}">
            <div class="col-12 col-lg-3 mt-3 mt-lg-0">
                <label>Distancia inicial</label>
                <input type="hidden" value="{{ id }}" class="rango-id">
                <input type="number" class="form-control distancia-ini" placeholder="0" value="{{ distancia_ini }}">
            </div>
            <div class="col-12 col-lg-3 mt-3 mt-lg-0">
                <label>Distancia final</label>
                <input type="number" class="form-control distancia-fin" placeholder="5" value="{{ distancia_fin }}">
            </div>
            <div class="col-12 col-lg-3 mt-3 mt-lg-0">
                <label>Precio</label>
                <input type="number" class="form-control rango-precio" placeholder="1.5" value="{{ precio }}">
            </div>
            <div class="col-12 col-lg-3 mt-3 mt-lg-0 justify-content-end justify-content-lg-start d-flex align-items-end">
                <button class="btn btn-danger btnRemoverRango" data-id="{{ id }}">
                    <i data-feather="x"></i>
                </button>
            </div>
        </div>
    </script>
</body>
</html>