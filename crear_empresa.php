<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";
require_once "clases/cl_fidelizacion.php";
require_once "clases/cl_usuarios.php";
require_once "clases/cl_sucursales.php";
require_once "clases/cl_notificaciones.php";

if(!isLogin()){
    header("location:login.php");
}

$Clempresas = new cl_empresas(NULL);
$Clfidelizacion = new cl_fidelizacion(NULL);
$Clusuarios = new cl_usuarios();
$ClSucursales = new cl_sucursales(NULL);
$ClNotificaciones = new cl_notificaciones();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

$cod_empresa = 0;
$alias = "";
$imagen = url_sistema.'/assets/img/200x200.jpg';
$nombre = "";
$password = passRandom();
$telefono = "";
$usuario = "";
$rnombre = "";
$rcorreo = "";
$urlWeb ="";
$color = "#8250FF";

$contacto_nombre = "";
$contacto_correo = "";
$api = "";
$chkProgramar = "";
$chkGravaIva = "checked";
$chkFidelizacion = "";
$chkProduccion = "";
$ckStartOnMenu = "";

$imgPdf = "";
$urlPDF = "";

$disabledBtnLogos = "disabled";

$tipoRecorte = "square";

if(isset($_GET['id'])){
  $alias = $_GET['id'];
  $empresa = $Clempresas->getByAlias($alias);
  if($empresa){
    $filesActuales = url_sistema.'assets/empresas/'.$empresa['alias'].'/';
    $filesActualesUp = url_upload.'assets/empresas/'.$empresa['alias'].'/';
    $cod_empresa = $empresa['cod_empresa'];
    $cod_plan = $empresa['cod_plan'];
    $imagen = url_sistema.'assets/empresas/'.$empresa['alias'].'/'.$empresa['logo'];
    $nombre = $empresa['nombre'];
    $telefono = $empresa['telefono'];
    $alias = $empresa['alias'];
    $api = $empresa['api_key'];
    $folder = $empresa['folder'];
    $tipoRecorte = $empresa['tipo_recorte'];
    $tipoEmpresa = $empresa['cod_tipo_empresa'];
    $impuesto = $empresa['impuesto'];
    if($folder <> ""){
        $disabledBtnLogos = "";
    }

    if(file_exists($filesActualesUp."menu.pdf")){
        $imgPdf = url_sistema.'/assets/img/logoPDF.png';
        $urlPDF = $filesActuales."menu.pdf";
    }

    $rnombre = $empresa['representante_nombre'];
    $rcorreo = $empresa['representante_correo'];
    $urlWeb = $empresa['url_web'];
    $color= $empresa['color'];
    
    $description = $empresa['description'];
    $keywords = $empresa['keywords'];
    $pixel = $empresa['facebook_pixel'];
    $pixel_verify = $empresa['facebook_pixel_verify'];
    
    $chkProgramar = ($empresa['programar_pedido'] == 1) ? 'checked' : '';
    $chkGravaIva = ($empresa['envio_grava_iva'] == 1) ? 'checked' : '';
    $chkFidelizacion = ($empresa['fidelizacion'] == 1) ? 'checked' : '';
    $chkProduccion = ($empresa['ambiente'] == 'production') ? 'checked' : '';
    $ckStartOnMenu = ($empresa['iniciar_en_menu'] == 1) ? 'checked' : '';
    
    $infoU=$Clusuarios->user_administrador($cod_empresa);
    if($infoU){
        $coduser=$infoU['cod_usuario'];
        $usuario=$infoU['usuario'];
    }
    $password = "";
    
    /*LISTA USUARIOS ADMINS*/
    $admins = $Clusuarios->getAdmins($cod_empresa);
    
    /*LISTA DE SUCURSALES POR EMPRESA*/
    $sucursales = $ClSucursales->listaByEmpresa($cod_empresa);
  }else{
    header("location: ./index.php");
  }
}
$rectangle = "";
if("rectangle" == $tipoRecorte)
    $rectangle = "selected";
$htmlTipoRecorte = '
    <option value="square">Cuadrado</option>    
    <option value="rectangle" '.$rectangle.'>Rect&aacute;ngulo</option>'; 

