<?php
require_once "../funciones.php";
$cod_empresa = 78;  //MAGA STUDIO

/*VACIAR PUNTOS*/
echo '<br/><h3>VACIAR PUNTOS</h3><br/>';
$query = "SELECT p.cod_cliente_punto, c.nombre, c.cod_cliente, c.num_documento
    FROM tb_clientes c, tb_clientes_puntos p
    WHERE c.cod_cliente = p.cod_cliente
    AND c.cod_empresa = $cod_empresa";
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $puntos){
    $cod = $puntos['cod_cliente_punto'];
    $query = "DELETE FROM tb_clientes_puntos WHERE cod_cliente_punto = $cod";
    $respDelete = Conexion::ejecutar($query,NULL);
    if($respDelete){
        echo '<br/>Eliminado $cod<br/>';
    }else{
        echo '<br/><span style="color:red;">Error punto $cod</span><br/>';
    }
}

/*VACIAR DINERO*/
echo '<br/><h3>VACIAR DINERO</h3><br/>';
$query = "SELECT p.cod_cliente_dinero, c.nombre, c.cod_cliente, c.num_documento
    FROM tb_clientes c, tb_cliente_dinero p
    WHERE c.cod_cliente = p.cod_cliente
    AND c.cod_empresa = $cod_empresa";
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $puntos){
    $cod = $puntos['cod_cliente_dinero'];
    $query = "DELETE FROM tb_cliente_dinero WHERE cod_cliente_dinero = $cod";
    $respDelete = Conexion::ejecutar($query,NULL);
    if($respDelete){
        echo '<br/>Eliminado $cod<br/>';
    }else{
        echo '<br/><span style="color:red;">Error dinero $cod</span><br/>';
    }
}

/*VACIAR SALDO*/
echo '<br/><h3>VACIAR SALDO</h3><br/>';
$query = "SELECT p.cod_cliente_saldo, c.nombre, c.cod_cliente, c.num_documento
    FROM tb_clientes c, tb_clientes_saldos p
    WHERE c.cod_cliente = p.cod_cliente
    AND c.cod_empresa = $cod_empresa";
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $puntos){
    $cod = $puntos['cod_cliente_saldo'];
    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente_saldo = $cod";
    $respDelete = Conexion::ejecutar($query,NULL);
    if($respDelete){
        echo '<br/>Eliminado $cod<br/>';
    }else{
        echo '<br/><span style="color:red;">Error saldo $cod</span><br/>';
    }
}

/*ORDENES DE NUEVO LISTAS PARA EJECUTARSE*/
echo '<br/><h3>ORDENES LIMPIAS</h3><br/>';
$query = "SELECT op.cod_orden_puntos 
        FROM tb_orden_puntos op, tb_orden_cabecera o
        WHERE o.cod_orden = op.cod_orden
        AND o.estado NOT IN('ANULADA')
        AND o.cod_empresa = $cod_empresa";
$resp = Conexion::buscarVariosRegistro($query);
foreach($resp as $puntos){
    $cod = $puntos['cod_orden_puntos'];
    $query = "UPDATE tb_orden_puntos SET estado = 0 WHERE cod_orden_puntos = $cod";
    $respDelete = Conexion::ejecutar($query,NULL);
    if($respDelete){
        echo "<br/>Editado $cod<br/>";
    }else{
        echo '<br/><span style="color:red;">Error orden puntos $cod</span><br/>';
    }
}

?>
    
    