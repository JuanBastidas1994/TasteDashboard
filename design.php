<?php

function css_mandatory(){
	echo '
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    	<title>';
    	name_page();
    echo '</title>
		<link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
	    <!-- BEGIN GLOBAL MANDATORY STYLES -->
	    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
	    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	    <link href="assets/css/plugins.css" rel="stylesheet" type="text/css" />
	    <!-- END GLOBAL MANDATORY STYLES -->
	        
	    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/datatables.css">
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_html5.css">
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/dt-global_style.css">
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_custom.css">
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/responsive/responsive.bootstrap.min.css">
	    <link rel="stylesheet" type="text/css" href="plugins/table/datatable/button-ext/buttons.dataTables.min.css">

	    

	    <link href="assets/css/scrollspyNav.css" rel="stylesheet" type="text/css" />
	    <link href="assets/css/components/custom-modal.css" rel="stylesheet" type="text/css" />

	    <link rel="stylesheet" href="plugins/font-icons/fontawesome/css/regular.css">
	    <link rel="stylesheet" href="plugins/font-icons/fontawesome/css/fontawesome.css">
	    <link href="plugins/flatpickr/flatpickr.css" rel="stylesheet" type="text/css">
	    <link rel="stylesheet" type="text/css" href="assets/css/forms/switches.css">
	    <link rel="stylesheet" type="text/css" href="assets/css/forms/theme-checkbox-radio.css">
    	<link rel="stylesheet" type="text/css" href="plugins/dropify/dropify.min.css">
	    <!-- SWEET ALERT -->
	    <script src="plugins/sweetalerts/promise-polyfill.js"></script>
    	<!--<link href="plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet" type="text/css" />-->
    	<!--<link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />-->
    	<link href="plugins/sweetalerts/sweetalert2-v11.min.css" rel="stylesheet" type="text/css" />
    	<link href="plugins/sweetalerts/sweetalert-customjc.css" rel="stylesheet" type="text/css" />
    	<!-- SELECT 2 -->
    	<link rel="stylesheet" type="text/css" href="plugins/select2/select2.min.css">
    	<link href="plugins/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="assets/css/custom.css">
		<link href="plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
	';
}