$folder_demo = url_folder_demo.$alias;
$linkdemo = '';
if(file_exists($folder_demo)){
    $linkdemo = "https://$alias.demo.mie-commerce.com/";
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <link href="plugins/croppie/croppie.css" rel="stylesheet">
    <link href="assets/css/components/tabs-accordian/custom-tabs.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
    .rounded-vertical-pills-icon .nav-pills .nav-link.active, .rounded-vertical-pills-icon .nav-pills .show > .nav-link {
    background-color: #1b55e2;
    }
    
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

      .table-hover:not(.table-dark) tbody tr:hover .new-control.new-checkbox .new-control-indicator {
          border: 0 !important;
      }

      .table td, .table th {
          padding: 8px;
      }
      .habilitado{
          background-color: #8dbf42;
      }
      .deshabilitado{
          background-color: #FFD83D;
      }
      .nopermitido{
          background-color: #FF8088;
      }
      #frmLogos img{
          background-color: #c3c3c3;
      }
      
    .bs-tooltip svg {
        width: 18px !important;
        color: gray !important;
    }
    
    .btnAcciones .opcion{
        cursor: pointer;
        margin-right: 15px;
    }
    
    .btnAcciones .opcion span{
        font-size: 16px; 
        vertical-align: middle;
        color:#888ea8;
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
    
    <!--MODAL NOTIFICAR-->
    <div class="modal fade bs-example-modal-lg" id="modalNotificacion" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Notificar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                            <form id="frmNotificar" name="frmNotificar" autocomplete="off">
                              <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <input type="hidden" id="cod_usuario" name="cod_usuario" value="" />
                                 <div class="col-md-6 col-sm-12 col-xs-12">
                                     <label>T&iacute;tulo <span class="asterisco">*</span></label>
                                     <input type="text" id="txt_titulo" name="txt_titulo" class="form-control" placeholder="T&iacute;tulo" required>
                                 </div>
                                 
                                 <div class="col-md-6 col-sm-12 col-xs-12">
                                     <label>Tipo de Notificación <span class="asterisco">*</span></label>
                                     <select id="cmb_tipo_noti" name="cmb_tipo_noti" class="form-control">
                                         <?php 
                                            $tipoNoti = $ClNotificaciones->getTipoNotificacion();
                                            foreach($tipoNoti as $tn){
                                                echo'<option value="'.$tn['icono'].'">'.$tn['nombre'].'</option>';
                                            }
                                         ?>
                                     </select>
                                 </div>
                                 
                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label>Descripci&oacute;n <span class="asterisco">*</span></label>
                                    <textarea style="display: initial;" placeholder="Descripcion" name="descripcion" id="txt_descripcion" class="form-control" required="required" autocomplete="off"></textarea>
                                 </div>
                              </div>
                            </form>
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btnSendNotification">Notificar</button>
                </div>
            </div>
        </div>
    </div>
    <!--FIN NOTIFICAR-->
    
    <!--MODAL EDITAR PASS-->
    <div class="modal fade bs-example-modal-lg" id="modalEditarPass" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Restablecer Usuario y/o Contrase&ntilde;a</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                
                    <div class="x_content">    
                      <div class="form-group">
                            <form id="frmEditarPass" name="frmEditarPass" autocomplete="off">
                              <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                <input type="hidden" id="cod_usuario2" name="cod_usuario2" value="" />
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                     <label>Usuario <span class="asterisco">*</span></label>
                                     <input type="text" id="txt_usuario" name="txt_usuario" class="form-control" placeholder="Ej: ejemplo@mail.com" required>
                                 </div>
                                 
                                 <div class="col-md-6 col-sm-12 col-xs-12">
                                     <label>Contrase&ntilde;a </label>
                                     <input type="text" id="txt_password_new" name="txt_password_new" class="form-control" placeholder="Contrase&ntilde;a">
                                 </div>
                              </div>
                            </form>
                      </div>              
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btnRestablecer">Editar</button>
                </div>
            </div>
        </div>
    </div>
     <!--FIN MODAL EDITAR PASS-->
     
     <!--MODAL Generar Demo-->
    <div class="modal fade bs-example-modal-lg" id="modalConstuirPagina" tabindex="99" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999999 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Construir Página</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="x_content">
                      <div class="form-group">
                          <p>
                              La idea es después poder escoger si es demo o producción de esa forma se actualizaría la carpeta demo.miecommerce o la carpeta que apunta a produccion
                          </p>
                            <input class="form-control" type="text" id="folder_demo" value="<?php echo $folder_demo; ?>"/>
                            <input class="form-control" type="text" id="folder_prod" value="/home1/digitalmind/<?php echo $folder; ?>"/>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Versión</th>
                                        <th>Fecha</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $versiones = $Clempresas->getVersionesWeb();
                                        foreach($versiones as $version){
                                            extract($version);
                                            $name = "$title ($version)";
                                            echo '<tr>
                                                <td>
                                                    '.$name.'
                                                    <dl>
                                                        <dd>'.$descripcion.'<dd>
                                                    </dl>
                                                </td>
                                                <td>'.$fecha_creacion.'</td>
                                                <td><button class="btn btn-primary btnCrearDemo" data-file="'.$filename.'">Crear</button></td>
                                                <td><button class="btn btn-outline-primary btnCrearDemoZip" data-file="'.$filename.'">Descargar Zip</button></td>
                                                <td><button class="btn btn-outline-primary btnSendOtherServer" data-file="'.$filename.'" data-url="'.$urlWeb.'">Enviar a otro hosting</button></td>
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
    <!--FIN NOTIFICAR-->
     
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
                <div class="col-md-12" style="margin-top:25px; ">
                    <div><span id="btnBack" data-module-back="productos.php" style="cursor: pointer;">
                      <i data-feather="chevron-left"></i><span style="font-size: 16px; vertical-align: middle;color:#888ea8;">Empresas</span></span>
                    </div>
                    <h3 id="titulo"><?php echo ($nombre != "") ? $nombre : "Agregar Empresa"; ?></h3>

                    <div class="btnAcciones" style="margin-bottom: 15px; <?php echo ($cod_empresa != 0) ? "" : "display: none;";  ?>">
                      <span class="opcion" id="btnNuevo">
                        <i class="feather-16" data-feather="plus"></i><span> Nueva Empresa</span>
                      </span>

                      <span class="opcion d-none">
                        <i class="feather-16" data-feather="copy"></i><span> Duplicar</span>
                      </span>

                      <span class="opcion" id="btnEliminar">
                        <i class="feather-16" data-feather="trash"></i><span> Eliminar</span>
                      </span>
                      
                      <span class="opcion">
                       <a href="editar_pagina.php?emp=<?php echo $alias?>" target="_blank">
                           <i class="feather-16" data-feather="link"></i><span> Home Página web </span>
                       </a> 
                      </span>
                      
                      <span class="opcion">
                          <?php
                            if($linkdemo !== ""){
                                echo '<a href="'.$linkdemo.'" target="_blank">
                                       <i class="feather-16" data-feather="airplay"></i><span> Ver Demo</span>
                                   </a>';
                            }else{
                                echo '
                                    <a href="#" class="btnGenerarDemo">
                                       <i class="feather-16" data-feather="tool"></i><span> Generar Demo</span>
                                   </a>';
                            }
                          ?>
                      </span>
                      
                        <span class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Más configuraciones <i data-feather="chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="empresas_buttonPayment.php?id=<?= $alias ?>" target="_blank"><i data-feather="credit-card"></i> Botón de Pagos</a>
                                <a class="dropdown-item" href="empresas_courier.php?id=<?= $alias ?>" target="_blank"><i data-feather="truck"></i> Courier</a>
                                <a class="dropdown-item" href="empresas_flotas.php?id=<?= $alias ?>" target="_blank"><i data-feather="truck"></i> Flotas</a>
                                <a class="dropdown-item" href="simulador-ordenes.php?alias=<?php echo $alias?>" target="_blank"><i data-feather="navigation"></i> Simulador Ordenes</a>
                                <a class="dropdown-item btnGenerarDemo" href="#"><i data-feather="airplay"></i> Generar demo</a>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing underline-content">
                        <div class="widget-content widget-content-area br-6">
                            <ul class="nav nav-tabs  mb-3 mt-3" id="lineTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="home" aria-selected="true"><i data-feather="info"></i> Informaci&oacute;n *</a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link" id="menu-tab" data-toggle="tab" href="#menu" role="tab" aria-controls="home" aria-selected="true"><i data-feather="menu"></i> Men&uacute; *</a>
                                </li>
                               
                                <li class="nav-item">
                                    <a class="nav-link" id="costoEnvio-tab" data-toggle="tab" href="#costoEnvio" role="tab" aria-controls="local" aria-selected="false"><i data-feather="truck"></i> Env&iacute;o *</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="" data-toggle="tab" href="#soporte" role="tab" aria-controls="soporte" aria-selected="false"><i data-feather="headphones"></i> Soporte *</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="permisosEmpresa-tab" data-toggle="tab" href="#permisosEmpresa" role="tab" aria-controls="local" aria-selected="false"><i data-feather="check-square"></i> Permisos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="fidelizacion-tab" data-toggle="tab" href="#fidelizacion" role="tab" aria-controls="fidelizacion" aria-selected="false"><i data-feather="gift"></i> Fidelizaci&oacute;n</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="notificacion-tab" data-toggle="tab" href="#notificacion" role="tab" aria-controls="notificaciones" aria-selected="false"><i data-feather="bell"></i> Notificaciones</a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link" id="ordenarItems-tab" data-toggle="tab" href="#ordenarItems" role="tab" aria-controls="ordenarItems" aria-selected="false"><i data-feather="move"></i> Ordenar Items</a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link" id="anuncWeb-tab" data-toggle="tab" href="#anuncWeb" role="tab" aria-controls="anuncWeb" aria-selected="false"><i data-feather="activity"></i> Anuncios Web</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="usuarios-tab" data-toggle="tab" href="#usuarios" role="tab" aria-controls="usuarios" aria-selected="false"><i data-feather="user"></i> Usuarios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="sucursales-tab" data-toggle="tab" href="#tab-sucursales" role="tab" aria-controls="sucursales" aria-selected="false"><i data-feather="map"></i> Sucursales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="logos-tab" data-toggle="tab" href="#tab-logos" role="tab" aria-controls="logos" aria-selected="false"><i data-feather="image"></i> Logos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="impuesto-tab" data-toggle="tab" href="#tab-impuesto" role="tab" aria-controls="Impuesto" aria-selected="false"><i data-feather="trending-up"></i> Impuesto</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="mensaje-pagos-tab" data-toggle="tab" href="#tab-mensaje-pagos" role="tab" aria-controls="Falta pagos" aria-selected="false"><i data-feather="dollar-sign"></i> Falta pagos</a>
                                </li>
                                
                            </ul>
                            
                            <div class="tab-content" id="simpletabContent">
                                
                                 <!--CONTENIDO INFO-->
                                <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                    <div class="col-xl-7 col-lg-12 col-sm-12  layout-spacing" style="margin-top: 35px;">
                                        <div class="widget-content widget-content-area br-6">
                                            <input type="hidden" name="id" id="id" value="<?php echo $cod_empresa; ?>">
                                            <input type="hidden" name="alias" id="alias" value="<?php echo $alias; ?>">
                                            <form name="frmSave" id="frmSave" autocomplete="off">
                                            
                                            <div class="x_content">   
                                                <div class="col-md-3 col-sm-3 col-xs-12">
                                                    <div class="upload mt-1 pr-md-1">
                                                        <input type="file" name="img_product" id="input-file-max-fs" class="dropify" data-default-file="<?php echo $imagen; ?>" data-max-file-size="1M" />
                                                        <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Subir Logo</p>
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                        <label>Nombre de la empresa<span class="asterisco">*</span></label>
                                                        <input type="text" placeholder="Nombre" name="txt_nombre" id="txt_nombre" class="form-control" required="required" autocomplete="off" value="<?php echo $nombre; ?>">
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                        <label>Nombre de contacto</label>
                                                        <input type="text" placeholder="Nombre para contacto" name="txt_contacto" id="txt_contacto" class="form-control" required="required" autocomplete="off" value="<?php echo $rnombre; ?>">
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                                        <label>Correo de contacto <span class="asterisco">*</span></label>
                                                        <input type="email" placeholder="Nombre" name="txt_correo" id="txt_correo" class="form-control" required="required" autocomplete="off" value="<?php echo $rcorreo; ?>">
                                                    </div>
                                                    <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                                        <label>Tel&eacute;fono</label>
                                                        <input type="text" placeholder="Telefono de contacto" name="txt_telefono" id="txt_telefono" class="form-control" required="required" autocomplete="off" value="<?php echo $telefono; ?>">
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                        <label>Tipo de Empresa <span class="asterisco">*</span></label>
                                                        <select class="form-control" id="cmbTipoE" name="cmbTipoE" style="margin-bottom: 15px;">
                                                            <?php
                                                            $tipos = $Clempresas->get_tipoem();
                                                            foreach ($tipos as $t) {
                                                                $selected = $tipoEmpresa == $t['cod_tipo_empresa'] ? 'selected' : '';
                                                                echo '<option value="'.$t['cod_tipo_empresa'].'" '.$selected.'>'.$t['tipo'].'</option>';
                                                            }    
                                                            ?>
                                                            </select>
                                                    </div>
                                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                        <label>Tipo de recorte 
                                                            <a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Tipo de recortador de imagen al crear o editar productos"><i data-feather="help-circle"></i></a>
                                                        </label>
                                                        <select class="form-control" id="cmbTipoRecorte" name="cmbTipoRecorte" style="margin-bottom: 15px;">
                                                            <?= $htmlTipoRecorte ?>   
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                        <label>Estado <span class="asterisco">*</span></label>
                                                        <div>
                                                            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                <input type="checkbox" name="chk_estado" id="chk_estado" checked>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                        <label>Programar pedido</label>
                                                            <div>
                                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                    <input type="checkbox" name="chk_programar" id="chk_programar" data-empresa="<?= $cod_empresa?>" <?= $chkProgramar?>>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                    </div>
                                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                        <label>Envío grava IVA</label>
                                                            <div>
                                                                <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                                                    <input type="checkbox" name="chk_envioIva" id="chk_envioIva" data-empresa="<?= $cod_empresa?>" <?= $chkGravaIva?>>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                    </div>
                                                
                                                </div>
                                                
                                                
                                                <div class="form-row">
                                                    <div class="col-md-12 col-sm-12 col-xs-12"><h4>Usuario Administrador</h4></div>
                                                    <div class="form-group col-md-8 col-sm-8 col-xs-12">
                                                        <label>Correo para usuario administrador <span class="asterisco">*</span></label>
                                                        <input type="email" placeholder="Usuario administrador" name="txt_usuario" id="txt_usuario" class="form-control" required="required" autocomplete="off" value="<?php echo $usuario; ?>">
                                                        <input type="hidden" name="txtiduser"  id="txtiduser" value="<?php echo $coduser; ?>">
                                                    </div>
                                                    <div class="form-group col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                                                        <label>Password</label>
                                                        <input type="text" placeholder="password" name="txt_password" id="txt_password" class="form-control" autocomplete="off" value="<?php echo $password; ?>">
                                                    </div>
                                                </div>
                                                
                                                </div>  
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-xl-5 col-lg-12 col-sm-12  layout-spacing" style="margin-top: 35px;">
                                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                        <!-- Informacion Automatica Start -->
                                            <div class="widget-content widget-content-area br-6">
                                                <div><h4>Informaci&oacute;n &Uacute;nica</h4></div>
                                                <div class="row" id="infoAlias">
                                                <div class="col-md-12 col-sm-12 col-xs-12" >
                                                        <label>C&oacute;digo de empresa</label>
                                                        <p>
                                                            <a class="btnCopiar" href="javascript:;" 
                                                            data-clipboard-action="copy" 
                                                            data-clipboard-text="<?php echo $cod_empresa; ?>">
                                                                <i data-feather="copy"></i><?php echo $cod_empresa; ?>
                                                            </a></p>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12" >
                                                    <label>Alias</label>
                                                    <p
                                                        <a class="btnCopiar" href="javascript:;" 
                                                            data-clipboard-action="copy" 
                                                            data-clipboard-text="<?php echo $alias; ?>">
                                                                <i data-feather="copy"></i><?php echo $alias; ?>
                                                        </a></p>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <label>Api Key</label>
                                                    <p>
                                                    <a class="btnCopiar" href="javascript:;" 
                                                            data-clipboard-action="copy" 
                                                            data-clipboard-text="<?php echo $api; ?>">
                                                                <i data-feather="copy"></i><?php echo $api; ?>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: right;">
                                                    <!--<a class="btn btn-danger" target="_blank"-->
                                                    <!--    href="https://exportar-justo.demo.mie-commerce.com/exportar-justo/index.php?cod_empresa=<?= $cod_empresa?>&business_name=<?= $nombre?>">Exportar a Justo</a>-->
                                                    <button class="btn btn-success LoginAdmins" data-value="<?= $alias?>" data-user="<?= $coduser ?>">Login</button>
                                                </div>	
                                                </div>  
                                            </div>
                                        <!-- Informacion Automatica End -->
                                        </div>
                                        
                                        <!--Configuracion pagina web-->
                                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                                <div class="mb-3"><h4>Configuración Página web</h4></div>
                                                <form id="frmWeb" name="frmWeb">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                            <label>Iniciar en menú <a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="La página inicial será el menú, esta pagina no tendría Home"><i data-feather="help-circle"></i></a></label>
                                                            <div>
                                                                <label class="switch s-icons s-outline  s-outline-success mr-2">
                                                                    <input type="checkbox" name="ckStartOnMenu" id="ckStartOnMenu" data-empresa="<?= $cod_empresa?>" <?= $ckStartOnMenu?>>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                            <label>Producción</label>
                                                                <div>
                                                                    <label class="switch s-icons s-outline  s-outline-success mr-2">
                                                                        <input type="checkbox" name="chk_produccion" id="chk_produccion" data-empresa="<?= $cod_empresa?>" <?= $chkProduccion?>>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Keywords <span class="asterisco">*</span><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Palabras clave relacionadas con el contenido de la página. (Es necesario separar con coma cada palabra)"><i data-feather="help-circle"></i></a></label>
                                                            <input type="text" placeholder="Ej: Desayunos, almuerzos, meriendas" name="txt_keywords" id="txt_keywords" class="form-control" required="required" autocomplete="off" value="<?php echo $keywords; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                            <label>Descripci&oacute;n <span class="asterisco">*</span><a href="javascript:void(0);" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Descripci&oacute;n resumida sobre la informaci&oacute;n que contiene la p&aacute;gina que estamos cargando."><i data-feather="help-circle"></i></a></label>
                                                            <input type="text" placeholder="Ej: Los mejores desayunos de Guayaquil" name="txt_description" id="txt_description" class="form-control" required="required" value="<?php echo $description; ?>">
                                                        </div>
                                                    </div>
                                                    
    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" >
                                                            <label>Pixel</label>
                                                            <input type="text" id="txt_pixel" name="txt_pixel" class="form-control" placeholder="Pixel de Facebook" value="<?php echo $pixel; ?>">
                                                        </div>
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" >
                                                            <label>Código META verificación Pixel</label>
                                                            <input type="text" id="txt_pixel_verify" name="txt_pixel_verify" class="form-control" placeholder="Código META verificación Pixel" value="<?php echo $pixel_verify; ?>">
                                                        </div>
                                                    </div>  
                                                    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                                            <label>Color<span class="asterisco">*</span></label>
                                                            <!--<input type="text" name="txtcolor" id="txtcolor" class="form-control" required="required" value="<?php echo $color; ?>">-->
                                                            <input class="form-control" name="txtcolor" id="txtcolor" data-jscolor="{}" value="<?php echo $color; ?>">
                                                        </div>
                                                        <div class="form-group col-md-8 col-sm-8 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Url Web</label>
                                                            <input type="text" placeholder="Ej: https://miempresa.com/" name="txt_urlWeb" id="txt_urlWeb" class="form-control" autocomplete="off" value="<?php echo $urlWeb; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Carpeta en hosting (no poner home1/digitalmind/)</label>
                                                            <input type="text" placeholder="Ej: micarpetaenelhosting" name="txt_folder" id="txt_folder" class="form-control" autocomplete="off" value="<?php echo $folder; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12 col-sm-12 col-xs-12" style="margin-bottom:10px;">
                                                            <label>Hosting en donde se encuentra</label>
                                                            <select class="form-control" name="hosting">
                                                                <option value="">No definido</option>
                                                                <option value="taste" <? if($empresa['hosting'] == 'taste') echo "selected"; ?>>Taste</option>
                                                                <option value="externo" <? if($empresa['hosting'] == 'externo') echo "selected"; ?>>Externo</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <!--Formas de pago-->
                                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                                <form id="frmFormaPago" name="frmFormaPago">
                                                    <div><h4>Formas de Pago</h4></div>
                                                    <div class="row" id="formasPago">
                                                        
                                                    <?php
                                                        $formaPago = $Clempresas->getFormasPagoEmp($cod_empresa);
                                                        
                                                        foreach($formaPago as $fp){
                                                            $selectedH = "";
                                                            $selectedI = "";
                                                            $selectedN = "";
                                                            
                                                            if($fp['estado'] == "A")
                                                                $selectedH = " selected ";
                                                        else if($fp['estado'] == "I")
                                                                $selectedI = " selected ";
                                                        else if($fp['estado'] == "D")
                                                                $selectedN = " selected ";
                                                                
                                                        if($fp['estado'] == null && ($fp['id_forma_pago'] == "E" || $fp['id_forma_pago'] == "T"))
                                                                $selectedH = " selected ";
                                                        else if($fp['estado'] == null && ($fp['id_forma_pago'] <> "E" || $fp['id_forma_pago'] <> "T"))
                                                                $selectedN = " selected ";
                                                                
                                                                
                                                            echo'   <div class="col-md-12 col-sm-12 col-xs-12" >
                                                                        <div class="col-md-6 col-sm-6 col-xs-6" >
                                                                            <p>'.$fp['fp_desc'].'</p>
                                                                        </div>
                                                                        <div class="col-md-6 col-sm-6 col-xs-6" >
                                                                            <select name="cmb_pago_permiso[]" class="input cmb_pago_permiso">
                                                                                <option class="habilitado" value="on-'.$fp['id_forma_pago'].'" '.$selectedH.'>Habilitado</option>
                                                                                <option class="deshabilitado" value="off-'.$fp['id_forma_pago'].'" '.$selectedI.'>Deshabilitado</option>
                                                                                <option class="nopermitido" value="no-'.$fp['id_forma_pago'].'" '.$selectedN.'>No permitido</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>';
                                                        }
                                                    ?>
                                                    
                                                    </div> 
                                                </form>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                    </div>
                                </div>

                                 <!--CONTENIDO MENU-->
                                <div class="tab-pane fade" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                                    <div class="col-md-4"><h4>Men&uacute;</h4></div>
                                    <div class="col-md-4">
                                    <select class="form-control" id="cmbRoles" style="margin-bottom: 15px;">
                                      <option value="">Escoja un rol</option>
                                    <?php
                                    $roles = $Clempresas->get_roles();
                                    foreach ($roles as $rol) {
                                        echo '<option value="'.$rol['cod_rol'].'">'.$rol['nombre'].'</option>';
                                    }    
                                    ?>
                                    </select>
                                    </div>
                                    <div class="table-responsive mb-4 mt-4">
                                        <div class="col-xl-6 col-lg-6 col-sm-12">                    
                                            <div><h4>P&aacute;ginas</h4></div>
                                            <form id="frmPaginas" method="POST" action="#">
                                            <div class=""> 
                                              <table id="tree-table" class="table table-hover table-bordered">
                                                <thead>
                                                    <th>T&iacute;tulo</th>
                                                    <th>P&aacute;gina</th>
                                                </thead>
                                                <tbody id="lstPaginas">
                                                </tbody>
                                              </table>
                                            </div>
                                            </form>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-sm-12">                    
                                            <div><h4>Posici&oacute;n del Men&uacute;</h4></div>
                                            <form id="frmPaginas" method="POST" action="#">
                                            <div class=""> 
                                              <table id="tree-table" class="table table-hover table-bordered">
                                                <thead>
                                                    <th>T&iacute;tulo</th>
                                                    <th>P&aacute;gina</th>
                                                </thead>
                                                <tbody id="lstOrden">
                                                </tbody>
                                              </table>
                                            </div>
                                            </form>
                                        </div>
                                    </div> 
                                </div>
                                
                                <!--CONTENIDO COSTO ENVIO-->
                                <div class="tab-pane fade" id="costoEnvio" role="tabpanel" aria-labelledby="costoEnvio-tab">
                                    <div class="col-md-12"><h4>Costo de env&iacute;o</h4></div>
                                    <div class="mb-4 mt-4">
                                        
                                         <div class="widget-content widget-content-area">
                                            <?php
                                            $base_km = 0;
                                            $base_dinero = 0;
                                            $adicional_km = 0;
                                            $codigo=0;
                                            
                                                $query = "SELECT * FROM tb_empresa_costo_envio WHERE cod_empresa = ".$cod_empresa ;
                                                $row = Conexion::buscarRegistro($query, NULL);
                                                if($row){
                                                    $base_km = $row['base_km'];
                                                    $base_dinero = $row['base_dinero'];
                                                    $adicional_km = $row['adicional_km'];
                                                    $codigo= $row['cod_empresa_costo_envio'];
                                                }
                                                   
                                            ?>
                                            <h3 class="">Costo de Env&iacute;o a Domicilio</h3>
                                            <p>Configura el valor por env&iacute;o a domicilio escoge un monto base para un rango de km (Ej. de 0 a 3 km costar&aacute; $2) y luego define cuanto costar&aacute; cada km adicional (Ej. 0.50ctvs)</p>
                                            <br>
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
                                            
                                            <br/>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                                
                                <!--CONTENIDO PERMISOS-->
                                <div class="tab-pane fade" id="permisosEmpresa" role="tabpanel" aria-labelledby="permisosEmpresa-tab">
                                    <div class="mb-4 mt-2">
                                         <div class="widget-content widget-content-area">
                                            <?php
                                            $permisoEmpresa = $Clempresas->getIdPermisionByBusiness($cod_empresa);
                                            $grupos = $Clempresas->getAllPermisionsGroup();
                                            foreach($grupos as $key => $grupo){
                                                echo '<h3 class="mt-4">'.$grupo['grupo'].'</h3>';
                                                $permisos = $grupo['permisos'];
                                                foreach($permisos as $key => $permiso){
                                                    
                                                    $check = "";
                                                    if($permisoEmpresa)
                                                        if(in_array($permiso['identificador'], $permisoEmpresa))     
                                                            $check = "checked";
                                                    
                                                    echo '<div class="row">
                                                        <div class="col-6">'.$permiso['nombre'].'</div>
                                                        <div class="col-4">'.$permiso['identificador'].'</div>
                                                        <div class="col-2">
                                                            <label class="switch s-icons s-outline  s-outline-success  mb-3 ml-2">
                                                                    <input type="checkbox" name="chk_permiso" class="chk_permiso" data-empresa="'.$cod_empresa.'" data-status="'.$permiso['identificador'].'" '.$check.'>
                                                                    <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                                
                                <!--CONTENIDO FIDELIZACION-->
                                <div class="tab-pane fade" id="fidelizacion" role="tabpanel" aria-labelledby="fidelizacion-tab" style="height: 450px;">
                                    <div class="col-md-12">
                                        <h4>Fidelizaci&oacute;n</h4>
                                        <?php
                                        $mostrarCheckFidelizacion = "display: none;";
                                            $resp = $Clfidelizacion->niveles($cod_empresa);
                                            if($resp)
                                                $mostrarCheckFidelizacion = "";
                                        ?>
                                        <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2 chkFidelizacion" style="<?= $mostrarCheckFidelizacion?>">
                                            <input type="checkbox" name="chk_fidelizacion" id="chk_fidelizacion" <?php echo $chkFidelizacion; ?> />
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="mb-4 mt-4">
                                        
                                        
                                        <div class="col-xl-4 col-lg-4 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6" style="height: 300px;">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Esquema</h4>
                                                </div>
                                                <table id="style-3" class="table style-3">
                                                    <tbody id="lstDisponibles">
                                                        <?php
                                                        $divisor_puntos=0;
                                                        $monto_puntos=0;
                                                        $cod_fidelizacion_puntos=0;
                                                        
                                                            $resp = $Clfidelizacion->datos_fidelizacion($cod_empresa);
                                                            if($resp){
                                                              $divisor_puntos=$resp['divisor_puntos'];
                                                              $monto_puntos=$resp['monto_puntos'];
                                                              $cod_fidelizacion_puntos=$resp['cod_fidelizacion_puntos'];
                                                            }
                                                            echo'
                                                            <tr>
                                                                <td>Por cada($):</td>
                                                                <td><input type="number" id="txt_divisor_puntos" class="form-control" value="'.$divisor_puntos.'" style="width: 90px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Recibes(Puntos):</td>
                                                                <td><input type="number" id="txt_monto_puntos" class="form-control" 
                                                                value="'.$monto_puntos.'" style="width: 90px;"></td>
                                                            </tr>
                                                            ';
                                                         
                                                               
                                                        ?>
                                                    </tbody>
                                                </table>
        
                                                <div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: right;" wfd-id="67">
                                                    <button type="button" class="btn btn-outline-primary btnFidelizacion" data-id="<?php echo $cod_fidelizacion_puntos?>" wfd-id="252">Actualizar</button>
                                                </div>
                                            </div>
                                        </div>
                                      
                                       
                                        <div class="col-xl-8 col-lg-8 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Niveles</h4>
                                                </div>
                                                <table id="style-3" class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Nombre</th>
                                                            <th class="text-center">Inicio</th>
                                                            <th class="text-center">Fin</th>
                                                            <th class="text-center">Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lstDisponibles">
                                                     <form action="" method="post" id="frmNiveles">   
                                                        <?php
                                                        $c=1;
                                                        $cod_nivel=0;
                                                        $nombre="";
                                                        $punto_inicial=0;
                                                        $punto_final=0;
                                                        $dinero_x_punto=0;
                                                        
                                                        $resp = $Clfidelizacion->niveles($cod_empresa);
                                                        if($resp)
                                                        {
                                                            $registro=1;
                                                            foreach ($resp as $niveles) {
                                                              $numeroitems=count($resp);
                                                              
                                                              $cod_nivel=$niveles['cod_nivel'];
                                                              $nombre=$niveles['nombre'];
                                                              $punto_inicial=$niveles['punto_inicial'];
                                                              $punto_final=$niveles['punto_final'];
                                                              $dinero_x_punto=$niveles['dinero_x_punto'];
                                                             
                                                              if($c==1){$atributo="readonly";}else{$atributo="";}
                                                              if($c==$numeroitems){$atributoFinal="readonly";}else{$atributoFinal="";}
                                                              
                                                            echo'
                                                            <tr>
                                                                <td class="text-center" >
                                                                   <input type="text" name="nombre[]" id="txt_nombre'.$cod_nivel.'" class="form-control" style="text-align: center;"
                                                                value="'.$nombre.'" >
                                                                </td>
                                                                <td class="text-center">
                                                                 <input type="number"  name="inicio[]" id="txt_inicio'.$cod_nivel.'" class="form-control" 
                                                                value="'.$punto_inicial.'" style="width: 80px;" '.$atributo.'>
                                                                </td>
                                                                <td class="text-center">
                                                                <input type="number" name="fin[]"  id="txt_fin'.$cod_nivel.'" class="form-control" 
                                                                value="'.$punto_final.'" style="width: 80px;" '.$atributoFinal.'>
                                                                </td>
                                                                <td class="text-center"><input type="number" name="monto[]" id="txt_monto'.$cod_nivel.'" class="form-control" 
                                                                value="'.$dinero_x_punto.'" style="width: 80px;">
                                                                </td>
                                                            </tr>
                                                            ';
                                                             $c++;
                                                            }
                                                        }
                                                        else
                                                        {
                                                             $registro=0;
                                                            echo'
                                                            <tr>
                                                                <td class="text-center" >
                                                                   <input type="text" name="nombre[]" id="txt_nombre1" class="form-control txt_nombre1" style="text-align: center;"
                                                                value="NIVEL 1" >
                                                                </td>
                                                                <td class="text-center">
                                                                 <input type="number" name="inicio[]" id="txt_inicio1" class="form-control" 
                                                                value="0" style="width: 80px;" '.$atributo.' readonly>
                                                                </td>
                                                                <td class="text-center">
                                                                <input type="number" name="fin[]" id="txt_fin1" class="form-control" 
                                                                value="'.$punto_final.'" style="width: 80px;" '.$atributoFinal.'>
                                                                </td>
                                                                <td class="text-center"><input type="number" name="monto[]" id="txt_monto1" class="form-control" 
                                                                value="'.$dinero_x_punto.'" style="width: 80px;">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center" >
                                                                   <input type="text" name="nombre[]" id="txt_nombre2" class="form-control txt_nombre2" style="text-align: center;"
                                                                value="NIVEL 2" >
                                                                </td>
                                                                <td class="text-center">
                                                                 <input type="number"  name="inicio[]" id="txt_inicio2" class="form-control" 
                                                                value="'.$punto_inicial.'" style="width: 80px;" '.$atributo.'>
                                                                </td>
                                                                <td class="text-center">
                                                                <input type="number" name="fin[]" id="txt_fin2" class="form-control" 
                                                                value="'.$punto_final.'" style="width: 80px;" '.$atributoFinal.'>
                                                                </td>
                                                                <td class="text-center"><input type="number" name="monto[]" id="txt_monto2" class="form-control" 
                                                                value="'.$dinero_x_punto.'" style="width: 80px;">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center" >
                                                                   <input type="text" name="nombre[]" id="txt_nombre3" class="form-control txt_nombre3" style="text-align: center;"
                                                                value="NIVEL 3" >
                                                                </td>
                                                                <td class="text-center">
                                                                 <input type="number" name="inicio[]" id="txt_inicio3" class="form-control" 
                                                                value="'.$punto_inicial.'" style="width: 80px;" '.$atributo.'>
                                                                </td>
                                                                <td class="text-center">
                                                                <input type="number" name="fin[]" id="txt_fin3" class="form-control" 
                                                                value="999" style="width: 80px;" '.$atributoFinal.' readonly>
                                                                </td>
                                                                <td class="text-center"><input type="number" name="monto[]" id="txt_monto3" class="form-control" 
                                                                value="'.$dinero_x_punto.'" style="width: 80px;">
                                                                </td>
                                                            </tr>
                                                            ';
                                                        }
                                                        
                                                   echo'
                                                     </form>
                                                    </tbody>
                                                </table>
                                                 <div class="text-center"><button type="button" class="btn btn-outline-primary btnInsertNiveles" data-registro="'.$registro.'">Actualizar</button></div>';
                                                  ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--FIN FIDELIZACION-->
                                
                                <!--CONTENIDO NOTIFICACION-->
                                <div class="tab-pane fade" id="notificacion" role="tabpanel" aria-labelledby="notificacion-tab" style="height: 300px;">
                                    <div class="col-md-12"><h4>Notificaciones</h4></div>
                                    <div class="mb-4 mt-4">
                                        <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                                <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Motorizado</h4>
                                                </div>
                                                <?php
                                                echo '<table id="style-3" class="table style-3">
                                                    <tbody id="lstDisponibles">';
                                                        $token="";
                                                        $topic="";
                                                        $cod_notificacion=0;
                                                        
                                                            $resp = $Clfidelizacion->notificaciones($cod_empresa,"MOTORIZADOS");
                                                            if($resp){
                                                              $token=$resp['token'];
                                                              $topic=$resp['topic'];
                                                              $cod_notificacion=$resp['cod_empresa_notificacion'];
                                                            }
                                                            echo'
                                                            <tr>
                                                                <td>Token:</td>
                                                                <td><input type="text" id="txt_token_motorizado" class="form-control" value="'.$token.'"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Topic:</td>
                                                                <td><input type="text" id="txt_topic_motorizado" class="form-control" value="'.$topic.'"></td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                                <div class="text-center"><button type="button" class="btn btn-outline-primary btnNotificacionMotorizado" data-codigo="'.$cod_notificacion.'">Actualizar</button></div>';
                                              ?>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-sm-6  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                               <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Usuario</h4>
                                                </div>
                                                
                                                <?php
                                                echo '<table id="style-3" class="table style-3">
                                                    <tbody id="lstDisponibles">';
                                                        $token="";
                                                        $topic="";
                                                        $cod_notificacion=0;
                                                        
                                                            $resp = $Clfidelizacion->notificaciones($cod_empresa,"USUARIOS");
                                                            if($resp){
                                                              $token=$resp['token'];
                                                              $topic=$resp['topic'];
                                                              $cod_notificacion=$resp['cod_empresa_notificacion'];
                                                            }
                                                            echo'
                                                            <tr>
                                                                <td>Token:</td>
                                                                <td><input type="text" id="txt_token_usuario" class="form-control" value="'.$token.'" ></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Topic:</td>
                                                                <td><input type="text" id="txt_topic_usuario" class="form-control"  value="'.$topic.'" ></td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                                <div class="text-center"><button type="button" class="btn btn-outline-primary btnNotificacionUsuario" data-codigo="'.$cod_notificacion.'">Actualizar</button></div>';
                                                ?>
                                            </div>
                                        </div>
                                     </div>
                                </div>
                                
                                <!--CONTENIDO ORDENAR ITEMS-->
                                <div class="tab-pane fade" id="ordenarItems" role="tabpanel" aria-labelledby="ordenarItems-tab" style="height: 600px;">
                                    <div class="col-md-12"><h4>Ordenar Items</h4></div>
                                    <div class="mb-4 mt-4">
                                        
                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                <label>Nombre:</label>
                                                <input type="text" class="form-control" id="text_nuevo_modulo" >
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                <label>Descripci&oacute;n:</label>
                                                <textarea class="form-control" id="txt_desc_items" ></textarea>
                                            </div>
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                               <div><button type="button" class="btn btn-outline-primary btnCrearModulo" data-codigo="">Crear Modulo</button></div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                               <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Modulos</h4>
                                                </div>
                                                <table id="style-3" class="table style-3">
                                                    <tbody id="tablaModulos">
                                                        <?php
                                                            $nombre="";
                                                            $cod_modulo_web=0;
                                                            
                                                                $resp = $Clfidelizacion->modulosWeb($cod_empresa);
                                                                if($resp){
                                                                
                                                                    foreach ($resp as $modulos) {
                                                                        $nombre=$modulos['nombre'];
                                                                        $cod_modulo_web=$modulos['cod_web_modulos_producto'];
                                                                        $desc = $modulos['descripcion'];
                                                                    echo'
                                                                    <tr id="contMo'.$cod_modulo_web.'">
                                                                        <td><span>'.$cod_modulo_web.'</span></td>
                                                                        <td><input type="text" id="txt_modulo'.$cod_modulo_web.'" class="form-control" value="'.$nombre.'" ></td>
                                                                        <td><textarea id="txa_modulo'.$cod_modulo_web.'" class="form-control" >'.$desc.'</textarea></td>
                                                                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarModulo" data-codigo="'.$cod_modulo_web.'">Editar</button></td>
                                                                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEliminarModulo" data-codigo="'.$cod_modulo_web.'">Eliminar</button></td>
                                                                    </tr>
                                                                    ';
                                                                    }
                                                                }
                                                              
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--CONTENIDO ANUNCIOS WEB-->
                                <div class="tab-pane fade" id="anuncWeb" role="tabpanel" aria-labelledby="anuncWeb-tab" style="height: 600px;">
                                    <div class="col-md-12"><h4>Anuncios Web</h4></div>
                                    <div class="mb-4 mt-4">
                                        
                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                <label>Nombre:</label>
                                                <input type="text" class="form-control" id="text_nuevo_anuncio" >
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                                <label>Descripci&oacute;n:</label>
                                                <textarea class="form-control" id="txt_desc_anuncio" ></textarea>
                                            </div>
                                            <div class="col-xl-6 col-md-6 col-sm-6 col-6" >
                                               <div><button type="button" class="btn btn-outline-primary btnCrearAnuncio" data-codigo="">Crear Anuncio</button></div>
                                            </div>
                                        </div>
                                        
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-xl-3 col-md-3 col-sm-3 col-3" >
                                                <label>Width:</label>
                                                <input type="text" class="form-control" id="txt_width" value="512" placeholder="500">
                                            </div>
                                            
                                            <div class="col-xl-3 col-md-3 col-sm-3 col-3" >
                                                <label>Height:</label>
                                                <input type="text" class="form-control" id="txt_height" value="512" placeholder="500">
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                            <div class="widget-content widget-content-area br-6">
                                               <div class="col-xl-12 col-md-12 col-sm-12 col-12" wfd-id="42">
                                                    <h4>Anuncios</h4>
                                                </div>
                                                <table id="style-4" class="table style-3">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Id</th>
                                                            <th>Nombre</th>
                                                            <th  class="text-center">Descripcion</th>
                                                            <th class="text-center">Width</th>
                                                            <th class="text-center">Height</th>
                                                            <th  colspan="2" class="text-center">Acci&oacute;n</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablaAnuncios">
                                                        <?php
                                                            $nombre="";
                                                            $cod_anuncio=0;
                                                            
                                                                $resp = $Clfidelizacion->anuncWeb($cod_empresa);
                                                                if($resp){
                                                                
                                                                    foreach ($resp as $anuncio) {
                                                                        $nombre = $anuncio['nombre'];
                                                                        $cod_anuncio = $anuncio['cod_anuncio_cabecera'];
                                                                        $desc = $anuncio['descripcion'];
                                                                        $width = $anuncio['width'];
                                                                        $height = $anuncio['height'];
                                                                    echo'
                                                                    <tr id="contAn'.$cod_anuncio.'">
                                                                        <td><span>'.$cod_anuncio.'</span></td>
                                                                        <td><input type="text" id="txt_anuncio'.$cod_anuncio.'" class="form-control" value="'.$nombre.'" ></td>
                                                                        <td><textarea id="txa_anuncio'.$cod_anuncio.'" class="form-control" >'.$desc.'</textarea></td>
                                                                        <td><input type="text" id="txt_width'.$cod_anuncio.'" class="form-control" value="'.$width.'" ></td>
                                                                        <td><input type="text" id="txt_height'.$cod_anuncio.'" class="form-control" value="'.$height.'" ></td>
                                                                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEditarAnuncio" data-codigo="'.$cod_anuncio.'">Editar</button></td>
                                                                        <td  class="text-center"><button type="button" class="btn btn-outline-primary btnEliminarAnuncio" data-codigo="'.$cod_anuncio.'">Eliminar</button></td>
                                                                    </tr>
                                                                    ';
                                                                    }
                                                                }
                                                              
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--CONTENIDO SOPORTE-->
                                <div class="tab-pane fade" id="soporte" role="tabpanel" aria-labelledby="costoEnvio-tab">
                                    <div class="mb-4 mt-4">
                                        
                                         <div class="widget-content widget-content-area">
                                            <h3 class="">Soporte</h3>
                                            <p>Puedes integrar la gestión de tickets "soporte" con <b>Clickup</b>, el sistema creará automaticamente una nueva lista en la sección <b>Soporte y Mantenimiento</b></p>
                                            <br>
                                            <div class="row">
                                            <?php
                                                $query = "SELECT * FROM tb_empresa_clickup WHERE cod_empresa = $cod_empresa AND estado = 'A'";
                                                $row = Conexion::buscarRegistro($query, NULL);
                                                if($row){
                                                    echo '
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <h4>Integrado con ClickUp</h4>
                                                            <p><b>'.$row['id_lista'].'</b></p>
                                                        </div>
                                                    ';
                                                    
                                                }else{
                                            ?>
                                            
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <h4>Integrarse con Click up</h4>
                                                    <button type="button" class="btn btn-outline-primary btnIntegrarClickUp">Comenzar</button>
                                                </div>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                </div>
                                            <?php
                                                }
                                            ?>
                                            </div>
                                            <br/>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                                <!-- FIN CONTENIDO SOPORTE -->
                                
                                <!--CONTENIDO USUARIOS-->
                                <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="costoEnvio-tab">
                                    <div class="mb-4 mt-4">
                                         <div class="widget-content widget-content-area">
                                            <h3 class="">Notificar a usuarios</h3>
                                            <br>
                                            <div class="row">
                                                <table id="tree-table" class="table table-hover table-bordered">
                                                <thead>
                                                    <th></th>
                                                    <th>Nombres</th>
                                                    <th>Usuario</th>
                                                    <th>Rol</th>
                                                    <th style="text-align: center;">Acciones</th>
                                                </thead>
                                                <tbody id="lstUsuarios">
                                                    <?php
                                                        foreach($admins as $adm){
                                                            echo'   <tr>
                                                                        <td>'.$adm['cod_usuario'].'</td>
                                                                        <td>'.$adm['nombre'].' '.$adm['apellido'].'</td>
                                                                        <td>'.$adm['usuario'].'</td>
                                                                        <td>'.$adm['rol'].'</td>
                                                                        <td style="text-align: center;">
                                                                            <a data-value="'.$adm['cod_usuario'].'" class="btn_notificar" href="javascript:void(0);"><i data-feather="bell"></i></a>
                                                                            <a data-value="'.$adm['cod_usuario'].'" class="btn_editar_pass" href="javascript:void(0);"><i data-feather="edit"></i></a>
                                                                            <a class="LoginAdmins" href="javascript:void(0);" data-value="'.$alias.'" data-user="'.$adm['cod_usuario'].'"><i data-feather="key"></i></a>
                                                                        </td>
                                                                    </tr>';
                                                        }
                                                    ?>
                                                </tbody>
                                              </table>
                                            </div>
                                            <br/>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                                <!-- FIN CONTENIDO USUARIOS -->
                                
                                <!--CONTENIDO SUCURSALES-->
                                <div class="tab-pane fade" id="tab-sucursales" role="tabpanel" aria-labelledby="Sucursales-tab">
                                    <div class="mb-4 mt-4">
                                         <div class="widget-content widget-content-area">
                                            <h3 class="">Lista de Sucursales</h3>
                                            <br>
                                            <div class="row">
                                                <table id="tree-table" class="table table-hover table-bordered">
                                                <thead>
                                                    <th></th>
                                                    <th>Nombre</th>
                                                    <th>Direccion</th>
                                                    <th>Cobertura</th>
                                                    <th>Estado</th>
                                                    <th style="text-align: center;">Acciones</th>
                                                </thead>
                                                <tbody id="lstSucursales">
                                                    <?php
                                                        foreach($sucursales as $suc){
                                                            $estado = ($suc['estado']=='A') ? 'Activo' : 'Inactivo';
                                                            echo'   <tr>
                                                                        <td>'.$suc['cod_sucursal'].'</td>
                                                                        <td>'.$suc['nombre'].'</td>
                                                                        <td>'.$suc['direccion'].'</td>
                                                                        <td>'.$suc['distancia_km'].'km</td>
                                                                        <td>'.$estado.'</td>
                                                                        <td style="text-align: center;">
                                                                            <a data-value="'.$suc['cod_sucursal'].'" class="" href="javascript:void(0);"><i data-feather="bell"></i></a>
                                                                            <a data-value="'.$suc['cod_sucursal'].'" class="" href="javascript:void(0);"><i data-feather="edit"></i></a>
                                                                        </td>
                                                                    </tr>';
                                                        }
                                                    ?>
                                                </tbody>
                                              </table>
                                            </div>
                                            <br/>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                                <!-- FIN CONTENIDO USUARIOS -->
                                
                                <!-- Contenido Logos -->
                                <div class="tab-pane fade" id="tab-logos" role="tabpanel" aria-labelledby="Logos-tab">
                                    <div class="mb-4 mt-4">
                                         <form id="frmLogos" name="frmLogos">
                                             <input type="hidden" id="hdIdLogo" name="hdIdLogo" value="<?= $cod_empresa?>">
                                             <div class="widget-content widget-content-area">
                                                <h3 class="">Logos</h3>
                                                <br>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo principal 512 <small>(nueva versón: 500 x 350px)</small></label>
                                                        <input class="form-control flLogos" type="file" data-name="logo.png" data-titulo="Logo principal">
                                                        <img src="<?= $filesActuales."logo.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo Footer 512</label>
                                                        <input class="form-control flLogos" type="file" data-name="logo-footer.png" data-titulo="Logo Footer">
                                                        <img src="<?= $filesActuales."logo-footer.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo resposive 512x512</label>
                                                        <input class="form-control flLogos" type="file" data-name="logo-xs.png" data-titulo="Logo resposive">
                                                        <img src="<?= $filesActuales."logo-xs.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono PWA 192x192</label>
                                                        <input class="form-control flLogos" type="file" data-name="icon-192x192.png" data-titulo="Icono PWA 192x192">
                                                        <img src="<?= $filesActuales."icon-192x192.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono PWA 512x512</label>
                                                        <input class="form-control flLogos" type="file" data-name="icon-512x512.png" data-titulo="Icono PWA 512x512">
                                                        <img src="<?= $filesActuales."icon-512x512.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Favicon 16x16</label>
                                                        <input class="form-control flLogos" type="file" data-name="favicon.png" data-titulo="Favicon">
                                                        <img src="<?= $filesActuales."favicon.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Logo Compartir 512x512 (.jpg con fondo blanco)</label>
                                                        <input class="form-control flLogos" type="file" data-name="compartir.jpg" data-titulo="Logo Compartir 512x512 (.jpg con fondo blanco)">
                                                        <img src="<?= $filesActuales."compartir.jpg"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Icono App Perfil</label>
                                                        <input class="form-control flLogos" type="file" data-name="profile.png" data-titulo="Icono App Perfil">
                                                        <img src="<?= $filesActuales."profile.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Men&uacute; PDF</label>
                                                        <input class="form-control flLogos" type="file" data-name="menu.pdf" data-titulo="Men&uacute; PDF subido">
                                                        <a href="<?= $urlPDF?>" target="_blank">
                                                            <img src="<?= $imgPdf?>" alt="" style="height: 50px;">
                                                        </a>
                                                            
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3 d-none">
                                                        <label for="">Imagen transferencias bancarias</label>
                                                        <input class="form-control flLogos" type="file" data-name="transferencia_bancaria.png" data-titulo="Imagen transferencias bancarias subida">
                                                        <img src="<?= $filesActuales."transferencia_bancaria.png"?>" alt="" style="height: 50px;">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                                                        <label for="">Imagen Bienvenida modal tipo envío (500x200)</label>
                                                        <input class="form-control flLogos" type="file" data-name="bienvenida_modal.png" data-titulo="Imagen bienvenida modal subida">
                                                        <img src="<?= $filesActuales."bienvenida_modal.png"?>" alt="" style="height: 50px;">
                                                        <button type="button" class="btn btn-danger btnEliminarLogo" data-name="bienvenida_modal.png">X</button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-6 col-12 mb-3" style="text-align: right;">
                                                        <input type="hidden" id="urlFolder" value="<?= $folder?>">
                                                        <button class="btn btn-primary btnActLogosPagina" <?= $disabledBtnLogos?>>Actualizar Logos en la p&aacute;gina</button>
                                                    </div>
                                                </div>
                                            </div>
                                         </form>
                                    </div>
                                </div>
                                <!-- Fin Contenido Logos -->
                                
                                <!--CONTENIDO Impuestos-->
                                <div class="tab-pane fade" id="tab-impuesto" role="tabpanel" aria-labelledby="boton-tab" style="height: 600px;">
                                    <div class="col-md-12 mt-3"><h3>Impuestos</h3></div>
                                    <div class="row">
                                        <div class="col-12">
                                        Cambiar el impuesto de esta empresa
                                        </div>
                                        <div class="col-4">
                                            <label>Porcentaje</label>
                                            <input value="<?= $impuesto ?>" id="txt_impuesto" class="form-control"/>
                                        </div>
                                        <div class="col-5">
                                            <label>Acción</label>
                                            <select class="form-control" id="cmb_tipo">
                                                <option value="mantener_precioNoTax">Mantener Precio sin impuesto</option>
                                                <option value="mantener_pvp">Mantener PVP</option>
                                            </select>
                                        </div>
                                        <div class="col-3 mt-3">
                                            <button class="btn btn-primary" onclick="actualizarImpuesto()">Actualizar</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin Contenido Impuestos -->

                                <div class="tab-pane fade" id="tab-mensaje-pagos" role="tabpanel" aria-labelledby="info-tab">
                                    <div class="col-12 layout-spacing" style="margin-top: 35px;">
                                        <div class="widget-content widget-content-area br-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <h3>Mensaje falta de pagos</h3>
                                                    <p>
                                                        Agrega un mensaje a los comercios que no estén al día en sus pagos.
                                                    </p>
                                                </div>
                                                <div class="col-12">
                                                    <input type="text" id="fp-title" class="form-control" placeholder="Título">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <textarea id="fp-message" class="form-control" placeholder="Mensaje"></textarea>
                                                </div>
                                                <div class="col-12  mt-3">
                                                    <button class="btn btn-primary" onclick="saveMessagePayment()">Guardar</button>
                                                    <button id="removeMessagePayment" class="btn btn-danger d-none" onclick="removeMessagePayment()">Eliminar</button>
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
    
    <?php js_mandatory(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> 
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/jscolor.js"></script>
    <script src="assets/js/pages/crear_empresas.js?v=4" type="text/javascript"></script>
    
    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/ckeditor/ckeditor.js"></script>
    <script src="plugins/croppie/croppie.js"></script>
    <script type="text/javascript" src="emoji/dist/emojionearea.js"></script>
    <link rel="stylesheet" type="text/css" href="emoji/dist/emojionearea.min.css" media="screen">
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->

    <script>
        $("#txt_descripcion").emojioneArea({
            container: "#containerEmoji",
            hideSource: false,
        });
    </script>
</body>
</html>