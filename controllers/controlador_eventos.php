<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_eventos.php";
$ClEventos = new cl_eventos();
$session = getSession();
$files = url_sistema.'assets/empresas/'.$session['alias'].'/';

controller_create();

function crear(){
    global $ClEventos;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    /*VALIDAR ARCHIVO*/
    if ($_FILES['fileEvent']['name'] != null) {
        $formatos = array('application/pdf');
        if(!in_array($_FILES["fileEvent"]['type'], $formatos)){
            $return['success'] = 0;
            $return['mensaje'] = "Sólo se admiten archivos PDF";
            return $return;
        }
    }

    $nameImg = 'evento_'.datetime_format().'.jpg';
    $nameFile = 'evento_'.datetime_format().'.pdf';
    
    $ClEventos->titulo = $txt_titulo;
    $ClEventos->cod_categoria = $cmbCategoria;
    $ClEventos->imagen = $nameImg;
    $ClEventos->archivo = $nameFile;
    $ClEventos->fecha = $fecha_evento;
    $ClEventos->hora_inicio = $hora_inicio;
    $ClEventos->hora_fin = $hora_fin;
    $ClEventos->user_create = $session['cod_usuario'];
    $ClEventos->color = $cmbCategoria;
    $ClEventos->descripcion = $txt_descripcion;
   
    $id=0;
    if($ClEventos->crear($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Evento creado correctamente";
        $return['id'] = $id;

        /*SUBIR IMAGEN*/
        if($txt_crop != "" && $txt_crop_min != ""){
            base64ToImage($txt_crop, $nameImg);
        }else{
            $img1 = url_upload.'/assets/img/200x200.jpg';
            $img2 = url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameImg;
            copy($img1, $img2);
        }

        /*SUBIR ARCHIVO*/
        if ($_FILES['fileEvent']['name'] != null) {
            if(move_uploaded_file($_FILES['fileEvent']['tmp_name'], url_upload.'/assets/empresas/'.$session['alias'].'/'.$nameFile))
                $return['pdf'] = "Subido correctamente";
            else
                $return['pdf'] = "Error al subir";         
        }
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al crear el evento, por favor vuelva a intentarlo";
    }
    return $return;
}

function get(){
    global $ClEventos;
    global $files;
    if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $evento = $ClEventos->get($id);
    if($evento){
        $evento['imagen'] = $files.$evento['imagen'];
        $return['success'] = 1;
        $return['mensaje'] = "Evento encontrado";
        $return['data'] = $evento;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Evento no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function editHoras(){
    global $ClEventos;
    global $session;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);
    
    $ClEventos->fecha = $fecha;
    $ClEventos->hora_inicio = $hora_inicio;
    $ClEventos->hora_fin = $hora_fin;
    if($ClEventos->editHoras($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Evento actualizado correctamente";
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al actualizar el evento, por favor vuelva a intentarlo";
    }
    return $return;
}

function delete(){
  global $ClEventos;
  if(!isset($_GET['id'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

  extract($_GET);

    $resp = $ClEventos->delete($id);
    if($resp){
      $return['success'] = 1;
      $return['mensaje'] = "Evento eliminado correctamente";
    }else{
      $return['success'] = 0;
      $return['mensaje'] = "Error al eliminar el evento";
    }
    return $return;
}

?>