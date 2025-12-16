<?php
require_once "../funciones.php";
error_reporting(E_ALL);

$cod_empresa = $_GET['cod_empresa'];
$eliminar = $_GET['eliminar'];

$queryEmp = "SELECT * 
            FROM tb_empresas
            WHERE cod_empresa = $cod_empresa";
$rowEmp = Conexion::buscarRegistro($queryEmp);

$nombreEmpresa = $rowEmp['nombre'];
$aliasEmpresa = $rowEmp['alias'];

echo "Empresa: $nombreEmpresa<br>";

$files = "assets/empresas/$aliasEmpresa/";


if($eliminar == "ordenes")
    eliminarOrdenes();
else if($eliminar == "productos")
    eliminarProductos();
else if($eliminar == "clientes")
    eliminarClientes();
else if($eliminar == "puntos")    
    eliminarPuntosClientes();
else if($eliminar == "usuarios")
    eliminarUsuarios();
else if($eliminar === "all") {
    eliminarOrdenes();
    eliminarProductos();
    eliminarPuntosClientes();
    eliminarClientes();
    eliminarUsuarios();
}
else
    echo'No se pudo realizar, revise la Url';

//Region ordenes
    function eliminarOrdenes(){
        global $cod_empresa;
        $queryOrd = "SELECT *
                        FROM tb_orden_cabecera 
                        WHERE cod_empresa = $cod_empresa";
        $respOrd = Conexion::buscarVariosRegistro($queryOrd);
        if($respOrd){
            foreach($respOrd as $ord){
                $cod_orden = $ord['cod_orden'];
                echo "<br><br>ORDEN: $cod_orden<br>";
                eliminarCalificaciones($cod_orden);
                eliminarCancelaciones($cod_orden);
                eliminarCourierCanceled($cod_orden);
                eliminarFacturacion($cod_orden);
                eliminarDestino($cod_orden);
                eliminarDetalle($cod_orden);
                eliminarDevolucion($cod_orden);
                eliminarErrores($cod_orden);
                eliminarFacturaElec($cod_orden);
                eliminarHistorial($cod_orden);
                eliminarMotorizado($cod_orden);
                eliminarPagos($cod_orden);
                eliminarOrdenPuntos($cod_orden);
                eliminarRunfood($cod_orden);
                eliminarOrden($cod_orden);
            }
        }
        else{
            echo "No hay datos a eliminar";
        }
        
    }

    function eliminarOrden($cod_orden){
        $query = "DELETE FROM tb_orden_cabecera WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Orden $cod_orden eliminada <br>";
        else
            echo "Orden $cod_orden no eliminada <br>";
        
    }

    function eliminarCalificaciones($cod_orden){
        $query = "DELETE FROM tb_orden_calificacion WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Calificacion $cod_orden eliminada <br>";
        else
            echo "Calificacion $cod_orden no eliminada <br>";
        
    }

    function eliminarCancelaciones($cod_orden){
        $query = "DELETE FROM tb_orden_cancelacion WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Cancelacion $cod_orden eliminada <br>";
        else
            echo "Cancelacion $cod_orden no eliminada <br>";
        
    }

    function eliminarCourierCanceled($cod_orden){
        $query = "DELETE FROM tb_orden_courier_canceled WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Courier canceled $cod_orden eliminada <br>";
        else
            echo "Courier canceled $cod_orden no eliminada <br>";
        
    }

    function eliminarFacturacion($cod_orden){
        $query = "DELETE FROM tb_orden_datos_facturacion WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Orden facturacion $cod_orden eliminada <br>";
        else
            echo "Orden facturacion $cod_orden no eliminada <br>";
        
    }

    function eliminarDestino($cod_orden){
        $query = "DELETE FROM tb_orden_destino WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Orden destino $cod_orden eliminada <br>";
        else
            echo "Orden destino $cod_orden no eliminada <br>";
        
    }

    function eliminarDetalle($cod_orden){
        $query = "DELETE FROM tb_orden_detalle WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Detalle $cod_orden eliminada <br>";
        else
            echo "Detalle $cod_orden no eliminada <br>";
        
    }

    function eliminarDevolucion($cod_orden){
        $query = "DELETE FROM tb_orden_devolucion WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Devolucion $cod_orden eliminada <br>";
        else
            echo "Devolucion $cod_orden no eliminada <br>";
        
    }

    function eliminarErrores($cod_orden){
        $query = "DELETE FROM tb_orden_errores WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Errores $cod_orden eliminada <br>";
        else
            echo "Errores $cod_orden no eliminada <br>";
        
    }

    function eliminarFacturaElec($cod_orden){
        $query = "DELETE FROM tb_orden_factura_electronica WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Electronica $cod_orden eliminada <br>";
        else
            echo "Electronica $cod_orden no eliminada <br>";
        
    }

    function eliminarHistorial($cod_orden){
        $query = "DELETE FROM tb_orden_historial WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Historial $cod_orden eliminada <br>";
        else
            echo "Historial $cod_orden no eliminada <br>";
        
    }

    function eliminarMotorizado($cod_orden){
        $query = "DELETE FROM tb_orden_motorizado WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Motorizado $cod_orden eliminada <br>";
        else
            echo "Motorizado $cod_orden no eliminada <br>";
        
    }

    function eliminarPagos($cod_orden){
        $query = "DELETE FROM tb_orden_pagos WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Pago $cod_orden eliminada <br>";
        else
            echo "Pago $cod_orden no eliminada <br>";
        
    }

    function eliminarOrdenPuntos($cod_orden){
        $query = "DELETE FROM tb_orden_puntos WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Puntos $cod_orden eliminada <br>";
        else
            echo "Puntos $cod_orden no eliminada <br>";
        
    }

    function eliminarRunfood($cod_orden){
        $query = "DELETE FROM tb_orden_runfood WHERE cod_orden = $cod_orden";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Runfood $cod_orden eliminada <br>";
        else
            echo "Runfood $cod_orden no eliminada <br>";
    }
