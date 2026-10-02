-- =====================================================================
-- Colapso histórico GLOBAL (todas las empresas) de órdenes hasta 2025.
--
-- Llena resumen_*_mensual (lo que lee el dashboard histórico) y
-- resumen_*_anual para todo el rango [@desde, @corte), y recalcula
-- tmp_primera_orden con TODAS las órdenes. Es idempotente: se puede
-- correr varias veces (ON DUPLICATE KEY UPDATE).
--
-- Mismas reglas que clases/cl_colapso_historico.php, con estas diferencias:
--   * LEFT JOIN a tb_sucursales / tb_productos: órdenes de sucursales o
--     productos eliminados no se pierden del resumen.
--   * Clientes del mes con COUNT(DISTINCT cod_usuario): el colapso por
--     pantalla cuenta 2 veces a un cliente con 2 órdenes en el mismo mes.
--
-- Correr ANTES de 2026_10_01_000005_limpieza_ordenes_hasta_2025.sql y
-- revisar que la validación del final dé diferencias en 0.
-- =====================================================================

SET @desde = '2000-01-01';
SET @corte = '2026-01-01';

-- ---------------------------------------------------------------------
-- 0) Primera orden de cada cliente (NUNCA se borra: la usa el colapso
--    mensual futuro para distinguir clientes nuevos vs recurrentes)
-- ---------------------------------------------------------------------
INSERT INTO tmp_primera_orden (cod_empresa, cod_usuario, primera_orden)
SELECT cod_empresa, cod_usuario, DATE(MIN(fecha))
FROM tb_orden_cabecera
WHERE fecha >= @desde
  AND cod_empresa IS NOT NULL
  AND cod_usuario IS NOT NULL
GROUP BY cod_empresa, cod_usuario
ON DUPLICATE KEY UPDATE
    primera_orden = LEAST(primera_orden, VALUES(primera_orden));

-- ---------------------------------------------------------------------
-- 1) MENSUAL: totales y tipos de entrega
-- ---------------------------------------------------------------------
INSERT INTO resumen_ventas_mensual (
    cod_empresa, cod_sucursal, nombre_sucursal, anio, mes,
    total_ventas, total_ordenes,
    total_pickup, total_delivery, total_mesa,
    monto_pickup, monto_delivery, monto_mesa
)
SELECT
    o.cod_empresa, o.cod_sucursal, COALESCE(MAX(s.nombre), ''), YEAR(o.fecha), MONTH(o.fecha),
    SUM(o.total), COUNT(*),
    SUM(o.is_envio = 0), SUM(o.is_envio = 1), SUM(o.is_envio = 2),
    SUM(CASE WHEN o.is_envio = 0 THEN o.total ELSE 0 END),
    SUM(CASE WHEN o.is_envio = 1 THEN o.total ELSE 0 END),
    SUM(CASE WHEN o.is_envio = 2 THEN o.total ELSE 0 END)
FROM tb_orden_cabecera o
LEFT JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
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
    nombre_sucursal = VALUES(nombre_sucursal);

-- 2) MENSUAL: clientes nuevos / recurrentes
UPDATE resumen_ventas_mensual r
INNER JOIN (
    SELECT
        o.cod_empresa, o.cod_sucursal, YEAR(o.fecha) AS anio, MONTH(o.fecha) AS mes,
        COUNT(DISTINCT CASE WHEN p.primera_orden >= DATE_FORMAT(o.fecha, '%Y-%m-01')
                            THEN o.cod_usuario END) AS clientes_nuevos,
        COUNT(DISTINCT CASE WHEN p.primera_orden <  DATE_FORMAT(o.fecha, '%Y-%m-01')
                            THEN o.cod_usuario END) AS clientes_recurrentes
    FROM tb_orden_cabecera o
    INNER JOIN tmp_primera_orden p ON p.cod_empresa = o.cod_empresa AND p.cod_usuario = o.cod_usuario
    WHERE o.estado = 'ENTREGADA'
      AND o.fecha >= @desde AND o.fecha < @corte
    GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha)
) sub ON r.cod_empresa = sub.cod_empresa AND r.cod_sucursal = sub.cod_sucursal
     AND r.anio = sub.anio AND r.mes = sub.mes
