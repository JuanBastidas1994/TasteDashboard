<?php
if(!isset($_GET['url'])){
    $return['success'] = 0;
    $return['mensaje'] = "Falta directorio donde se limpiará la página";
    showResponse($return);
}

$dir = $_GET['url'];

if(trim($dir) == ""){
    $return['success'] = 0;
    $return['mensaje'] = "El directorio no puede ser vacío";
    showResponse($return);
}

$dir = '/home1/digitalmind/'.$dir;
if(!file_exists($dir)){
    $return['success'] = 0;
    $return['mensaje'] = "Directorio no existe";
    showResponse($return);
}

echo "LIMPIAR PROYECTO EN $dir<br/>";

$parameterSearch = array(
    //'htaccess',
    'content.php',
    'new-index.php',
    'radio.php',
    '2index.php',
    'settings-title.php',
    //'index.php',
);

$mimeImportant = array(
    'text/x-php',
);

$foldersSearch = array(
    'wingstogo.ec'
);

setHtaccessOriginal($dir);
searchFilesMalignos($dir, 1);


//BUSCAR FICHEROS MAL INTENCIONADOS
function searchFilesMalignos($dir, $nivel){
    global $foldersSearch;
    $ficheros1  = scandir($dir);
    foreach($ficheros1 as $folder){
        if($folder == ".." || $folder == ".")
            continue;
        /*
        if(!in_array($folder, $foldersSearch)){
            continue;
        }*/
        
        $newDir = $dir."/".$folder;
        if(is_dir($newDir)){
            searchFilesMalignos($newDir, $nivel+1);
        }else{
            if($nivel == 1)
                continue;
            //VALIDAR SI ES PRIMER NIVEL NO BORRAR EL INDEX NI EL HTACCESS - LO DEMAS SI!!!
            gestionarFicheros($newDir);
        }
    }
}


function gestionarFicheros($dir){
    global $mimeImportant;
    $htmlPalabraClave = "";
    if(buscarPalabraClave($dir, $htmlPalabraClave)){
        echo $dir.'<b> <span style="color:red;">'.$htmlPalabraClave.'</span></b><br/>';
    }else{
        $mime = mime_content_type($dir);
        if(!in_array($mime, $mimeImportant)){
            return;
        }
        echo $dir.' <b><span style="color:orange;">'.$mime.'</span></b><br/>';
    }
}

function buscarPalabraClave($dir, &$resp){
    global $parameterSearch;
    foreach($parameterSearch as $palabra){
        if(strpos($dir, $palabra)){
            $resp = $palabra;
            unlink($dir);
            return true;
        }
    }
    return false;
}


//REGENERAR HTACCESS ORIGINAL
function setHtaccessOriginal($dir){
    $content_htaccess = '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    </IfModule>';
    
    chmod($dir."/.htaccess", 0644);
    $fichero = file_put_contents($dir."/.htaccess", $content_htaccess);
    echo '<br/><b><span style="color:green;">HtAccess Regenerado</span></b><br/>';
}

function showResponse($return){
    http_response_code(200);
    header("Content-type:application/json; charset=utf-8");
	echo json_encode($return);
	exit();
}
?>