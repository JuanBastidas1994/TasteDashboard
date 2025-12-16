<?php
require_once "funciones.php";

$query = 'SELECT pod.cod_producto_opciones_detalle, p.nombre, po.titulo, pod.item , pod.precio, pod.grava_iva, pod.aumentar_precio, p_item.nombre as "ITEM_PRODUCTO", p_item.precio as "PRECIO_ITEM"
            FROM tb_productos_opciones po
            INNER JOIN tb_productos_opciones_detalle pod ON po.cod_producto_opcion = pod.cod_producto_opcion
            INNER JOIN tb_productos p ON p.cod_producto = po.cod_producto
            INNER JOIN tb_productos p_item ON p_item.cod_producto = pod.item
            AND p.cod_empresa = 16
            AND po.isDatabase = 1
            AND pod.aumentar_precio = 1;';
$productos = Conexion::buscarVariosRegistro($query);
foreach($productos as $producto){
    echo '<b>'.$producto['ITEM_PRODUCTO'].'</b> - PVP ANTERIOR: '.$producto['precio'].' - PVP ACTUAL: '.$producto['PRECIO_ITEM'].' <br/>';
    actualizarOpciones($producto['PRECIO_ITEM'], $producto['cod_producto_opciones_detalle']);
    echo '<hr/><br/><br/>';
}



function actualizarOpciones($pvp, $id){
    $query = "UPDATE tb_productos_opciones_detalle SET precio=$pvp WHERE cod_producto_opciones_detalle = $id";
    echo '<b>'.$query.'</b>';
    //Conexion::ejecutar($query,NULL);
}
?>