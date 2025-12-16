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

$empresa = $Clempresas->get($_GET['id']);
if(!$empresa){
    $return['success'] = 0;
    $return['mensaje'] = "Empresa no existe";
    showResponse($return);
}

$detalle = "";

//URL DONDE SE REPLICARÁ
$alias = $empresa['alias'];
$businessPath = '/home1/digitalmind/dashboard.mie-commerce.com/assets/empresas/'.$alias.'/';
$resourcePath = '/home1/digitalmind/emprendedores.tastelatam.com/resources/'.$alias.'/';

$dirFiles = "./files/";
$dirExport = "./export/";

//FOLDER
if(!file_exists($resourcePath)){
    $dir = mkdir($resourcePath, 0755, true);
}

//COLORS
$fichero = 'colors.css';
$contenido = file_get_contents($dirFiles.$fichero);
$contenido = str_replace("#EB6341", $empresa['color'], $contenido);
file_put_contents($resourcePath.$fichero, $contenido);

//CONFIG JS
$fichero = 'config.js';
$tipoEmpresa = ($empresa['cod_tipo_empresa']==1) ? "RESTAURANTE" : "RETAIL";
$contenido = 'let config = {
    "alias": "'.$empresa['alias'].'",
    "name_site": "'.$empresa['nombre'].'",
    "tipoentrega": "envio", //pickup o envio,
    "tipoempresa": "'.$tipoEmpresa.'", //RESTAURANTE O RETAIL
    "apikey": "'.$empresa['api_key'].'",
    "apiURL": "https://api.mie-commerce.com/v4",
    "messageNoInternet": "Al parecer debes conectarte a una red wifi o datos móviles"
}';
file_put_contents($resourcePath.$fichero, $contenido);




$detalle .= '<h3>Proceso de replicación</h3>';
$detalle .= '1. Archivos de configuración creados correctamente <br/>';

/*COPIAR ARCHIVOS CREADOS A SUS URLS CORRESPONDIENTES*/
/*
$fichero = "colors.css";
$detalle .= (copy($dirExport.$fichero, $path.'/css/'.$fichero)) ? "3. Copio correctamente $fichero... <br/>" : "Error al copiar $fichero... <br/>";
$fichero = "config.js";
$detalle .= (copy($dirExport.$fichero, $path.'/'.$fichero)) ? "6. Copio correctamente $fichero... <br/>" : "Error al copiar $fichero... <br/>";*/
$fichero = "logo.png";  //LOGO PRINCIPAL
$detalle .= (copy($businessPath.$fichero, $resourcePath.$fichero)) ? "9. Copio correctamente Logo Principal... <br/>" : "Error al copiar Logo Principal... <br/>";
$fichero = "logo-xs.png";  //LOGO SMALL
$detalle .= (copy($businessPath.$fichero, $resourcePath.$fichero)) ? "10. Copio correctamente Logo XS... <br/>" : "Error al copiar Logo XS... <br/>";
$fichero = "logo-footer.png";  //LOGO FOOTER
$detalle .= (copy($businessPath.$fichero, $resourcePath.$fichero)) ? "11. Copio correctamente Logo Footer... <br/>" : "Error al copiar Logo Footer... <br/>";
$fichero = "favicon.png";  //FAVICON
$detalle .= (copy($businessPath.$fichero, $resourcePath.$fichero)) ? "12. Copio correctamente Favicon... <br/>" : "Error al copiar Favicon... <br/>";
$fichero = "compartir.jpg";  //LOGO JPG
$detalle .= (copy($businessPath.$fichero, $resourcePath."logo.jpg")) ? "12. Copio correctamente Logo con Fondo... <br/>" : "Error al copiar Logo con Fondo... <br/>";

//PWA
/*
$fichero = 'manifest.webmanifest'; //WEBMANIFEST
$contenido = '{
  "name": "'.$empresa['nombre'].'",
  "short_name": "'.$empresa['nombre'].'",
  "start_url": ".",
  "display": "standalone",
  "background_color": "'.$empresa['color'].'",
  "description": "Restaurante",
  "theme_color": "'.$empresa['color'].'",
  "icons": [
    {
      "src": "images/icons//icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    },
    {
      "src": "images/icons//icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ]
}';
file_put_contents($dirExport.$fichero, $contenido);
$fichero = "manifest.webmanifest";
$detalle .= (copy($dirExport.$fichero, $path.'/'.$fichero)) ? "7. Copio correctamente $fichero... <br/>" : "Error al copiar $fichero... <br/>";
$fichero = "icon-512x512.png";  //LOGO PWA 512
$detalle .= (copy($logoPath.$fichero, $path.'/images/icons/'.$fichero)) ? "13. Copio correctamente Logo PWA 512... <br/>" : "Error al copiar Logo PWA 512... <br/>";
$fichero = "icon-192x192.png";  //LOGO PWA 512
$detalle .= (copy($logoPath.$fichero, $path.'/images/icons/'.$fichero)) ? "14. Copio correctamente Logo PWA 192... <br/>" : "Error al copiar Logo PWA 192... <br/>";
*/

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