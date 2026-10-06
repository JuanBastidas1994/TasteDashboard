-- =====================================================================
-- DROP de tablas obsoletas (módulos que no son de Taste o ya no se usan).
-- 90 tablas (tb_telegram_usuarios se conserva), ~2.9 MB. Ninguna tiene FK ni vistas que dependan de ellas.
--
-- IMPORTANTE:
--   * Desplegar ANTES el código que deja de usarlas (telegram, menú digital,
--     clickup, helpdesk, etc.); si no, api / gestión / dashboard fallan.
--   * Correr DESPUÉS de 2026_10_01_000005_limpieza_ordenes_hasta_2025.sql
--     (esa limpieza borra filas de tb_orden_json_entrante y tb_preorden_token_json).
-- =====================================================================

-- A) Sin uso en ningún proyecto
DROP TABLE IF EXISTS
    datos_compra,
    tb_catalogos, tb_catalogo_items,
    tb_cierre_caja,
    tb_factura_cabecera, tb_factura_detalle, tb_factura_pagos,
    tb_rol_pagos,
    tb_visitantes,
    tb_shopping_car,
    tb_promociones, tb_promocion_pos,
    tb_usuario_cupon,
    tb_orden_json_entrante,
    tb_lista_deseos,
    tb_descuentos, tb_metodos_pago, tb_proveedores, tb_salsas,
    tb_galeria,
    tb_guia, tb_guia_pasos, tb_guia_usuario,
    tb_idioma_frases,
    tb_importar_categorias, tb_importar_productos,
    tb_empresa_stripe, tb_empresas_suscripciones,
    tb_pos_fidelizacion,
    tb_ptos_emision,
    tb_roles_empresa,
    tb_zonas_sucursal,
    tb_runfood_productos, tb_runfood_producto_opcion_detalle,
    tb_test,
    tb_web_servicios,
    tb_usuario_mis_giftcards,
    tb_paises, timezones,
    tb_prioridad, tb_tipo_correo;

-- B) Telegram (bot de grupos viejo) y menú digital
-- OJO: tb_telegram_usuarios NO se borra. La usan las notificaciones por Telegram a administradores
-- (permiso NOTIFY_TELEGRAM): aviso de nueva orden (api) y de orden asignada a la flota (api_gestion_ordenes).
DROP TABLE IF EXISTS
    tb_telegram, tb_telegram_grupos, tb_telegram_sucursal,
    tb_telegram_usuarios_ubicacion,
    tb_menu_digital, tb_menu_digital_imagenes;

-- C) Módulos de otros giros / funcionalidades muertas
DROP TABLE IF EXISTS
    -- agenda / agendamiento
    tb_agenda, tb_agenda_categorias, tb_disponibilidad, tb_indisponibilidad, tb_productos_usuarios,
    -- programas
    tb_programas, tb_programa_usuario,
    -- timelines
    tb_timelines, tb_timeline_detalles,
    -- novedades del dashboard
    tb_updates, tb_updates_detalle, tb_updates_visualizado,
    -- helpdesk / notificaciones del sistema
    tb_dashboard_helpdesk, tb_system_notification, tb_system_notification_tipos,
    -- servicios / personal
    tb_personal, tb_productos_detalle,
    -- payphone viejo y cobros a empresas dentro de Taste
    tb_transaccion, log_pagos, mie_log_pago, mie_log_pago_error, mie_log_pago_success,
    -- facturación móvil
    tb_factmovil_empresa, tb_cliente_facturacion,
    -- demos / planes
    tb_demos, tb_planes, tb_empresa_tarjeta,
    -- clickup
    tb_empresa_clickup,
    -- kiosco, descripciones, archivos, preferencias e inventario viejo
    tb_productos_kiosco, tb_productos_descripciones, tb_productos_archivos, tb_productos_preferencia,
    tb_inventario, tb_stock,
    -- varios
    log_error_app,
    tb_cotizaciones_json,
    tb_modal_eventos,
    tb_usuario_giftcards_compradas,
    tb_preorden_token_json,
    tb_idiomas,
    tb_usuario_client_email,
    tb_usuario_cliente,
    tb_size_crop;
