<?php

class cl_colapso_historico {

    var $session;

    public function __construct() {
        $this->session = getSession();
    }

    public function getEstado($cod_empresa, $anio_limite) {
        $anio_vigente = intval(date('Y'));

        $qMensual      = "SELECT DISTINCT anio FROM resumen_ventas_mensual WHERE cod_empresa = $cod_empresa ORDER BY anio";
        $qAnual        = "SELECT DISTINCT anio FROM resumen_ventas_anual   WHERE cod_empresa = $cod_empresa ORDER BY anio";
        $qOrigen       = "SELECT DISTINCT YEAR(fecha) AS anio
                          FROM tb_orden_cabecera
                          WHERE cod_empresa = $cod_empresa AND YEAR(fecha) < $anio_limite
                          ORDER BY anio";
        $qMesesVigente = "SELECT DISTINCT mes FROM resumen_ventas_mensual
                          WHERE cod_empresa = $cod_empresa AND anio = $anio_vigente
                          ORDER BY mes";

        $mensual       = array_column(Conexion::buscarVariosRegistro($qMensual), 'anio');
        $anual         = array_column(Conexion::buscarVariosRegistro($qAnual), 'anio');
        $origen        = array_column(Conexion::buscarVariosRegistro($qOrigen), 'anio');
        $meses_vigente = array_column(Conexion::buscarVariosRegistro($qMesesVigente), 'mes');

        $pendientes = array_values(array_diff($origen, $anual));

        return [
            'anio_limite'   => $anio_limite,
            'mensual'       => $mensual,
            'anual'         => $anual,
            'pendientes'    => $pendientes,
            'meses_vigente' => $meses_vigente,
            'anio_vigente'  => $anio_vigente,
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

    public function ejecutarColapsoMensual($cod_empresa, $anio, $mes) {

        // PASO 1: asegurar tmp_primera_orden para esta empresa
        $q1 = "INSERT INTO tmp_primera_orden (cod_empresa, cod_usuario, primera_orden)
               SELECT cod_empresa, cod_usuario, DATE(MIN(fecha))
               FROM tb_orden_cabecera
               WHERE cod_empresa = $cod_empresa
               GROUP BY cod_empresa, cod_usuario
               ON DUPLICATE KEY UPDATE
                   primera_orden = LEAST(primera_orden, VALUES(primera_orden))";
        Conexion::ejecutar($q1, NULL);

        // PASO 2: totales y tipos de entrega
        $q2 = "INSERT INTO resumen_ventas_mensual (
                    cod_empresa, cod_sucursal, nombre_sucursal, anio, mes,
                    total_ventas, total_ordenes,
                    total_pickup, total_delivery, total_mesa,
                    monto_pickup, monto_delivery, monto_mesa
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, s.nombre, YEAR(o.fecha), MONTH(o.fecha),
                    SUM(o.total), COUNT(*),
                    SUM(o.is_envio = 0), SUM(o.is_envio = 1), SUM(o.is_envio = 2),
                    SUM(CASE WHEN o.is_envio = 0 THEN o.total ELSE 0 END),
                    SUM(CASE WHEN o.is_envio = 1 THEN o.total ELSE 0 END),
                    SUM(CASE WHEN o.is_envio = 2 THEN o.total ELSE 0 END)
               FROM tb_orden_cabecera o
               INNER JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) = $anio
                 AND MONTH(o.fecha) = $mes
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha)
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
        Conexion::ejecutar($q2, NULL);

        // PASO 3: clientes nuevos vs recurrentes
        $q3 = "UPDATE resumen_ventas_mensual r
               INNER JOIN (
                   SELECT
                        o.cod_empresa, o.cod_sucursal,
                        YEAR(o.fecha) AS anio, MONTH(o.fecha) AS mes,
                        SUM(CASE
                            WHEN YEAR(p.primera_orden)  = YEAR(o.fecha)
                             AND MONTH(p.primera_orden) = MONTH(o.fecha)
                            THEN 1 ELSE 0
                        END) AS clientes_nuevos,
                        SUM(CASE
                            WHEN p.primera_orden < DATE_FORMAT(o.fecha, '%Y-%m-01')
                            THEN 1 ELSE 0
                        END) AS clientes_recurrentes
                   FROM (
                       SELECT DISTINCT cod_empresa, cod_sucursal, cod_usuario, fecha
                       FROM tb_orden_cabecera
                       WHERE cod_empresa = $cod_empresa
                         AND estado = 'ENTREGADA'
                         AND YEAR(fecha) = $anio
                         AND MONTH(fecha) = $mes
                   ) o
                   INNER JOIN tmp_primera_orden p
                          ON p.cod_empresa = o.cod_empresa
                         AND p.cod_usuario = o.cod_usuario
                   GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha)
               ) sub ON r.cod_empresa  = sub.cod_empresa
                    AND r.cod_sucursal = sub.cod_sucursal
                    AND r.anio = sub.anio
                    AND r.mes  = sub.mes
               SET r.clientes_nuevos      = sub.clientes_nuevos,
                   r.clientes_recurrentes = sub.clientes_recurrentes";
        Conexion::ejecutar($q3, NULL);

