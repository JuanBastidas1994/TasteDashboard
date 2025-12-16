<?php
require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_frontscript.php";
error_reporting(E_ALL);

$Clempresas = new cl_empresas(NULL);
$ClScript = new cl_frontscript();

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

$nameTemplate = 'front-react.zip';
if(isset($_GET['template'])){
    $nameTemplate = $_GET['template'];
}

$empresa = $Clempresas->get($_GET['id']);
if(!$empresa){
    $return['success'] = 0;
    $return['mensaje'] = "Empresa no existe";
    showResponse($return);
}
$alias = $empresa['alias'];

$path = $_GET['url'];
if(!file_exists($path)){
    $return['success'] = 0;
    $return['mensaje'] = "Directorio no existe";
    showResponse($return);
}

if($empresa['color'] == ''){
    $return['success'] = 0;
    $return['mensaje'] = "El color no puede estar vacío, primero cambie el color de la empresa";
    showResponse($return);
}

$detalle = "";
//URL DONDE SE REPLICARÁ
$logoPath = url_upload.'assets/empresas/'.$empresa['alias'].'/';


$dirFiles = "./files/";
$dirExport = "./export/";

/*COPIAR TODO EL TEMPLATE*/
$detalle .= '<h3>Proceso de replicación</h3>';
$zip = new ZipArchive;
if ($zip->open('./template/'.$nameTemplate) === TRUE) {
    $zip->extractTo($path); // Extraemos el contenido en el directorio actual
    $zip->close();
    $detalle .= '1. Página descomprimida correctamente<br/>';
} else {
    $detalle .= '1. Falló descomprimir la página en la URL indicada <br/> Culminó el proceso por error.';
    $return['success'] = 0;
    $return['mensaje'] = "Falló descomprimir la página en la URL indicada";
    showResponse($return);
}

//CONFIG JS
$start_home = ($empresa['iniciar_en_menu'] == 0) ? 1 : 0;

$img_bienvenida = file_exists($logoPath.'bienvenida_modal.png') ? 1 : 0;
// $permit_insitu = $Clempresas->tienePermiso($empresa['cod_empresa'], 'OFFICE_INSITE') ? 1 : 0;
// $has_loyalty = ($empresa['fidelizacion'] == 1) ? 1 : 0;

$logo_rectangle = 0;
if ($image_data = @getimagesize($logoPath."logo.png")) {
    list($width, $height) = $image_data;
    $logo_rectangle = ($height <= 150) ? 1 : 0;
}

$fichero = 'config.js';
$contenido = 'window.APP_CONFIG = {
    VITE_APP_TITLE:"'.$empresa['nombre'].'",
    VITE_API_KEY: "'.$empresa['api_key'].'",
    VITE_COLOR_PRIMARY: "'.$empresa['color'].'",
    VITE_PIXEL_FACEBOOK: "'.$empresa['facebook_pixel'].'",
    VITE_PRODUCT_ITEM_VERSION: "'.$empresa['front_product_card'].'",
    VITE_PAGE_INITIAL_HOME: '.$start_home.',
    VITE_WELCOME_IMAGE: '.$img_bienvenida.',
    VITE_LOGO_RECTANGLE: '.$logo_rectangle.'
};';
file_put_contents($dirExport.$fichero, $contenido);

$detalle .= '2. Archivos de configuración creados correctamente <br/>';


//INDEX HTML
$metaTags = [
    'keywords' => $empresa['keywords'],
    'description' => $empresa['description'],
];
$ogTags = [
    'og:title' => $empresa['nombre'],
    'og:description' => $empresa['description'],
    'og:image' => "https://digitalmindtec.com/assets/empresas/$alias/compartir.jpg",
];

// Llamar a la función para actualizar el HTML
actualizarMetaTags($path.'/index.html', $empresa['nombre'], $metaTags, $ogTags);

$scripts = $ClScript->lista();
if($scripts)
    actualizarScriptIndex($path.'/index.html', $scripts);

/*COPIAR ARCHIVOS CREADOS A SUS URLS CORRESPONDIENTES*/
$fichero = "config.js";
$detalle .= (copy($dirExport.$fichero, $path.'/'.$fichero)) ? "3. Copio correctamente $fichero... <br/>" : "Error al copiar $fichero... <br/>";


$fichero = "logo.png";  //LOGO PRINCIPAL
$detalle .= (copy($logoPath.$fichero, $path.'/'.$fichero)) ? "4. Copio correctamente Logo Principal... <br/>" : "Error al copiar Logo Principal... <br/>";
$fichero = "logo-footer.png";  //LOGO FOOTER
$detalle .= (copy($logoPath.$fichero, $path.'/logofooter.png')) ? "5. Copio correctamente Logo Footer... <br/>" : "Error al copiar Logo Footer... <br/>";
$fichero = "favicon.png";  //FAVICON
$detalle .= (copy($logoPath.$fichero, $path.'/favicon.ico')) ? "6. Copio correctamente Favicon... <br/>" : "Error al copiar Favicon... <br/>";

