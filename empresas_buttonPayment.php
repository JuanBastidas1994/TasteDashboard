<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_botonPagos.php";

$Clempresas = new cl_empresas(NULL);
$ClPagos = new cl_botonpagos(NULL);
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

if(!isLogin()){
    header("location:login.php");
}

if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $empresa = $Clempresas->getByAlias($alias);
  if($empresa){
    $cod_empresa = $empresa['cod_empresa'];
    $imagen = url_sistema.'assets/empresas/'.$empresa['alias'].'/'.$empresa['logo'];
    $nombre = $empresa['nombre'];
    $alias = $empresa['alias'];
    $api = $empresa['api_key'];

    //AMBIENTES DE PRUEBA Y PRODUCCIÓN
    $ambientes = [];
    $desarrollo = [];
    $produccion = [];

    $desarrollo['valor'] = "development";
    $desarrollo['nombre'] = "Desarrollo";
    $produccion['valor'] = "production";
    $produccion['nombre'] = "Producci&oacute;n";

    $ambientes[] = $desarrollo;
    $ambientes[] = $produccion;

    // FASES DATA FAST
    $fases = [];
    $faseUno = [];
    $faseDos = [];

    $faseUno['valor'] = "FASE1";
    $faseDos['valor'] = "FASE2";
    $fases[] = $faseUno;
    $fases[] = $faseDos;
  }else{
    header("location: ./index.php");
  }
}else{
    header("location: ./crear_empresa.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
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

      .rounded-pills-icon .nav-pills .nav-link.active, .rounded-pills-icon .nav-pills .show > .nav-link {
          box-shadow: 0px 5px 15px 0px rgb(0 0 0 / 30%) !important;
          background-color: #f1f2f3 !important;
          border: solid 1px #009688;
          color: #000 !important;
      }
    </style>
    <link href="plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/select2/select2totree.css" rel="stylesheet">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <!--  BEGIN NAVBAR  -->
    <?php echo top() ?>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php echo navbar(true,"categorias.php"); ?>
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
                    <div><span id="btnBack" data-module-back="crear_empresa.php?id=<?php echo $alias; ?>" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Información Empresa <?php echo $nombre; ?></span></span>
                    </div>
                    <h3 id="titulo">Configurar botones de Pago</h3>
                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_producto != 0) ? "" : "display: none;";  ?>">
                      <span id="btnNuevo" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="plus"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Nueva Categor&iacute;a</span>
                      </span>

                      <span style="cursor: pointer;margin-right: 15px;display: none;">
                        <i class="feather-16" data-feather="copy"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Duplicar</span>
                      </span>

                      <span id="btnEliminar" style="cursor: pointer;margin-right: 15px;">
                        <i class="feather-16" data-feather="trash"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;"> Eliminar</span>
                      </span>
                    </div>
                </div>

                <div class="row layout-top-spacing">
                    <div class="col-md-12 col-xs-12">
                      <div class="widget-content widget-content-area br-6">
                        <div class="row">
                                                
                          <?php
                            $botonActual = $ClPagos->botonActualEmpresa($cod_empresa);
                            if(!$botonActual){
                              $opBotones = '<option value="0">Primero configura algún botón</option>';
                              $nameBotonActual = "Ningún boton establecido";
                            }
                            else{
                              $botonAct = $botonActual['cod_proveedor_botonpagos'];
                              $nameBotonActual = $botonActual['nombre'];
                            }

                            $botones = $ClPagos->listaByEmpresas($cod_empresa);
                            foreach ($botones as $boton) {
                              $selectedB = "";
                              $nombreBoton = $boton['nombre'];
                              $cod_boton = $boton['cod_proveedor_botonpagos'];
                              if($botonAct == $cod_boton)
                                $selectedB = "selected";
                              $opBotones.= '<option value="'.$cod_boton.'" '.$selectedB.'>'.$nombreBoton.'</option>';
                            }
                          ?>
                          <div class="col-md-12 col-xs-12" style="margin-bottom: 20px;">
                            <h5>Bot&oacute;n en uso: <span class="btnActual text-info"><?= $nameBotonActual?></span></h5>
                          </div>
                            
                          <div class="col-md-4 col-xs-12">
                            <label for="">Botones de pago</label>
                            <select class="form-control" name="cmbBotonActual" id="cmbBotonActual">
                              <?= $opBotones?>
                            </select>
                          </div>
                          <div class="col-md-4 col-xs-12" style="margin-top: 35px;">
                            <button class="btn btn-primary btn-select-boton">Seleccionar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <input type="hidden" name="id" id="id" value="<?php echo $cod_empresa; ?>">
                        <div class="widget-content widget-content-area br-6">
                            
                            
                            <div class="widget-content widget-content-area rounded-pills-icon">
                                    
                                    <ul class="nav nav-pills mb-4 mt-3  justify-content-center" id="rounded-pills-icon-tab" role="tablist">
                                      <?php
                                        $botones = $ClPagos->lista();
                                        $x = 0;
                                        foreach ($botones as $boton) {
                                          $active = "";
                                          if($x == 0)
                                            $active = "active";
                                          $nombreBoton = $boton['nombre'];
                                          $imagen = "assets/img/".$boton['imagen'];

                                          echo '  <li class="nav-item ml-2 mr-2">
                                                      <a class="nav-link mb-2 '.$active.' text-center" id="rounded-pills-icon-home-tab" data-toggle="pill" href="#rounded-pills-icon-'.strtolower($nombreBoton).'" role="tab" aria-controls="rounded-pills-icon-'.strtolower($nombreBoton).'" aria-selected="true">
                                                          <img src="'.$imagen.'" style="height: 30px;"> 
                                                          '.$nombreBoton.'
                                                      </a>
                                                  </li>';
                                          $x++;
                                        }
                                      ?>                                        
                                    </ul>
                                    

                                    <div class="tab-content" id="rounded-pills-icon-tabContent">
                                        <!-- TAB DATAFAST -->
                                        <input type="hidden" name="txt_api_desarrollo" id="txt_api_desarrollo" value="OGE4Mjk0MTg1YTY1YmY1ZTAxNWE2YzhjNzI4YzBkOTV8YmZxR3F3UTMyWA==" />
                                        <input type="hidden" name="txt_entity_desarrollo" id="txt_entity_desarrollo" value="8ac7a4c8795a087501795ccf3742158e" />
                                        <input type="hidden" name="txt_mid_desarrollo" id="txt_mid_desarrollo" value="1000000505" />
                                        <input type="hidden" name="txt_tid_desarrollo" id="txt_tid_desarrollo" value="PD100406" />

                                        <?php
                                          $apiDatafast = "";
                                          $entity = "";
                                          $mid = "";
                                          $tid = "";
                                          $codDataFast=0;
                                          $btn = "";
                                          $conf = '<span class="text-danger btn-config-estado-D"><i data-feather="alert-triangle"></i> Bot&oacute;n no configurado</span> &nbsp;';

                                          $infoDataFast = $ClPagos->datos_datafast($cod_empresa);
                                          if($infoDataFast){
                                            $apiDatafast = $infoDataFast['api'];
                                            $entity = $infoDataFast['entityId'];
                                            $mid = $infoDataFast['mid'];
                                            $tid = $infoDataFast['tid'];
                                            $ambienteActual = $infoDataFast['ambiente'];
                                            $faseActual = $infoDataFast['fase'];

                                            $conf = '<span class="text-success btn-config-estado-D"><i data-feather="check-circle"></i> Bot&oacute;n configurado</span> &nbsp;';
                                          }
                                          else{
                                            $btn = '<button type="button" class="btn btn-primary btn-datafast-desarrollo-tokens">Obtener tokens de desarrollo</button>';
                                          }
                                          foreach ($ambientes as $amb) {
                                            $selectedAmb = "";
                                            $valorAmbiente = $amb['valor'];
                                            $nombreAmbiente = $amb['nombre'];
                                            if($ambienteActual == $valorAmbiente){
                                              $selectedAmb = "selected";
                                            }
                                            $opcionesAmbiente.='<option value="'.$valorAmbiente.'" '.$selectedAmb.'>'.$nombreAmbiente.'</option>';
                                          }
                                          //var_dump($fases);
                                          foreach ($fases as $fase) {
                                            $selectedFase = "";
                                            $ValorFase = $fase['valor'];
                                            if($faseActual == $ValorFase){
                                              $selectedFase = "selected";
                                            }
                                            $optionsFases.= '<option value="'.$ValorFase.'" '.$selectedFase.'>'.$ValorFase.'</option>';
                                          }
                                        ?>
                                        
                                        <div class="tab-pane fade show active" id="rounded-pills-icon-datafast" role="tabpanel" aria-labelledby="rounded-pills-icon-home-tab">
                                            <?= $btn?>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px;">
                                              <div class="col-md-6 col-xs-12">
                                                <label>API</label>
                                                <input type="text" class="form-control" name="txt_api" id="txt_api" value="<?= $apiDatafast?>">
                                              </div>

                                              <div class="col-md-6 col-xs-12">
                                                <label>Entity ID</label>
                                                <input type="text" class="form-control" name="txt_entityId" id="txt_entityId" value="<?= $entity?>">
                                              </div>
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                              <div class="col-md-6 col-xs-12">
                                                <label>MID</label>
                                                <input type="text" class="form-control" name="txt_mid" id="txt_mid" value="<?= $mid?>">
                                              </div>

                                              <div class="col-md-6 col-xs-12">
                                                <label>TID</label>
                                                <input type="text" class="form-control" name="txt_tid" id="txt_tid" value="<?= $tid?>">
                                              </div>
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                              <div class="col-md-6 col-xs-12">
                                                <label>Ambiente</label>
                                                <select class="form-control" name="cmbAmbienteDatafast" id="cmbAmbienteDatafast">
                                                  <?= $opcionesAmbiente?>
                                                </select>
                                              </div>
                                              <div class="col-md-6 col-xs-12">
                                                <label>Fase</label>
                                                <select class="form-control" name="cmbFaseDatafast" id="cmbFaseDatafast">
                                                  <?= $optionsFases?>
                                                </select>
                                              </div>
                                            </div>

                                            <div class="text-right">
                                              <?= $conf?>
                                              <button type="button" class="btn btn-outline-primary btn-updateDataFast" data-codigo="<?= $codDataFast?>">Actualizar</button>
                                            </div>
                                        </div>
                                        
                                        
                                        <!-- TAB PAYMENTEZ -->
                                        <div class="tab-pane fade" id="rounded-pills-icon-paymentez" role="tabpanel" aria-labelledby="rounded-pills-icon-profile-tab" style="height: 600px;">
                                            <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                                <input type="hidden" name="txt_clientCode_desarrollo" id="txt_clientCode_desarrollo" value="TPP3-EC-CLIENT" />
                                                <input type="hidden" name="txt_clientKey_desarrollo" id="txt_clientKey_desarrollo" value="ZfapAKOk4QFXheRNvndVib9XU3szzg" />
                                                <input type="hidden" name="txt_serverCode_desarrollo" id="txt_serverCode_desarrollo" value="TPP3-EC-SERVER" />
                                                <input type="hidden" name="txt_serverKey_desarrollo" id="txt_serverKey_desarrollo" value="JdXTDl2d0o0B8ANZ1heJOq7tf62PC6" />
                                        
                                                <?php   

                                                  $clientCode="";
                                                  $clientKey="";
                                                  $serverCode="";
                                                  $serverKey="";
                                                  $codPay=0;
                                                  $btn = "";
                                                  $conf = '<span class="text-danger btn-config-estado-P"><i data-feather="alert-triangle"></i> Bot&oacute;n no configurado</span> &nbsp;';
                                                  
                                                  $info = $ClPagos->datos_paymentez($cod_empresa);
                                                  if($info){
                                                      $clientCode=$info['client_code'];
                                                      $clientKey=$info['client_key'];
                                                      $serverCode=$info['server_code'];
                                                      $serverKey=$info['server_key'];
                                                      $codPay=$info['cod_empresa_paymentez'];
                                                      $ambiente = $info['ambiente'];
                                                      
                                                      
                                                      $conf = '<span class="text-success btn-config-estado-P"><i data-feather="check-circle"></i> Bot&oacute;n configurado</span> &nbsp;';
                                                  }
                                                  else{
                                                      $btn = '<button class="btn btn-primary btn-getTokens">Obtener tokens de desarrollo</button>';
                                                  }
                                                  $opcionesAmbiente = "";
                                                  foreach ($ambientes as $amb) {
                                                    $selected = "";
                                                    $valorAmbiente = $amb['valor'];
                                                    $nombreAmbiente = $amb['nombre'];
                                                    if($ambiente == $valorAmbiente)
                                                          $selected = "selected";
                                                    $opcionesAmbiente.='<option value="'.$valorAmbiente.'" '.$selected.'>'.$nombreAmbiente.'</option>';
                                                  }
                                                  
                                                ?>

                                                <label>Ambiente <span class="asterisco">*</span></label>
                                                <select class="form-control" id="cmbTipoBP" name="cmbTipoBP" style="margin-bottom: 15px;">
                                                  <?php echo $opcionesAmbiente;?>
                                                </select>
                                                <?php echo $btn;?>
                                            </div>
                                        <div class="mb-4 mt-4">
                                        <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Cliente</h4>
                                                </div>
                                                <?php
                                                echo '<table id="style-3" class="table style-3">
                                                    <tbody id="lstDisponibles">';
                                                            echo'
                                                            <tr>
                                                                <td>Client Code:</td>
                                                                <td><input type="text" id="txt_clientcode" class="form-control" value="'.$clientCode.'"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Client Key:</td>
                                                                <td><input type="text" id="txt_clientkey" class="form-control" value="'.$clientKey.'"></td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                                ';
                                              ?>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                               <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Servidor</h4>
                                                </div>
                                                
                                                <?php
                                                echo '<table id="style-3" class="table style-3">
                                                    <tbody id="lstDisponibles">';
                                                        echo'
                                                        <tr>
                                                            <td>Server Code:</td>
                                                            <td><input type="text" id="txt_servercode" class="form-control" value="'.$serverCode.'" ></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Server Key:</td>
                                                            <td><input type="text" id="txt_serverkey" class="form-control"  value="'.$serverKey.'" ></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                
                                                <div class="text-center">
                                                    '.$conf.'
                                                    <button type="button" class="btn btn-outline-primary btn-verificarToken" style="display:none">Verificar</button>
                                                    <button type="button" class="btn btn-outline-primary btn-updatePay" data-codigo="'.$codPay.'">Actualizar</button></div>';
                                                ?>
                                            </div>
                                        </div>
                                     </div>
                                        </div>
                                    </div>

                                </div>
                        </div>
                    </div>

                    <!-- <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing">
                                              
                        <div class="widget-content widget-content-area br-6">
                          <div><h4>Productos en esta categor&iacute;a</h4></div>
                            <div class="row"> 
                                <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px;">
                                  <table class="table style-3  table-hover">
                                    <thead>
                                      <tr>
                                        <th>&nbsp;</th>
                                        <th>Producto</th>
                                        <th>Quitar</th>
                                      </tr>
                                    </thead>
                                    <tbody id="contentCategorias" class="connectedSortable">
                                      <?php
                                        if($cod_producto != 0){
                                          $productos = new cl_productos(NULL);
                                          $resp = $productos->listaByCategoria($cod_producto);
                                          if(count($resp)>0){
                                            foreach ($resp as $p){
                                              $imagen = $files.$p['image_min'];
                                               echo '
                                                  <tr data-codigo="'.$p['cod_producto'].'">
                                                    <td class="text-center">
                                                        <span><img src="'.$imagen.'" class="profile-img" alt="Imagen"></span>
                                                    </td>
                                                    <td>'.$p['nombre'].'</td>
                                                    <td>
                                                      <a href="javascript:void(0);" data-value="'.$p['cod_producto'].'"  class="bs-tooltip btnEliminarOpciones" data-toggle="tooltip" data-placement="top" title="" data-original-title="Quitar de la categor&iacute;a"><i data-feather="x"></i></a>
                                                    </td>
                                                  </tr>';
                                            }
                                          }else
                                            echo '<p>No hay elementos</p>';
                                        }else
                                          echo '<p>No hay elementos</p>';
                                        ?>
                                    </tbody>
                                  </table>  
                                  
                                </div>
                            </div>
                      </div>
                        
                    </div> -->

                </div>

            </div>
            <?php footer(); ?>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    
    <?php js_mandatory(); ?>
    <script src="assets/js/pages/boton_pagos.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="plugins/select2/select2totree.js"></script>
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
</body>
</html>