//End Region


//Region productos
    function eliminarProductos(){
        global $cod_empresa;
        
        $queryProd = "SELECT *
                        FROM tb_productos 
                        WHERE cod_empresa = $cod_empresa
                        AND estado = 'D'";
        $respProd = Conexion::buscarVariosRegistro($queryProd);
        if($respProd){
            foreach($respProd as $prod){
                $cod_producto = $prod['cod_producto'];
                $nom_prod = $prod['nombre'];
                echo "<br><br>PRODUCTO: $cod_producto<br>";
                eliminarArchivo($cod_producto);
                eliminarCategoria($cod_producto);
                eliminarProdDias($cod_producto);
                eliminarFact($cod_producto);
                eliminarImagenes($cod_producto);
                eliminarIngredientes($cod_producto);
                eliminarOpciones($cod_producto);
                //eliminarOpcionesDetalles($cod_producto);
                eliminarPreferencia($cod_producto);
                eliminarSucursal($cod_producto);
                eliminarVariante($cod_producto);
                eliminarCaracteristica($cod_producto);
                //eliminarCaracteristicaDetalle($cod_producto);
                eliminarDescuento($cod_producto);
                eliminarExtras($cod_producto);
                //eliminarExtrasDetalle($cod_producto);
                eliminarProducto($cod_producto);
            }
        }
        else{
            echo "No hay datos a eliminar";
        }
    }

    function eliminarProducto($cod_producto){
        global $files;

        $queryA = "SELECT image_max, image_min FROM tb_productos WHERE cod_producto = $cod_producto";
        $respA = Conexion::buscarVariosRegistro($queryA);
        if($respA){
            foreach($respA as $arc){
                $archivo = $files. $arc['image_max'];
                $archivo_min = $files. $arc['image_min'];
                echo "$archivo<br>";
                echo "$archivo_min<br>";
                unlink($archivo);
                unlink($archivo_min);
            }
        }
        
        $query = "DELETE FROM tb_productos WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Producto $cod_producto eliminada <br>";
        else
            echo "Producto $cod_producto no eliminado <br>";
    }

    function eliminarArchivo($cod_producto){
        global $files;
        
        $queryA = "SELECT nombre_archivo FROM tb_productos_archivos WHERE cod_producto = $cod_producto";
        $respA = Conexion::buscarVariosRegistro($queryA);
        if($respA){
            foreach($respA as $arc){
                $archivo = $files. $arc['nombre_archivo'];
                echo "$archivo<br>";
                unlink($archivo);
            }
        }
        
        $query = "DELETE FROM tb_productos_archivos WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Archivo $cod_producto eliminada <br>";
        else
            echo "Archivo $cod_producto no eliminado <br>";
    }

    function eliminarCategoria($cod_producto){
        $query = "DELETE FROM tb_productos_categorias WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Categoria $cod_producto eliminada <br>";
        else
            echo "Categoria $cod_producto no eliminado <br>";
    }

    function eliminarProdDias($cod_producto){
        $query = "DELETE FROM tb_productos_dias WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Dias $cod_producto eliminada <br>";
        else
            echo "Dias $cod_producto no eliminado <br>";
    }

    function eliminarFact($cod_producto){
        $query = "DELETE FROM tb_productos_facturacion WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Fact $cod_producto eliminada <br>";
        else
            echo "Fact $cod_producto no eliminado <br>";
    }

    function eliminarImagenes($cod_producto){
        global $files;
        
        $queryA = "SELECT nombre_img FROM tb_productos_imagenes WHERE cod_producto = $cod_producto";
        $respA = Conexion::buscarVariosRegistro($queryA);
        if($respA){
            foreach($respA as $arc){
                $archivo = $files. $arc['nombre_archivo'];
                echo "$archivo<br>";
                unlink($archivo);
            }
        }
        
        $query = "DELETE FROM tb_productos_imagenes WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Imagenes $cod_producto eliminada <br>";
        else
            echo "Imagenes $cod_producto no eliminado <br>";
    }

    function eliminarIngredientes($cod_producto){
        $query = "DELETE FROM tb_productos_ingredientes WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Ingredientes $cod_producto eliminada <br>";
        else
            echo "Ingredientes $cod_producto no eliminado <br>";
    }

    function eliminarOpciones($cod_producto){
        $query = "DELETE FROM tb_productos_opciones WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Opciones $cod_producto eliminada <br>";
        else
            echo "Opciones $cod_producto no eliminado <br>";
    }

    function eliminarOpcionesDetalles($cod_producto){
        $query = "DELETE FROM tb_productos_opciones_detalle WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "OpcionesDetalles $cod_producto eliminada <br>";
        else
            echo "OpcionesDetalles $cod_producto no eliminado <br>";
    }

    function eliminarPreferencia($cod_producto){
        $query = "DELETE FROM tb_productos_preferencia WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Preferencia $cod_producto eliminada <br>";
        else
            echo "Preferencia $cod_producto no eliminado <br>";
    }

    function eliminarSucursal($cod_producto){
        $query = "DELETE FROM tb_productos_sucursal WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Sucursal $cod_producto eliminada <br>";
        else
            echo "Sucursal $cod_producto no eliminado <br>";
    }

    function eliminarVariante($cod_producto){
        $query = "DELETE FROM tb_productos_variante WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Variante $cod_producto eliminada <br>";
        else
            echo "Variante $cod_producto no eliminado <br>";
    }

    function eliminarCaracteristica($cod_producto){
        $query = "DELETE FROM tb_producto_caracteristica WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Caracteristica $cod_producto eliminada <br>";
        else
            echo "Caracteristica $cod_producto no eliminado <br>";
    }

    function eliminarCaracteristicaDetalle($cod_producto){
        $query = "DELETE FROM tb_producto_caracteristica_detalle WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "CaracteristicaDetalle $cod_producto eliminada <br>";
        else
            echo "CaracteristicaDetalle $cod_producto no eliminado <br>";
    }

    function eliminarDescuento($cod_producto){
        $query = "DELETE FROM tb_producto_descuento WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Descuento $cod_producto eliminada <br>";
        else
            echo "Descuento $cod_producto no eliminado <br>";
    }

    function eliminarExtras($cod_producto){
        $query = "DELETE FROM tb_producto_extras WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "Extras $cod_producto eliminada <br>";
        else
            echo "Extras $cod_producto no eliminado <br>";
    }

    function eliminarExtrasDetalle($cod_producto){
        $query = "DELETE FROM tb_producto_extras_detalle WHERE cod_producto = $cod_producto";
        $resp = Conexion::ejecutar($query,NULL);
        if($resp)
            echo "ExtrasDetalle $cod_producto eliminada <br>";
        else
            echo "ExtrasDetalle $cod_producto no eliminado <br>";
    }
