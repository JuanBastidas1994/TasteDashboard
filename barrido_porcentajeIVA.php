<?php
    require_once "funciones.php";
    
    if(!isset($_GET['alias'])){
        header("location:index.php");
    }
    $alias = $_GET['alias'];
    
    $query = "SELECT cod_empresa, nombre, impuesto FROM tb_empresas WHERE alias = '$alias'";
    $empresa = Conexion::buscarRegistro($query);
    if($empresa){
        $cod_empresa = $empresa['cod_empresa'];
        $porcentaje_iva = $empresa['impuesto'];
    }else{
        header("location:index.php");
    }
?>
<html>
    <style>
        th, td{
            border: 1px solid gray;
        }
    </style>
    <head>
        <title>Cambio de Iva</title>
    </head>
    <body>
        <h3>Actualizador de iva</h3>
        <h4><?php echo $empresa['nombre'] . "- cambiar a " . $empresa['impuesto'] . "%"; ?></h4>
        
        
        <table style="width: 100%; border: 1px solid gray;">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio no tax</th>
                    <th>Pvp anterior</th>
                    <th>Iva nuevo</th>
                    <th>Pvp nuevo</th>
                    <th>Query</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // ACTUALIZAR PRECIO DE PRODUCTOS
                    $query = "SELECT cod_producto, nombre, precio, precio_no_tax, iva_valor, iva_porcentaje 
                                FROM tb_productos 
                                WHERE cod_empresa = $cod_empresa AND cobra_iva = 1 AND estado IN ('A', 'I')";
                    $productos = Conexion::buscarVariosRegistro($query);
                    foreach ($productos as $producto){
                        $cod_producto = $producto['cod_producto'];
                        $nombre = $producto['nombre'];
                        $precio = $producto['precio'];
                        $precio_no_tax = $producto['precio_no_tax'];
                        $iva_valor = $producto['iva_valor'];
                        $iva_porcentaje = $producto['iva_porcentaje'];
                        
                        if($iva_porcentaje != $porcentaje_iva){
                            $nuevo_iva_valor = number_format($precio_no_tax * ($porcentaje_iva / 100), 2);
                            $nuevo_precio = number_format($precio_no_tax + $nuevo_iva_valor, 2);
                            
                            $update = "UPDATE tb_productos SET iva_porcentaje = $porcentaje_iva,
                                                                iva_valor = $nuevo_iva_valor, 
                                                                precio = $nuevo_precio
                                                                WHERE cod_producto = $cod_producto";
                            Conexion::ejecutar($update, NULL);
                        }else{
                            $nuevo_iva_valor = "";
                            $nuevo_precio = "No aplica";
                            $update = "Mismo iva";
                        }
                        
                        echo "
                            <tr>
                                <td>$nombre</td>
                                <td>$precio_no_tax</td>
                                <td>$precio</td>
                                <td>$nuevo_iva_valor</td>
                                <td>$nuevo_precio</td>
                                <td>$update</td>
                            </tr>";
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>