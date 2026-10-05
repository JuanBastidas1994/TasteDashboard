-- Schema completo de Taste. GENERADO con: php bin/migrate.php schema:dump
-- No editar a mano: los cambios van en database/migrations/ y luego se regenera este archivo.
-- Generado: 2026-10-05 06:01:17 desde la BD 'prod_snapshot_20261001'

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `auth_tokens` (
  `cod_token` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `token` varchar(300) DEFAULT NULL,
  `navigator` varchar(25) NOT NULL,
  `operative_system` varchar(25) NOT NULL,
  `is_mobile` int NOT NULL DEFAULT '0',
  `fecha_creacion` date DEFAULT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `carrito_sesion` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cart_token` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_empresa` int unsigned NOT NULL,
  `cod_sucursal` int unsigned DEFAULT NULL,
  `cod_usuario` int unsigned DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `origen` enum('WEB','APP') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'WEB',
  `estado` enum('ACTIVO','ABANDONADO','CONVERTIDO','EXPIRADO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `total_estimado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cantidad_productos` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `abandoned_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `converted_at` datetime DEFAULT NULL,
  `recovery_source` enum('EMAIL','PUSH','WHATSAPP') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_preorden` int unsigned DEFAULT NULL,
  `cod_orden` int unsigned DEFAULT NULL,
  `cart_json` json DEFAULT NULL COMMENT 'Snapshot completo del intento de compra',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_token` (`cart_token`),
  KEY `idx_empresa_estado` (`cod_empresa`,`estado`),
  KEY `idx_empresa_fecha` (`cod_empresa`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `currency` (
  `iso` char(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`iso`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `mie_auth_intent_login` (
  `cod_intent_login` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `ip` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `success` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_intent_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promocion_producto` (
  `cod_promocion` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promocion_recompensa` (
  `cod_recompensa` int NOT NULL AUTO_INCREMENT,
  `cod_promocion` int NOT NULL,
  `cod_producto_regalo` int NOT NULL,
  `cantidad_regalo` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`cod_recompensa`),
  KEY `cod_promocion` (`cod_promocion`),
  CONSTRAINT `promocion_recompensa_ibfk_1` FOREIGN KEY (`cod_promocion`) REFERENCES `promociones` (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promocion_recurrente` (
  `cod_promocion` int DEFAULT NULL,
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado','domingo') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promocion_sucursal` (
  `cod_promocion` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promocion_tipo_entrega` (
  `cod_promocion` int NOT NULL,
  `tipo_entrega` enum('DELIVERY','PICKUP','EN_MESA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_promocion`,`tipo_entrega`),
  CONSTRAINT `promocion_tipo_entrega_ibfk_1` FOREIGN KEY (`cod_promocion`) REFERENCES `promociones` (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `promociones` (
  `cod_promocion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_porcentaje` int DEFAULT '0',
  `cantidad` int NOT NULL DEFAULT '0',
  `valor` decimal(10,2) DEFAULT NULL,
  `texto` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `is_recurrente` tinyint(1) DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `tipo_promocion` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'descuento',
  `is_distintivo` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `resumen_horas_anual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `hora` tinyint NOT NULL,
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_hora_anio` (`cod_empresa`,`cod_sucursal`,`anio`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_horas_mensual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `mes` tinyint NOT NULL,
  `hora` tinyint NOT NULL,
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_hora_periodo` (`cod_empresa`,`cod_sucursal`,`anio`,`mes`,`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_medios_anual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `medio_compra` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_medio_anio` (`cod_empresa`,`cod_sucursal`,`anio`,`medio_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_medios_mensual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `mes` tinyint NOT NULL,
  `medio_compra` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_medio_periodo` (`cod_empresa`,`cod_sucursal`,`anio`,`mes`,`medio_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_productos_anual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `cod_producto` int NOT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cantidad` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_anio` (`cod_empresa`,`cod_sucursal`,`anio`,`cod_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_productos_mensual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `anio` smallint NOT NULL,
  `mes` tinyint NOT NULL,
  `cod_producto` int NOT NULL,
  `nombre_producto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cantidad` int NOT NULL DEFAULT '0',
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_periodo` (`cod_empresa`,`cod_sucursal`,`anio`,`mes`,`cod_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_ventas_anual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `nombre_sucursal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `anio` smallint NOT NULL,
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_pickup` int NOT NULL DEFAULT '0',
  `total_delivery` int NOT NULL DEFAULT '0',
  `total_mesa` int NOT NULL DEFAULT '0',
  `monto_pickup` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `monto_delivery` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `monto_mesa` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `clientes_nuevos` int NOT NULL DEFAULT '0',
  `clientes_recurrentes` int NOT NULL DEFAULT '0',
  `fecha_proceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_sucursal_anio` (`cod_empresa`,`cod_sucursal`,`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resumen_ventas_mensual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `nombre_sucursal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `anio` smallint NOT NULL,
  `mes` tinyint NOT NULL,
  `total_ventas` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_ordenes` int NOT NULL DEFAULT '0',
  `total_pickup` int NOT NULL DEFAULT '0',
  `total_delivery` int NOT NULL DEFAULT '0',
  `total_mesa` int NOT NULL DEFAULT '0',
  `monto_pickup` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `monto_delivery` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `monto_mesa` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `clientes_nuevos` int NOT NULL DEFAULT '0',
  `clientes_recurrentes` int NOT NULL DEFAULT '0',
  `fecha_proceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_sucursal_periodo` (`cod_empresa`,`cod_sucursal`,`anio`,`mes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_app_registro_reglas` (
  `cod_app_registro_regla` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `origen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `version_code` int DEFAULT NULL,
  `campo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_app_registro_regla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_banner` (
  `cod_banner` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `subtitulo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descuento` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_boton` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url_boton` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` enum('APP','WEB') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_min` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'A',
  `ubicacion` enum('top_left','top_center','top_right','center_left','center','center_right','bottom_left','bottom_center','bottom_right') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'bottom_center',
  PRIMARY KEY (`cod_banner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_cajero_gestionordenes_config` (
  `cod_cajero_go` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `recordatorio` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `printer` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`cod_cajero_go`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_card_fidelizacion` (
  `cod_card_fidelizacion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_cliente` int DEFAULT '0',
  `codigo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'I',
  PRIMARY KEY (`cod_card_fidelizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_categorias` (
  `cod_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_categoria_padre` int DEFAULT '0',
  `alias` varchar(250) DEFAULT NULL,
  `categoria` varchar(250) DEFAULT NULL,
  `desc_corta` varchar(250) DEFAULT NULL,
  `desc_larga` text,
  `image_min` varchar(150) DEFAULT NULL,
  `image_max` varchar(150) DEFAULT NULL,
  `posicion` int DEFAULT '0',
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` enum('A','I','D') DEFAULT NULL,
  `image_path` text,
  PRIMARY KEY (`cod_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_categorias_dependientes` (
  `cod_categoria_dependiente` int NOT NULL AUTO_INCREMENT,
  `cod_categoria` int DEFAULT NULL,
  `cod_categoria_padre` int DEFAULT NULL,
  PRIMARY KEY (`cod_categoria_dependiente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_categorias_noticias` (
  `cod_categorias_noticias` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `cod_categoria_padre` int DEFAULT NULL,
  `estado` enum('A','I','D') NOT NULL,
  PRIMARY KEY (`cod_categorias_noticias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_ciudades` (
  `cod_ciudad` int NOT NULL AUTO_INCREMENT,
  `cod_courier` int NOT NULL DEFAULT '2',
  `nombre` char(35) NOT NULL DEFAULT '',
  `codigo` varchar(20) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `trayecto` varchar(5) NOT NULL,
  `estado` enum('A','I','D') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cod_ciudad`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_cliente_dinero` (
  `cod_cliente_dinero` int NOT NULL AUTO_INCREMENT,
  `cod_cliente` int DEFAULT NULL,
  `cod_orden` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '',
  `cod_tipo_pago` int NOT NULL,
  `dinero` float DEFAULT NULL,
  `saldo` float NOT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_caducidad` date NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_create` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_cliente_dinero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_clientes` (
  `cod_cliente` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `cod_nivel` int NOT NULL DEFAULT '1',
  `nombre` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_nac` date DEFAULT NULL,
  `tipo_documento` int NOT NULL,
  `num_documento` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono_2` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `direccion` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo_persona` int NOT NULL,
  `es_extranjero` int NOT NULL,
  `cod_prioridad` int NOT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_clientes_puntos` (
  `cod_cliente_punto` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_cliente` int DEFAULT NULL,
  `cod_nivel` int DEFAULT NULL,
  `puntos` int DEFAULT NULL,
  `dinero` float DEFAULT NULL,
  `fecha_create` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_cliente_punto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_clientes_saldos` (
  `cod_cliente_saldo` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_cliente` int NOT NULL,
  `dinero` float DEFAULT NULL,
  `saldo_anterior` float NOT NULL,
  `fecha_create` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_cliente_saldo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_codigo_promocional` (
  `cod_codigo_promocional` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` enum('descuento','giftcard') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `por_o_din` int NOT NULL,
  `monto` float DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `usos_restantes` int DEFAULT NULL,
  `restriccion` float NOT NULL,
  `fecha_create` date DEFAULT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `cod_empresa` int NOT NULL,
  `ilimitado` int NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_codigo_promocional`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_config_api` (
  `clave` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_contifico_empresa` (
  `cod_contifico_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `ambiente` enum('development','production') DEFAULT NULL,
  `razon_social` varchar(50) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `api` varchar(150) DEFAULT NULL,
  `categoria` varchar(20) NOT NULL,
  `cuenta_id_products` varchar(60) NOT NULL,
  `estado` enum('A','I') NOT NULL,
  `facturar` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_contifico_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_contifico_empresa_postokens` (
  `cod_postoken` int NOT NULL AUTO_INCREMENT,
  `cod_contifico_empresa` int NOT NULL,
  `cod_empresa` int DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pos` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `emisor` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ptoemision` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `secuencial` int DEFAULT NULL,
  `secuencial_dna` int NOT NULL DEFAULT '1',
  `tipo_documento` enum('FAC','DNA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `facturar` int NOT NULL,
  PRIMARY KEY (`cod_postoken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_contifico_sucursal` (
  `cod_contifico_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `cod_contifico_empresa` int NOT NULL,
  `cod_postoken` int DEFAULT NULL,
  `id_bodega` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name_bodega` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `inventario` int NOT NULL,
  PRIMARY KEY (`cod_contifico_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_cotizacion_envio_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `latitud` decimal(10,6) DEFAULT NULL,
  `longitud` decimal(10,6) DEFAULT NULL,
  `distancia_km` decimal(6,3) DEFAULT NULL,
  `distancia_fuente` varchar(20) DEFAULT NULL,
  `courier_precio` varchar(20) DEFAULT NULL,
  `tariff_id` int DEFAULT NULL,
  `device_type` varchar(10) DEFAULT NULL,
  `precio` decimal(8,2) DEFAULT NULL,
  `origen` varchar(20) DEFAULT NULL COMMENT 'que endpoint disparo la cotizacion: PRECIO_CONFIRM, PRECIO_VALIDAR, etc',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cod_usuario` (`cod_usuario`),
  KEY `idx_cod_sucursal` (`cod_sucursal`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_cotizacion_precio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `courier_nombre` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tariff_id` int NOT NULL DEFAULT '0',
  `latitud` decimal(10,7) NOT NULL,
  `longitud` decimal(10,7) NOT NULL,
  `distancia_km` decimal(10,3) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cod_usuario` int DEFAULT NULL,
  `device_type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL,
  `vigente_hasta` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vigente` (`vigente_hasta`),
  KEY `idx_sucursal` (`cod_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_courier` (
  `cod_courier` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` enum('MOTO','CAMION') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'MOTO',
  `imagen` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_courier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_cupones` (
  `cod_cupon` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `cantidad_dias_disponibles` int DEFAULT NULL,
  `tipo` enum('CUMPLEANIOS','REGISTRO','NIVEL2','NIVEL3') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_cupon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_cupones_usuarios` (
  `cod_cupon_usuario` int NOT NULL AUTO_INCREMENT,
  `cod_cupon` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `estado` enum('ACTIVO','USADO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_cupon_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_botonpagos` (
  `cod_empresa_botonpagos` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_proveedor_botonpagos` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `user_create` int DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_botonpagos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_configuraciones` (
  `cod_empresa_configuracion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `encender_tienda` int DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_configuracion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_costo_envio` (
  `cod_empresa_costo_envio` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `base_dinero` float DEFAULT NULL,
  `base_km` float DEFAULT NULL,
  `adicional_km` float DEFAULT NULL,
  `tipo` enum('carro','moto','camion') NOT NULL DEFAULT 'moto',
  `peso_maximo` int DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_costo_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_empresa_courier` (
  `cod_empresa_courier` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_courier` int NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_empresa_courier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_datafast` (
  `cod_empresa_datafast` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `api` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `entityId` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mid` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tid` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fase` enum('FASE1','FASE2') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `user_creacion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_empresa_datafast`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_facturacion` (
  `cod_empresa_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `prioridad` int NOT NULL DEFAULT '1',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `await_status` enum('ASIGNADA','ENTREGADA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ENTREGADA',
  PRIMARY KEY (`cod_empresa_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_faqs` (
  `cod_empresa_faqs` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `imagen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(600) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `tipo` enum('PUNTOS') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_faqs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_fidelizacion_puntos` (
  `cod_fidelizacion_puntos` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `divisor_puntos` int DEFAULT NULL COMMENT 'Cada cuanto saldo gana puntos',
  `monto_puntos` int DEFAULT NULL COMMENT 'cantidad de puntos que gana por cada divisor de puntos',
  `valor_regalo_cumple` float NOT NULL,
  `dias_regalo_cumple` int NOT NULL DEFAULT '0',
  `compra_minimo_regalo_cumple` int NOT NULL DEFAULT '0',
  `cant_dias_caducidad_puntos` int NOT NULL DEFAULT '90',
  `cant_dias_caducidad_dinero` int NOT NULL DEFAULT '90',
  `cant_dias_caducidad_saldo` int NOT NULL DEFAULT '90',
  `generate_barcode` tinyint(1) NOT NULL DEFAULT '0',
  `tipo_fidelizacion` enum('clasico','simple') NOT NULL DEFAULT 'simple',
  `meta_puntos` decimal(10,2) NOT NULL DEFAULT '20.00',
  PRIMARY KEY (`cod_fidelizacion_puntos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_empresa_forma_pago` (
  `cod_empresa_forma_pago` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_forma_pago` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `monto_maximo` float NOT NULL DEFAULT '0',
  `nombre` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `posicion` int DEFAULT '99',
  `is_delivery` int NOT NULL DEFAULT '1',
  `is_pickup` int NOT NULL DEFAULT '1',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_modal_cumple` (
  `cod_modal_cumple` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `imagen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_modal_cumple`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_notificaciones` (
  `cod_empresa_notificacion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `aplicacion` enum('USUARIOS','MOTORIZADOS','DASHBOARD','OTRAS') DEFAULT NULL,
  `token` text,
  `topic` varchar(25) DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_notificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_empresa_pagos` (
  `cod_empresa_pago` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `titulo` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_create` datetime NOT NULL,
  PRIMARY KEY (`cod_empresa_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_paymentez` (
  `cod_empresa_paymentez` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `client_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `client_key` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `server_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `server_key` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `save_card` int NOT NULL DEFAULT '0',
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_creacion` int DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_paymentez`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_payphone` (
  `cod_empresa_payphone` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `identificador` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `token` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_payphone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_progresos` (
  `cod_empresa_progreso` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `porcentaje` float DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_progreso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_red_social` (
  `cod_red_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_red_social` int DEFAULT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_red_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_sucursal_datafast` (
  `cod_empresa_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `api` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `entityId` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mid` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tid` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fase` enum('FASE1','FASE2') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_sucursal_paymentez` (
  `cod_empresa_sucursal_paymentez` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `client_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `client_key` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `server_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `server_key` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `save_card` int NOT NULL DEFAULT '0',
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `user_creacion` int DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_sucursal_paymentez`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_sucursal_payphone` (
  `cod_empresa_sucursal_payphone` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `identificador` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `token` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `storeid` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_sucursal_payphone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresa_suscripciones` (
  `cod_empresa_suscripcion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `origen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_suscripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresas` (
  `cod_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_tipo_empresa` int NOT NULL,
  `cod_plan` int NOT NULL,
  `ruc` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `folder` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `direccion` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `envio_grava_iva` int NOT NULL DEFAULT '0',
  `representante_nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `representante_documento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `representante_celular` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `representante_correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `mensualidad` float DEFAULT '0',
  `logo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_min` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url_web` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url_android` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url_ios` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facebook_pixel` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `facebook_pixel_verify` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `api_key` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `timezone` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `paginacion` int NOT NULL,
  `impuesto` double NOT NULL DEFAULT '15',
  `service_percentage` int NOT NULL DEFAULT '0',
  `currency` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `datetime_format` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_consumidor_final` int NOT NULL,
  `programar_pedido` int NOT NULL DEFAULT '0',
  `cant_dias_programar_pedido` int NOT NULL DEFAULT '5',
  `fidelizacion` int NOT NULL DEFAULT '0',
  `giftcard` int NOT NULL DEFAULT '0' COMMENT 'Indica si la empresa tiene habilitado el uso de giftcards',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_create` int NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `promo_text` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `keywords` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `front_header` enum('RESTAURANTE','RETAIL') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `front_menu` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `iniciar_en_menu` int NOT NULL DEFAULT '0',
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'development',
  `hosting` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `recordar_ordenes` int NOT NULL DEFAULT '0',
  `recordar_ordenes_tiempo` int NOT NULL DEFAULT '30' COMMENT 'en minutos',
  `tipo_recorte` enum('square','rectangle') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'square',
  `menu_type` enum('grid','list') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'list',
  `is_emprendedor` int NOT NULL DEFAULT '0',
  `stockExterno` int NOT NULL DEFAULT '0',
  `is_delivery` int DEFAULT '1',
  `is_pickup` int DEFAULT '1',
  `is_insitu` int DEFAULT '0',
  `front_product_card` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'V2',
  `cart_abandonment_minutes` int NOT NULL DEFAULT '60' COMMENT 'Minutos de inactividad para marcar carrito como abandonado',
  `cart_expiry_hours` int NOT NULL DEFAULT '72' COMMENT 'Horas hasta expirar un carrito abandonado',
  `cart_campaigns_enabled` tinyint NOT NULL DEFAULT '0' COMMENT '1 = campañas de recuperación activas',
  `cart_campaign_min_interval` int NOT NULL DEFAULT '60' COMMENT 'Minutos mínimos entre campañas de recuperación',
  `cart_campaign_max` int NOT NULL DEFAULT '3' COMMENT 'Número máximo de campañas por carrito',
  `cart_recovery_email` tinyint NOT NULL DEFAULT '0' COMMENT '1 = recuperación por email activa',
  `cart_recovery_push` tinyint NOT NULL DEFAULT '0' COMMENT '1 = recuperación por push activa',
  `cart_recovery_whatsapp` tinyint NOT NULL DEFAULT '0' COMMENT '1 = recuperación por WhatsApp activa',
  `tabs_order` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Orden de los tabs movibles del bottom bar de la app, separados por coma. Ej: giftcards,menu,billetera,pedidos. Valores validos: menu,orders,wallet,giftcards,profile. NULL = usa el default de la app (menu,orders,wallet,profile).',
  `mesa_tipo` enum('NUMERO','NOMBRE') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'NUMERO' COMMENT 'Tipo de identificación de mesa para pedidos EN_MESA',
  `prep_time_badge_minutes` int NOT NULL DEFAULT '120',
  PRIMARY KEY (`cod_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresas_versiones_app` (
  `cod_empresa_version` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` int DEFAULT NULL,
  `texto` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `obligatorio` int DEFAULT NULL,
  `aplicacion` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_modificacion` datetime DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`cod_empresa_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_estado_ordenes` (
  `cod_estado` varchar(15) NOT NULL,
  `nombre` varchar(15) DEFAULT NULL,
  `icono` varchar(20) NOT NULL,
  `posicion` int DEFAULT NULL,
  `is_envio` int NOT NULL DEFAULT '1',
  `estado` enum('A','I','D') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_faqs` (
  `cod_faq` int NOT NULL AUTO_INCREMENT,
  `cod_tipo_empresa` int NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_corta` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_larga` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `posicion` int NOT NULL DEFAULT '99',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_faq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_flota_codigo` (
  `cod_codigo` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` enum('GENERAL','REACTIVACION') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_usuario_objetivo` int DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `fecha_uso` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_codigo`),
  UNIQUE KEY `uq_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_formas_pago` (
  `cod_forma_pago` varchar(3) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_formas_pago_facturacion` (
  `cod_forma_pago_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_forma_pago` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `cod_contifico_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_forma_pago_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_front_pagina_detalle` (
  `cod_front_pagina_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_front_pagina` int DEFAULT NULL,
  `cod_tipo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `forma` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `num_columnas` int DEFAULT NULL,
  `md` int NOT NULL DEFAULT '1',
  `sm` int NOT NULL DEFAULT '1',
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `detalle2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `cod_detalle` int DEFAULT NULL,
  `posicion` int DEFAULT '99',
  `html` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `extra_params` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` date DEFAULT NULL,
  `classname` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `showTitle` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`cod_front_pagina_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_front_pagina_detalle_contenido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_front_pagina_detalle` int DEFAULT NULL,
  `imagen` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accion_id` enum('FILTER','PRODUCTO','NOTICIA','URL','INFO','ZOOM') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'INFO',
  `accion_desc` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_front_pagina_tipos` (
  `cod_front_pagina_tipo` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_front_pagina_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_front_paginas` (
  `cod_front_pagina` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `home` int NOT NULL DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_front_pagina`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_front_scripts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ubicacion` enum('head','body') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `codigo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_gacela_sucursal` (
  `cod_gacela_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `ambiente` enum('development','production') DEFAULT NULL,
  `api` varchar(100) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `store_id` varchar(20) NOT NULL,
  `api_key` varchar(150) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` varchar(150) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `email` varchar(100) NOT NULL,
  `pickup_custom_field_template` varchar(100) NOT NULL,
  `custom_field_template` varchar(100) NOT NULL,
  `estado` enum('A','I','D') NOT NULL,
  PRIMARY KEY (`cod_gacela_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_giftcards` (
  `cod_giftcard` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `imagen` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `montos` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_giftcard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_impresoras` (
  `cod_impresora` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_sucursal` int NOT NULL,
  `estacion_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `size` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '80',
  `paginas` int NOT NULL DEFAULT '1',
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_visto` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_impresora`),
  KEY `idx_sucursal` (`cod_sucursal`),
  KEY `idx_estacion` (`cod_sucursal`,`estacion_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_ingredientes` (
  `cod_ingrediente` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_unidad_medida` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ingrediente` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `id_contifico` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `precio` float NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cod_ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_ingredientes_facturacion` (
  `cod_ingrediente_facturacion` int NOT NULL AUTO_INCREMENT,
  `id` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_ingrediente` int DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_contifico_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_ingrediente_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_inlog_sucursal` (
  `cod_inlog_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `idCliente` int DEFAULT NULL,
  `token` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_inlog_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_laar_sucursal` (
  `cod_laar_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(2000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_laar_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_marketing_envios` (
  `cod_marketing_envio` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `porcentaje` int DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `solo_horario` int NOT NULL DEFAULT '0' COMMENT 'Para que respete la promo solo dentro del horario',
  `dias` varchar(20) NOT NULL COMMENT 'lun=1, dom=7',
  `estado` enum('A','I','D') NOT NULL,
  PRIMARY KEY (`cod_marketing_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_motorizado_asignacion` (
  `cod_motorizado_asignacion` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_motorizado` int DEFAULT NULL,
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_aceptacion` datetime DEFAULT NULL,
  `fecha_llegada_local` datetime DEFAULT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `fecha_llegada` datetime DEFAULT NULL,
  `push_cercania_enviado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_motorizado_asignacion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_motorizado_conexion` (
  `cod_conexion` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_conexion`),
  KEY `idx_usuario_fecha` (`cod_usuario`,`fecha_inicio`),
  CONSTRAINT `fk_motorizado_conexion_usuario` FOREIGN KEY (`cod_usuario`) REFERENCES `tb_usuarios` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_motorizado_empresa` (
  `cod_motorizado_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL COMMENT 'tb_usuarios.cod_usuario, rol 17',
  `cod_empresa` int NOT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=afiliacion activa, I=inactiva/retirada',
  `es_invitado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_afiliacion` datetime NOT NULL,
  PRIMARY KEY (`cod_motorizado_empresa`),
  UNIQUE KEY `uq_motorizado_empresa` (`cod_usuario`,`cod_empresa`),
  KEY `idx_cod_usuario` (`cod_usuario`),
  KEY `idx_cod_empresa` (`cod_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_motorizado_link` (
  `cod_motorizado_link` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `token` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL,
  `aceptada` int DEFAULT '0',
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_motorizado_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_motorizado_seguimiento` (
  `cod_motorizado_seguimiento` int NOT NULL AUTO_INCREMENT,
  `cod_motorizado` int DEFAULT NULL,
  `latitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `longitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_motorizado_seguimiento`),
  KEY `idx_motorizado_seguimiento` (`cod_motorizado`,`cod_motorizado_seguimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_niveles` (
  `cod_nivel` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `punto_inicial` int DEFAULT NULL,
  `punto_final` int DEFAULT NULL,
  `dinero_x_punto` float DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int NOT NULL,
  PRIMARY KEY (`cod_nivel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_noticias` (
  `cod_noticia` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `alias` varchar(300) DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `desc_corta` text,
  `desc_larga` text,
  `image_min` varchar(100) DEFAULT NULL,
  `imagen_max` varchar(100) DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `fecha_modificacion` datetime DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_noticia`),
  FULLTEXT KEY `titulo` (`titulo`,`desc_corta`,`desc_larga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_noticias_categoria` (
  `cod_noticias_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_noticia` int DEFAULT NULL,
  `cod_categoria` int DEFAULT NULL,
  PRIMARY KEY (`cod_noticias_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_noticias_imagenes` (
  `cod_imagen` int NOT NULL AUTO_INCREMENT,
  `cod_noticia` int DEFAULT NULL,
  `nombre_img` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_imagen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_notificaciones` (
  `cod_notificacion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa_notificacion` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `detalle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`cod_notificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_notificaciones_auto_config` (
  `cod_empresa` int NOT NULL,
  `recompra_activo` tinyint(1) NOT NULL DEFAULT '0',
  `recompra_dias` int NOT NULL DEFAULT '15',
  `recompra_titulo` varchar(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recompra_mensaje` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fecha_update` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_notificaciones_auto_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_tipo_clave` (`cod_usuario`,`tipo`,`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_notificaciones_expo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `cod_usuario_admin` int NOT NULL,
  `total_enviados` int NOT NULL DEFAULT '0',
  `estado` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ENVIADA',
  `audiencia` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_programada` datetime DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `total_ok` int NOT NULL DEFAULT '0',
  `total_error` int NOT NULL DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cod_empresa` (`cod_empresa`),
  KEY `idx_programadas` (`estado`,`fecha_programada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_notificaciones_expo_aperturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notificacion_id` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notificacion_usuario` (`notificacion_id`,`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_notificaciones_tipo` (
  `cod_notificacion_tipo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_notificacion_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_cabecera` (
  `cod_orden` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_usuario` int DEFAULT NULL,
  `cod_sucursal` int NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `subtotal0` decimal(10,2) DEFAULT NULL,
  `subtotal12` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `envio` decimal(10,2) DEFAULT NULL,
  `envio_iva` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `iva_porcentaje` int NOT NULL DEFAULT '12',
  `service` decimal(10,2) DEFAULT NULL,
  `giftcard` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `cod_descuento` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_giftcard` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_envio` int NOT NULL DEFAULT '1',
  `is_programado` int DEFAULT '0',
  `is_express` int DEFAULT '0',
  `hora_retiro` datetime NOT NULL,
  `pago` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_suelto` int NOT NULL,
  `monto_suelto` float NOT NULL,
  `latitud` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `longitud` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `distancia` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombres` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cedula` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `referencia` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `referencia2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `datos_facturacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `medio_compra` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `calificada` int NOT NULL DEFAULT '0',
  `cod_courier` int NOT NULL,
  `is_gacela` int NOT NULL DEFAULT '0',
  `order_token` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `api_version` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'v3',
  `app_version` int NOT NULL,
  `estado` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_altademanda` int DEFAULT '0',
  `distancia_km` decimal(6,3) DEFAULT NULL COMMENT 'Distancia real usada para calcular el precio de envio',
  `distancia_fuente` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'LINEA_RECTA o GOOGLE_MAPS',
  `courier_precio` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Quien calculo el precio: GOOGLE_MAPS, LINEA_RECTA, GACELA, PICKER, PEDIDOS_YA',
  `tariff_id` int DEFAULT NULL COMMENT 'Tarifa usada para el calculo de envio',
  `device_type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'ANDROID, IOS o WEB, capturado al momento de validar la orden',
  `cotizacion_id` int DEFAULT NULL,
  `mesa_referencia` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_calificacion` (
  `cod_orden_calificacion` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `calificacion` int DEFAULT NULL,
  `texto` varchar(300) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_orden_calificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_orden_cancelacion` (
  `cod_orden_cancelacion` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `motivo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  PRIMARY KEY (`cod_orden_cancelacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_courier_canceled` (
  `cod_orden_courier_canceled` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_courier` int DEFAULT NULL,
  `orden_token` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `motivo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`cod_orden_courier_canceled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_cuponera` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_datos_facturacion` (
  `cod_orden_dato_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `nombre` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `num_documento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_extranjero` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`cod_orden_dato_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_destino` (
  `cod_orden_destino` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `num_casa` int NOT NULL,
  `cod_postal` int NOT NULL,
  `cod_ciudad` int NOT NULL,
  PRIMARY KEY (`cod_orden_destino`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_detalle` (
  `cod_orden_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `comentarios` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` float DEFAULT NULL,
  `precio_no_tax` float NOT NULL,
  `descuento` float NOT NULL,
  `descuento_porcentaje` float NOT NULL,
  `desc_text` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cantidad` int DEFAULT NULL,
  `base_0` float NOT NULL,
  `base_12` float NOT NULL,
  `subtotal_0` float NOT NULL,
  `subtotal_12` float NOT NULL,
  `precio_final` float DEFAULT NULL,
  `adicional_total` float NOT NULL,
  `adicional_no_tax_unidad` float NOT NULL,
  `adicional_no_tax_total` float NOT NULL,
  `cod_promocion` int DEFAULT NULL COMMENT 'Promoción que generó el descuento en este item',
  `es_regalo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = producto gratis por promoción avanzada',
  PRIMARY KEY (`cod_orden_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_orden_devolucion` (
  `id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `respuesta` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_errores` (
  `cod_orden_errores` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `tipo` enum('FACTURA','COURIER') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `proveedor` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `motivo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_orden_errores`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_evento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_orden_detalle` int NOT NULL DEFAULT '0',
  `dia` date DEFAULT NULL,
  `estado` enum('EJECUTAR','EJECUTADO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'EJECUTAR',
  `cod_producto` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_factura_electronica` (
  `cod_orden_factura_electronica` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `tipo` enum('FAC','DNA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `num_factura` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `clave_acceso` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado_inventario` enum('NO_APLICA','NO_DEBITADO','DEBITADO','NO_REVERTIDO','REVERTIDO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'NO_DEBITADO',
  `cod_sistema_facturacion` int NOT NULL,
  `cod_contifico_empresa` int NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_orden_factura_electronica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_historial` (
  `cod_orden_historial` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `order_token` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_orden_historial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_incidencia` (
  `cod_incidencia` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `cod_motorizado` int NOT NULL,
  `motivo` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `comentario` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_reporte` datetime NOT NULL,
  `estado` enum('PENDIENTE','DESCARTADA','CONFIRMADA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `cod_admin_resuelve` int DEFAULT NULL,
  `resuelto_por` enum('MOTORIZADO','FLOTA','SUCURSAL') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `motivo_resolucion` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `comentario_resolucion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_resolucion` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_incidencia`),
  KEY `cod_motorizado` (`cod_motorizado`),
  KEY `idx_orden_estado` (`cod_orden`,`estado`),
  CONSTRAINT `tb_orden_incidencia_ibfk_1` FOREIGN KEY (`cod_orden`) REFERENCES `tb_orden_cabecera` (`cod_orden`),
  CONSTRAINT `tb_orden_incidencia_ibfk_2` FOREIGN KEY (`cod_motorizado`) REFERENCES `tb_usuarios` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_inventario` (
  `cod_orden_inventario` int NOT NULL AUTO_INCREMENT,
  `cod_contifico_empresa` int DEFAULT NULL,
  `cod_orden` int DEFAULT NULL,
  `tipo` enum('EGR','ING','TRA','AJU') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `codigo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_orden_inventario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_motorizado` (
  `cod_orden_motorizado` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `num_documento` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `placa` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `proceso` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_orden_motorizado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_pagos` (
  `cod_orden_pagos` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `forma_pago` varchar(3) DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `observacion` varchar(50) NOT NULL,
  `observacion2` varchar(30) NOT NULL,
  `cod_proveedor_botonpagos` int NOT NULL DEFAULT '2',
  `lote` varchar(10) NOT NULL,
  PRIMARY KEY (`cod_orden_pagos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_orden_puntos` (
  `cod_orden_puntos` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `estado` int DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_orden_puntos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_orden_recipientes` (
  `cod_orden_recipiente` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `cod_recipiente` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  PRIMARY KEY (`cod_orden_recipiente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_runfood` (
  `cod_orden_runfood` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `id` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`cod_orden_runfood`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_ordenes_flota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_flota` int DEFAULT NULL,
  `cod_orden` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_pagina_rol` (
  `cod_pagina_rol` int NOT NULL AUTO_INCREMENT,
  `cod_pagina` int DEFAULT NULL,
  `cod_rol` int DEFAULT NULL,
  `cod_empresa` int NOT NULL,
  `posicion` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`cod_pagina_rol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_paginas` (
  `cod_pagina` int NOT NULL AUTO_INCREMENT,
  `cod_padre` int NOT NULL,
  `id` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data_translate` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int NOT NULL,
  `estado` enum('A','MANTENIMIENTO','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_pagina`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_pedidosya_sucursales` (
  `cod_pedidosya_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `recolectar_dinero` int NOT NULL DEFAULT '1',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_pedidosya_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_permisos` (
  `cod_permiso` int NOT NULL AUTO_INCREMENT,
  `identificador` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `grupo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_permiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_permisos_empresas` (
  `cod_permiso_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `identificador` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `habilitado` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_permiso_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_picker_sucursal` (
  `cod_picker_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `api` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_picker_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_plantilla_correo` (
  `cod_plantilla_correo` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_tipo_correo` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `html` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `asunto` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correos` varchar(1500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_plantilla_correo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_preorden_json` (
  `cod_preorden` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('VALIDADA','CREANDO_ORDEN','PAGADA','PAGADA_NO_CREADA','FALLADA','CERRADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_detail` int DEFAULT NULL,
  `paymentId` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paymentAuth` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lot_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_intentos_creacion` int NOT NULL DEFAULT '0',
  `cod_orden` int DEFAULT '0',
  `amount` float DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `motivo_fallo` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook` int DEFAULT NULL,
  PRIMARY KEY (`cod_preorden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_producto_agotado_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `estado` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `minutos` int DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_caracteristica` (
  `cod_producto_caracteristica` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `caracteristica` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` enum('TEXTO','COLOR') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_producto_caracteristica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_caracteristica_detalle` (
  `cod_producto_caracteristica_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_producto_caracteristica` int DEFAULT NULL,
  `detalle` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detalle2` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_producto_caracteristica_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_descuento` (
  `cod_producto_descuento` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `is_porcentaje` int DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `cantidad` int NOT NULL,
  `texto` varchar(10) NOT NULL,
  `estado` enum('A','I','D') NOT NULL,
  PRIMARY KEY (`cod_producto_descuento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_producto_empaque_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `unidades` int DEFAULT NULL,
  `alto` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_evento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `dias_anticipacion` int NOT NULL DEFAULT '1',
  `dias_fin` int NOT NULL DEFAULT '365',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `titulo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_extras` (
  `cod_producto_extra` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `costo_adicional` float DEFAULT NULL,
  `posicion` int NOT NULL,
  PRIMARY KEY (`cod_producto_extra`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_producto_extras_detalle` (
  `cod_producto_extra_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_producto_extra` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_producto_extra_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos` (
  `cod_producto` int NOT NULL AUTO_INCREMENT,
  `cod_producto_padre` int DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `open_detalle` int NOT NULL,
  `is_combo` int NOT NULL DEFAULT '0',
  `alias` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dia` int NOT NULL DEFAULT '0',
  `peso` int NOT NULL DEFAULT '0',
  `volumen` int NOT NULL DEFAULT '0',
  `sku` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `noStock` int NOT NULL DEFAULT '0',
  `cobra_iva` int NOT NULL,
  `iva_porcentaje` int NOT NULL,
  `iva_valor` double NOT NULL,
  `bien` enum('Producto','Servicio') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `costo` double NOT NULL,
  `precio_no_tax` double NOT NULL,
  `precio` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `precio_anterior` float NOT NULL,
  `desc_corta` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `desc_larga` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `image_min` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_max` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` datetime NOT NULL,
  `user_create` int NOT NULL,
  `iscategoria` int DEFAULT NULL,
  `variante_visualizacion` enum('LISTA','SELECCIONAR') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'LISTA',
  `posicion` int DEFAULT NULL,
  `id_contifico` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `intervalo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_path` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `oahu_arma_bowl` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tiempo_preparacion` int DEFAULT '0',
  `venta_delivery` tinyint(1) NOT NULL DEFAULT '1',
  `venta_pickup` tinyint(1) NOT NULL DEFAULT '1',
  `venta_mesa` tinyint(1) NOT NULL DEFAULT '1',
  `precio_especial` decimal(10,4) DEFAULT NULL COMMENT 'Precio temporal mientras esté vigente',
  `precio_especial_inicio` date DEFAULT NULL COMMENT 'Inicio del precio especial',
  `precio_especial_fin` date DEFAULT NULL COMMENT 'Fin del precio especial',
  PRIMARY KEY (`cod_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_categorias` (
  `cod_producto_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_categoria` int DEFAULT NULL,
  PRIMARY KEY (`cod_producto_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_productos_dias` (
  `cod_producto_dias` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `dia` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_inicio` time NOT NULL DEFAULT '00:00:00',
  `hora_fin` time NOT NULL DEFAULT '23:59:59',
  PRIMARY KEY (`cod_producto_dias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_envio_facturacion` (
  `id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_empresa` int DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `cod_contifico_empresa` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_facturacion` (
  `cod_producto_facturacion` int NOT NULL AUTO_INCREMENT,
  `id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_contifico_empresa` int NOT NULL,
  PRIMARY KEY (`cod_producto_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_imagenes` (
  `cod_imagen` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `nombre_img` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` enum('IMAGEN','VIDEO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int NOT NULL,
  PRIMARY KEY (`cod_imagen`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_ingredientes` (
  `cod_producto_ingrediente` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_ingrediente` int DEFAULT NULL,
  `valor` float DEFAULT NULL,
  PRIMARY KEY (`cod_producto_ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_opciones` (
  `cod_producto_opcion` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `cantidad_min` int NOT NULL,
  `isCheck` int NOT NULL DEFAULT '0',
  `isDatabase` int NOT NULL DEFAULT '0',
  `posicion` int NOT NULL DEFAULT '99',
  `productos` text,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cod_producto_opcion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_productos_opciones_detalle` (
  `cod_producto_opciones_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_producto_opcion` int DEFAULT NULL,
  `item` varchar(200) DEFAULT NULL,
  `aumentar_precio` int NOT NULL DEFAULT '0',
  `precio` float DEFAULT '0',
  `grava_iva` int NOT NULL DEFAULT '1',
  `debitInventario` int NOT NULL DEFAULT '1',
  `posicion` int DEFAULT '0',
  `detalle` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cod_producto_opciones_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_productos_opciones_detalle_facturacion` (
  `cod_opcion_detalle_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_producto_opciones_detalle` int NOT NULL,
  `cod_contifico_empresa` int NOT NULL,
  `id_runfood` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre_runfood` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_opcion_detalle_facturacion`),
  UNIQUE KEY `uq_detalle_empresa` (`cod_producto_opciones_detalle`,`cod_contifico_empresa`),
  CONSTRAINT `tb_productos_opciones_detalle_facturacion_ibfk_1` FOREIGN KEY (`cod_producto_opciones_detalle`) REFERENCES `tb_productos_opciones_detalle` (`cod_producto_opciones_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_opciones_ingredientes` (
  `cod_producto_opcion_ingrediente` int NOT NULL AUTO_INCREMENT,
  `cod_producto_opcion` int DEFAULT NULL,
  `cod_ingrediente` int DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_producto_opcion_ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_sucursal` (
  `cod_producto_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `replacePrice` int NOT NULL DEFAULT '0',
  `precio` float DEFAULT NULL,
  `precio_anterior` float DEFAULT NULL,
  `precio_no_tax` float DEFAULT NULL,
  `iva_valor` float DEFAULT NULL,
  `estado` enum('A','I','D','AGOTADO') DEFAULT NULL,
  `agotado_inicio` datetime DEFAULT NULL,
  `agotado_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_producto_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_productos_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cod_producto` (`cod_producto`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `tb_productos_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tb_tags` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_variante` (
  `cod_producto_variante` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `atributo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_producto_variante`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_promocion_producto_gratis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `imagen` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` enum('REGISTRO','FIRST_ORDER','PURCHASE') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `producto_nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_web` int DEFAULT '0',
  `is_app` int DEFAULT '0',
  `monto_minimo` int DEFAULT '0',
  `titulo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion_no_aplica` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_proveedor_botonpagos` (
  `cod_proveedor_botonpagos` int NOT NULL AUTO_INCREMENT,
  `identificador` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT '99',
  PRIMARY KEY (`cod_proveedor_botonpagos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_push_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `plataforma` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sonido` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_cod_usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_recipientes` (
  `cod_recipiente` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_recipiente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_recipientes_facturacion` (
  `cod_recipiente_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_recipiente` int DEFAULT NULL,
  `id` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `cod_contifico_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_recipiente_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_red_social` (
  `cod_red` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `icono` varchar(75) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_red`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_roles` (
  `cod_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_rol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_runfood_sucursal` (
  `cod_runfood_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `dominio` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `facturar` int DEFAULT '0',
  `tipo_documento` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'FAC',
  PRIMARY KEY (`cod_runfood_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sistema_facturacion` (
  `cod_sistema_facturacion` int NOT NULL AUTO_INCREMENT,
  `identificador` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_sistema_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_steps_timeline` (
  `estado` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_complete` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_no_complete` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `cod_step` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`cod_step`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_alta_demanda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_cobertura` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `zone` polygon NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_costo_envio` (
  `cod_sucursal_costo_envio` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `base_dinero` float DEFAULT NULL,
  `base_km` float DEFAULT NULL,
  `adicional_km` float DEFAULT NULL,
  PRIMARY KEY (`cod_sucursal_costo_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_costo_envio_rango` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `distancia_ini` float DEFAULT NULL COMMENT 'Distancia inicial incluida',
  `distancia_fin` float DEFAULT NULL COMMENT 'Distancia Final no incluida',
  `precio` float DEFAULT '0',
  `cod_tarifa` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_courier` (
  `cod_sucursal_courier` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `cod_courier` int DEFAULT NULL,
  `validar_cobertura` int NOT NULL DEFAULT '0' COMMENT 'Si esta en 1 se valida estrictamente esta cobertura',
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'A',
  `prioridad` int DEFAULT '5',
  `detalle` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_sucursal_courier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_disponibilidad` (
  `cod_sucursal_disponibilidad` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `dia` int NOT NULL,
  `hora_ini` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`cod_sucursal_disponibilidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_festivos` (
  `cod_sucursal_festivos` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` varchar(10) DEFAULT NULL,
  `hora_fin` varchar(10) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_sucursal_festivos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_sucursal_flota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `cod_flota` int DEFAULT NULL COMMENT 'EMPRESA DE TIPO COURIER',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_forma_pago` (
  `cod_sucursal_forma_pago` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `cod_forma_pago` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `monto_maximo` decimal(10,2) DEFAULT '0.00',
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `is_delivery` tinyint(1) DEFAULT '1',
  `is_pickup` tinyint(1) DEFAULT '1',
  `estado` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'A',
  PRIMARY KEY (`cod_sucursal_forma_pago`),
  UNIQUE KEY `uq_suc_fp` (`cod_sucursal`,`cod_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursal_tiempo_programar` (
  `cod_sucursal_tiempo_programar` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `tipo` enum('DELIVERY','PICKUP') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hora_apertura` int DEFAULT NULL,
  `hora_cierre` int DEFAULT NULL,
  PRIMARY KEY (`cod_sucursal_tiempo_programar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sucursales` (
  `cod_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_ciudad` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `direccion` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `latitud` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `longitud` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `distancia_km` float NOT NULL,
  `hora_ini` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `intervalo` int NOT NULL DEFAULT '30',
  `emisor` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_min` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `transferencia_img` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `banner_xl` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `delivery` int NOT NULL DEFAULT '1',
  `insite` int NOT NULL DEFAULT '0',
  `pickup` int NOT NULL DEFAULT '1',
  `show_banner` int NOT NULL DEFAULT '0',
  `tipo_bodega` int NOT NULL DEFAULT '0',
  `programar_pedido` int NOT NULL DEFAULT '0',
  `grava_iva` int NOT NULL DEFAULT '1',
  `envio_grava_iva` int NOT NULL DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_sucursal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `es_predefinido` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_tarifa` (
  `cod_tarifa` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Estándar',
  `peso_max_kg` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`cod_tarifa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tarjetas` (
  `cod_tarjeta` int NOT NULL AUTO_INCREMENT,
  `id1` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `id2` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_tarjeta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_taste_portafolio` (
  `cod_taste_portafolio` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `categories` json NOT NULL,
  `cities` json NOT NULL,
  PRIMARY KEY (`cod_taste_portafolio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tipo_dinero` (
  `cod_tipo_pago` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_tipo_pago`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tipo_empresas` (
  `cod_tipo_empresa` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_tipo_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_unidades_medidas` (
  `cod_unidad_medida` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_unidad_medida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_bloqueo` (
  `cod_usuario_bloqueo` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_bloqueo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_cards` (
  `cod_usuario_cards` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cod_sucursal_created` int NOT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bin` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `number` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reference` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `expiry_month` int DEFAULT NULL,
  `expiry_year` int DEFAULT NULL,
  `alias` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `predeterminada` int NOT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_cards`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_codigo_login` (
  `cod_usuario_code` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_codigo_registro` (
  `cod_usuario_code` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_codigo_telefono` (
  `cod_usuario_code` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL,
  `codigo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_direcciones` (
  `cod_usuario_direccion` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `referencia` varchar(200) NOT NULL,
  `latitud` varchar(15) DEFAULT NULL,
  `longitud` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_direccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_usuario_giftcard` (
  `cod_usuario_giftcard` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cod_giftcard` int DEFAULT NULL,
  `cod_preorden` int DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cod_usuario_receptor` int NOT NULL,
  `fecha_utilizacion` datetime NOT NULL,
  `estado` enum('A','I','D') NOT NULL,
  `monto` float DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `paymentId` varchar(60) DEFAULT NULL,
  `paymentAuth` varchar(15) DEFAULT NULL,
  `payment_provider` int DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_giftcard`),
  KEY `idx_cod_preorden` (`cod_preorden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_usuario_intento_pago` (
  `cod_usuario_intento_pago` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cod_proveedor_botonpagos` int DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `monto` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `origen` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` enum('success','failure') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fraude` int NOT NULL DEFAULT '0',
  `json` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` enum('I','A') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_intento_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_keystore` (
  `cod_usuario_keystore` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `clave` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `temporal` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_keystore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_purchase_code` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `codigo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL,
  `estado` enum('CREADO','USADO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_orden` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuarios` (
  `cod_usuario` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_rol` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `apellido` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `imagen` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apple_user_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono_verificado` int NOT NULL DEFAULT '0',
  `direccion` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `num_documento` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `cod_idioma` int NOT NULL DEFAULT '1',
  `cod_sucursal` int NOT NULL DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recuperacion_pass` int NOT NULL DEFAULT '0',
  `latitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `longitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `placa` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `is_active` int NOT NULL DEFAULT '1',
  `fecha_ubicacion` datetime DEFAULT NULL,
  `motivo_bloqueo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`cod_usuario`),
  UNIQUE KEY `uq_tb_usuarios_apple_user_id` (`apple_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuarios_datos_facturacion` (
  `cod_usuario_dato_facturacion` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `nombre` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `num_documento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `direccion` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_extranjero` int NOT NULL DEFAULT '1',
  `tipo_documento` enum('DNI','RUCN','RUCJ') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_dato_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_variante_caracteristica` (
  `cod_variante_caracteristica` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_caracteristica_detalle` int DEFAULT NULL,
  PRIMARY KEY (`cod_variante_caracteristica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_version_web` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `version` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `filename` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_web_adicionales` (
  `cod_web_adicionales` int NOT NULL AUTO_INCREMENT,
  `cod_categoria` int DEFAULT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_categoria_items` int DEFAULT NULL,
  `posicion` int DEFAULT '99',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_web_adicionales`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_web_modulos_productos` (
  `cod_web_modulos_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(300) DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `descripcion` varchar(500) NOT NULL,
  `modulo` enum('HOME','SUGERENCIAS') NOT NULL DEFAULT 'HOME',
  PRIMARY KEY (`cod_web_modulos_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_web_modulos_productos_detalle` (
  `cod_web_modulos_producto_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_web_modulos_producto` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_web_modulos_producto_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tmp_primera_orden` (
  `cod_empresa` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `primera_orden` date NOT NULL,
  PRIMARY KEY (`cod_empresa`,`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW `view_asignacion_motorizado` AS select `oc`.`cod_orden` AS `cod_orden`,`u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`u`.`num_documento` AS `num_documento`,`u`.`imagen` AS `foto`,`u`.`telefono` AS `telefono`,`oc`.`cod_courier` AS `is_gacela`,`u`.`cod_usuario` AS `cod_usuario` from ((`tb_orden_cabecera` `oc` join `tb_motorizado_asignacion` `ma` on((`ma`.`cod_orden` = `oc`.`cod_orden`))) join `tb_usuarios` `u` on((`u`.`cod_usuario` = `ma`.`cod_motorizado`))) where ((`u`.`cod_rol` = 17) and (`oc`.`cod_courier` in ('0','1'))) union select `u`.`cod_orden` AS `cod_orden`,`u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`u`.`num_documento` AS `num_documento`,`u`.`foto` AS `foto`,`u`.`telefono` AS `telefono`,`oc`.`cod_courier` AS `is_gacela`,0 AS `cod_usuario` from (`tb_orden_motorizado` `u` join `tb_orden_cabecera` `oc` on((`u`.`cod_orden` = `oc`.`cod_orden`)));

CREATE OR REPLACE VIEW `vw_producto_sucursal` AS select `p`.`cod_producto` AS `cod_producto`,`p`.`cod_producto_padre` AS `cod_producto_padre`,`p`.`cod_empresa` AS `cod_empresa`,`p`.`alias` AS `alias`,`p`.`nombre` AS `nombre`,`p`.`desc_corta` AS `desc_corta`,`p`.`desc_larga` AS `desc_larga`,`p`.`image_min` AS `image_min`,`p`.`image_max` AS `image_max`,`ps`.`agotado_inicio` AS `agotado_inicio`,`ps`.`agotado_fin` AS `agotado_fin`,`p`.`estado` AS `estado`,`p`.`is_combo` AS `is_combo`,`p`.`open_detalle` AS `open_detalle`,`p`.`fecha_modificacion` AS `fecha_modificacion`,`p`.`dia` AS `dia`,`p`.`sku` AS `sku`,`p`.`cobra_iva` AS `cobra_iva`,`p`.`posicion` AS `posicion`,`p`.`peso` AS `peso`,`s`.`nombre` AS `sucursal`,`s`.`cod_sucursal` AS `cod_sucursal_original`,coalesce(`ps`.`cod_sucursal`,0) AS `cod_sucursal`,coalesce(`ps`.`precio_no_tax`,`p`.`precio_no_tax`) AS `precio_no_tax`,coalesce(`ps`.`iva_valor`,`p`.`iva_valor`) AS `iva_valor`,coalesce(`ps`.`precio`,`p`.`precio`) AS `precio`,coalesce(`ps`.`precio_anterior`,`p`.`precio_anterior`) AS `precio_anterior`,`p`.`venta_delivery` AS `venta_delivery`,`p`.`venta_pickup` AS `venta_pickup`,`p`.`venta_mesa` AS `venta_mesa` from ((`tb_productos` `p` left join `tb_productos_sucursal` `ps` on(((`p`.`cod_producto` = `ps`.`cod_producto`) and (`ps`.`estado` = 'A')))) join `tb_sucursales` `s` on(((`s`.`cod_sucursal` = `ps`.`cod_sucursal`) and (`s`.`estado` = 'A'))));

SET FOREIGN_KEY_CHECKS = 1;

-- Migraciones ya contenidas en este schema
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_08_21_000001_add_go_scan_barcode_permission.sql', 0, '4194b940d1645874ae0a00c9196ff1ee');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_09_24_000001_show_banner_default_0_tb_sucursales.sql', 0, '59a3399e5008efbd6c0cd2c379f9d22a');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_09_24_000002_add_precio_especial_tb_productos.sql', 0, '807b3e5039d696cc58c4fc79172e6d0d');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_09_25_000001_fidelizacion_esquema_simple_default.sql', 0, '819c15d884059e6908a3d686f6ecf1ac');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000001_notificaciones_push_programadas_automaticas.sql', 0, '40df379300e592e2e97f798d0d22bcae');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000002_add_prep_time_badge_minutes_tb_empresas.sql', 0, '4c75c9d72f517897fa64c61b338e9ea3');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000003_recreate_view_asignacion_motorizado.sql', 0, 'e53c7409691d3effa79f6c47583418b6');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000004_colapso_historico_global_hasta_2025.sql', 0, 'a1cf3dd60456b3e748493bca27e3c086');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000005_limpieza_ordenes_hasta_2025.sql', 0, '693b6b91a3bc95a60fe9c26ee0409804');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000006_drop_tablas_obsoletas.sql', 0, '51aac558b3dbcedc6946ae38433ef5f4');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000007_menu_quitar_paginas_obsoletas.sql', 0, '2db4846661a01fca6cb1e6b54f598955');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000008_limpieza_huerfanos_y_logs.sql', 0, '9591e618ed81a9b914b919821cdf134f');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000009_limpieza_preordenes_y_seguimiento.sql', 0, 'd18b405f945dd9cc17b0fd4a944908d6');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000010_drop_anuncios_y_esquema_web.sql', 0, 'e6f856e790036133a6bfa7720fa95591');
