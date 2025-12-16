<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_productos_descripcion.php";
$ClproductosDescripcion = new cl_productos_descripcion();
$session = getSession();

controller_create();

function crear()
{
    global $ClproductosDescripcion;
    global $session;
    if (count($_POST) == 0) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }


    extract($_POST);
    if ($cod_producto_descripcion == 0) {

        $id  = 0;
        $ClproductosDescripcion->cod_producto = $cod_producto;
        $ClproductosDescripcion->titulo = $titulo;
        $ClproductosDescripcion->descripcion = $descripcion;
        if ($ClproductosDescripcion->crear($id)) {
            $return['success'] = 1;
            $return['descripciones'] = $ClproductosDescripcion->listarPorProducto($cod_producto);
            $return['mensaje'] = "Descripción agregada con éxito";
        } else {
            $return['success'] = 0;
            $return['mensaje'] = "Error registrando";
        }

        return $return;
    }else{
        $ClproductosDescripcion->cod_producto = $cod_producto;
        $ClproductosDescripcion->titulo = $titulo;
        $ClproductosDescripcion->descripcion = $descripcion;
        if ($ClproductosDescripcion->editar($cod_producto_descripcion)) {
            $return['success'] = 1;
            $return['descripciones'] = $ClproductosDescripcion->listarPorProducto($cod_producto);
            $return['mensaje'] = "Descripción actualizada con éxito";
        } else {
            $return['success'] = 0;
            $return['mensaje'] = "Error actualizando";
        }
        return $return;
    }
}

function get()
{
    global $ClproductosDescripcion;
    if (!isset($_GET['cod_producto_descripciones'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);

    $productoDescripcion = $ClproductosDescripcion->obtenerPorId($cod_producto_descripciones);
    if ($productoDescripcion) {
        $return['success'] = 1;
        $return['mensaje'] = "Descripción encontrado";
        $return['data'] = $productoDescripcion;
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "Descripción no existe, por favor intentelo nuevamente";
    }
    return $return;
}

function delete()
{
    global $ClproductosDescripcion;
    if (!isset($_GET['cod_producto'])) {
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }

    extract($_GET);
    if ($ClproductosDescripcion->destroy($cod_producto_descripciones)) {
        $return['descripciones'] = $ClproductosDescripcion->listarPorProducto($cod_producto);
        $return['success'] = 1;
        $return['mensaje'] = "Descripción eliminada con éxito";
    } else {
        $return['success'] = 0;
        $return['mensaje'] = "Problemas eliminando";
    }
    return $return;
}

function orderDescriptions()
{
    global $ClproductosDescripcion;
    if(!isset($_POST['cod_producto'])){
        $return['success'] = 0;
        $return['mensaje'] = "Falta informacion";
        return $return;
    }
    extract($_POST);
    $elementosOrdernar = explode ( ',', $orderItems );
    for($i = 0; $i < count($elementosOrdernar); $i++){
        $ClproductosDescripcion->posicion = $i+1;
        $ClproductosDescripcion->descripcion = $elementosOrdernar[$i];
        $ClproductosDescripcion->updatePosition();
    }
    $return['success'] = 1;
    $return['mensaje'] = 'Posiciones actualizadas con exito';
    return $return;
}