//End region


/*CLIENTES*/
function eliminarClientes(){
    global $cod_empresa;
    
    $queryProd = "SELECT *
                    FROM tb_clientes 
                    WHERE cod_empresa = $cod_empresa";
    $respProd = Conexion::buscarVariosRegistro($queryProd);
    if($respProd){
        foreach($respProd as $prod){
            $cod_cliente = $prod['cod_cliente'];
            $nom_prod = $prod['nombre'];
            echo "<br><br>CLIENTE: $cod_cliente - $nom_prod<br>";
            eliminarFidelizacion($cod_cliente);
        }
    }
    else{
        echo "No hay clientes a eliminar";
    }
}

function eliminarFidelizacion($cod_cliente){
    $query = "DELETE FROM tb_clientes_puntos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClientePuntos $cod_cliente eliminada <br>";
    else
        echo "ClientePuntos $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteSaldo $cod_cliente eliminada <br>";
    else
        echo "ClienteSaldo $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_cliente_dinero WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteDinero $cod_cliente eliminada <br>";
    else
        echo "ClienteDinero $cod_cliente no eliminado <br>";
        
        
    $query = "DELETE FROM tb_clientes WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "Cliente $cod_cliente eliminada <br>";
    else
        echo "Cliente $cod_cliente no eliminado <br>";    
}

