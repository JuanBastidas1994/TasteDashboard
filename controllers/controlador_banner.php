<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_banners.php";
$ClBanner = new cl_banners();
$session = getSession();

controller_create();

function crear(){
    global $ClBanner;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $nameImg = 'banner_'.datetime_format().'.jpg';
    $ClBanner->titulo = $txt_titulo;
    $ClBanner->subtitulo = $txt_subtitulo;
    $ClBanner->descuento = $txt_descuento;
    $ClBanner->image_min = $nameImg;
    $ClBanner->text_boton = $txt_text_boton;
    $ClBanner->url_boton = $txt_url;
    $ClBanner->estado = $estado;

    if(!isset($_POST['cod_banner'])){
        $id=0;
        if($ClBanner->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Banner creado correctamente";
            $return['id'] = $id;
            $return['banner'] = $ClBanner->get($id);

            /*SUBIR IMAGEN*/
            if(!uploadFile($_FILES["img_profile"], $nameImg)){
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                @copy($img1, $img2);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el banner, por favor vuelva a intentarlo";
        }
    }else{
        $ClBanner->cod_banner = $cod_banner;
        if($ClBanner->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Banner editado correctamente";
            $return['id'] = $ClBanner->cod_banner;
            $data = $ClBanner->get($ClBanner->cod_banner);
            if($data){
                uploadFile($_FILES["img_profile"], $data['image_min']);
                $return['imagen'] = "editada";
                $return['banner'] = $data;
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el banner";
        }
    }
    return $return;
}

function get(){
    global $session;
    global $ClBanner;
    if(!isset($_GET['cod_banner'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($ClBanner->getArray($cod_banner, $array)){
        $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
        $array['image_min'] = $files.$array['image_min'];

        $return['success'] = 1;
        $return['mensaje'] = "Banner encontrado";
        $return['data'] = $array;


    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Banner no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $ClBanner;
	if(!isset($_GET['cod_banner']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $ClBanner->set_estado($cod_banner, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Banner editado correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el banner";
    }
    return $return;
}


function actualizar(){
   global $ClBanner;

    if(!isset($_POST['banners'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

   
    for ($i=0; $i < count($banners); $i++) { 
        $ClBanner->moverBanners($banners[$i], $i+1);
    }
  
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

?>