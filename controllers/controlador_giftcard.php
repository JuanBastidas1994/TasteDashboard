<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_giftcards.php";


$Clgiftcard = new cl_giftcards();
$session = getSession();

controller_create();

function crear(){
    global $Clgiftcard;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $nameImg = 'giftcard_'.datetime_format().'.png';
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
     $montos .= $cmb_montos[0];
    for($i=1; $i<count($cmb_montos); $i++){
           $montos .= ",".$cmb_montos[$i];
        }
    $Clgiftcard->nombre = $txt_nombre;
    $Clgiftcard->monto = $montos;
    $Clgiftcard->imagen= $nameImg;    

    if(isset($_POST['chk_estado']))
        $Clgiftcard->estado = 'A';
    else
        $Clgiftcard->estado = 'I';
   
    $pasar = false;
    if(!isset($_POST['cod_giftcard'])){
        $id=0;
        if($Clgiftcard->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Giftcard guardada correctamente";
            $return['id'] = $id;
            $return['imagen'] = url_sistema.'assets/empresas/'.$session['alias'].'/'.$nameImg;
            
             /*SUBIR IMAGEN*/
            if($txt_crop != ""){
                base64ToImage($txt_crop, $nameImg);
            }else{
                $img1 = url_upload.'/assets/img/200x200.jpg';
                $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
                copy($img1, $img2);
            }
            
            /*--NUEVO--*/
          
            $pasar = true;
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la giftcard";
        }
    }else{
        $Clgiftcard->cod_giftcard = $cod_giftcard;
        if($Clgiftcard->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Giftcard editada correctamente";
            $return['id'] = $Clgiftcard->$cod_giftcard;
            /*if($Clgiftcard->getArray($Clgiftcard->cod_sucursal, $data))
            {
                //uploadFile($_FILES["img_product"], $data['image_min']);
                if($txt_crop != ""){
                    base64ToImage($txt_crop, $data['image']);
                }
                $return['imagen'] = "editada";
                 $return['nom'] = $data['image'];
            }*/
            $pasar = true;
            
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la giftcard";
        }
    }
    
    return $return;
}

function get(){
    global $Clgiftcard;
    global $session;
    if(!isset($_GET['cod_giftcard'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
$opcSelesct="";
    $array = NULL;
    if($Clgiftcard->getArray($cod_giftcard, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Giftcard encontrada";
        $return['data'] = $array;
        $montos = $array['montos'];
        $inf = explode(",",$montos);
               $x = 0;
               foreach($inf as $r){
                    $selected = "";
                     $selected = 'selected="selected"';
                   $opcSelesct.='<option '.$selected.' value="'.$inf[$x].'">'.$inf[$x].'</option>';
                   $x++;
               }
        $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
        $return['data']['image'] = $files.$array['imagen'];
        $return['data']['htmlMontos']=$opcSelesct;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Giftcard no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
    global $Clgiftcard;
    global $session;
    if(count($_GET)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    
    if ($Clgiftcard->set_estado($cod_giftcard, $estado)){
            $return['success'] = 1;
            $return['mensaje'] = "Editado correctamente";
    }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar la giftcard";
        }
    return $return;    
}

function actualizar(){
    global $Clgiftcard;

    extract($_POST);
    if($tipo == "opciones")
    {
        for ($i=0; $i < count($datos); $i++) { 
        $Clgiftcard->actPosicionOpciones($datos[$i], $i+1);
        }
    }
    $return['success'] = 1;
    $return['mensaje'] = "Actualizado correctamente";
    return $return;
}

?>