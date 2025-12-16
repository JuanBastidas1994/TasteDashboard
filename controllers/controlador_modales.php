<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_modales.php";
$ClModales = new cl_modales();
$session = getSession();

controller_create();

function lista(){
    global $ClModales;
    global $session;
    extract($_GET);
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    
    $resp = $ClModales->lista();
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Hay modales";

        foreach($resp as $key => $modal){
            $resp[$key]['imagen'] = $files.$modal['imagen'];
        }
        $return['data'] = $resp;

    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay modales";
    }
    
    return $return;
}

function get(){
    global $ClModales;
    global $session;
    extract($_GET);
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    
    $resp = $ClModales->get($id);
    if($resp){
        $return['success'] = 1;
        $return['mensaje'] = "Existe modal";
        $resp['imagen'] = $files.$resp['imagen'];
        $return['data'] = $resp;

    }else{
        $return['success'] = 0;
        $return['mensaje'] = "No existe el modal evento";
    }
    
    return $return;
}

function crear(){
    global $ClModales;
    global $session;

    extract($_POST);
    
    $nameImg = 'modal_'.datetime_format().'.jpg';
    
    if(!isset($_POST['cmbAccion'])){
        $accion = "URL";
    }else{
        $accion = $cmbAccion;
        if($accion == "URL" || $accion == "FILTER"){
            $txt_url_boton = $txt_accion_desc;
        }else if($accion == "PRODUCTO"){
            $txt_url_boton = $cmbProductos;
        }else if($accion == "NOTICIA"){
            $txt_url_boton = $cmbNoticias;
        }else if($accion == "INFO"){
            $txt_url_boton = "";
        }
    }
    
    $ClModales->titulo = $txt_titulo;
    $ClModales->descripcion = "";
    $ClModales->accion_id = $accion;
    $ClModales->accion_desc = $txt_url_boton;
    $ClModales->fecha_inicio = $fecha_inicio;
    $ClModales->fecha_fin = $fecha_fin;
    $ClModales->imagen = $nameImg;
    $ClModales->estado = 'A';
    
    $files = url_sistema.'assets/empresas/'.$session['alias'].'/';
    
    if($uid == ""){
        if($crop === ""){
            $return['success'] = 0;
            $return['mensaje'] = "Es obligatorio subir una imagen para crear un modal evento";
            return $return;
        }

        $id = 0;
        if($ClModales->crear($id)){
            base64ToImage($crop, $nameImg);
            $return['success'] = 1;
            $return['mensaje'] = "El modal se creo correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al crear el modal evento, por favor vuelva a intentarlo";
        }
    }
    else{
        $resp = $ClModales->get($uid);
        if(!$resp){
            $return['success'] = 0;
            $return['mensaje'] = "No existe el modal evento";
            return $return;
        }
        if($crop !== ""){
            base64ToImage($txt_crop, $resp['imagen']);
        }
        
        if($ClModales->editar($uid)){
            $return['success'] = 1;
            $return['mensaje'] = "El modal se edito correctamente";
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al editar el modal evento, por favor vuelva a intentarlo";
        }
    }
    return $return;
}

function eliminar(){
    global $ClWebAnuncios;
    global $session;
    
    extract($_GET);
    
    if($ClWebAnuncios->eliminar($cod_anuncio_detalle)){
        $return['success'] = 1;
        $return['mensaje'] = "Anuncio eliminado";
        $return['id'] = $cod_anuncio_detalle;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar";
    }
    return $return;
}

?>