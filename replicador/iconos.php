<?php
require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
error_reporting(E_ALL);

$Clempresas = new cl_empresas(NULL);

if(!isset($_GET['id'])){
    $return['success'] = 0;
    $return['mensaje'] = "Falta identificador de la empresa";
    showResponse($return);
}

if(!isset($_GET['url'])){
    $return['success'] = 0;
    $return['mensaje'] = "Falta directorio donde se replicará la página";
    showResponse($return);
}

$empresa = $Clempresas->get($_GET['id']);
if(!$empresa){
    $return['success'] = 0;
    $return['mensaje'] = "Empresa no existe";
    showResponse($return);
}

$path = $_GET['url'];
if(!file_exists($path)){
    $return['success'] = 0;
    $return['mensaje'] = "Directorio no existe";
    showResponse($return);
}

$detalle = "";
//URL DONDE SE REPLICARÁ
$logoPath = '/home1/digitalmind/dashboard.mie-commerce.com/assets/empresas/'.$empresa['alias'].'/';

$detalle .= '<h3>Proceso de replicación de Gráficos</h3>';

/*COPIAR ARCHIVOS CREADOS A SUS URLS CORRESPONDIENTES*/
$fichero = "logo.png";  //LOGO PRINCIPAL
$detalle .= (copy($logoPath.$fichero, $path.'/images/'.$fichero)) ? "1. Copio correctamente Logo Principal... <br/>" : "Error al copiar Logo Principal... <br/>";
$fichero = "logo-xs.png";  //LOGO SMALL
$detalle .= (copy($logoPath.$fichero, $path.'/images/'.$fichero)) ? "2. Copio correctamente Logo XS... <br/>" : "Error al copiar Logo XS... <br/>";
$fichero = "logo-footer.png";  //LOGO FOOTER
$detalle .= (copy($logoPath.$fichero, $path.'/images/'.$fichero)) ? "3. Copio correctamente Logo Footer... <br/>" : "Error al copiar Logo Footer... <br/>";
$fichero = "favicon.png";  //FAVICON
$detalle .= (copy($logoPath.$fichero, $path.'/images/'.$fichero)) ? "4. Copio correctamente Favicon... <br/>" : "Error al copiar Favicon... <br/>";
$fichero = "icon-512x512.png";  //LOGO PWA 512
$detalle .= (copy($logoPath.$fichero, $path.'/images/icons/'.$fichero)) ? "5. Copio correctamente Logo PWA 512... <br/>" : "Error al copiar Logo PWA 512... <br/>";
$fichero = "icon-192x192.png";  //LOGO PWA 512
$detalle .= (copy($logoPath.$fichero, $path.'/images/icons/'.$fichero)) ? "6. Copio correctamente Logo PWA 192... <br/>" : "Error al copiar Logo PWA 192... <br/>";
$fichero = "profile.png";  //LOGO PROFILE
$detalle .= (copy($logoPath.$fichero, $path.'/images/'.$fichero)) ? "7. Copio correctamente Profile PNG... <br/>" : "Error al copiar Profile PNG... <br/>";
$fichero = "menu.pdf";  //MENU PDF
$detalle .= (copy($logoPath.$fichero, $path.'/'.$fichero)) ? "8. Copio correctamente Menú PDF... <br/>" : "Error al copiar Menú PDF... <br/>";


$return['success'] = 1;
$return['mensaje'] = "Culminó proceso de replicación";
$return['detalle'] = $detalle;
showResponse($return);

function showResponse($return){
    http_response_code(200);
    header("Content-type:application/json; charset=utf-8");
	echo json_encode($return);
	exit();
}
?>