//PWA
$fichero = "icon-192x192.png";  //LOGO PWA 192
$detalle .= (copy($logoPath.$fichero, $path.'/android-chrome-192x192.png')) ? "7. Copio correctamente Logo PWA 192... <br/>" : "Error al copiar Logo PWA 192... <br/>";
$fichero = "icon-512x512.png";  //LOGO PWA 512 
$detalle .= (copy($logoPath.$fichero, $path.'/android-chrome-512x512.png')) ? "8. Copio correctamente Logo PWA 512... <br/>" : "Error al copiar Logo PWA 512... <br/>";
$detalle .= (copy($logoPath.$fichero, $path.'/any_icon.png')) ? "9. Copio correctamente Logo PWA nommal... <br/>" : "Error al copiar Logo PWA normal... <br/>";
$detalle .= (copy($logoPath.$fichero, $path.'/apple-touch-icon.png')) ? "10. Copio correctamente Apple touch icon... <br/>" : "Error al copiar Logo Apple touch icon... <br/>";
$detalle .= (copy($logoPath.$fichero, $path.'/maskable_icon.png')) ? "11. Copio correctamente Maskable Icon... <br/>" : "Error al copiar Maskable Icon... <br/>";

if($img_bienvenida == 1){
    $fichero = "bienvenida_modal.png";  //BIENVENIDA MODAL
    $detalle .= (copy($logoPath.$fichero, $path.'/bienvenida_modal.png')) ? "12. Copio correctamente Bienvenida Modal... <br/>" : "Error al copiar Bienvenida Modal... <br/>";
}

$zipFileDownload = "";
$download = isset($_GET['download']) ? true : false;
$compress = isset($_GET['compress']) ? true : false;

if($download || $compress){
    $zipName = 'page-'.$alias.'.zip';
    $zipFile = './zip/'.$zipName;
    if (zipFolderContents($path, $zipFile)) {
        $zipFileDownload = url_pages_installers.$zipName;
        if($download){
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            exit;
        }
        
    } else {
        showResponse([ 'success' => 0, 'mensaje' => 'No se pudo comprimir la carpeta' ]);
    }
}


$return['success'] = 1;
$return['mensaje'] = "Culminó proceso de replicación";
$return['zipfile'] = $zipFileDownload;
$return['detalle'] = $detalle;
showResponse($return);

function showResponse($return){
    http_response_code(200);
    header("Content-type:application/json; charset=utf-8");
	echo json_encode($return);
	exit();
}

function actualizarMetaTags($filePath, $nuevoTitulo, $metaTags = [], $ogTags = []) {
    // Leer el contenido del archivo HTML
    $fileContent = file_get_contents($filePath);
    
    // Reemplazar el <title>
    $fileContent = preg_replace(
        '/<title>(.*?)<\/title>/i',
        "<title>$nuevoTitulo</title>",
        $fileContent
    );

    // Reemplazar meta tags con `name`
    foreach ($metaTags as $name => $content) {
        $fileContent = preg_replace(
            '/<meta name="' . preg_quote($name, '/') . '" content="(.*?)"\s*\/?>/i',
            '<meta name="' . $name . '" content="' . $content . '"/>',
            $fileContent
        );
    }

    // Reemplazar meta tags con `property` para Open Graph (og:tags)
    foreach ($ogTags as $property => $content) {
        $fileContent = preg_replace(
            '/<meta property="' . preg_quote($property, '/') . '" content="(.*?)"\s*\/?>/i',
            '<meta property="' . $property . '" content="' . $content . '"/>',
            $fileContent
        );
    }

    // Guardar los cambios en el archivo HTML
    file_put_contents($filePath, $fileContent);
}

function actualizarScriptIndex($filePath, $scripts = []) {
    $htmlHead = "";
    $htmlBody = "";
    
    foreach($scripts as $script){
        $codigo = html_entity_decode($script['codigo'], ENT_QUOTES, 'UTF-8');
        if($script['ubicacion'] == 'head'){
            $htmlHead .= $codigo;
        }else{
            $htmlBody .= $codigo;
        }
    }
    
    // Leer el contenido del archivo HTML
    $fileContent = file_get_contents($filePath);
    if ($fileContent === false) return;
    
    $fileContent = str_replace("<!-- head script custom -->", $htmlHead, $fileContent);
    $fileContent = str_replace("<!-- body script custom -->", $htmlBody, $fileContent);


    // Guardar los cambios en el archivo HTML
    file_put_contents($filePath, $fileContent);
}

function zipFolderContents($folderPath, $zipFilePath) {
    $zip = new ZipArchive();
    
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $relativePath = substr($file, strlen($folderPath) + 1);
            $zip->addFile($file, $relativePath);
        }

        $zip->close();
        return true;
    } else {
        return false;
    }
}
?>