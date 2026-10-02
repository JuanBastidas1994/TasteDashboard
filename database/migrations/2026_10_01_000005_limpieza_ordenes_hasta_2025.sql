-- =====================================================================
-- LIMPIEZA de órdenes hasta 2025 (todas las empresas). DESTRUCTIVO.
--
-- Requisitos:
--   1. Respaldo completo descargado y verificado.
--   2. Haber corrido 2026_10_01_000004_colapso_historico_global_hasta_2025.sql
--      con la validación en 0.
--
-- Protección: una orden ENTREGADA solo entra a la purga si su
-- empresa/sucursal/año/mes existe en resumen_ventas_mensual. Si el colapso
-- no se corrió, esas órdenes no se tocan.
--
-- NO se borran (tablas que alimentan saldos, límites o temas legales):
--   tb_cliente_dinero, tb_clientes_saldos, tb_clientes_puntos, tb_orden_puntos
--     -> el saldo y los puntos del cliente se calculan con SUM() sobre ellas
--   tb_usuario_purchase_code, tb_usuario_giftcard, tb_orden_cuponera
--     -> códigos, giftcards y límites de uso de cupones
--   tb_orden_calificacion, tb_orden_inventario
--   tb_orden_factura_electronica, tb_orden_datos_facturacion -> respaldo tributario
--   tmp_primera_orden -> clientes nuevos vs recurrentes del colapso futuro
--   carrito_sesion
-- =====================================================================

SET @desde = '2000-01-01';
SET @corte = '2026-01-01';

-- ---------------------------------------------------------------------
-- ANTES: filas por tabla y lo que muestra el dashboard (guardar resultado)
-- ---------------------------------------------------------------------
SELECT 'ANTES' AS momento, t.* FROM (
    SELECT 'tb_orden_cabecera' tabla, COUNT(*) filas FROM tb_orden_cabecera UNION ALL
    SELECT 'tb_orden_detalle',          COUNT(*) FROM tb_orden_detalle UNION ALL
    SELECT 'tb_orden_historial',        COUNT(*) FROM tb_orden_historial UNION ALL
    SELECT 'tb_orden_pagos',            COUNT(*) FROM tb_orden_pagos UNION ALL
    SELECT 'tb_orden_motorizado',       COUNT(*) FROM tb_orden_motorizado UNION ALL
    SELECT 'tb_preorden_json',          COUNT(*) FROM tb_preorden_json UNION ALL
    SELECT 'tb_cliente_dinero (NO se toca)',  COUNT(*) FROM tb_cliente_dinero UNION ALL
    SELECT 'tb_clientes_puntos (NO se toca)', COUNT(*) FROM tb_clientes_puntos
) t;

SELECT 'ANTES' AS momento, anio, SUM(total_ordenes) ordenes, ROUND(SUM(total_ventas), 2) ventas
FROM resumen_ventas_mensual GROUP BY anio ORDER BY anio;

-- ---------------------------------------------------------------------
-- 1) Lista de órdenes a purgar
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS tmp_purga_ordenes;
CREATE TABLE tmp_purga_ordenes (
    cod_orden INT NOT NULL PRIMARY KEY
) ENGINE=InnoDB;

INSERT INTO tmp_purga_ordenes (cod_orden)
SELECT o.cod_orden
FROM tb_orden_cabecera o
LEFT JOIN resumen_ventas_mensual r
       ON r.cod_empresa  = o.cod_empresa
      AND r.cod_sucursal = o.cod_sucursal
      AND r.anio = YEAR(o.fecha)
      AND r.mes  = MONTH(o.fecha)
WHERE o.fecha >= @desde AND o.fecha < @corte
  AND (o.estado <> 'ENTREGADA' OR r.id IS NOT NULL);

-- ordenes_protegidas_sin_colapso debería ser 0
SELECT
    (SELECT COUNT(*) FROM tmp_purga_ordenes) AS ordenes_a_purgar,
    (SELECT COUNT(*) FROM tb_orden_cabecera o
      WHERE o.fecha >= @desde AND o.fecha < @corte
        AND o.cod_orden NOT IN (SELECT cod_orden FROM tmp_purga_ordenes)) AS ordenes_protegidas_sin_colapso;

-- ---------------------------------------------------------------------
-- 2) Tablas hijas (antes de la cabecera; tb_orden_incidencia tiene FK)
-- ---------------------------------------------------------------------
DELETE t FROM tb_orden_detalle           t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_evento            t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_historial         t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_motorizado        t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_pagos             t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_runfood           t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_motorizado_asignacion   t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_motorizado_link         t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_ordenes_flota           t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_cancelacion       t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_courier_canceled  t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_errores           t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_recipientes       t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_incidencia        t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_json_entrante     t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_orden_destino           t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE t FROM tb_preorden_token_json     t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;

-- Preórdenes (la tabla más pesada): las ligadas a órdenes purgadas y las
-- que nunca llegaron a ser orden, creadas antes del corte.
DELETE t FROM tb_preorden_json t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;
DELETE FROM tb_preorden_json
WHERE fecha_create < @corte
  AND (cod_orden IS NULL OR cod_orden = 0);

-- ---------------------------------------------------------------------
-- 3) Cabecera
-- ---------------------------------------------------------------------
DELETE t FROM tb_orden_cabecera t INNER JOIN tmp_purga_ordenes p ON p.cod_orden = t.cod_orden;

DROP TABLE IF EXISTS tmp_purga_ordenes;

-- ---------------------------------------------------------------------
-- DESPUÉS: mismas consultas. El resumen del dashboard debe ser idéntico
-- al de ANTES; las tablas de saldo/puntos no deben cambiar.
-- ---------------------------------------------------------------------
SELECT 'DESPUES' AS momento, t.* FROM (
    SELECT 'tb_orden_cabecera' tabla, COUNT(*) filas FROM tb_orden_cabecera UNION ALL
    SELECT 'tb_orden_detalle',          COUNT(*) FROM tb_orden_detalle UNION ALL
    SELECT 'tb_orden_historial',        COUNT(*) FROM tb_orden_historial UNION ALL
    SELECT 'tb_orden_pagos',            COUNT(*) FROM tb_orden_pagos UNION ALL
    SELECT 'tb_orden_motorizado',       COUNT(*) FROM tb_orden_motorizado UNION ALL
    SELECT 'tb_preorden_json',          COUNT(*) FROM tb_preorden_json UNION ALL
    SELECT 'tb_cliente_dinero (NO se toca)',  COUNT(*) FROM tb_cliente_dinero UNION ALL
    SELECT 'tb_clientes_puntos (NO se toca)', COUNT(*) FROM tb_clientes_puntos
) t;

SELECT 'DESPUES' AS momento, anio, SUM(total_ordenes) ordenes, ROUND(SUM(total_ventas), 2) ventas
FROM resumen_ventas_mensual GROUP BY anio ORDER BY anio;

-- ---------------------------------------------------------------------
-- 4) Recuperar espacio en disco (opcional; reconstruye cada tabla, correr
--    en horario de poco tráfico)
-- ---------------------------------------------------------------------
-- OPTIMIZE TABLE tb_preorden_json, tb_orden_detalle, tb_orden_cabecera,
--                tb_orden_historial, tb_orden_motorizado, tb_orden_pagos,
--                tb_orden_runfood, tb_motorizado_asignacion, tb_ordenes_flota;