SET r.clientes_nuevos      = sub.clientes_nuevos,
    r.clientes_recurrentes = sub.clientes_recurrentes;

-- 3) MENSUAL: productos
INSERT INTO resumen_productos_mensual (
    cod_empresa, cod_sucursal, anio, mes,
    cod_producto, nombre_producto, cantidad, total_ventas
)
SELECT
    c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), MONTH(c.fecha),
    d.cod_producto, LEFT(COALESCE(MAX(p.nombre), MAX(d.descripcion), ''), 250),
    SUM(d.cantidad), SUM(d.precio_final)
FROM tb_orden_cabecera c
INNER JOIN tb_orden_detalle d ON d.cod_orden    = c.cod_orden
LEFT  JOIN tb_productos     p ON p.cod_producto = d.cod_producto
WHERE c.estado = 'ENTREGADA'
  AND c.fecha >= @desde AND c.fecha < @corte
  AND d.cod_producto IS NOT NULL
GROUP BY c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), MONTH(c.fecha), d.cod_producto
ON DUPLICATE KEY UPDATE
    cantidad        = VALUES(cantidad),
    total_ventas    = VALUES(total_ventas),
    nombre_producto = VALUES(nombre_producto);

-- 4) MENSUAL: horas
INSERT INTO resumen_horas_mensual (
    cod_empresa, cod_sucursal, anio, mes, hora, total_ordenes, total_ventas
)
SELECT
    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), HOUR(o.fecha),
    COUNT(*), SUM(o.total)
FROM tb_orden_cabecera o
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), HOUR(o.fecha)
ON DUPLICATE KEY UPDATE
    total_ordenes = VALUES(total_ordenes),
    total_ventas  = VALUES(total_ventas);

-- 5) MENSUAL: medios / canales
INSERT INTO resumen_medios_mensual (
    cod_empresa, cod_sucursal, anio, mes, medio_compra, total_ordenes, total_ventas
)
SELECT
    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha),
    COALESCE(o.medio_compra, 'OTRO'), COUNT(*), SUM(o.total)
FROM tb_orden_cabecera o
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), MONTH(o.fecha), COALESCE(o.medio_compra, 'OTRO')
ON DUPLICATE KEY UPDATE
    total_ordenes = VALUES(total_ordenes),
    total_ventas  = VALUES(total_ventas);

-- ---------------------------------------------------------------------
-- 6) ANUAL: totales y tipos de entrega
-- ---------------------------------------------------------------------
INSERT INTO resumen_ventas_anual (
    cod_empresa, cod_sucursal, nombre_sucursal, anio,
    total_ventas, total_ordenes,
    total_pickup, total_delivery, total_mesa,
    monto_pickup, monto_delivery, monto_mesa
)
SELECT
    o.cod_empresa, o.cod_sucursal, COALESCE(MAX(s.nombre), ''), YEAR(o.fecha),
    SUM(o.total), COUNT(*),
    SUM(o.is_envio = 0), SUM(o.is_envio = 1), SUM(o.is_envio = 2),
    SUM(CASE WHEN o.is_envio = 0 THEN o.total ELSE 0 END),
    SUM(CASE WHEN o.is_envio = 1 THEN o.total ELSE 0 END),
    SUM(CASE WHEN o.is_envio = 2 THEN o.total ELSE 0 END)
FROM tb_orden_cabecera o
LEFT JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
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
    nombre_sucursal = VALUES(nombre_sucursal);

-- 7) ANUAL: clientes nuevos / recurrentes
UPDATE resumen_ventas_anual r
INNER JOIN (
    SELECT
        o.cod_empresa, o.cod_sucursal, YEAR(o.fecha) AS anio,
        COUNT(DISTINCT CASE WHEN YEAR(p.primera_orden) = YEAR(o.fecha) THEN o.cod_usuario END) AS clientes_nuevos,
        COUNT(DISTINCT CASE WHEN YEAR(p.primera_orden) < YEAR(o.fecha) THEN o.cod_usuario END) AS clientes_recurrentes
    FROM tb_orden_cabecera o
    INNER JOIN tmp_primera_orden p ON p.cod_empresa = o.cod_empresa AND p.cod_usuario = o.cod_usuario
    WHERE o.estado = 'ENTREGADA'
      AND o.fecha >= @desde AND o.fecha < @corte
    GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha)
) sub ON r.cod_empresa = sub.cod_empresa AND r.cod_sucursal = sub.cod_sucursal AND r.anio = sub.anio
SET r.clientes_nuevos      = sub.clientes_nuevos,
    r.clientes_recurrentes = sub.clientes_recurrentes;

