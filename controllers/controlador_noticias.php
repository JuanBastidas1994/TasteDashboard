<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_noticias.php";
$Clnoticias = new cl_noticias();
$session = getSession();

controller_create();

function crear(){
    global $Clnoticias;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $aux = "";
    do{
        $alias = create_slug(sinTildes($txt_nombre.$aux));
        $aux = intval(rand(1,100)); 
    }while(!$Clnoticias->aliasDisponible($alias));
   

    $desc_larga = editor_encode($desc_larga);
    $nameImg = 'noticia_'.datetime_format().'.jpg';
    $nameImgMin = 'min_'.$nameImg;

    $Clnoticias->nombre = $txt_nombre;
    $Clnoticias->categorias = $cmb_categoria;
    $Clnoticias->alias = $alias;
    $Clnoticias->desc_corta = $txt_descripcion_corta;
    $Clnoticias->desc_larga = $desc_larga;
    $Clnoticias->image_min = $nameImgMin;
    $Clnoticias->image_max = $nameImg;
     if(isset($_POST['chk_estado']))
        $Clnoticias->estado = 'A';
    else
        $Clnoticias->estado = 'I';

   
    if($_POST['id_noti']==0){
        $id=0;
        if($Clnoticias->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Noticia creada correctamente";
            $return['id'] = $id;

            /*SUBIR IMAGEN*/
            if($txt_crop != "" && $txt_crop_min != ""){
                base64ToImage($txt_crop, $nameImg);
                base64ToImage($txt_crop_min, $nameImgMin);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                $img3 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImgMin;
                copy($img1, $img2);
                copy($img1, $img3);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear la noticia, por favor vuelva a intentarlo";
        }
    }else{
        $Clnoticias->cod_noticia = $_POST['id_noti'];
        $cod_noticia = $_POST['id_noti'];
        if($Clnoticias->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Noticia editada correctamente";
            $return['id'] = $Clnoticias->cod_noticia;
            $idP = $Clnoticias->cod_noticia;

            $data = NULL;
            if($Clnoticias->getArray($cod_noticia, $data)){
                //uploadFile($_FILES["img_product"], $data['image_min']);
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['imagen_max'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clnoticias->setImage($nameImgMax, 'max', $idP);
                        if($cambio){
                            deleteFile($data['imagen_max']);
                        }
                    }
                }
                if($txt_crop_min != ""){
                    $nameImgMin = ($data['image_min']!=$data['imagen_max']) ? $data['image_min'] : 'min_'.$data['image_min'];
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMin = getNameImagejpg($nameImgMin, $cambio);
                    if(base64ToImage($txt_crop_min, $nameImgMin)){
                        $Clnoticias->setImage($nameImgMin, 'min', $idP);
                        if($cambio){
                            deleteFile($data['image_min']);
                        }
                    }
                }
                $return['imagen'] = "editada";
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la noticia";
        }
    }
    return $return;
}

function get(){
    global $Clproductos;
    if(!isset($_GET['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($Clproductos->getArray($cod_producto, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Producto encontrado";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Producto no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
  global $Clnoticias;
  if(!isset($_GET['cod_noticia']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

  extract($_GET);

    $resp = $Clnoticias->set_estado($cod_noticia, $estado);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Noticia editada correctamente";
      if($estado == "D")
        $return['mensaje'] = "Noticia eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al editar la noticia";
    }
    return $return;
}


function upload_img(){
    global $Clnoticias;
    global $session;
    if(!isset($_POST['cod_noticia'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    $nameImg = 'noticia_'.$cod_noticia.'-'.datetime_format().'.jpg';
    if(uploadFile($_FILES["img_galery"], $nameImg)){
        /*CODIGO PARA GUARDAR*/
        $id=0;
        if($Clnoticias->add_img_noticia($cod_noticia, $nameImg, $id)){
            $return['success'] = 1;
            $return['mensaje'] = "Imagen Subida con éxito";

            $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
            $img = $files.$nameImg;
            $html =  '<div class="col-md-4 col-sm-4 col-xs-12">
                    <img src="'.$img.'" style="width: 100%;height: 120px;object-fit: cover;"/>
                    <span data-value="'.$id.'" class="deleteImg custom-file-container__image-multi-preview__single-image-clear">
                        <span class="custom-file-container__image-multi-preview__single-image-clear__icon" data-upload-token="fbjn5kugte6vr2cegadi4t">×</span>
                    </span>
                  </div>';
            $return['html'] = $html;      

        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al agregar la imagen a la noticia, por favor intentelo nuevamente";
        }
        
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al subir la imagen";
    }
    return $return;
}

function delete_img(){
    global $Clnoticias;
    if(!isset($_GET['cod_imagen'])){
            $return['success'] = 0;
            $return['mensaje'] = "Falta informacion";
            return $return;
    }
    extract($_GET);

    $resp = $Clnoticias->delete_imagen($cod_imagen);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Imagen eliminada correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al eliminar la imagen";
    }
    return $return;
}


?>