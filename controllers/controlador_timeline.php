<?php
require_once "../funciones.php";

//Clases
require_once "../clases/cl_timeline.php";

$Cltimeline = new cl_timeline();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function crear(){
    global $Cltimeline;
    extract($_GET);
    
    $Cltimeline->nombre = $nombre;
    $Cltimeline->estado = $estado;
    if($id == 0){
        $producto = $Cltimeline->getProductoByAlias($alias);
        if($producto){
            $cod_producto = $producto["cod_producto"];
            $Cltimeline->cod_producto = $cod_producto;
            if($Cltimeline->crear($id)){
                $return["success"] = 1;
                $return["mensaje"] = "Timeline guardado correctamente";
                $return["id"] = $id;
            }
            else{
                $return["success"] = 0;
                $return["mensaje"] = "Error al guardar timeline";
            }
        }
        else{
            $return["success"] = 0;
            $return["mensaje"] = "Alias no existe";
        }
    }
    else{
        if($Cltimeline->editar($id)){
            $return["success"] = 1;
            $return["mensaje"] = "Timeline editado correctamente";
        }
        else{
            $return["success"] = 0;
            $return["mensaje"] = "Error al editar timeline";
        }
    }
    
    return $return;
}

function crearDetalle(){
    global $Cltimeline;
    extract($_POST);

    for($i=0; $i<count($titulo); $i++){
        $nombreImagen = "timeline_".fechaSignos()."-$i.jpg";
        $Cltimeline->titulo = $titulo[$i];
        $Cltimeline->subtitulo = $subtitulo[$i];
        $Cltimeline->imagen = $nombreImagen;
        $Cltimeline->posicion = $i;
        if($idDetalle[$i] == 0){
            if($Cltimeline->crearDetalle($id)){
                if($_FILES["image"]["tmp_name"][$i] <> null){
                    uploadImageTM($_FILES["image"]["tmp_name"][$i], $nombreImagen);
                }
                $return['success'] = 1;
                $return['mensaje'] = "Detalle guardado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al guardar el detalle";
            }
        }
        else{
            if($Cltimeline->editarDetalle($idDetalle[$i])){
                $det = $Cltimeline->obtenerDetalle($idDetalle[$i]);
                if($det){
                    $nombreImagen = $det["imagen"];
                    if($_FILES["image"]["tmp_name"][$i] <> null){
                        uploadImageTM($_FILES["image"]["tmp_name"][$i], $nombreImagen);
                    }
                }
                $return['success'] = 1;
                $return['mensaje'] = "Detalle editado correctamente";
            }
            else{
                $return['success'] = 0;
                $return['mensaje'] = "Error al editar el detalle";
            }
        }
    }
    return $return;
}

function obtenerDetalles(){
    global $Cltimeline;
    extract($_GET);

    $detalles = $Cltimeline->obtenerDetalles($id);
    if($detalles){
        $return['success'] = 1;
        $return['mensaje'] = "Detalles obtenidos";
        $return["data"] = $detalles;
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "No hay detalles";
    }
    return $return;
}

function ordenar(){
    global $Cltimeline;
    extract($_GET);

    for ($i=0; $i<count($data); $i++) { 
        $Cltimeline->posicion = $i;
        $Cltimeline->editarPosicion($data[$i]);
    }
    $return['success'] = 1;
    $return['mensaje'] = "ok";
    return $return;
}

function uploadImageTM($image, $name){
    $session = getSession();
    $files = url_upload.'/assets/empresas/'.$session['alias'].'/';
    $dir = $files.$name;
    if(move_uploaded_file($image, $dir)) {
        return true;
    }
    else{
        return false;
    }
}

function eliminarDetalle(){
    global $Cltimeline;
    extract($_GET);

    if($Cltimeline->eliminarDetalle($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Detalle eliminado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el detalle";
    }
    return $return;
}

function obtenerLista(){
    global $Cltimeline;
    extract($_GET);

    $producto = $Cltimeline->getProductoByAlias($alias);
    if($producto){
        $timelines = $Cltimeline->lista($producto["cod_producto"]);
        if($timelines){
            $return['success'] = 1;
            $return['mensaje'] = "Lista obtenida";
            $return['data'] = $timelines;

        }
        else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al traer la lista";
        }
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Alias no existe";
    }
    return $return;
}

function eliminar(){
    global $Cltimeline;
    extract($_GET);

    if($Cltimeline->eliminar($id)){
        $return['success'] = 1;
        $return['mensaje'] = "Timeline eliminado correctamente";
    }
    else{
        $return['success'] = 0;
        $return['mensaje'] = "Error al eliminar el timeline";
    }
    return $return;
}
?>