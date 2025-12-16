<?php
require_once "../funciones.php";

$cod_empresa = 133; //MICHAELS
$cod_categoria = 988; //CATEGORÍA LUNCH

$estado = $_GET["status"];
$query = "SELECT *
            FROM tb_productos_categorias
            WHERE cod_categoria = $cod_categoria";
$productos = Conexion::buscarVariosRegistro($query);
if($productos) {
    foreach ($productos as $producto) {
        $cod_producto = $producto["cod_producto"];
        $query = "UPDATE tb_productos 
                    SET estado = '$estado'
                    WHERE cod_producto = $cod_producto
                    AND cod_empresa = $cod_empresa";
        $resp = Conexion::ejecutar($query, null);
        if($resp)
            $mensaje = "Estado de los productos: $estado";
        else
            $mensaje = "Error al actualizar el estado de los productos";
    }

    $query = "UPDATE tb_categorias
                SET estado = '$estado'
                WHERE cod_categoria = $cod_categoria
                AND cod_empresa = $cod_empresa";
    $resp = Conexion::ejecutar($query, null);
    if($resp)
        $mensaje2 = "Estado de la categoría: $estado";
    else
        $mensaje2 = "Error al actualizar el estado de la categoría";

    $return["success"] = 1;
    $return["mensaje_productos"] = $mensaje;
    $return["mensaje_categoria"] = $mensaje2;
    $return["data"] = $productos;
}
else {
    $return["success"] = 0;
    $return["mensaje"] = "No hay productos en esa categoría";
}

/* GUARDAR LOGS */
$folder = "logs";
if (!file_exists($folder)) {
    mkdir($folder, 0777);
}
$file = $folder."/turn_on_off_products.log";
$fecha = fecha();
$log = "[$fecha] Se ejecutó el cronjob estado: $estado";
file_put_contents($file, PHP_EOL . $log, FILE_APPEND);

header("Content-Type: application/json");
echo json_encode($return);
?>