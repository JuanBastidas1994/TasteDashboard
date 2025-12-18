<?php
require_once "funciones.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_productos.php";
require_once "clases/cl_fidelizacion.php";
require_once "clases/cl_empresas.php";
if(!isLogin()){
    header("location:login.php");
}

$cod_sucursal = 0;
$Clsucursales = new cl_sucursales(NULL);
$Clproductos = new cl_productos(NULL);
$Clfidelizacion = new cl_fidelizacion(NULL);
$Clempresas = new cl_empresas();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_empresa = $session['cod_empresa'];
$empresa = $Clempresas->get($cod_empresa);
if(!$empresa){
    header("location:index.php");
}

$direccion=$empresa['direccion'];
$telefono=$empresa['telefono'];
$correo=$empresa['correo'];
$isFidelizacion = $empresa['fidelizacion'];
$chkGravaIva = "checked";
if($empresa['envio_grava_iva'] == 0){
    $chkGravaIva = "";
}

//FIDELIZACION
$divisor = 0;
$monto = 0;
$valor_cumple = 0;
$diascumple = 0;
$restriccioncumple = 0;
$fidelizacion = $Clfidelizacion->datos_fidelizacion($cod_empresa);
if($fidelizacion){
    $divisor = $fidelizacion['divisor_puntos'];
    $monto = $fidelizacion['monto_puntos'];
    $valor_cumple = $fidelizacion['valor_regalo_cumple'];
    $diascumple = $fidelizacion['dias_regalo_cumple'];
    $restriccioncumple = $fidelizacion['compra_minimo_regalo_cumple'];

$cantDiasPuntos = $fidelizacion['cant_dias_caducidad_puntos'];
$cantDiasDinero = $fidelizacion['cant_dias_caducidad_dinero'];
$cantDiasSaldo = $fidelizacion['cant_dias_caducidad_saldo'];
}

//IMG CUMPLE 
$imgCumple = "";
$estadoimgCumple = 'I';
$chkCumple = "";

$cumple = $Clempresas->getImgCumple($cod_empresa);
if($cumple){
    $imgCumple = $files.$cumple['imagen'];
    $estadoimgCumple = $cumple['estado'];
    if($estadoimgCumple == "A")
        $chkCumple = 'checked="checked"';
}

