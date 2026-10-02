-- =====================================================================
-- LIMPIEZA de preórdenes y seguimiento GPS de motorizados. DESTRUCTIVO.
-- Correr DESPUÉS de 000008.
--
-- tb_preorden_json: es un log del checkout. Cuando el pago entra bien, el
--   paymentId y paymentAuth quedan en tb_orden_pagos (observacion /
--   observacion2), así que se conservan solo las del último trimestre
--   (reclamos y webhooks recientes de Nuvei/Datafast).
--
-- tb_motorizado_seguimiento: un punto por cada ping de ubicación
--   (~2.000-3.000 por día desde mayo 2026). Solo se lee el último ping de
--   cada motorizado (api_flotas getUltimoPing), así que se conservan 30 días.
--   El índice nuevo evita que esa consulta recorra toda la tabla.
-- =====================================================================

SET @corte_preorden = '2026-07-01';

SELECT 'ANTES' AS momento,
       (SELECT COUNT(*) FROM tb_preorden_json)          AS preorden_json,
       (SELECT COUNT(*) FROM tb_motorizado_seguimiento) AS motorizado_seguimiento;

DELETE FROM tb_preorden_json          WHERE fecha_create < @corte_preorden;
DELETE FROM tb_motorizado_seguimiento WHERE fecha < DATE_SUB(NOW(), INTERVAL 30 DAY);

ALTER TABLE tb_motorizado_seguimiento
    ADD INDEX idx_motorizado_seguimiento (cod_motorizado, cod_motorizado_seguimiento);

SELECT 'DESPUES' AS momento,
       (SELECT COUNT(*) FROM tb_preorden_json)          AS preorden_json,
       (SELECT COUNT(*) FROM tb_motorizado_seguimiento) AS motorizado_seguimiento;

-- Recuperar espacio en disco (opcional; correr en horario de poco tráfico)
-- OPTIMIZE TABLE tb_preorden_json, tb_motorizado_seguimiento;