function js_mandatory(){
	echo '
		<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
	    <script src="assets/js/libs/jquery-3.1.1.min.js"></script>

	    <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
    	<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase-messaging.js"></script>
    	<script src="assets/js/firebase.js?v=1"></script>
    	<script src="assets/js/firebase_orders.js?v=2"></script>

	    <script src="bootstrap/js/popper.min.js"></script>
	    <script src="bootstrap/js/bootstrap.min.js"></script>
	    <script src="plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	    <script src="assets/js/app.js"></script>
	    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
	    <script>
	        $(document).ready(function() {
	            App.init();
	            jQuery.extend(jQuery.validator.messages, {
				    required: "Este campo es obligatorio.",
				    remote: "Please fix this field.",
				    email: "Por favor ingrese un correo correcto.",
				    url: "Please enter a valid URL.",
				    date: "Please enter a valid date.",
				    dateISO: "Please enter a valid date (ISO).",
				    number: "Please enter a valid number.",
				    digits: "Please enter only digits.",
				    creditcard: "Please enter a valid credit card number.",
				    equalTo: "Please enter the same value again.",
				    accept: "Please enter a value with a valid extension.",
				    maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
				    minlength: jQuery.validator.format("Please enter at least {0} characters."),
				    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
				    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
				    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
				    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
				});
	        });
	        
	    </script>
	    <script src="assets/js/js.cookie.js"></script>
	    <script src="assets/js/custom.js?v=7"></script>
	    <!-- END GLOBAL MANDATORY SCRIPTS -->

	    <!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
	    <script src="plugins/table/datatable/datatables.js"></script>
	    <!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
	    <script src="plugins/table/datatable/button-ext/dataTables.buttons.min.js"></script>
	    <script src="plugins/table/datatable/button-ext/jszip.min.js"></script>    
	    <script src="plugins/table/datatable/button-ext/buttons.html5.min.js"></script>
	    <script src="plugins/table/datatable/button-ext/buttons.print.min.js"></script>

	    <script src="plugins/table/datatable/responsive/dataTables.responsive.min.js"></script>
	    <script src="plugins/table/datatable/responsive/responsive.bootstrap.js"></script>
	    <script src="assets/js/scrollspyNav.js"></script>
	    <script src="plugins/flatpickr/flatpickr.js"></script>
	    <script src="plugins/blockui/jquery.blockUI.min.js"></script>
	    <!--<script src="plugins/sweetalerts/sweetalert2.min.js"></script>-->
	    <script src="plugins/sweetalerts/sweetalert2-v11.min.js"></script>
	    <script src="plugins/sweetalerts/sweetalert-customjc.js"></script>
	    <script src="plugins/bootstrap-maxlength/bootstrap-maxlength.js"></script>
	    <script src="plugins/font-icons/feather/feather.min.js"></script>
	    <script type="text/javascript">
	        feather.replace();
	        $(\'.maxlength\').maxlength();
	    </script>
	    <script src="plugins/dropify/dropify.js"></script>
	    <!-- SELECT 2 -->
	    <script src="plugins/select2/select2.min.js"></script>
	    <script src="plugins/notification/snackbar/snackbar.min.js"></script>
	    
	    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="plugins/translate/jqueryTranslator.js"></script>
        <script src="plugins/ion.sound/ion.sound.js"></script>
        <script src="assets/js/gestion-ordenes-v5/sounds.js" type="text/javascript"></script>
        <script src="assets/js/gestion-ordenes-v5/toastJc.js?v=001" type="text/javascript"></script>
	    ';
}

function menu()
{
	$session = getSession();
    $cod_rol = $session['cod_rol'];
    $cod_empresa = $session['cod_empresa'];

    $query = "SELECT p.* FROM tb_paginas p, tb_pagina_rol pr WHERE p.cod_pagina = pr.cod_pagina AND p.estado = 'A' AND p.cod_padre = 0 AND pr.cod_rol = $cod_rol AND pr.cod_empresa = $cod_empresa order by pr.posicion ASC";
    $menuData=Conexion::buscarVariosRegistro($query);
    foreach ($menuData as $menu)
    {
        $codigo = $menu['cod_pagina'];
        $id = $menu['id'];
        $icono = $menu['icono'];
        $url = $menu['nombre'];
        $titulo = $menu['titulo'];
        $translate = $menu['data_translate'];
        
        $query = "SELECT p.* FROM tb_paginas p, tb_pagina_rol pr WHERE p.cod_pagina = pr.cod_pagina AND p.estado = 'A' AND p.cod_padre = ".$codigo." AND pr.cod_rol = ".$cod_rol." AND pr.cod_empresa = ".$cod_empresa." order by posicion ASC ";
        $Submenu2 = Conexion::buscarVariosRegistro($query);
        if(count($Submenu2)>0)
        {
            echo '<li class="menu">
            	<a href="#'.$id.'" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            		<div>
            			<i data-feather="'.$icono.'"></i>
            			<span data-translate="'.$translate.'">'.$titulo.' </span>
            		</div>
            		<div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
            	</a>
                <ul class="collapse submenu list-unstyled" id="'.$id.'" data-parent="#accordionExample">';
            foreach($Submenu2 as $Submenu)
            {
                $id = $Submenu['id'];
                $icono = $Submenu['icono'];
                $url = $Submenu['nombre'];
                $titulo = $Submenu['titulo'];
                $translate = $Submenu['data_translate'];
                echo '<li id="'.$id.'"><a href="'.$url.'" data-translate="'.$translate.'">'.$titulo.'</a></li>';
            }
            echo '</ul></li>';
        }
        else
        {
            echo '<li class="menu">
            	<a href="'.$url.'" aria-expanded="false" class="dropdown-toggle">
            		<div>
            			<i data-feather="'.$icono.'"></i>
            			<span data-translate="'.$translate.'">'.$titulo.' </span>
            		</div>
            	</a>	
            </li>';
        }
    }
}

function sidebar()
{
	echo '<div class="sidebar-wrapper sidebar-theme">
            <nav id="sidebar">
                <div class="shadow-bottom"></div>
                <ul class="list-unstyled menu-categories" id="accordionExample">';
                    menu();
    echo '      </ul>
            </nav>
        </div>';
}

/*BREADCRUM*/
function navbar($saveButton = false, $url = ""){
	$saveHtml = '';
	if($saveButton){
		$module = '';
		if($url != ""){
			$module = 'data-module-back="'.$url.'"';
		}
		$saveHtml = '<ul class="navbar-nav flex-row ml-auto ">
	            	<li>
	            		<button type="button" class="btn btn-outline-primary" id="btnDescartar" '.$module.'>Descartar</button>
	            	</li>
	            	<li>
	            		<button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
	            	</li>
	            </ul>';
	}

	echo '<div class="sub-header-container">
	        <header class="header navbar navbar-expand-sm">
	            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>

	            <ul class="navbar-nav flex-row">
	                <li>
	                    <div class="page-header">

	                        <nav class="breadcrumb-one" aria-label="breadcrumb">
	                            <ol class="breadcrumb">
	                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
	                                <li class="breadcrumb-item active showModalSupport" aria-current="page"><span>Ventas</span></li>
	                            </ol>
	                        </nav>

	                    </div>
	                </li>
	            </ul>
	            '.$saveHtml.'
	        </header>
	    </div>';
}

function navbar2($saveButton = false, $url = ""){
	$saveHtml = '';
	if($saveButton){
		$module = '';
		if($url != ""){
			$module = 'data-module-back="'.$url.'"';
		}
		$saveHtml = '<ul class="navbar-nav flex-row ml-auto ">
	            	<li>
	            		<button type="button" class="btn btn-outline-primary" id="btnDescartar" '.$module.'>Descartar</button>
	            	</li>
	            	<li>
	            		<button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
	            	</li>
	            </ul>';
	}

	echo '<div class="sub-header-container">
	        <header class="header navbar navbar-expand-sm expand-header">
	            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>

	            <ul class="navbar-nav flex-row">
	                <li>
	                    <div class="page-header">

	                        <nav class="breadcrumb-one" aria-label="breadcrumb">
	                            <ol class="breadcrumb">
	                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
	                                <li class="breadcrumb-item active showModalSupport" aria-current="page"><span>Ventas</span></li>
	                            </ol>
	                        </nav>

	                    </div>
	                </li>
	            </ul>
	            '.$saveHtml.'
	        </header>
	    </div>';
}

function top()
{
	$session = getSession();
	$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

	$logo = $files.$session['logo'];
	$perfil = $files.$session['imagen'];
	echo '<div class="header-container fixed-top">
	        <header class="header navbar navbar-expand-sm">

	            <ul class="navbar-item theme-brand flex-row  text-center">
	                <li class="nav-item theme-logo">
	                    <a href="index.php">
	                        <img src="'.$logo.'" class="navbar-logo" alt="logo">
	                    </a>
	                </li>
	                <li class="nav-item theme-text">
	                    <a href="index.php" class="nav-link"> '.$session['empresa'].' </a>
	                </li>
	            </ul>

	            <ul class="navbar-item flex-row ml-md-auto">
	                <li class="nav-item dropdown language-dropdown">
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="language-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img id="img-lang" src="assets/img/id.png" class="flag-width" alt="flag">
                        </a>
                        <div class="dropdown-menu position-absolute" aria-labelledby="language-dropdown">
                            <a data-value="es" class="dropdown-item d-flex set-lang" href="javascript:void(0);"><img id="img-lang-es" src="assets/img/es.png" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;Espa&ntilde;ol</span></a>
                            <a data-value="en" class="dropdown-item d-flex set-lang" href="javascript:void(0);"><img id="img-lang-en" src="assets/img/en.png" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;English</span></a>
                        </div>
                    </li>
	                '.notificaciones().'
	                <li class="nav-item dropdown user-profile-dropdown">
	                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	                        <img src="'.$perfil.'" alt="avatar">
	                    </a>
	                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
	                        <div class="">
	                            <div class="dropdown-item">
	                                <a class="" href="perfil.php"><i data-feather="user"></i> <span data-translate="navbar-miperfil">Mi Perfil</span></a>
	                            </div>
								<div class="dropdown-item">
	                                <a class="" href="scrumboard.php"><i data-feather="headphones"></i> <span data-translate="navbar-soporte">Soporte</span></a>
	                            </div>
	                            <div class="dropdown-item">
	                                <a class="exitApplication" href="javascript:void(0)"><i data-feather="log-out"></i> <span data-translate="navbar-cerrarsesion">Cerrar Sesi&oacute;n</span></a>
	                            </div>
	                        </div>
	                    </div>
	                </li>

	            </ul>
	        </header>
	    </div>
	    <div id="toastJcContent">
            <div class="position-fixed p-3 toast-bottom-right" style="z-index: 1040; right: 0; bottom: 0;"></div>
            <div class="position-fixed p-3 toast-bottom-left" style="z-index: 1040; left: 0; bottom: 0;"></div>
            <div class="position-fixed p-3 toast-top-right" style="z-index: 1040; right: 0; top: 0;"></div>
            <div class="position-fixed p-3 toast-top-left" style="z-index: 1040; left: 0; top: 0;"></div>
        </div>';
}

function notificaciones(){
	$session = getSession();
	$cod_usuario = $session['cod_usuario'];
	$html = '<li class="nav-item dropdown notification-dropdown">
	                    <a style="display: none;" href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-success"></span>
	                    </a>
	                    <div class="dropdown-menu position-absolute" aria-labelledby="notificationDropdown" style="max-height: 500px; overflow: auto;">
	                        <div class="notification-scroll">';

                            Conexion::ejecutar("SET NAMES 'utf8mb4'", NULL);
	                        $query = "SELECT * 
            	                        FROM tb_system_notification 
            	                        WHERE cod_usuario = $cod_usuario
            	                        ORDER BY fecha DESC
            	                        LIMIT 0, 10";
        					$resp = Conexion::buscarVariosRegistro($query);
        					foreach ($resp as $notif) {
        						$titulo = $notif['titulo'];
        						$icono = $notif['icono'];
        						$page = $notif['url'];
        						$detalle = html_entity_decode($notif['detalle']);
        						$html.='<div class="dropdown-item">
	                                <a href="'.$page.'" class="media">
	                                    <i data-feather="'.$icono.'"></i>
	                                    <div class="media-body">
	                                        <div class="notification-para"><span class="user-name">'.$titulo.'</span> '.$detalle.'</div>
	                                    </div>
	                                </a>
	                            </div>';
        					}     

	$html .= '              </div>
	                    </div>
	                </li>';
	return $html;                
}

function footer()
{
    echo '<div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © '.date('Y').' <a target="_blank" href="https://www.digitalmindtec.com">Digital Mind</a>, All rights reserved.</p>
                </div>
                <div class="footer-section f-section-2">
                    <p class="">Desarrollado por <a target="_blank" href="https://www.digitalmindtec.com"> Digital Mind </a> </p>
                </div>
            </div>';
}



function itemOrden($orden, $noRead = false){
	$id = $orden['cod_orden'];
	$estado = $orden['estado'];
	$total = number_format($orden['total'],2);
    $nombre = $orden['nombre'].' '.$orden['apellido'];
    $telefono = $orden['telefono'];
    $direccion = $orden['referencia'];
    $fecha = fechaLatinoShort($orden['fecha']);
    $hora = explode(" ",$orden['fecha'])[1];
    $hora_retiro = "";
    
    $icon = '<i data-feather="map-pin"></i>';
	$bgItem = 'bg-success-light';
    if($orden['is_envio'] == 0){
		$bgItem = 'bg-warning-light';
    	$hora_retiro = " | Hora Retiro: ".$orden['hora_retiro'];
    	$icon = '<i data-feather="package"></i>';
    }

	/*Validar si es programado*/     
	$fecha = $orden['fecha'];  
	$fechaP = $orden['fecha'];  
	$spanProgramado = ""; 
	if($orden['is_programado'] == 1){
		$fechaP = $orden['hora_retiro'];
		$orden['is_envio'] == 0 ? $textoP = "Fecha de retiro" : $textoP = "Enviar para el";
		$spanProgramado = '<br><span>'.$textoP.': '.fechaHoraLatinoShort($orden['hora_retiro']).'</span>';
	} 
	$fOrden = explode(" ", $fechaP);
	if(date($fOrden[0]) < date(fecha_only()))
		$bgItem = 'bg-dark-light';
    
    if($orden['pago'] == "E")
        $icon .= ' <i data-feather="dollar-sign"></i>';
    else if($orden['pago'] == "T")
        $icon .= ' <i data-feather="credit-card"></i>';
    
    if($orden['is_programado'] == 1)
    	$icon .= ' <i data-feather="watch"></i>';

    $classNoRead="";
    if($noRead){
		$classNoRead = "unread-mail";
	}
	

	return '<div id="orden'.$id.'" data-value="'.$id.'" class="mail-item '.$estado.' '.$classNoRead.'">
            <div class="animated animatedFadeInUp fadeInUp" id="mailHeadingThree">
                <div class="mb-0">
                    <div class="mail-item-heading '.$orden['pago'].' '.$bgItem.'"  role="navigation" data-target="#mailCollapse'.$id.'" aria-expanded="false">
                        <div class="mail-item-inner">
                            <div class="d-flex">
                                <div class="f-head">
                                    <span><b>#'.$id.'</b></span>
                                </div>
                                <div class="f-body">
                                    <div class="meta-mail-time">
                                        <p class="user-email">'.$nombre.'</p>
                                    </div>
                                    <div class="meta-title-tag">
                                        <p class="mail-content-excerpt">$<span class="mail-title"></span>'.$total.' &nbsp;&nbsp;'.$icon.'<span class="mail-title" data-mailTitle="Pedido a domicilio"></span> '.$direccion.$hora_retiro.'
										'.$spanProgramado.'
                                        </p>
                                        <div class="tags">
                                            <span class="g-dot-primary"></span>
                                            <span class="g-dot-warning"></span>
                                            <span class="g-dot-success"></span>
                                            <span class="g-dot-danger"></span>
                                        </div>
                                        <p class="meta-time align-self-center">'.$fecha.'</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}

function itemOrdenExpandido($orden){
	global $session;
	$Clordenes = new cl_ordenes(NULL);
	$Clusuarios = new cl_usuarios(NULL);
	$Clsucursales = new cl_sucursales(NULL);
	$Clempresas = new cl_empresas(NULL);

	$id = $orden['cod_orden'];
	$estado = $orden['estado'];
	$cod_usuario = $orden['cod_usuario'];
	
    $nombre = $orden['nombre'].' '.$orden['apellido'];
    $telefono = ($orden['telefono']!=="") ? $orden['telefono'] : $orden['telefono_user'];
    $direccion = $orden['referencia'];
    $referencia = $orden['referencia2'];
    $correo = $orden['correo'];
    $fecha = fechaLatinoShort($orden['fecha']);
    $hora = explode(" ",$orden['fecha'])[1];
    $observacion = $orden['observacion'];
    $cod_descuento = $orden['cod_descuento'];
	$pedProgramado = "";
	$spanProgramado = "";
	if(1 == $orden['is_programado']){
		$pedProgramado = '<span class="shadow-none badge badge-warning">Programado</span>';
		$spanProgramado = '<p><span class="text-danger">Fecha de retiro: '.fechaHoraLatinoShort($orden['hora_retiro']).'</span></p>';
	}
    $medioCompra = '<span><i data-feather="smartphone"></i> '.$orden['medio_compra'].'</span>';
    /*-----TIPO COURIER---*/
    $cod_courier=$orden['cod_courier'];
    $infoGacela = $Clsucursales->getgacelaSucursal($orden['cod_sucursal']);
    $infoLaar = $Clsucursales->getLaarSucursal($orden['cod_sucursal']);
    /*-----TIPO COURIER---*/
    /*UBICACION*/
    $latitud =  $orden['latitud'];
    $longitud =  $orden['longitud'];
    $sucursal = $Clsucursales->getInfo($orden['cod_sucursal']);

	/*IMPRIMIR GUIA*/
	$btnPrint = "";
	$empresa = $Clempresas->get($sucursal['cod_empresa']);
	$alias = $empresa['alias'];
	if("bolon-city" == $alias){
		$btnPrint .= '	<a href="javascript:void(0)" class="printOrden" data-value="'.$id.'" title="Imprimir">
							<i data-feather="printer"></i>
						</a>';
	}else if("danilo-restaurante" == $alias){
	    $btnPrint .= '	<a href="javascript:void(0)" class="printOrdenV2" data-value="'.$id.'" title="Imprimir V2">
							<i data-feather="printer"></i>
						</a>';
	}
	else if("hamburguesas-dm" == $alias){
	    $btnPrint .= '	<a href="javascript:void(0)" class="printOrden" data-value="'.$id.'" title="Imprimir">
							<i data-feather="printer"></i>
						</a>';
		$btnPrint .= '	<a href="javascript:void(0)" class="printOrdenV2" data-value="'.$id.'" title="Imprimir V2">
							<i data-feather="printer"></i>
						</a>';
	}
	/*else if("babys" == $alias || "paap" == $alias){
		
	}*/
	$btnPrint .= '	<a href="orden_tracking.php?id='.$id.'" target="_blank" title="Tracking">
							<i data-feather="truck"></i> Tracking
						</a>';

    /*DATOS DE FACTURACION*/
    $fact = $orden['datos_facturacion'];
    $datos_facturacion = json_decode($fact,true);
    if (JSON_ERROR_NONE !== json_last_error()){
    	$datos_facturacion['nombre'] = "";
    	$datos_facturacion['apellido'] = "";
    	$datos_facturacion['empresa'] = "";
    	$datos_facturacion['direccion'] = "";
    	$datos_facturacion['telefono'] = "";
    	$datos_facturacion['cedula'] = "";
    	$datos_facturacion['correo'] = "";
    }	


    /*DINERO*/
	$subtotal = number_format($orden['subtotal'],2);
	$descuento = number_format($orden['descuento'],2);
	$envio = number_format($orden['envio'],2);
	$iva = number_format($orden['iva'],2);
    $total = number_format($orden['total'],2);

    $pagoDetalle='';
    $pago = "";

    $pago =$orden['pagos'][0]['descripcion'];
    switch($orden['pago'])
    {
        case "E":
            if($orden['is_suelto'] == 1){
            $pagoDetalle = 'Enviar suelto de $'.number_format($orden['monto_suelto'],2);
            }
            break;
            
    }
    
    $tablaPagos = "";
    foreach ($orden['pagos'] as $pagos) {
         $tablaPagos .= '<tr>
            <td>'.$pagos['descripcion'].'</td>
            <td>$'.number_format($pagos['monto'],2).'</td>';
            
            $td = "";
            if($pagos['descripcion'] == "Efectivo"){
                if($orden['pago']=="E"){
                    if($orden['is_suelto'] == 1){
                		$td = 'Enviar suelto de $'.number_format($orden['monto_suelto'],2);
                	}
                }
            }
            
         $tablaPagos .= '
            <td>'.$td.'</td>
        </tr>';
    }
    

	$html = '<div id="mailCollapse'.$id.'" class="detalleOrden" aria-labelledby="mailHeadingThree" data-parent="#mailbox-inbox">
            <div class="mail-content-container mailInbox" >

                <div class="d-flex justify-content-between">

                    <div class="d-flex user-info">
                        <div class="f-head">
                            <img src="assets/img/90x90.jpg" class="user-profile" alt="avatar">
                        </div>
                        <div class="f-body">
                            <div class="meta-title-tag">
                                <h4 class="mail-usr-name" data-mailtitle="Orden"><b>#'.$id.'</b> - '.$nombre.'</h4>
                            </div>
                            <div class="meta-mail-time">
                                <p class="user-email" data-mailto="'.$correo.'">'.$correo.'</p>
                                <p class="mail-content-meta-date current-recent-mail">'.$fecha.' -</p>
                                <p class="meta-time align-self-center">'.$hora.'</p>
								'.$spanProgramado.'
                            </div>
                            <div style="line-height: 8px;">'.$sucursal['nombre'].' '.$pedProgramado.' '.$medioCompra.'</div>
                        </div>
                    </div>

                    <div class="action-btns">
                        <button class="btn btn-primary btnModalNotify" id="btnModalNotify"><i data-feather="bell"></i> Notificar</button>
                        <a href="tel:'.$telefono.'" data-toggle="tooltip" data-placement="top" title="Llamar">
                            <i data-feather="phone-call"></i> '.$telefono.'
                        </a>
                        <a href="orden_detalle.php?id='.$id.'" target="_blank" title="Ver mas informacion">
                            <i data-feather="eye"></i> Ver m&aacute;s
                        </a>
                        '.$btnPrint.'
                    </div>
                </div>

                <p class="mail-content" data-mailTitle="Orden" data-maildescription=\'{"ops":[{"insert":""}]}\'> </p>
                <input type="hidden" value='.$cod_usuario.' class="codusuarioOrden">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                	<h3 style="border-bottom: 1px solid #dee2e6;">Datos de Facturaci&oacute;n</h3>
                	<p><span><b>Nombre: </b></span>'.$datos_facturacion['nombre'].' '.$datos_facturacion['apellido'].'</p>
                	<p><span><b>Cedula/Ruc: </b></span>'.$datos_facturacion['cedula'].'</p>
                	<p><span><b>Correo: </b></span>'.$datos_facturacion['correo'].'</p>
                	<!--<p><span><b>Empresa: </b></span>'.$datos_facturacion['empresa'].'</p>-->
                	<p><span><b>Telef&oacute;no: </b></span>'.$datos_facturacion['telefono'].'</p>
                	<p><span><b>Direcci&oacute;n: </b></span>'.$datos_facturacion['direccion'].'</p>
                	<h3>Orden</h3>
                	<div class="">
	                	<table class="table table-sm">
	                		<thead>
	                			<tr>
	                				<th>&Iacute;tem</th>
	                				<th>Cant</th>
	                				<th>Precio</th>
	                				<th>Desc</th>
	                				<th>Total</th>
	                			</tr>
	                		</thead>
	                		<tbody>';

	                		$resp = $Clordenes->get_detalle_orden($id);
	                        foreach ($resp as $detalle) {
	                            $code = ($detalle['sku']!=="") ? "<b>SKU: ".$detalle['sku']."</b><br/>" : "";
	                        	$comentariosDetalle = '';
	                        	if($detalle['descripcion']!=""){
									$numVersion = explode("v", $orden['api_version'])[1];
									if($numVersion < 4){
	                        			$texto = $str = str_replace("&nbsp;", " ", $detalle['descripcion']);
									}
									else{
										$texto = "";
										$arr = json_decode($detalle['descripcion'], true);
										if (JSON_ERROR_NONE !== json_last_error()){
											$texto = $detalle['descripcion'];
										}
										else{
											foreach($arr as $det){
											    $classText = "";
											    if(strrpos($det['text'], 'Promo martes') === 0)
											        $classText = 'style="margin-left: -20px !important;font-size: 15px !important; font-weight: bold !important; color: black !important;"';    
											    
												$texto.= '<p class="font-weight-bold mt-4" '.$classText.'>'.$det['text'].'</p>';
												foreach($det['detalles'] as $det2){
													$preAdi = "";
													if(0 < $det2['precio_adicional'])
														$preAdi = "+$".$det2['precio_adicional'];
													$texto.= '<p class="m-0">'.$det2['cantidad'].' '.$det2['text'].' '.$preAdi;
												}
												
											}
											if($detalle['comentarios'] !== ""){
											    $texto.= '<p class="font-weight-bold mt-4">Comentario</p>';
											    $texto.= '<p class="m-0">'.$detalle['comentarios'].'</p>';
											}
										}
									}
	                        		$comentariosDetalle='<dl>
										  <dd class="text-comments" style="padding-left: 20px;">'.$texto.'</dd>
										</dl>';
	                        	}
	                            $html .= '<tr>
	                            	<td><p style="font-size: 15px; font-weight: bold; color: black !important;">'.$code.$detalle['nombre'].'</p>
	                            		'.$comentariosDetalle.'
	                            	</td>
	                            	<td>'.$detalle['cantidad'].'</td>
	                            	<td>'.$detalle['precio'].'</td>
	                            	<td>'.$detalle['descuento'].'</td>
	                            	<td>'.($detalle['precio_final']).'</td>
	                            </tr>';
	                        }
	        $html .= '     		</tbody>
	                	</table>

	                	<div class="col-sm-12 col-12 order-sm-1 order-0">
                            <div class="inv--total-amounts text-sm-right">
                                <div class="row">
                                    <div class="col-sm-8 col-7">
                                        <p class="">SubTotal: </p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="">$'.$subtotal.'</p>
                                    </div>
                                    <div class="col-sm-8 col-7">
                                        <p class=" discount-rate">Descuento : </p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="">$'.$descuento.'</p>
                                    </div>
                                    <div class="col-sm-8 col-7">
                                        <p class="">Envío: </p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="">$'.$envio.'</p>
                                    </div>
                                    <div class="col-sm-8 col-7">
                                        <p class="">Impuestos: </p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="">$'.$iva.'</p>
                                    </div>
                                    
                                    <div class="col-sm-8 col-7 grand-total-title">
                                        <h4 class="">Total : </h4>
                                    </div>
                                    <div class="col-sm-4 col-5 grand-total-amount">
                                        <h4 class="">$'.$total.'</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-12">
                            <h4 class=""><h6>Descuento: $'.$descuento.' <span class="badge badge-secondary">'.$cod_descuento.'</span></h6></h4>
                        </div>

                        <div class="col-sm-12 col-12 order-sm-1 order-0">
                            <div class="col-sm-12 col-12">
                                <h5>Formas de Pago</h5>
                            	<div class="">
            	                	<table class="table">
            	                		<thead>
            	                			<tr>
            	                				<th>Forma</th>
            	                				<th>Monto</th>
            	                				<th>Detalle</th>
            	                			</tr>
            	                		</thead>
            	                		<tbody>
            	                		'.$tablaPagos.'
            	                		</tbody>
            	                	</table>
            	                </div>	
                            </div>
                        </div>';
	                
	                if($observacion != ""){
                		$html.= '<p style="font-size: 18px;"><b style="color: #1b55e2;">Comentario General: </b>'.$observacion.'</p>';
                	}
                    $html.='    	
	                </div>
	                <div>
	                	<div class="col-md-12 col-sm-12 col-xs-12">
                			<h3 style="margin-top:15px;">Historial de Pedidos</h3>
                		</div>';

	                $cod_empresa = $session['cod_empresa'];
	                $query = "SELECT ca.estado, count(ca.estado) as numero
						FROM tb_orden_cabecera ca
						WHERE ca.cod_usuario = $cod_usuario
						AND ca.cod_empresa = $cod_empresa 
                        GROUP BY ca.estado
                        ORDER BY numero DESC";
                    $resp = Conexion::buscarVariosRegistro($query);
                    foreach ($resp as $items) {
                        $html .= '<p><b>'.$items['estado'].':</b>&nbsp;'.$items['numero'].'</p>';
                    }    

	        $html .='</div>
                </div>';


            /*SOLO PARA PICKUP - OBJETIVO: Cambiar el panel del mapa por la hora que vendra el usuario*/
            if($orden['is_envio'] == 0){
            	$html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            				<h3 style="border-bottom: 1px solid #dee2e6;">Hora de Retiro</h3>
                				Hora escogida por el cliente: '.$orden['hora_retiro'].'
            				
            				<div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:25px;">
                				<h3 style="margin-top:15px;">Entregado/Cancelado</h3>
                				<p><textarea class="form-control" id="txt_comentario_cambio_estado" placeholder="Escribe unos comentarios"></textarea></p>
                				<button class="btn btn-success btn-asignacion-pickup" data-value="'.$id.'" data-estado="PREPARANDO">Preparando</button>
                				<button class="btn btn-primary btn-asignacion-pickup" data-value="'.$id.'" data-estado="ENTREGADA">Entregada</button>
                				<button class="btn btn-danger btn-asignacion-pickup" data-usuario="'.$cod_usuario.'" data-value="'.$id.'" data-estado="CANCELADA">Cancelada</button>
                			</div>

                			<!-- <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:25px;">
            					<h3 style="margin-top:15px;border-bottom: 1px solid #dee2e6;">Enviar una notificacion</h3>
                				<textarea class="form-control textRecordatorio" placeholder="Escribe lo que le enviaras al cliente"></textarea>
                				<button class="btn btn-primary btnEnviarRecordatorio" style="margin-top:10px;" data-usuario="'.$cod_usuario.'">Enviar notificaci&oacute;n</button>
                			</div> -->
            			</div>	';

            	$html .= '
            			</div> <!-- FIN COLUMNA MAPA -->
		            </div>
		        </div>';

            	return $html;
            }

		$detalleRetail = $Clordenes->GetRetailDestino($id);
		$displayMapa = "";
		$displayFormulario = "display: none;";
		if($detalleRetail){
			$displayMapa = "display: none;";
			$displayFormulario = "";
		}

        $html .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                	<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px; margin-bottom: 15px; '.$displayMapa.'">
                            <div id="mapa" class="gllpMap" style="margin-left: 0; width: 100%; height: 250px;" data-latitud="'.$latitud.'" data-longitud="'.$longitud.'">Google Maps</div>
                        </div>
                        <hr/>
                        <p style="text-align:left; font-size:18px;">Direcci&oacute;n: '.$direccion.'</p>                          
                        <p style="text-align:left;font-size:14px;">Referencia: '.$referencia.'</p>
						<p style="text-align:left; font-size:14px; '.$displayFormulario.'">Bodega: <span>'.$detalleRetail['nom_sucursal'].'</span></p>
						<p style="text-align:left; font-size:14px; '.$displayFormulario.'">Ciudad origen: <span>'.$detalleRetail['nom_ciudad_origen'].'</span></p>
						<p style="text-align:left; font-size:14px; '.$displayFormulario.'">Ciudad destino: <span>'.$detalleRetail['nom_ciudad_destino'].'</span></p>
						<p style="text-align:left; font-size:14px; '.$displayFormulario.'">C&oacute;digo postal: <span>'.$detalleRetail['cod_postal'].'</span></p>
						<p style="text-align:left; font-size:14px; '.$displayFormulario.'">N&uacute;mero de casa: <span>'.$detalleRetail['num_casa'].'</span></p>
                	</div>
                	<div class="alert alert-info col-md-12 col-sm-12 col-xs-12 detailAction" style="text-align: center;display:none">
                	</div>
                	';


            if($estado == "ENTRANTE"){
                	$html .= '<div class="col-md-12 col-sm-12 col-xs-12 formaAsignacion" style="margin-top: 25px;">
                		<div class="col-md-6 col-sm-6 col-xs-12" style="display:none;">
                			<h3>Pedidos Cercanos</h3>';
                		if($latitud != "" && $longitud != ""){
	                		$distanciaMax = 10;
	                		$resp = $Clordenes->ordenesCercas($id,$latitud, $longitud, $distanciaMax);
	                		if($resp){
	                			foreach ($resp as $asignacion) {
	                				$motorizado = $asignacion['nombre'].' '.$asignacion['apellido'];;
	                				$direccion = $asignacion['referencia'];
	                				$fecha_salida = $asignacion['fecha_salida'];
	                				$distancia = number_format($asignacion['distance'],3);


	                				$html .= '<div class="col-md-12 col-sm-12 col-xs-12" style="padding: 10px;">
	                						<div class="card" style="padding: 10px;">
												<p>'.$motorizado.'</p>
	                							<p>'.$distancia.' Km</p>
	                							<p>'.$fecha_salida.'</p>
	                						</div>
	                					</div>';
		                    	}
	                		}else{
	                			$html .= '<p>No hay pedidos asignados a un radio de 10km</p>';
	                		}
	                    }	
                	$html .= '
                		</div>';
                		/*---COURIER---*/
						$op="";
						$couriers = $Clsucursales->getCouriers($orden['cod_sucursal']);
						if($couriers){
							foreach($couriers as $courier)
								$op .='<option value="'.$courier['cod_courier'].'">'.$courier['nombre'].'</option>';
						}

                		$style="block";
                		if (count($couriers) > 0){
                		    $style="none";
							if($cod_courier == 0){
                		        $text = "Asignar";
                		        $styleasig="block";
                		        $stylecan="none";
                		        $disabled = "";
                		    }else{
                		        $text = "Cancelar";
                		        $styleasig="none";
                		        $stylecan="block";
                		        $disabled="disabled";
                		    }
                		 $html .='
                		 <div class="col-md-12 col-sm-12 col-xs-12">
                		 <h3>Forma de Asignaci&oacute;n</h3>
                		    <select class="form-control cmbFormaCourier" id="cmbFormaCourier" name="cmbFormaCourier" '.$disabled.'>
                		        '.$op.'
                		        <option value="0">Mis Motorizados</option>
                		    </select>
                		 </div>';  
                		    $offset = "offset-md-6 offset-sm-6";
                		 $html .='
                			<div class=" col-md-12 col-sm-12 col-xs-12 infoCourier">
                			<h3>Asignaci&oacute;n</h3>
                			    <label id="lbl-g">'.$text.'</label><br/>
                				<button class="btn btn-success btn-asignacioncourier" id="asignacioncourier" data-value="'.$id.'" style="display:'.$styleasig.'">Asignar Orden</button>
                				<button class="btn btn-danger btn-cancelarcourier"  id="cancelarcourier" data-value="'.$id.'" style="display:'.$stylecan.'">Cancelar asignaci&oacute;n</button>
                			</div>
                			</br>
                			</div>';
						}
                		/*---COURIER---*/
                		
                	$html .='<div class="col-md-12 col-sm-12 col-xs-12 infoAsignacion" style="display:'.$style.'">
                		    <h3>Asignaci&oacute;n</h3>
                			<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Motorizado</label>
                				<select class="form-control cmbMotorizado">';
                					$respUser = $Clusuarios->lista_motorizadosByEmpresa($orden['cod_empresa']);
                					foreach ($respUser as $moto) {
                						$html .= '<option value="'.$moto['cod_usuario'].'">'.$moto['nombre'].' '.$moto['apellido'].'</option>';
                					}	
                	$html .=	'</select>
                			</div>
                			<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Hora Partida</label>
                				<input name="hora_ini" id="hora_ini" class="form-control flatpickr flatpickr-input active hora_partida" type="text" placeholder="Seleccione hora" value="'.fecha().'">
                			</div>
                			<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Asignar</label><br/>
                				<button class="btn btn-primary btn-asignacion" data-value="'.$id.'">Asignar Orden</button>
                			</div>';

                	$html .='
                		</div>
                		
						<div class="infoAnular">
							<div class="pedidoAntiguo" style="display: none;">
								<div class="alert alert-danger">
									<b><i data-feather="alert-triangle" style="width: 14px;"></i> Advertencia: <i data-feather="alert-triangle" style="width: 14px;"></i></b> <br> Este pedido es antiguo, asígnale el estado correcto según corresponda. <br>
									"Orden entregada" o "Cancelada".
								</div>
								<div>
									<button class="btn btn-success btn-estado" data-value="'.$id.'" data-estado="ENTREGADA">Orden entregada</button>
								</div>
							</div>

							<div class="col-md-12 col-sm-12 col-xs-12">
								<h3 style="margin-top:15px;">Cancelar Pedido</h3>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<label>Motivo</label><br/>
								<p><textarea class="form-control" id="txt_comentario_cambio_estado" placeholder="Escribe unos comentarios"></textarea></p>
								<button class="btn btn-danger btn-asignacion-pickup" data-usuario="'.$cod_usuario.'" data-value="'.$id.'" data-estado="CANCELADA">Cancelada</button>
							</div>
						</div>
                		
                	</div>';
            }else{
            	/*$query = "SELECT ma.*, u.nombre, u.apellido
							FROM tb_motorizado_asignacion ma, tb_usuarios u
							WHERE ma.cod_motorizado = u.cod_usuario
							AND ma.cod_orden = ".$orden['cod_orden'];*/
				$query = "SELECT * FROM `view_asignacion_motorizado` where cod_orden = ".$orden['cod_orden'];
		    	$resp = Conexion::buscarRegistro($query);
		    	
		    	/*HISTORIAL*/
		    	$queryH = "SELECT * FROM `tb_orden_historial` where estado IN ('ASIGNADA','ENVIANDO','ENTREGADA','NO_ENTREGADA') and cod_orden = ".$orden['cod_orden'];
		    	$respH = Conexion::buscarVariosRegistro($queryH);
		    	foreach ($respH as $h) {
		    	    switch ($h['estado']) {
                        case "ASIGNADA":
                            $fecha = $h['fecha'];
		    	            $fechaAr = explode(" ",$fecha);
                        break;
                        default:
                            $fecha = $h['fecha'];
                        break;
		    	        
		    	    }
		    	}
		    	/*-----*/
		    
		    	
		    	if($fecha != "" && $estado == "ENTREGADA"){
		    		$fechaLle = explode(" ",$fecha);
		    		$entrega = fechaLatinoShort($fechaLle[0]).' a las '.$fechaLle[1];
		    	}else{
		    		$entrega = "No se ha entregado el pedido";
		    	}
		    	
		    	if($estado != "ASIGNADA"){
		    		$html .= '<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Motorizado</label>
                				<p>'.$resp['nombre'].' '.$resp['apellido'].'</p>
                			</div>';
		    	}else{
		    	    $button = "";
		    	    if($resp['nombre'] != "")
		    	        $button = '<button class="btn btn-primary btnNotifOrdenLista" data-value="'.$orden['cod_orden'].'">Notificar Orden Lista</button> ';
		    	     
		    	     $button .= ' <a class="btn btn-secondary" href="orden_tracking.php?id='.$orden['cod_orden'].'" target="_blank">Tracking</a>';   
		    	        
		    		$html .= '<div class="col-md-12 col-sm-12 col-xs-12 infoAsignacion">
                				<label>Motorizado</label>
                				<p>'.$resp['nombre'].' '.$resp['apellido'].' </p>
                				<p>'.$button.'</p>
                			</div>';
		    	}	
		    	
                $html .= '<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Fecha Asignada</label>';
                				if($fechaAr != "")
                				    $html .='<p>'.fechaLatinoShort($fechaAr[0]).' a las '.$fechaAr[1].'</p>';
                				else
                				    $html .='<p>Sin Fecha Asignada</p>';
            	$html .='</div>';
                $html .= '<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Fecha Entrega</label>
                				<p>'.$entrega.'</p>
                			</div>';	

                if($estado == "ASIGNADA"){
                	$html .= '
                	<div class="col-md-12 col-sm-12 col-xs-12 infoAsignacion" >
                	<button class="btn btn-warning btn-estado" data-value="'.$id.'" data-estado="ENVIANDO">Orden en camino</button>
                	<button class="btn btn-success btn-estado" data-value="'.$id.'" data-estado="ENTREGADA">Orden entregada</button>';
                		$html .='<div class="col-md-12 col-sm-12 col-xs-12">
                			<h3 style="margin-top:15px;">Cancelar Pedido</h3>
                			</div>
                			<div class="col-md-12 col-sm-12 col-xs-12">
                				<label>Motivo</label><br/>
                				<p><textarea class="form-control" id="txt_comentario_cambio_estado" placeholder="Escribe unos comentarios"></textarea></p>
                				<button class="btn btn-danger btn-asignacion-pickup" data-usuario="'.$cod_usuario.'" data-value="'.$id.'" data-estado="CANCELADA">Cancelada</button>
                			</div>
                			
                	</div>		';
                }
                if($estado == "ENVIANDO"){
                	$html .= '
                	<button class="btn btn-success btn-estado" data-value="'.$id.'" data-estado="ENTREGADA">Orden entregada</button>';
                }								
            }
                	
    $html .= '   </div> <!-- FIN COLUMNA MAPA -->
            </div>
        </div>';
        return $html;
}

?>