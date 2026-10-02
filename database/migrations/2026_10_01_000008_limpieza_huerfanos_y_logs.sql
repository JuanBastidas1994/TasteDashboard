-- =====================================================================
-- LIMPIEZA de huérfanos de órdenes y de logs / registros vencidos.
-- DESTRUCTIVO. Correr DESPUÉS de 000005 (limpieza de órdenes hasta 2025).
--
-- Huérfano = fila hija cuyo cod_orden ya no existe en tb_orden_cabecera
-- (incluye cod_orden = 0 / NULL).
--
-- NO se tocan (aunque tengan huérfanos):
--   tb_orden_puntos, tb_cliente_dinero, tb_clientes_puntos, tb_clientes_saldos
--     -> saldo y puntos del cliente se calculan con SUM() sobre ellas
--   tb_usuario_purchase_code -> códigos de compra
--   tb_orden_factura_electronica, tb_orden_datos_facturacion -> respaldo tributario
--   tb_preorden_json sin orden -> incluye preórdenes PAGADA sin orden (reclamos)
--   carrito_sesion, tb_cotizacion_precio, tb_cotizacion_envio_log -> todo es 2026
-- =====================================================================

SET @corte = '2026-01-01';

-- ---------------------------------------------------------------------
-- ANTES
-- ---------------------------------------------------------------------
SELECT 'ANTES' AS momento, t.* FROM (
    SELECT 'tb_orden_historial' tabla, COUNT(*) filas FROM tb_orden_historial UNION ALL
    SELECT 'tb_orden_pagos',                 COUNT(*) FROM tb_orden_pagos UNION ALL
    SELECT 'tb_orden_detalle',               COUNT(*) FROM tb_orden_detalle UNION ALL
    SELECT 'tb_orden_motorizado',            COUNT(*) FROM tb_orden_motorizado UNION ALL
    SELECT 'tb_orden_calificacion',          COUNT(*) FROM tb_orden_calificacion UNION ALL
    SELECT 'tb_motorizado_seguimiento',      COUNT(*) FROM tb_motorizado_seguimiento UNION ALL
    SELECT 'tb_usuario_codigo_registro',     COUNT(*) FROM tb_usuario_codigo_registro UNION ALL
    SELECT 'tb_cupones_usuarios',            COUNT(*) FROM tb_cupones_usuarios UNION ALL
    SELECT 'auth_tokens',                    COUNT(*) FROM auth_tokens UNION ALL
    SELECT 'tb_notificaciones',              COUNT(*) FROM tb_notificaciones UNION ALL
    SELECT 'tb_usuario_intento_pago',        COUNT(*) FROM tb_usuario_intento_pago UNION ALL
    SELECT 'tb_cliente_dinero (NO se toca)', COUNT(*) FROM tb_cliente_dinero UNION ALL
    SELECT 'tb_clientes_puntos (NO se toca)',COUNT(*) FROM tb_clientes_puntos
) t;

-- ---------------------------------------------------------------------
-- 1) Huérfanos de órdenes
-- ---------------------------------------------------------------------
DELETE t FROM tb_orden_detalle          t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_historial        t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_pagos            t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_motorizado       t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_motorizado_asignacion  t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_motorizado_link        t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_cancelacion      t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_courier_canceled t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_errores          t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_recipientes      t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_calificacion     t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_inventario       t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;
DELETE t FROM tb_orden_cuponera         t LEFT JOIN tb_orden_cabecera c ON c.cod_orden = t.cod_orden WHERE c.cod_orden IS NULL;

-- ---------------------------------------------------------------------
-- 2) Logs y registros vencidos
-- ---------------------------------------------------------------------
DELETE FROM tb_motorizado_seguimiento     WHERE fecha < @corte;
DELETE FROM tb_notificaciones             WHERE fecha < @corte;
DELETE FROM tb_producto_agotado_historial WHERE fecha_inicio < @corte;
DELETE FROM tb_usuario_intento_pago       WHERE fecha < @corte;
DELETE FROM mie_auth_intent_login         WHERE fecha < @corte;

DELETE FROM tb_usuario_codigo_registro WHERE fecha_expiracion < NOW();
DELETE FROM tb_usuario_codigo_login    WHERE fecha_expiracion < NOW();
DELETE FROM tb_usuario_codigo_telefono WHERE fecha_expiracion < NOW();
DELETE FROM auth_tokens                WHERE fecha_expiracion < NOW();
DELETE FROM tb_cupones_usuarios        WHERE fecha_caducidad < NOW();
DELETE FROM tb_usuario_bloqueo         WHERE fecha_fin < NOW();

-- ---------------------------------------------------------------------
-- DESPUÉS (saldo y puntos no deben cambiar)
-- ---------------------------------------------------------------------
SELECT 'DESPUES' AS momento, t.* FROM (
    SELECT 'tb_orden_historial' tabla, COUNT(*) filas FROM tb_orden_historial UNION ALL
    SELECT 'tb_orden_pagos',                 COUNT(*) FROM tb_orden_pagos UNION ALL
    SELECT 'tb_orden_detalle',               COUNT(*) FROM tb_orden_detalle UNION ALL
    SELECT 'tb_orden_motorizado',            COUNT(*) FROM tb_orden_motorizado UNION ALL
    SELECT 'tb_orden_calificacion',          COUNT(*) FROM tb_orden_calificacion UNION ALL
    SELECT 'tb_motorizado_seguimiento',      COUNT(*) FROM tb_motorizado_seguimiento UNION ALL
    SELECT 'tb_usuario_codigo_registro',     COUNT(*) FROM tb_usuario_codigo_registro UNION ALL
    SELECT 'tb_cupones_usuarios',            COUNT(*) FROM tb_cupones_usuarios UNION ALL
    SELECT 'auth_tokens',                    COUNT(*) FROM auth_tokens UNION ALL
    SELECT 'tb_notificaciones',              COUNT(*) FROM tb_notificaciones UNION ALL
    SELECT 'tb_usuario_intento_pago',        COUNT(*) FROM tb_usuario_intento_pago UNION ALL
    SELECT 'tb_cliente_dinero (NO se toca)', COUNT(*) FROM tb_cliente_dinero UNION ALL
    SELECT 'tb_clientes_puntos (NO se toca)',COUNT(*) FROM tb_clientes_puntos
) t;

-- ---------------------------------------------------------------------
-- 3) Recuperar espacio en disco (opcional; correr en horario de poco tráfico)
-- ---------------------------------------------------------------------
-- OPTIMIZE TABLE tb_motorizado_seguimiento, tb_orden_historial, tb_orden_pagos,
--                tb_usuario_codigo_registro, tb_usuario_codigo_login, auth_tokens,
--                tb_notificaciones, tb_usuario_intento_pago, tb_cupones_usuarios,
--                tb_producto_agotado_historial, mie_auth_intent_login;
