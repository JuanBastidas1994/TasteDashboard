<?php

/* require_once "funciones.php";

$query = "SELECT c.nombre, cd.*
            FROM tb_cliente_dinero cd , tb_clientes c
            WHERE cd.cod_cliente = c.cod_cliente
            AND c.cod_empresa = 78
            AND c.estado = 'A'
            AND cd.fecha > '2023-01-23'
            AND cd.cod_cliente <> 1148
            AND c.nombre <> ''
            ORDER BY c.nombre";
$clientes = Conexion::buscarVariosRegistro($query);

echo count($clientes)."<br><br>";

foreach ($clientes as $cliente) {
    // echo "{$cliente["cod_cliente_dinero"]} | {$cliente["cod_cliente"]} - {$cliente["nombre"]} * {$cliente["fecha_caducidad"]} <br>";

    $query = "UPDATE tb_cliente_dinero set fecha_caducidad = DATE_ADD('{$cliente["fecha"]}', INTERVAL 365 DAY) WHERE cod_cliente_dinero = {$cliente["cod_cliente_dinero"]}";

    // echo "$query <br>";

    Conexion::ejecutar($query, null);
} */