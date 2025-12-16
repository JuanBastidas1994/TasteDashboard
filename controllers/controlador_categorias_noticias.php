<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_categorias_noticias.php";
$ClCategoriasNoticias = new cl_categorias_noticias(NULL);

controller_create();

function crear(){
    global $ClCategoriasNoticias;
    if(count($_POST)==0){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_POST);

    $ClCategoriasNoticias->nombre = $txt_nombre;
    $ClCategoriasNoticias->cod_categoria_padre = $cmb_categoria;
    $ClCategoriasNoticias->estado = 'A';

    if(!isset($_POST['cod_categoria_noticia'])){
        $id=0;
        if($ClCategoriasNoticias->crear($id)){
            $return['success'] = 1;
            $return['mensaje'] = "Categoria Noticia guardada correctamente";
            $return['id'] = $id;
            $return['data'] = "";
            $ClCategoriasNoticias->getArray($id, $return['data']);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la sucursal";
        }
    }else{
        $ClCategoriasNoticias->cod_categoria_noticia = $cod_categoria_noticia;
        if($ClCategoriasNoticias->editar()){
            $return['success'] = 1;
            $return['mensaje'] = "Categoria Noticias editada correctamente";
            $return['id'] = $ClCategoriasNoticias->cod_categoria_noticia;
            $return['data'] = "";
            $ClCategoriasNoticias->getArray($ClCategoriasNoticias->cod_categoria_noticia, $return['data']);
        }else{
            $return['success'] = 0;
            $return['mensaje'] = "Error al guardar la Categoria Noticia";
        }
    }
    return $return;
}

function get(){
    global $ClCategoriasNoticias;
    if(!isset($_GET['cod_categoria_noticia'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $array = NULL;
    if($ClCategoriasNoticias->getArray($cod_categoria_noticia, $array)){
        $return['success'] = 1;
        $return['mensaje'] = "Categoria Noticia encontrada";
        $return['data'] = $array;
    }else{
        $return['success'] = 0;
        $return['mensaje'] = "Categoria Noticia no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function set_estado(){
	global $ClCategoriasNoticias;
	if(!isset($_GET['cod_categoria_noticia']) || !isset($_GET['estado'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

	extract($_GET);

    $resp = $ClCategoriasNoticias->set_estado($cod_categoria_noticia, $estado);
    if($resp){
    	$return['success'] = 1;
    	$return['mensaje'] = "Noticia editada correctamente";
        if($estado == "D")
            $return['mensaje'] = "Noticia eliminada correctamente";
    }else{
    	$return['success'] = 0;
    	$return['mensaje'] = "Error al editar la Noticia";
    }
    return $return;
}


?>