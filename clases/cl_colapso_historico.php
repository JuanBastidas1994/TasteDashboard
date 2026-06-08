<?php

class cl_colapso_historico {

    var $session;

    public function __construct() {
        $this->session = getSession();
    }

    public function getEstado($cod_empresa, $anio_limite) {
        $qMensual = "SELECT DISTINCT anio FROM resumen_ventas_mensual WHERE cod_empresa = $cod_empresa ORDER BY anio";
        $qAnual   = "SELECT DISTINCT anio FROM resumen_ventas_anual   WHERE cod_empresa = $cod_empresa ORDER BY anio";
        $qOrigen  = "SELECT DISTINCT YEAR(fecha) AS anio
                     FROM tb_orden_cabecera
                     WHERE cod_empresa = $cod_empresa AND YEAR(fecha) < $anio_limite
                     ORDER BY anio";

        $mensual = array_column(Conexion::buscarVariosRegistro($qMensual), 'anio');
        $anual   = array_column(Conexion::buscarVariosRegistro($qAnual), 'anio');
        $origen  = array_column(Conexion::buscarVariosRegistro($qOrigen), 'anio');

        $pendientes = array_values(array_diff($origen, $anual));

        return [
            'anio_limite' => $anio_limite,
            'mensual'     => $mensual,
            'anual'       => $anual,
            'pendientes'  => $pendientes
        ];
    }

    public function ejecutarColapsoAnual($cod_empresa, $anio_limite) {

        // 1) Totales generales y tipos de entrega
        $q1 = "INSERT INTO resumen_ventas_anual (
                    cod_empresa, cod_sucursal, nombre_sucursal, anio,
                    total_ventas, total_ordenes,
                    total_pickup, total_delivery, total_mesa,
                    monto_pickup, monto_delivery, monto_mesa
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, s.nombre, YEAR(o.fecha),
                    SUM(o.total), COUNT(*),
                    SUM(o.is_envio = 0), SUM(o.is_envio = 1), SUM(o.is_envio = 2),
                    SUM(CASE WHEN o.is_envio = 0 THEN o.total ELSE 0 END),
                    SUM(CASE WHEN o.is_envio = 1 THEN o.total ELSE 0 END),
                    SUM(CASE WHEN o.is_envio = 2 THEN o.total ELSE 0 END)
               FROM tb_orden_cabecera o
               INNER JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) < $anio_limite
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha)
               ON DUPLICATE KEY UPDATE
                    total_ventas    = VALUES(total_ventas),
                    total_ordenes   = VALUES(total_ordenes),
                    total_pickup    = VALUES(total_pickup),
                    total_delivery  = VALUES(total_delivery),
                    total_mesa      = VALUES(total_mesa),
                    monto_pickup    = VALUES(monto_pickup),
                    monto_delivery  = VALUES(monto_delivery),
                    monto_mesa      = VALUES(monto_mesa),
                    nombre_sucursal = VALUES(nombre_sucursal)";
        Conexion::ejecutar($q1, NULL);

        // 2) Clientes nuevos / recurrentes (agrupado por AÑO directo desde la data cruda,
        //    para no duplicar el conteo de clientes que compraron en varios meses del año)
        $q2 = "UPDATE resumen_ventas_anual r
               INNER JOIN (
                   SELECT
                        o.cod_empresa, o.cod_sucursal, YEAR(o.fecha) AS anio,
                        COUNT(DISTINCT CASE WHEN YEAR(p.primera_orden) = YEAR(o.fecha) THEN o.cod_usuario END) AS clientes_nuevos,
                        COUNT(DISTINCT CASE WHEN YEAR(p.primera_orden) < YEAR(o.fecha) THEN o.cod_usuario END) AS clientes_recurrentes
                   FROM tb_orden_cabecera o
                   INNER JOIN tmp_primera_orden p ON p.cod_empresa = o.cod_empresa AND p.cod_usuario = o.cod_usuario
                   WHERE o.cod_empresa = $cod_empresa
                     AND o.estado = 'ENTREGADA'
                     AND YEAR(o.fecha) < $anio_limite
                   GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha)
               ) sub ON r.cod_empresa = sub.cod_empresa AND r.cod_sucursal = sub.cod_sucursal AND r.anio = sub.anio
               SET r.clientes_nuevos      = sub.clientes_nuevos,
                   r.clientes_recurrentes = sub.clientes_recurrentes";
        Conexion::ejecutar($q2, NULL);

        // 3) Productos
        $q3 = "INSERT INTO resumen_productos_anual (
                    cod_empresa, cod_sucursal, anio,
                    cod_producto, nombre_producto, cantidad, total_ventas
               )
               SELECT
                    c.cod_empresa, c.cod_sucursal, YEAR(c.fecha),
                    d.cod_producto, p.nombre, SUM(d.cantidad), SUM(d.precio_final)
               FROM tb_orden_cabecera c
               INNER JOIN tb_orden_detalle d ON d.cod_orden = c.cod_orden
               INNER JOIN tb_productos     p ON p.cod_producto = d.cod_producto
               WHERE c.cod_empresa = $cod_empresa
                 AND c.estado = 'ENTREGADA'
                 AND YEAR(c.fecha) < $anio_limite
               GROUP BY c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), d.cod_producto
               ON DUPLICATE KEY UPDATE
                    cantidad        = VALUES(cantidad),
                    total_ventas    = VALUES(total_ventas),
                    nombre_producto = VALUES(nombre_producto)";
        Conexion::ejecutar($q3, NULL);

        // 4) Horas
        $q4 = "INSERT INTO resumen_horas_anual (
                    cod_empresa, cod_sucursal, anio, hora, total_ordenes, total_ventas
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), HOUR(o.fecha), COUNT(*), SUM(o.total)
               FROM tb_orden_cabecera o
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) < $anio_limite
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), HOUR(o.fecha)
               ON DUPLICATE KEY UPDATE
                    total_ordenes = VALUES(total_ordenes),
                    total_ventas  = VALUES(total_ventas)";
        Conexion::ejecutar($q4, NULL);

        // 5) Medios / canales
        $q5 = "INSERT INTO resumen_medios_anual (
                    cod_empresa, cod_sucursal, anio, medio_compra, total_ordenes, total_ventas
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), o.medio_compra, COUNT(*), SUM(o.total)
               FROM tb_orden_cabecera o
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) < $anio_limite
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), o.medio_compra
               ON DUPLICATE KEY UPDATE
                    total_ordenes = VALUES(total_ordenes),
                    total_ventas  = VALUES(total_ventas)";
        Conexion::ejecutar($q5, NULL);

        return true;
    }
}
?>