-- 8) ANUAL: productos
INSERT INTO resumen_productos_anual (
    cod_empresa, cod_sucursal, anio,
    cod_producto, nombre_producto, cantidad, total_ventas
)
SELECT
    c.cod_empresa, c.cod_sucursal, YEAR(c.fecha),
    d.cod_producto, LEFT(COALESCE(MAX(p.nombre), MAX(d.descripcion), ''), 250),
    SUM(d.cantidad), SUM(d.precio_final)
FROM tb_orden_cabecera c
INNER JOIN tb_orden_detalle d ON d.cod_orden    = c.cod_orden
LEFT  JOIN tb_productos     p ON p.cod_producto = d.cod_producto
WHERE c.estado = 'ENTREGADA'
  AND c.fecha >= @desde AND c.fecha < @corte
  AND d.cod_producto IS NOT NULL
GROUP BY c.cod_empresa, c.cod_sucursal, YEAR(c.fecha), d.cod_producto
ON DUPLICATE KEY UPDATE
    cantidad        = VALUES(cantidad),
    total_ventas    = VALUES(total_ventas),
    nombre_producto = VALUES(nombre_producto);

-- 9) ANUAL: horas
INSERT INTO resumen_horas_anual (
    cod_empresa, cod_sucursal, anio, hora, total_ordenes, total_ventas
)
SELECT
    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), HOUR(o.fecha), COUNT(*), SUM(o.total)
FROM tb_orden_cabecera o
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), HOUR(o.fecha)
ON DUPLICATE KEY UPDATE
    total_ordenes = VALUES(total_ordenes),
    total_ventas  = VALUES(total_ventas);

-- 10) ANUAL: medios / canales
INSERT INTO resumen_medios_anual (
    cod_empresa, cod_sucursal, anio, medio_compra, total_ordenes, total_ventas
)
SELECT
    o.cod_empresa, o.cod_sucursal, YEAR(o.fecha),
    COALESCE(o.medio_compra, 'OTRO'), COUNT(*), SUM(o.total)
FROM tb_orden_cabecera o
WHERE o.estado = 'ENTREGADA'
  AND o.fecha >= @desde AND o.fecha < @corte
GROUP BY o.cod_empresa, o.cod_sucursal, YEAR(o.fecha), COALESCE(o.medio_compra, 'OTRO')
ON DUPLICATE KEY UPDATE
    total_ordenes = VALUES(total_ordenes),
    total_ventas  = VALUES(total_ventas);

-- ---------------------------------------------------------------------
-- VALIDACIÓN: órdenes crudas vs resúmenes, por año. Todas las columnas
-- dif_* deben dar 0 antes de correr la limpieza.
-- ---------------------------------------------------------------------
SELECT o.anio, o.ordenes, o.ventas,
       o.ordenes - COALESCE(m.ordenes, 0) AS dif_ordenes_mensual,
       ROUND(o.ventas - COALESCE(m.ventas, 0), 2) AS dif_ventas_mensual,
       o.ordenes - COALESCE(a.ordenes, 0) AS dif_ordenes_anual,
       ROUND(o.ventas - COALESCE(a.ventas, 0), 2) AS dif_ventas_anual
FROM (
    SELECT YEAR(fecha) anio, COUNT(*) ordenes, SUM(total) ventas
    FROM tb_orden_cabecera
    WHERE estado = 'ENTREGADA' AND fecha >= @desde AND fecha < @corte
    GROUP BY YEAR(fecha)
) o
LEFT JOIN (SELECT anio, SUM(total_ordenes) ordenes, SUM(total_ventas) ventas
           FROM resumen_ventas_mensual GROUP BY anio) m ON m.anio = o.anio
LEFT JOIN (SELECT anio, SUM(total_ordenes) ordenes, SUM(total_ventas) ventas
           FROM resumen_ventas_anual GROUP BY anio) a ON a.anio = o.anio
ORDER BY o.anio;
