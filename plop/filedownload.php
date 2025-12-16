<?php
require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
error_reporting(E_ALL);

$Clempresas = new cl_empresas(NULL);
if(!isset($_GET['id']))
    showResponse([ 'success' => 0, 'mensaje' => 'Falta identificador de la empresa' ]);


$empresa = $Clempresas->get($_GET['id']);
if(!$empresa)
    showResponse([ 'success' => 0, 'mensaje' => 'No existe la empresa' ]);

$alias = $empresa['alias'];
$assetsPath = url_sistema."assets/empresas/$alias/";
$assets = [
    'logo' => $assetsPath."logo.png",
    'logofooter' => $assetsPath."logo-footer.png",
    'favicon' => $assetsPath."favicon.png",
    'compartir' => $assetsPath."compartir.jpg",
    'bienvenida_modal' => $assetsPath."bienvenida_modal.png",
];

$welcomeImg = url_upload."assets/empresas/$alias/bienvenida_modal.png";
$img_bienvenida = file_exists($welcomeImg) ? 1 : 0;
$logo_rectangle = 0;
if ($image_data = @getimagesize($assets['logo'])) {
    list($width, $height) = $image_data;
    $logo_rectangle = ($height <= 150) ? 1 : 0;
}


$info = [
    'nombre' => $empresa['nombre'],
    'color' => $empresa['color'],
    'api_key' => $empresa['api_key'],
    'keywords' => $empresa['keywords'],
    'description' => $empresa['description'],
    'facebook_pixel' => $empresa['facebook_pixel'],
    'front_product_card' => $empresa['front_product_card'],
    'logo_rectangle' => $logo_rectangle,
    'welcome_image' => $img_bienvenida,
    'start_home' => ($empresa['iniciar_en_menu'] == 0) ? 1 : 0
];

$return['success'] = 1;
$return['mensaje'] = "Información de assets y empresa";
$return['info'] = $info;
$return['assets'] = $assets;
showResponse($return);

function showResponse($return){
    http_response_code(200);
    header("Content-type:application/json; charset=utf-8");
	echo json_encode($return);
	exit();
}

?>