$ck_permisoTienda = "";
if($Clempresas->getPermisoTienda($cod_empresa))
    $ck_permisoTienda = "checked";
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
                          </div>
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <!--<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>-->
                    <button class="btn btn-dark crop-rotate" data-deg="-90">Rotate Left</button>
                    <button class="btn btn-dark crop-rotate" data-deg="90">Rotate Right</button>
                    <button type="button" class="btn btn-primary" id="crop-get">Recortar y Subir</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Escriba una descripcion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                        <form id="frmDescripcion" method="POST" action="#">
                                <div class="row">
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                <label>Descripción Larga</label>
                                                <textarea name="txt_descripcion_larga" id="editor1" class="form-control txt_descripcion_larga" autocomplete="off" style="resize: none;"></textarea>
                                            </div>
                                </div> 
                              </div>
                        </form>         
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary btnSaveDesc" data-id="">Guardar</button>
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
                    <h3 id="titulo" data-translate="conf-titulo1">Configuraci&oacute;n</h3>
                </div>
                

                <div class="row layout-top-spacing" style="display: block;">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                      <div class="widget-content widget-content-area br-6">
                        <ul class="nav nav-tabs mb-3 mt-3" id="lineTab" role="tablist">
                            <li class="nav-item">
                                <a  class="nav-link active" data-toggle="tab" href="#tab-info" role="tab" aria-controls="pills-info" aria-selected="true">
                                    <i data-feather="home"></i> 
                                    <span data-translate="conf-tab1">Informaci&oacute;n de Empresa</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-envio" role="tab" aria-controls="pills-home" aria-selected="true">
                                    <i data-feather="truck"></i> 
                                    <span data-translate="conf-tab2">Costo de env&iacute;o</span>
                                </a>
                            </li>
                          
                            <?php if($isFidelizacion == 1){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-fidelizacion" role="tab" aria-controls="pills-contact" aria-selected="false">
                                    <i data-feather="star"></i> 
                                    <span data-translate="conf-tab3">Fidelizaci&oacute;n</span>
                                </a>
                            </li>
                            <?php } ?>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-formaspago" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="credit-card"></i> 
                                    <span data-translate="conf-tab4">Formas de pago</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-cumple" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="gift"></i> 
                                    <span data-translate="conf-tab5">Cumplea&ntilde;os</span>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-permisos" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="check-square"></i> 
                                    <span data-translate="conf-tab5">Permisos</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-courier" role="tab" aria-controls="pills-pago" aria-selected="false">
                                    <i data-feather="truck"></i> 
                                    <span data-translate="conf-tab5">Courier</span>
                                </a>
                            </li>
                            <!-- 
                            <li class="nav-item">
                                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                            </li>-->
                        </ul>
                        <div class="tab-content" id="pills-tabContent">

                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="pills-info-tab">
                                
                                <br>
                                    <div class="row">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <h3 class="" data-translate="conf-tab1-titulo1">Contacto</h3>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label data-translate="conf-tab1-subtitulo1">Direcci&oacute;n<span class="asterisco">*</span> </label>
                                                <input type="text" placeholder="Ingrese Direcci&oacute;n" name="txt_direccion" id="txt_direccion" class="form-control" value="<?php echo $direccion?>" >
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label data-translate="conf-tab1-subtitulo2">Tel&eacute;fono<span class="asterisco">*</span> </label>
                                                <input type="text" placeholder="Ingrese Tel&eacute;fono" required="required" name="txt_telefono" id="txt_telefono" class="form-control" value="<?php echo $telefono?>">
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label data-translate="conf-tab1-subtitulo3">Correo<span class="asterisco">*</span> </label>
                                                <input type="text" placeholder="Ingrese Correo" required="required" name="txt_correo" id="txt_correo" class="form-control" value="<?php echo $correo?>">
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <h3 class="" data-translate="conf-tab1-titulo2">Redes Sociales</h3>
                                            <?php
                                            $query = "SELECT * FROM tb_red_social WHERE estado = 'A'";
                                            $row = Conexion::buscarVariosRegistro($query, NULL);
                                            foreach ($row as $rs) {
                                                $queryR = "SELECT * from tb_empresa_red_social where cod_empresa = ".$session['cod_empresa']." and cod_red_social=".$rs['cod_red'];
                                                $rowR = Conexion::buscarRegistro($queryR);
                                                
                                               echo' <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                        <label>'.$rs['nombre'].'</label>
                                                        <input type="text" placeholder="Ingrese url '.$rs['nombre'].'" name="txt_redes[]" id="'.$rs['cod_red'].'" class="form-control" value="'.$rowR['descripcion'].'">
                                                    </div>';
                                            }
                                            ?>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="row"> 
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                            <button type="button" class="btn btn-outline-primary actualizarInfo" id="btnActualizarInfo"  data-id="<?php echo $session['cod_empresa'] ?>">Actualizar</button>
                                        </div>
                                    </div> 
                                    <br/>
                            </div>
                            
                            <div class="tab-pane fade" id="tab-envio" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div class="widget-content widget-content-area">
                                    <?php
                                        $query = "SELECT * FROM tb_empresa_costo_envio WHERE cod_empresa = ".$session['cod_empresa'];
                                        $row = Conexion::buscarRegistro($query, NULL);
                                        $tipoTransporte =['carro', 'moto', 'camion'];
                                        if($cod_empresa!=72 && $cod_empresa!=135){
                                            if($row){
                                                $base_km = $row['base_km'];
                                                $base_dinero = $row['base_dinero'];
                                                $adicional_km = $row['adicional_km'];
                                                $codigo= $row['cod_empresa_costo_envio'];
                                            }
                                        }else{
                                            $row = Conexion::buscarVariosRegistro($query);
                                            if($row){
                                                $base_km = $row['base_km'];
                                                $base_dinero = $row['base_dinero'];
                                                $adicional_km = $row['adicional_km'];
                                                $codigo= $row['cod_empresa_costo_envio'];
                                                $tipo = $row['tipo'];
                                            }
                                        }
                                           
                                    ?>
                                    <h3 class="">Costo de Env&iacute;o a Domicilio</h3>
                                    <div class="row d-none">
                                        <div class="col-md-6 col-sm-6 col-xs-12" >
                                            <label>Envío grava IVA</label>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Este campo indica si los envíos a domicilio gravan IVA o no."><i data-feather="help-circle"></i></a>
                                                <div>
                                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                        <input type="checkbox" name="chk_envioIva" id="chk_envioIva" data-empresa="<?= $cod_empresa?>" <?= $chkGravaIva?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                        </div>
                                    </div>
                                    <br>
                                    <p>Configura el valor por env&iacute;o a domicilio escoge un monto base para un rango de km (Ej. de 0 a 3 km costar&aacute; $2) y luego define cuanto costar&aacute; cada km adicional (Ej. 0.50ctvs)</p>
                                    <br>
                                    <?php if($cod_empresa != 72 && $cod_empresa != 135):?> <!-- MEGA OUTLET -->
                                        <div class="row">
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Rango Km de 0 a n? <span class="asterisco">*</span> </label>
                                                <input type="number" placeholder="" name="base_km" id="base_km" class="form-control" autocomplete="off" value="<?php echo $base_km; ?>">
                                            </div>
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Tarifa por rango km <span class="asterisco">*</span></label>
                                                <input type="text" placeholder="" name="base_dinero" id="base_dinero" class="form-control" required="required" autocomplete="off" value="<?php echo $base_dinero; ?>">
                                            </div>
                                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                <label>Tarifa por km adicional? <span class="asterisco">*</span> </label>
                                                <input type="number" placeholder="" name="adicional_km" id="adicional_km" class="form-control" autocomplete="off" value="<?php echo $adicional_km; ?>">
                                            </div>
                                        </div>
                                        <div class="row"> 
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                <button type="button" data-id="<?php echo $codigo ?>"class="btn btn-outline-primary" id="btnActualizarCostoEnvio">Actualizar costo de env&iacute;o</button>
                                            </div>
                                        </div> 
                                    <?php else: ?>
                                            <form id="frmConfigTransporte" method="POST" action="controllers/controlador_configuraciones.php?metodo=configuracionTransporte">
                                                <?php foreach($row as $r): ?>
                                                    <div class="row">
                                                        <?php if($r['tipo'] == 'carro'):?>
                                                            <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/2554/2554969.png" height="100px" alt="">
                                                            </div>
                                                        <?php elseif($r['tipo'] == 'moto'): ?>
                                                            <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/62/62620.png" height="100px" alt="">
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/664/664468.png" height="100px" alt="">
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="hidden" name="cod_empresa_costo_envio[]" value="<?php echo $r['cod_empresa_costo_envio']?>">
                                                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                            <label>Rango Km de 0 a n? <span class="asterisco">*</span> </label>
                                                            <input type="number" placeholder="" name="base_km[]" id="base_km" class="form-control" autocomplete="off" value="<?php echo $r['base_km']; ?>">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                            <label>Tarifa por rango km <span class="asterisco">*</span></label>
                                                            <input step="0.01" type="number" placeholder="" name="base_dinero[]" id="base_dinero" class="form-control" required="required" autocomplete="off" value="<?php echo $r['base_dinero']; ?>">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                            <label>Tarifa por km adicional? <span class="asterisco">*</span> </label>
                                                            <input step="0.01" type="number" placeholder="" name="adicional_km[]" id="adicional_km" class="form-control" autocomplete="off" value="<?php echo $r['adicional_km'];; ?>">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                            <label>Peso <br> máximo <span class="asterisco">*</span> </label>
                                                            <input step="0.01" type="number" placeholder="" name="peso_maximo[]" id="adicional_km" class="form-control" autocomplete="off" value="<?php echo $r['peso_maximo']; ?>">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if(count($row) ==0): ?>
                                                    <?php foreach($tipoTransporte as $t): ?>
                                                        <div class="row">
                                                            <?php if($t == 'carro'):?>
                                                                <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                    <img src="https://cdn-icons-png.flaticon.com/512/2554/2554969.png" height="100px" alt="">
                                                                </div>
                                                            <?php elseif($t == 'moto'): ?>
                                                                <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                    <img src="https://cdn-icons-png.flaticon.com/512/62/62620.png" height="100px" alt="">
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="form-group col-md-3 col-sm-4 col-xs-12 text-center">
                                                                    <img src="https://cdn-icons-png.flaticon.com/512/664/664468.png" height="100px" alt="">
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                                <label>Rango Km de 0 a n? <span class="asterisco">*</span> </label>
                                                                <input type="number" placeholder="" name="base_km[]" id="base_km" class="form-control" autocomplete="off" value="">
                                                            </div>
                                                            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                                <label>Tarifa por rango km <span class="asterisco">*</span></label>
                                                                <input step="0.01" type="number" placeholder="" name="base_dinero[]" id="base_dinero" class="form-control" required="required" autocomplete="off" value="">
                                                            </div>
                                                            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                                <label>Tarifa por km adicional? <span class="asterisco">*</span> </label>
                                                                <input step="0.01" type="number" placeholder="" name="adicional_km[]" id="adicional_km" class="form-control" autocomplete="off" value="">
                                                            </div>
                                                            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                                                                <label>Peso <br> máximo <span class="asterisco">*</span> </label>
                                                                <input step="0.01" type="number" placeholder="" name="peso_maximo[]" id="adicional_km" class="form-control" autocomplete="off" value="">
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                    <div class="row"> 
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                            <input type="submit" class="btn btn-outline-primary" id="btnActualizarCostodeEnvioVarios" value="Actualizar costo de env&iacute;o"></input>
                                                        </div>
                                                    </div> 
                                            </form>
                                    <?php endif; ?>
                                    <br/>
                                </div>      
                            </div>

                            <div class="tab-pane fade" id="tab-fidelizacion" role="tabpanel" aria-labelledby="pills-profile-tab2" style="height: 350px;">

                                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                    <div class="widget-content widget-content-area br-6">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>Niveles</h4>
                                        </div>
                                        <table id="style-3" class="table style-3">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">&nbsp;</th>
                                                    <th class="text-center">Nombre</th>
                                                    <th class="text-center">Inicio</th>
                                                    <th class="text-center">Fin</th>
                                                    <th class="text-center">Monto</th>
                                                    <th class="text-center">Actualizar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="lstDisponibles">
                                                <?php
                                                $c=1;
                                                  $resp = $Clfidelizacion->niveles($session['cod_empresa']);
                                                  foreach ($resp as $niveles) {
                                                      $numeroitems=count($resp);
                                                      if($c==1){$atributo="readonly";}else{$atributo="";}
                                                      if($c==$numeroitems){$atributoFinal="readonly";}else{$atributoFinal="";}
                                                      $imagen = $files.$niveles['imagen']."?v=".fecha();
                                                    echo'
                                                    <tr>
                                                        <td class="text-center">
                                                            <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                        </td>
                                                        <td class="text-center" >
                                                           <input type="text" id="txt_nombre'.$niveles['cod_nivel'].'" class="form-control" style="text-align: center;"
                                                        value="'.$niveles['nombre'].'" >
                                                        </td>
                                                        <td class="text-center">
                                                         <input type="number" id="txt_inicio'.$niveles['cod_nivel'].'" class="form-control" 
                                                        value="'.$niveles['punto_inicial'].'"'.$atributo.'>
                                                        </td>
                                                        <td class="text-center">
                                                        <input type="number" id="txt_fin'.$niveles['cod_nivel'].'" class="form-control" 
                                                        value="'.$niveles['punto_final'].'"'.$atributoFinal.'>
                                                        </td>
                                                        <td class="text-center"><input type="number" id="txt_monto'.$niveles['cod_nivel'].'" class="form-control" 
                                                        value="'.$niveles['dinero_x_punto'].'"
                                                        </td>
                                                        <td class="text-center"><button type="button" class="btn btn-outline-primary btnNiveles" data-id="'.$niveles['cod_nivel'].'">Actualizar</button></td>
                                                    </tr>
                                                    ';
                                                    $c++;
                                                  }
                                                       
                                                ?>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-sm-12  layout-spacing">
                                    <div class="widget-content widget-content-area br-6" style="height: 300px;">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>Esquema</h4>
                                        </div>
                                        <table id="style-3" class="table style-3">
                                            <tbody id="lstDisponibles">
                                                <tr>
                                                    <td>Por cada($):</td>
                                                    <td><input type="number" id="txt_divisor_puntos" class="form-control" value="<?php echo $divisor; ?>" style="width: 90px;"></td>
                                                </tr>
                                                <tr>
                                                    <td>Recibes(Puntos):</td>
                                                    <td><input type="number" id="txt_monto_puntos" class="form-control" value="<?php echo $monto; ?>" style="width: 90px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;" wfd-id="67">
                                            <button type="button" class="btn btn-outline-primary btnFidelizacion" data-id="<?php echo $resp['cod_fidelizacion_puntos']?>" wfd-id="252">Actualizar</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-sm-12 ">
                                    <div class="widget-content widget-content-area br-6">
                                        <div class="row">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                <h4>Tiempos de Caducidad</h4>
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                      <label>Caducidad de los Puntos (En d&iacute;as)</label>
                                                      <input class="form-control" type="number" id="txt_cdPuntos" name="txt_cdPuntos" value="<?= $cantDiasPuntos?>">
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                      <label>Caducidad del Dinero (En d&iacute;as)</label>
                                                      <input class="form-control" type="number" id="txt_cdDinero" name="txt_cdDinero" value="<?= $cantDiasDinero?>">
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                      <label>Caducidad del Saldo (En d&iacute;as)</label>
                                                      <input class="form-control" type="number" id="txt_cdSaldo" name="txt_cdSaldo" value="<?= $cantDiasSaldo?>">
                                                </div>
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" style="text-align: right;">
                                                      <button style="margin-top: 20px;" class="btn btn-outline-primary btnCaducidad" data-empresa="<?=$cod_empresa?>">Actualizar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                            </div>
                            
                            <div class="tab-pane fade" id="tab-formaspago" role="tabpanel" aria-labelledby="pills-pago-tab2" style="height: 350px;">
                                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                    <div class="widget-content widget-content-area br-6">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                            <h4>Formas de pago</h4>
                                        </div>
                                        <table class="table style-3  table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Forma Pago</th>
                                                    <th class="text-center">Monto máximo ($) <br> <small>(0 = sin límite)</small></th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Descripción</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="moveCategorias" class="connectedSortable"> 
                                                <?php
                                                $resp = $Clempresas->getFormasPagoEmp($session['cod_empresa']);
                                                  foreach ($resp as $forma) {
                                                    $chk = "";
                	                                if($forma['estado'] == "A")
                	                                    $chk = "checked ";
        	                                        $desc_larga = editor_decode($forma['descripcion']);  
                                                    
                                                    //TIPO ENVIO
                                                    $cKDelivery = "";
                                                    $cKPickup = "";
                                                    if($forma["is_delivery"] == 1)
                                                        $cKDelivery = "checked";
                                                    if($forma["is_pickup"] == 1)
                                                        $cKPickup = "checked";
        	                                        
        	                                        if($forma['estado'] != "" && $forma['estado'] != "D")
        	                                        {
        	                                            echo'
                                                    <tr data-id="'.$forma['cod_empresa_forma_pago'].'">
                                                        <td class="text-center" >
                                                            <div class="row rowNombreFP">
                                                                <div class="col-10 pr-1">
                                                                    <input type="text" id="txt_nombre'.$forma['id_forma_pago'].'" class="form-control nombreFP" style="text-align: center;" value="'.$forma['nomFP'].'" disabled>
                                                                    <input type="hidden" id="txt_descripcion'.$forma['cod_empresa_forma_pago'].'" class="form-control" style="text-align: center;" value="'.$desc_larga.'" >
                                                                </div>
                                                                                                                           
                                                                <div class="pl-0 col-2 align-self-center">
                                                                    <button class="btn btn-primary btnEditarNombreFP" data-fp=\'{"forma_pago": "'.$forma['id_forma_pago'].'", "id": '.$forma['cod_empresa_forma_pago'].'}\'>
                                                                        <i data-feather="edit-2"></i>    
                                                                    </button>
                                                                    <button class="btn btn-success btnGuardarNombreFP" data-fp=\'{"forma_pago": "'.$forma['id_forma_pago'].'", "id": '.$forma['cod_empresa_forma_pago'].'}\' style="display: none;">
                                                                        <i data-feather="save"></i>    
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        
                                                        </td>
                                                        
                                                        <td class="text-right">
                                                            <div class="row rowMontoMaximo pl-4">
                                                                <div class="col-10 pr-1">
                                                                    <input type="number" class="form-control txtMontoMaximo" value="'.$forma['monto_maximo'].'" disabled>
                                                                </div>
                                                                <div class="pl-0 col-2 align-self-center">
                                                                    <button class="btn btn-primary btnEditarMaximo" data-fp="'.$forma['id_forma_pago'].'" data-id="'.$forma['cod_empresa_forma_pago'].'">
                                                                        <i data-feather="edit-2"></i>    
                                                                    </button>
                                                                    <button class="btn btn-success btnGuardarMaximo" data-fp="'.$forma['id_forma_pago'].'" data-id="'.$forma['cod_empresa_forma_pago'].'" style="display: none;">
                                                                        <i data-feather="save"></i>    
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                        </td>
                                                        
                                                        <td class="text-center">
                                                            <div class="col-md-12 col-sm-12 col-xs-12" >
                                                                <div>
                                                                    <label class="switch s-icons s-outline  s-outline-success">
                                                                        <input type="checkbox" name="chk_estado" id="chk_estado'.$forma['cod_empresa_forma_pago'].'" class="btnEditarFormaP" data-id="'.$forma['cod_empresa_forma_pago'].'" '.$chk.'>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center"><button type="button" class="btn btn-outline-primary btnDescripcion" data-id="'.$forma['cod_empresa_forma_pago'].'">Ingresar Descripción</button></td>
                                                     
                                                        <td>
                                                            <div class="d-flex">
                                                                <label class="switch s-icons s-outline  s-outline-success">
                                                                    <input type="checkbox" '.$cKPickup.' class="ckTipoEnvio" data-fp=\'{"id": '.$forma['cod_empresa_forma_pago'].', "tipo_envio": "P"}\'>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <label>Pickup</label> 
                                                            </div>
                                                            <div class="d-flex">
                                                                <label class="switch s-icons s-outline  s-outline-success">
                                                                    <input type="checkbox" '.$cKDelivery.' class="ckTipoEnvio"  data-fp=\'{"id": '.$forma['cod_empresa_forma_pago'].', "tipo_envio": "D"}\'>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <label>Delivery</label> 
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    ';
        	                                        }
                                                    
                                                    $c++;
                                                  }
                                                       
                                                ?>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              
                            </div>
                        
                            <div class="tab-pane fade" id="tab-cumple" role="tabpanel" aria-labelledby="pills-info-tab">
                                
                                <br>
                                    <div class="row">
                                        <div class="form-group col-md-7 col-sm-7 col-xs-12">
                                            <h3 class="">Imagen Para el usuario</h3>
                                            <p>Imagen que verá el usuario el día de su cumpleaños</p>
                                            <hr/>
                                            <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                <label>Cambiar Imagen </label>
                                                <input type="file" name="image_cumple" id="image_cumple">
                                            </div>
                                            <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                <label>Estado Imagen</label>
                                                <div>
                                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                        <input type="checkbox" name="chk_img_cumple" id="chk_img_cumple" <?php echo $chkCumple; ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <br>
                                            <p><b>Imagen Actual</b></p>
                                            <img id="imgCumple" src="<?php echo $imgCumple; ?>" />
                                        </div>

                                        <div class="form-group col-md-5 col-sm-5 col-xs-12">
                                        <?php if($isFidelizacion == 1){ ?>
                                            <h3 class="">Premio Fidelizaci&oacute;n</h3>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label>Monto que el Usuario ganar&aacute; en su cumplea&ntilde;os (en d&oacute;lares)<span class="asterisco">*</span> </label>
                                                <input type="number" name="txt_monto_cumple" id="txt_monto_cumple" class="form-control" value="<?php echo $valor_cumple?>" >
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label>Cant. d&iacute;as disponible para usar<span class="asterisco">*</span> </label>
                                                <input type="number" name="txt_dias_cumple" id="txt_dias_cumple" class="form-control" value="<?php echo $diascumple?>" >
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label>Valor Mínimo en compras para dar el premio ($0 = sin restricci&oacute;n)<span class="asterisco">*</span> </label>
                                                <input type="number" name="txt_restriccion_cumple" id="txt_restriccion_cumple" class="form-control" value="<?php echo $restriccioncumple?>" >
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;margin-bottom:15px;">
                                                <button type="button" class="btn btn-outline-primary actualizarCumple" data-id="<?php echo $session['cod_empresa'] ?>">Actualizar</button>
                                                <hr/>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>

                                    <br/>
                            </div>

                            <div class="tab-pane fade" id="tab-permisos" role="tabpanel" aria-labelledby="pills-info-tab">
                                
                                <br>
                                    <div class="row">
                                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <h5 class="">Permiso Admin sucursal Encender/Apagar Tienda</h5>
                                            <hr/>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <input type="checkbox" name="ck_permisoTienda" id="ck_permisoTienda" <?=$ck_permisoTienda?>>
                                                <span>Habilitado </span>
                                            </div>
                                            <div class="form-group col-md-6 col-sm-6 col-xs-12" style="text-align: right;">
                                               <button type="button" class="btn btn-primary" id="btnPermisoTienda">Guardar</button>
                                            </div>
                                        </div>
                                    </div>

                                    <br/>
                            </div>
                            
                            <div class="tab-pane fade" id="tab-courier" role="tabpanel" aria-labelledby="pills-info-tab">
                                <br>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <h3 class="">Courier</h3>
                                        <hr/>
                                        <div>
                                            <div>
                                                <h4>Sucursales</h4>
                                            </div>
                                            <div id="acordeones">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
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
    
    <?php js_mandatory(); ?>
     <script src="assets/js/scrollspyNav.js"></script>
     <script src="plugins/ckeditor-standar/ckeditor.js"></script>
    <script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>
    <!--<script src="plugins/ckeditor-standar/plugins2/link/dialogs/link.js"></script>-->
    <script src="plugins/croppie/croppie.js"></script>
    <script src="assets/js/pages/configuraciones.js" type="text/javascript"></script>
    <script src="assets/js/pages/sucursales/config_couriers.js" type="text/javascript"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> -->
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>