/*FIDELIZACION*/
function eliminarPuntosClientes(){
    global $cod_empresa;
    
    $queryProd = "SELECT *
                    FROM tb_clientes 
                    WHERE cod_empresa = $cod_empresa";
    $respProd = Conexion::buscarVariosRegistro($queryProd);
    if($respProd){
        foreach($respProd as $prod){
            $cod_cliente = $prod['cod_cliente'];
            $nom_prod = $prod['nombre'];
            echo "<br><br>CLIENTE FIDELIZACION: $cod_producto<br>";
            eliminarFidelizacionNoClientes($cod_cliente);
        }
    }
    else{
        echo "No hay clientes a eliminar";
    }
}

function eliminarFidelizacionNoClientes($cod_cliente){
    $query = "DELETE FROM tb_clientes_puntos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClientePuntos $cod_cliente eliminada <br>";
    else
        echo "ClientePuntos $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteSaldo $cod_cliente eliminada <br>";
    else
        echo "ClienteSaldo $cod_cliente no eliminado <br>";
    
        
    $query = "DELETE FROM tb_cliente_dinero WHERE cod_cliente = $cod_cliente";
    $resp = Conexion::ejecutar($query,NULL);
    if($resp)
        echo "ClienteDinero $cod_cliente eliminada <br>";
    else
        echo "ClienteDinero $cod_cliente no eliminado <br>";
}

/*USUARIOS*/
function eliminarUsuarios(){
    global $cod_empresa;
    
    $queryProd = "SELECT *
                    FROM tb_usuarios 
                    WHERE cod_rol = 4
                    AND cod_empresa = $cod_empresa";
    $respProd = Conexion::buscarVariosRegistro($queryProd);
    if($respProd){
        foreach($respProd as $prod){
            $cod_usuario = $prod['cod_usuario'];
            $nom_prod = $prod['nombre'];
            echo "<br><br>USUARIO: $cod_usuario - $nom_prod<br>";
            
            $query = "DELETE FROM tb_usuarios WHERE cod_usuario = $cod_usuario";
            $resp = Conexion::ejecutar($query,NULL);
            if($resp)
                echo "Cliente $cod_usuario $nom_prod eliminada <br>";
            else
                echo "Cliente $cod_usuario $nom_prod no eliminado <br>";  
        }
    }
    else{
        echo "No hay usuarios a eliminar";
    }
}
?>