<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_modal_eventos.php";
require_once "../clases/cl_productos.php";

$Clmodaleventos = new cl_modal_eventos();
$session = getSession();

controller_create();

function crear(){
    global $Clmodaleventos;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $nameImg = 'modal_'.datetime_format().'.jpg';
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';

    $Clmodaleventos->titulo = $txt_titulo;
    $Clmodaleventos->accion_id = $cmb_accion;
    $Clmodaleventos->accion_desc = $txt_desc_accion;
    $Clmodaleventos->descripcion = $txt_desc;

    $Clmodaleventos->fecha_ini = $txt_fecha_ini;
    $Clmodaleventos->fecha_fin = $txt_fecha_fin;
    $Clmodaleventos->imagen= $nameImg;
    
    if(isset($_POST['ckEstado']))
        $Clmodaleventos->estado = 'A';
    else
        $Clmodaleventos->estado = 'I';

    if(0 == $_POST['cod_modal_evento']){
        $id=0;
        if($Clmodaleventos->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Guardado correctamente";
            $return['id'] = $id;
            $return['data'] = "";
            $cod_modal_evento = $id;
            $Clmodaleventos->getArray($id, $return['data']);
            $return['imagen'] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$nameImg;
            
             /*SUBIR IMAGEN*/
            if($txt_crop != ""){
                base64ToImage($txt_crop, $nameImg);
            }
            else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                copy($img1, $img2);
            }
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar";
        }
    }else{
        $Clmodaleventos->cod_modal_evento = $cod_modal_evento;
        if($Clmodaleventos->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Editado correctamente";
            $return['id'] = $Clmodaleventos->cod_modal_evento;
            $idP = $Clmodaleventos->cod_modal_evento;
            $return['data'] = "";
            if($Clmodaleventos->getArray($Clmodaleventos->cod_modal_evento, $data)){
                //uploadFile($_FILES["img_product"], $data['image_min']);
                if($txt_crop != ""){
                    $cambio=false; //CAMBIARA EN LA FUNCION PARA SABER SI EL NOMBRE CAMBIO
                    $nameImgMax = getNameImagejpg($data['imagen'], $cambio);
                    if(base64ToImage($txt_crop, $nameImgMax)){
                        $Clmodaleventos->setImage($nameImgMax, 'max', $idP);
                        if($cambio){
                            deleteFile($data['imagen']);
                        }
                    }
                }
                $return['imagen'] = "editada";
                 $return['nom'] = $data['imagen'];
            }            
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar el evento";
        }
    }
    return $return;
}

function set_estado(){
	global $Clmodaleventos;
	if(!isset($_GET['cod_modal_evento']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $Clmodaleventos->set_estado($cod_modal_evento, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Evento editado correctamente";
        if($estado == "D")
            $return['mensaje'] = "Evento eliminado correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar el Evento";
    }
    return $return;
}
?>