        // PASO 4: productos más vendidos
        $q4 = "INSERT INTO resumen_productos_mensual (
                    cod_empresa, cod_sucursal, anio, mes,
                    cod_producto, nombre_producto, cantidad, total_ventas
               )
               SELECT
                    c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), MONTH(c.fecha),
                    d.cod_producto, p.nombre, SUM(d.cantidad), SUM(d.precio_final)
               FROM tb_orden_cabecera c
               INNER JOIN tb_orden_detalle d ON d.cod_orden    = c.cod_orden
               INNER JOIN tb_productos     p ON p.cod_producto = d.cod_producto
               WHERE c.cod_empresa = $cod_empresa
                 AND c.estado = 'ENTREGADA'
                 AND YEAR(c.fecha) = $anio
                 AND MONTH(c.fecha) = $mes
               GROUP BY c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), MONTH(c.fecha), d.cod_producto
               ON DUPLICATE KEY UPDATE
                    cantidad        = VALUES(cantidad),
                    total_ventas    = VALUES(total_ventas),
                    nombre_producto = VALUES(nombre_producto)";
        Conexion::ejecutar($q4, NULL);

        // PASO 5: horas pico
        $q5 = "INSERT INTO resumen_horas_mensual (
                    cod_empresa, cod_sucursal, anio, mes, hora,
                    total_ordenes, total_ventas
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), HOUR(o.fecha),
                    COUNT(*), SUM(o.total)
               FROM tb_orden_cabecera o
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) = $anio
                 AND MONTH(o.fecha) = $mes
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), HOUR(o.fecha)
               ON DUPLICATE KEY UPDATE
                    total_ordenes = VALUES(total_ordenes),
                    total_ventas  = VALUES(total_ventas)";
        Conexion::ejecutar($q5, NULL);

        // PASO 6: medios / canales
        $q6 = "INSERT INTO resumen_medios_mensual (
                    cod_empresa, cod_sucursal, anio, mes,
                    medio_compra, total_ordenes, total_ventas
               )
               SELECT
                    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha),
                    COALESCE(o.medio_compra, 'OTRO'), COUNT(*), SUM(o.total)
               FROM tb_orden_cabecera o
               WHERE o.cod_empresa = $cod_empresa
                 AND o.estado = 'ENTREGADA'
                 AND YEAR(o.fecha) = $anio
                 AND MONTH(o.fecha) = $mes
               GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), o.medio_compra
               ON DUPLICATE KEY UPDATE
                    total_ordenes = VALUES(total_ordenes),
                    total_ventas  = VALUES(total_ventas)";
        Conexion::ejecutar($q6, NULL);

        return true;
    }
}
?>
