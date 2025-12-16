<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_menu_digital.php";
$ClmenuDigital = new cl_menu_digital();
$session = getSession();

controller_create();

function getImagenes(){
    global $ClmenuDigital;
    extract($_GET);

    $imagenes = $ClmenuDigital->getImagenes($cod_menu_digital);
    if($imagenes){
        $html = "";
        $files = url_sistema."assets/empresas/".$alias."/";
        foreach ($imagenes as $imagen) {
            $html.='    <tr data-id="'.$imagen['cod_menu_digital_imagenes'].'">
                            <td class="text-center">
                                <img src="'.$files.$imagen['imagen'].'" height="200">
                            </td>
                            <td class="text-center">
                                <a class="btnEliminarImagen" href="javascript:void(0);" data-value="'.$imagen['cod_menu_digital_imagenes'].'" data-imagen="'.$imagen['imagen'].'">
                                    <i data-feather="trash"></i>
                                </a>
                            </td>
                        </tr>';
        }
        $return['success'] = 1;
        $return['mensaje'] = "Datos obtenidos";
        $return['html'] = $html;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay datos";
    }
    return $return;
}

function actualizarPosicionImagenes(){
    global $ClmenuDigital;
    extract($_POST);

    for ($i=0; $i < count($imagenes); $i++) { 
        $ClmenuDigital->actPosicion($imagenes[$i], $i+1);
    }
    
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

function subirImagen(){
    global $ClmenuDigital;
    extract($_POST);

    if (($_FILES["imagen"]["type"] == "image/pjpeg")
    || ($_FILES["imagen"]["type"] == "image/jpeg")
    || ($_FILES["imagen"]["type"] == "image/png")){  
        
        $formatosNoProhibidos = array('image/jpeg', 'image/png'); 
        $name = $_FILES["imagen"]["name"];
        $formato = getFormatoFile($name);
        $nombreImg = "menu-digital-".fechaSignos().$formato;
        /* SUBIR IMAGEN */
        if(uploadFileWithAlias($alias, $_FILES["imagen"], $nombreImg)){
            /* VALIDAR QUE SEA UN FORMATO VÁLIDO */
            $imgSubida = url_upload."/assets/empresas/".$alias."/".$nombreImg;
            $getMime = mime_content_type($imgSubida);
            if(!in_array($getMime, $formatosNoProhibidos)){
                if(unlink($imgSubida)){
                    $return['success'] = 0;
                    $return['mensaje'] = "El archivo subido no es una imagen PNG o JPG, eliminado";
                }
                else{
                    $return['success'] = 0;
                    $return['mensaje'] = "El archivo subido no es una imagen PNG o JPG, eliminar manualmente";
                }
            }
            else{
                $ClmenuDigital->insertImagen($cod_menu_digital, $nombreImg);
                $return['success'] = 1;
                $return['mensaje'] = "Imagen subida con éxito";
                $return['url'] = url_sistema."assets/empresas/".$alias."/".$nombreImg;
            }
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "El formato de la imagen debe ser PNG o JPG";
        }
    } 
    else{
        $return['success'] = 0;
        $return['mensaje'] = "El formato de la imagen debe ser PNG o JPG";
    }
    return $return;
}

function eliminarImagen(){
    global $ClmenuDigital;
    extract($_GET);
    $files = url_upload."/assets/empresas/".$alias."/".$nomImagen;

    if(unlink($files)){
        if($ClmenuDigital->eliminarImagen($cod_menu_digital_imagen)){
            $return['success'] = 1;
            $return['mensaje'] = "Imagen eliminada correctamente";
        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al eliminar la imagen, bd";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el fichero";
    }
    return $return;
}

?>