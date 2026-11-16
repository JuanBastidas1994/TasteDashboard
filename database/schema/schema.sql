-- Schema completo de Taste. GENERADO con: php bin/migrate.php schema:dump
-- No editar a mano: los cambios van en database/migrations/ y luego se regenera este archivo.
-- Generado: 2026-11-16 12:24:20 desde la BD 'jc_taste'

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
  `cart_token` varchar(64) NOT NULL,
  `cod_empresa` int unsigned NOT NULL,
  `cod_sucursal` int unsigned DEFAULT NULL,
  `cod_usuario` int unsigned DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `origen` enum('WEB','APP') NOT NULL DEFAULT 'WEB',
  `estado` enum('ACTIVO','ABANDONADO','CONVERTIDO','EXPIRADO') NOT NULL DEFAULT 'ACTIVO',
  `total_estimado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cantidad_productos` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `abandoned_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `converted_at` datetime DEFAULT NULL,
  `recovery_source` enum('EMAIL','PUSH','WHATSAPP') DEFAULT NULL,
  `cod_preorden` int unsigned DEFAULT NULL,
  `cod_orden` int unsigned DEFAULT NULL,
  `cart_json` json DEFAULT NULL COMMENT 'Snapshot completo del intento de compra',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_token` (`cart_token`),
  KEY `idx_empresa_estado` (`cod_empresa`,`estado`),
  KEY `idx_empresa_fecha` (`cod_empresa`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `currency` (
  `iso` char(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`iso`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `datos_compra` (
  `cod_dato_compra` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `apellido` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `empresa` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ciudad` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_dato_compra`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `log_error_app` (
  `cod_empresa` int DEFAULT NULL,
  `SO` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `version_code` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario_logeado` int DEFAULT NULL,
  `obs1` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `obs2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `error` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `log_pagos` (
  `cod_log_pago` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `detalle` text,
  `mensaje_resp` text,
  `estado_resp` text,
  `fecha` datetime DEFAULT NULL,
  `cod_transaccion` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`cod_log_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `mie_log_pago` (
  `cod_mie_log_pago` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `card_type` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `card_number` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `card_token` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_mie_log_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `mie_log_pago_error` (
  `cod_mie_log_pago_error` int NOT NULL AUTO_INCREMENT,
  `cod_mie_log_pago` int DEFAULT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `json` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`cod_mie_log_pago_error`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `mie_log_pago_success` (
  `cod_mie_log_pago_success` int NOT NULL AUTO_INCREMENT,
  `cod_mie_log_pago` int DEFAULT NULL,
  `transaction_id` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `transaction_status` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `transaction_reference` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `transaction_autorizacion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_mie_log_pago_success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `migrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `executed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `promocion_condicion` (
  `cod_condicion` int NOT NULL AUTO_INCREMENT,
  `cod_promocion` int NOT NULL,
  `tipo_condicion` enum('producto','monto') NOT NULL,
  `cod_producto` int DEFAULT NULL,
  `cantidad_necesaria` int DEFAULT NULL,
  `monto_minimo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`cod_condicion`),
  KEY `cod_promocion` (`cod_promocion`),
  CONSTRAINT `promocion_condicion_ibfk_1` FOREIGN KEY (`cod_promocion`) REFERENCES `promociones` (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `tipo_entrega` enum('DELIVERY','PICKUP','EN_MESA') NOT NULL,
  PRIMARY KEY (`cod_promocion`,`tipo_entrega`),
  CONSTRAINT `promocion_tipo_entrega_ibfk_1` FOREIGN KEY (`cod_promocion`) REFERENCES `promociones` (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `promociones` (
  `cod_promocion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_porcentaje` int DEFAULT '0',
  `valor` decimal(10,2) DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `texto` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `is_recurrente` tinyint(1) DEFAULT '0',
  `estado` enum('A','I','D') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  `imagen` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `tipo_promocion` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'descuento',
  `is_distintivo` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_academia_tutoriales` (
  `cod_tutorial` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `youtube_id` varchar(50) DEFAULT NULL,
  `duracion` varchar(10) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `categoria` varchar(60) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `posicion` int DEFAULT '0',
  PRIMARY KEY (`cod_tutorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_agenda` (
  `cod_agenda` int NOT NULL AUTO_INCREMENT,
  `cod_categoria` int NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `archivo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  `color` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_agenda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_agenda_categorias` (
  `cod_agenda_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_agenda_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_anuncio_cabecera` (
  `cod_anuncio_cabecera` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  PRIMARY KEY (`cod_anuncio_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_anuncio_detalle` (
  `cod_anuncio_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_anuncio_cabecera` int DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `subtitulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(600) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `text_boton` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url_boton` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accion_id` enum('FILTER','PRODUCTO','NOTICIA','URL','ZOOM','INFO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'URL',
  `categorias` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_anuncio_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `ubicacion` enum('top_left','top_center','top_right','center_left','center','center_right','bottom_left','bottom_center','bottom_right') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'bottom_center',
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

CREATE TABLE `tb_catalogo_items` (
  `cod_catalogo_item` int NOT NULL AUTO_INCREMENT,
  `cod_catalogo` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_catalogo_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_catalogos` (
  `cod_catalogo` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_catalogo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_cierre_caja` (
  `cod_cierre` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `valor_inicial` float NOT NULL,
  `hora_inicial` datetime NOT NULL,
  `efectivo_cierre` float DEFAULT NULL,
  `otros_cierre` float DEFAULT NULL,
  `hora_cierre` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_cierre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `tb_cliente_facturacion` (
  `cod_cliente_facturacion` int NOT NULL AUTO_INCREMENT,
  `id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  PRIMARY KEY (`cod_cliente_facturacion`)
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
  `clave` varchar(50) NOT NULL,
  `valor` text NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `courier_nombre` varchar(30) NOT NULL,
  `tariff_id` int NOT NULL DEFAULT '0',
  `latitud` decimal(10,7) NOT NULL,
  `longitud` decimal(10,7) NOT NULL,
  `distancia_km` decimal(10,3) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cod_usuario` int DEFAULT NULL,
  `device_type` varchar(10) DEFAULT NULL,
  `creado_en` datetime NOT NULL,
  `vigente_hasta` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vigente` (`vigente_hasta`),
  KEY `idx_sucursal` (`cod_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_cotizaciones_json` (
  `cod_cotizacion_json` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `json` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_create` date DEFAULT NULL,
  PRIMARY KEY (`cod_cotizacion_json`)
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

CREATE TABLE `tb_dashboard_helpdesk` (
  `cod_helpdesk` int NOT NULL AUTO_INCREMENT,
  `alias` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `titulo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `video` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_corta` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_larga` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_create` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `posicion` int DEFAULT '99',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_helpdesk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_demos` (
  `cod_demo` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alias` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `direccion` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `color` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_demo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_descuentos` (
  `cod_descuento` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `porcentaje` int DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `cod_empresa` int NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_descuento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_disponibilidad` (
  `cod_disponibilidad` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `cod_usuario` int DEFAULT NULL,
  `dia` int NOT NULL,
  `hora_inicio` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_final` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_disponibilidad`)
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

CREATE TABLE `tb_empresa_clickup` (
  `cod_empresa_clickup` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `id_lista` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_clickup`)
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

CREATE TABLE `tb_empresa_stripe` (
  `cod_empresa_stripe` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dev_public` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dev_secret` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pro_public` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pro_secret` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_stripe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_empresa_sucursal_medianet` (
  `cod_empresa_sucursal_medianet` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `key_webservice` varchar(36) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'development',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cod_empresa_sucursal_medianet`),
  UNIQUE KEY `uq_medianet_sucursal` (`cod_sucursal`)
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

CREATE TABLE `tb_empresa_tarjeta` (
  `cod_empresa_tarjeta` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `token` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `number` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reference` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `expiry_month` int DEFAULT NULL,
  `expiry_year` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_tarjeta`)
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
  `facebook_pixel_verify` varchar(250) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
  `menu_type` enum('grid','list') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'list',
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
  `tabs_order` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Orden de los tabs movibles del bottom bar de la app, separados por coma. Ej: giftcards,menu,billetera,pedidos. Valores validos: menu,orders,wallet,giftcards,profile. NULL = usa el default de la app (menu,orders,wallet,profile).',
  `mesa_tipo` enum('NUMERO','NOMBRE') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'NUMERO' COMMENT 'Tipo de identificación de mesa para pedidos EN_MESA',
  `prep_time_badge_minutes` int NOT NULL DEFAULT '120',
  `max_carreras` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Carreras a la vez por motorizado (0 = sin límite)',
  `nuevo_dashboard` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = usa el nuevo dashboard (legacy redirige, API front no crea ordenes)',
  PRIMARY KEY (`cod_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_empresas_suscripciones` (
  `cod_empresa_suscripcion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `origen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_empresa_suscripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'APK o tienda de la versión',
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

CREATE TABLE `tb_factmovil_empresa` (
  `cod_factmovil_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ambiente` enum('development','production') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_factmovil_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_factura_cabecera` (
  `cod_factura` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `tipo_documento` varchar(3) DEFAULT NULL,
  `emisor` varchar(3) DEFAULT NULL,
  `ptoemision` varchar(3) DEFAULT NULL,
  `secuencial` varchar(10) DEFAULT NULL,
  `num_factura` varchar(20) DEFAULT NULL,
  `num_autorizacion` varchar(30) DEFAULT NULL,
  `cod_cliente` int DEFAULT NULL,
  `cod_pto_emision` int DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cod_gift_card` int DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `descuento_porcentaje` float DEFAULT NULL,
  `impuesto_0` float DEFAULT NULL,
  `impuesto_12` float DEFAULT NULL,
  `total` float DEFAULT NULL,
  `num_items` int DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('ENVIADA','NO ENVIADA','ANULADA') DEFAULT NULL,
  PRIMARY KEY (`cod_factura`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_factura_detalle` (
  `cod_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_factura` int DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `costo_producto` float NOT NULL,
  `costo_total_producto` float NOT NULL,
  `nombre_producto` varchar(150) DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_total` float DEFAULT NULL,
  PRIMARY KEY (`cod_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_factura_pagos` (
  `cod_pagos` int NOT NULL AUTO_INCREMENT,
  `cod_factura` int DEFAULT NULL,
  `cod_tipo_pago` int DEFAULT NULL,
  `valor_pagado` float DEFAULT NULL,
  `valor_cambio` float DEFAULT NULL,
  `fecha` date NOT NULL,
  `detalle` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`cod_pagos`)
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
  `codigo` varchar(10) NOT NULL,
  `tipo` enum('GENERAL','REACTIVACION') NOT NULL,
  `cod_usuario_objetivo` int DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `fecha_uso` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_codigo`),
  UNIQUE KEY `uq_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `extra_params` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha` date DEFAULT NULL,
  `classname` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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

CREATE TABLE `tb_galeria` (
  `cod_galeria` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_categoria_galeria` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_min` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_max` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `isVideo` int DEFAULT NULL,
  `url_video` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_galeria`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_guia` (
  `cod_guia` int NOT NULL AUTO_INCREMENT,
  `cod_pagina` int DEFAULT NULL,
  `cod_rol` int DEFAULT NULL,
  `texto` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `elemento_inicio` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `evento_inicio` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `modal` int NOT NULL,
  `name_modal` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_guia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_guia_pasos` (
  `cod_paso` int NOT NULL AUTO_INCREMENT,
  `cod_guia` int DEFAULT NULL,
  `texto` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `elemento` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `evento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_paso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_guia_usuario` (
  `cod_guia_usuario` int NOT NULL AUTO_INCREMENT,
  `cod_guia` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  PRIMARY KEY (`cod_guia_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_idioma_frases` (
  `cod_frase` int NOT NULL AUTO_INCREMENT,
  `cod_idioma` int DEFAULT NULL,
  `etiqueta` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `texto` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_frase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_idiomas` (
  `cod_idioma` int NOT NULL AUTO_INCREMENT,
  `idioma` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bandera` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `prefijo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_idioma`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_importar_categorias` (
  `cod_importar_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_categoria` int DEFAULT NULL,
  `identificador` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_importar_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_importar_productos` (
  `cod_importar_producto` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `identificador` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_importar_producto`)
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

CREATE TABLE `tb_indisponibilidad` (
  `cod_indisponibilidad` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `dia` int NOT NULL,
  `hora_inicio` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_final` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_indisponibilidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_inventario` (
  `cod_inventario` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  PRIMARY KEY (`cod_inventario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_lista_deseos` (
  `cod_lista_deseos` int NOT NULL AUTO_INCREMENT,
  `cod_libro` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  PRIMARY KEY (`cod_lista_deseos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `tb_menu_digital` (
  `cod_menu_digital` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alias` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_menu_digital`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_menu_digital_imagenes` (
  `cod_menu_digital_imagenes` int NOT NULL AUTO_INCREMENT,
  `cod_menu_digital` int DEFAULT NULL,
  `imagen` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_menu_digital_imagenes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_metodos_pago` (
  `cod_metodo_pago` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_metodo_pago`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_modal_eventos` (
  `cod_modal_evento` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accion_id` enum('FILTER','PRODUCTO','NOTICIA','URL','ZOOM','INFO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'INFO',
  `accion_desc` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_modal_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `fecha_cerca` datetime DEFAULT NULL COMMENT 'Llegó cerca del cliente (geofence)',
  `llegada_local_distancia_m` int unsigned DEFAULT NULL COMMENT 'Metros al local al marcar la llegada; NULL = sin ubicación o marcada por GPS',
  `llegada_local_lejos` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Marcó la llegada estando lejos del local (y confirmó)',
  `entrega_distancia_m` int unsigned DEFAULT NULL COMMENT 'Metros al cliente al marcar entregado; NULL = sin ubicación',
  `entrega_lejos` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Marcó entregado estando lejos del cliente (y confirmó)',
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
  `estado` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=afiliacion activa, I=inactiva/retirada',
  `es_invitado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_afiliacion` datetime NOT NULL,
  PRIMARY KEY (`cod_motorizado_empresa`),
  UNIQUE KEY `uq_motorizado_empresa` (`cod_usuario`,`cod_empresa`),
  KEY `idx_cod_usuario` (`cod_usuario`),
  KEY `idx_cod_empresa` (`cod_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

CREATE TABLE `tb_motorizado_login_intento` (
  `cod_intento` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(190) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'rider-login:usuario|ip, rider-google:ip o rider-code:ip',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`cod_intento`),
  KEY `idx_motorizado_intento_clave` (`clave`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_motorizado_seguimiento` (
  `cod_motorizado_seguimiento` int NOT NULL AUTO_INCREMENT,
  `cod_motorizado` int DEFAULT NULL,
  `latitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `longitud` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_motorizado_seguimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_motorizado_token` (
  `cod_token` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL COMMENT 'Motorizado dueño de la sesión (tb_usuarios, rol 17)',
  `token_hash` char(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'sha256 del token; el token en claro nunca se guarda',
  `dispositivo` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'ANDROID | IOS, del header Device-Type',
  `ultimo_uso` datetime DEFAULT NULL,
  `fecha_revocado` datetime DEFAULT NULL COMMENT 'Cierre de sesión o baja del motorizado; NULL = sesión vigente',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_token`),
  UNIQUE KEY `uq_motorizado_token_hash` (`token_hash`),
  KEY `idx_motorizado_token_usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_niveles` (
  `cod_nivel` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
  `recompra_titulo` varchar(65) NOT NULL DEFAULT '',
  `recompra_mensaje` varchar(200) NOT NULL DEFAULT '',
  `fecha_update` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_notificaciones_auto_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `clave` varchar(30) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_tipo_clave` (`cod_usuario`,`tipo`,`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_notificaciones_expo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `data` text,
  `cod_usuario_admin` int NOT NULL,
  `total_enviados` int NOT NULL DEFAULT '0',
  `estado` varchar(15) NOT NULL DEFAULT 'ENVIADA',
  `audiencia` varchar(255) DEFAULT NULL,
  `fecha_programada` datetime DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `total_ok` int NOT NULL DEFAULT '0',
  `total_error` int NOT NULL DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cod_empresa` (`cod_empresa`),
  KEY `idx_programadas` (`estado`,`fecha_programada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_notificaciones_expo_aperturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notificacion_id` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notificacion_usuario` (`notificacion_id`,`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `telefono` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `referencia` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `referencia2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `datos_facturacion` text COLLATE utf8mb3_unicode_ci,
  `medio_compra` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `calificada` int NOT NULL DEFAULT '0',
  `cod_courier` int NOT NULL DEFAULT '0',
  `is_gacela` int NOT NULL DEFAULT '0',
  `order_token` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `api_version` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'v3',
  `app_version` int NOT NULL,
  `estado` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `recordatorio_calificacion_enviado` tinyint(1) NOT NULL DEFAULT '0',
  `is_altademanda` int DEFAULT '0',
  `distancia_km` decimal(6,3) DEFAULT NULL COMMENT 'Distancia real usada para calcular el precio de envio',
  `distancia_fuente` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'LINEA_RECTA o GOOGLE_MAPS',
  `courier_precio` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Quien calculo el precio: GOOGLE_MAPS, LINEA_RECTA, GACELA, PICKER, PEDIDOS_YA',
  `tariff_id` int DEFAULT NULL COMMENT 'Tarifa usada para el calculo de envio',
  `device_type` varchar(10) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'ANDROID, IOS o WEB, capturado al momento de validar la orden',
  `cotizacion_id` int DEFAULT NULL,
  `mesa_referencia` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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

CREATE TABLE `tb_orden_comanda` (
  `cod_orden_comanda` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `cod_sistema_facturacion` int NOT NULL,
  `cod_proveedor` int NOT NULL COMMENT 'cod_sucursal para Runfood',
  `external_order_id` varchar(50) NOT NULL COMMENT 'id del pedido en el sistema externo',
  `external_tab_id` varchar(50) DEFAULT NULL COMMENT 'id de la cuenta (tab) a facturar; puede ser 0',
  `order_number` varchar(20) DEFAULT NULL,
  `estado` enum('ABIERTA','FACTURADA','ANULADA') NOT NULL DEFAULT 'ABIERTA',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_orden_comanda`),
  KEY `idx_orden_comanda` (`cod_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `tipo` enum('FACTURA','COURIER','COMANDA') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
  `estado` enum('EJECUTAR','EJECUTADO') NOT NULL DEFAULT 'EJECUTAR',
  `cod_producto` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_orden_factura_electronica` (
  `cod_orden_factura_electronica` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int DEFAULT NULL,
  `tipo` enum('FAC','DNA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `num_factura` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `clave_acceso` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado_inventario` enum('NO_APLICA','NO_DEBITADO','DEBITADO','NO_REVERTIDO','REVERTIDO') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'NO_DEBITADO',
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
  `motivo` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR','OTRO') NOT NULL,
  `comentario` text,
  `fecha_reporte` datetime NOT NULL,
  `estado` enum('PENDIENTE','DESCARTADA','CONFIRMADA') NOT NULL DEFAULT 'PENDIENTE',
  `cod_admin_resuelve` int DEFAULT NULL,
  `resuelto_por` enum('MOTORIZADO','FLOTA','SUCURSAL') DEFAULT NULL,
  `motivo_resolucion` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR','OTRO') DEFAULT NULL,
  `comentario_resolucion` text,
  `fecha_resolucion` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_incidencia`),
  KEY `idx_orden_estado` (`cod_orden`,`estado`),
  KEY `fk_incidencia_motorizado` (`cod_motorizado`),
  CONSTRAINT `fk_incidencia_motorizado` FOREIGN KEY (`cod_motorizado`) REFERENCES `tb_usuarios` (`cod_usuario`),
  CONSTRAINT `fk_incidencia_orden` FOREIGN KEY (`cod_orden`) REFERENCES `tb_orden_cabecera` (`cod_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_orden_inventario` (
  `cod_orden_inventario` int NOT NULL AUTO_INCREMENT,
  `cod_contifico_empresa` int DEFAULT NULL,
  `cod_orden` int DEFAULT NULL,
  `tipo` enum('EGR','ING','TRA','AJU') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `codigo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` text COLLATE utf8mb3_unicode_ci,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_orden_inventario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_orden_json_entrante` (
  `cod_orden_json_entrante` int NOT NULL AUTO_INCREMENT,
  `cod_orden` int NOT NULL,
  `json` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_create` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_orden_json_entrante`)
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

CREATE TABLE `tb_paises` (
  `cod_pais` char(3) NOT NULL DEFAULT '',
  `nombre` char(52) NOT NULL DEFAULT '',
  `continente` varchar(50) NOT NULL DEFAULT 'Asia',
  `region` varchar(26) NOT NULL DEFAULT '',
  `area` float NOT NULL DEFAULT '0',
  `independencia` smallint DEFAULT NULL,
  `poblacion` int NOT NULL DEFAULT '0',
  `expectativaDeVida` float DEFAULT NULL,
  `productoInternoBruto` float DEFAULT NULL,
  `productoInternoBrutoAntiguo` float DEFAULT NULL,
  `nombreLocal` varchar(45) NOT NULL DEFAULT '',
  `gobierno` varchar(45) NOT NULL DEFAULT '',
  `jefeDeEstado` varchar(60) DEFAULT NULL,
  `capital` int DEFAULT NULL,
  `codigo2` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_pais`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `tb_personal` (
  `cod_personal` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `apellido` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_personal`)
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

CREATE TABLE `tb_planes` (
  `cod_plan` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `precio_mensual` float NOT NULL,
  `posicion` int NOT NULL,
  PRIMARY KEY (`cod_plan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_pos_fidelizacion` (
  `cod_pos_fidelizacion` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `secuencial` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `casher_id` int NOT NULL,
  `office_id` int NOT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `points` int NOT NULL,
  `total` int NOT NULL,
  `create_at` datetime NOT NULL,
  `status` int NOT NULL,
  PRIMARY KEY (`cod_pos_fidelizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_preorden_json` (
  `cod_preorden` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('VALIDADA','CREANDO_ORDEN','PAGADA','PAGADA_NO_CREADA','FALLADA','CERRADA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_detail` int DEFAULT NULL,
  `paymentId` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paymentAuth` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lot_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_intentos_creacion` int NOT NULL DEFAULT '0',
  `cod_orden` int DEFAULT '0',
  `amount` float DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `motivo_fallo` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook` int DEFAULT NULL,
  PRIMARY KEY (`cod_preorden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_preorden_token_json` (
  `cod_preorden` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `json` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('VALIDADA','CREANDO_ORDEN','PAGADA','FALLADA','CERRADA') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `paymentId` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `paymentAuth` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_orden` int DEFAULT '0',
  `amount` float DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `motivo_fallo` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_preorden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_prioridad` (
  `cod_prioridad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_prioridad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

CREATE TABLE `tb_producto_tarifa_forzada` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `cod_tarifa` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id_contifico` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `intervalo` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_path` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `oahu_arma_bowl` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tiempo_preparacion` int DEFAULT '0',
  `venta_delivery` tinyint(1) NOT NULL DEFAULT '1',
  `venta_pickup` tinyint(1) NOT NULL DEFAULT '1',
  `venta_mesa` tinyint(1) NOT NULL DEFAULT '1',
  `precio_especial` decimal(10,2) DEFAULT NULL,
  `precio_especial_inicio` date DEFAULT NULL,
  `precio_especial_fin` date DEFAULT NULL,
  PRIMARY KEY (`cod_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_archivos` (
  `cod_archivo` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `nombre_archivo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int NOT NULL,
  PRIMARY KEY (`cod_archivo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_categorias` (
  `cod_producto_categoria` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_categoria` int DEFAULT NULL,
  PRIMARY KEY (`cod_producto_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_productos_descripciones` (
  `cod_productos_descripciones` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `titulo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posicion` int DEFAULT NULL,
  PRIMARY KEY (`cod_productos_descripciones`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_detalle` (
  `cod_producto_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_producto_padre` int NOT NULL,
  `cod_producto_hijo` int NOT NULL,
  `cantidad` int NOT NULL,
  PRIMARY KEY (`cod_producto_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_dias` (
  `cod_producto_dias` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `dia` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
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
  `sku` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_producto` int DEFAULT NULL,
  `cod_sistema_facturacion` int DEFAULT NULL,
  `name_in_contifico` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_contifico_empresa` int NOT NULL,
  PRIMARY KEY (`cod_producto_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_facturacion_componentes` (
  `cod_componente` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `cod_contifico_empresa` int NOT NULL COMMENT 'Sucursal Runfood (ruc)',
  `id_runfood` varchar(50) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `nombre_runfood` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_componente`),
  KEY `idx_producto_sucursal` (`cod_producto`,`cod_contifico_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

CREATE TABLE `tb_productos_kiosco` (
  `cod_producto_kiosco` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_custom` int DEFAULT NULL,
  PRIMARY KEY (`cod_producto_kiosco`)
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
  `id_runfood` varchar(50) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `nombre_runfood` varchar(200) DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_opcion_detalle_facturacion`),
  UNIQUE KEY `uq_detalle_empresa` (`cod_producto_opciones_detalle`,`cod_contifico_empresa`),
  CONSTRAINT `tb_productos_opciones_detalle_facturacion_ibfk_1` FOREIGN KEY (`cod_producto_opciones_detalle`) REFERENCES `tb_productos_opciones_detalle` (`cod_producto_opciones_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_productos_opciones_ingredientes` (
  `cod_producto_opcion_ingrediente` int NOT NULL AUTO_INCREMENT,
  `cod_producto_opcion` int DEFAULT NULL,
  `cod_ingrediente` int DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_producto_opcion_ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_preferencia` (
  `cod_producto_preferencia` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_producto_preferencia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_productos_usuarios` (
  `cod_producto` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_productos_variante` (
  `cod_producto_variante` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `atributo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_producto_variante`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_programa_usuario` (
  `cod_programa_usuario` int NOT NULL AUTO_INCREMENT,
  `cod_programa` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `nombre_alumno` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `user_update` int DEFAULT NULL,
  `fecha_last_pago` date DEFAULT NULL,
  PRIMARY KEY (`cod_programa_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_programas` (
  `cod_programa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `precio` float DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_programa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_promocion_pos` (
  `cod_promocion_pos` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int DEFAULT NULL,
  `dia` int NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `precio_tarjeta` float NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_promocion_pos`)
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

CREATE TABLE `tb_promociones` (
  `cod_promocion` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `descripcion_corta` text,
  `descripcion_larga` text,
  `imagen_min` varchar(200) DEFAULT NULL,
  `imagen_max` varchar(200) DEFAULT NULL,
  `dia` int NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_proveedor_botonpagos` (
  `cod_proveedor_botonpagos` int NOT NULL AUTO_INCREMENT,
  `identificador` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT '99',
  PRIMARY KEY (`cod_proveedor_botonpagos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_proveedores` (
  `cod_proveedor` int NOT NULL AUTO_INCREMENT,
  `cod_tipo_documento` int NOT NULL,
  `num_documento` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `logo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `correo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `direccion` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_empresa` int NOT NULL,
  `observacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_ptos_emision` (
  `cod_pto_emision` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `pto_emision` int DEFAULT NULL,
  `secuencialFAC` int DEFAULT NULL,
  `secuencialDNA` int DEFAULT NULL,
  `secuencialCOT` int NOT NULL DEFAULT '1',
  `autorizacion` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bloque_inicial` int DEFAULT NULL,
  `bloque_final` int DEFAULT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_pto_emision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_push_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `plataforma` varchar(10) DEFAULT NULL,
  `sonido` varchar(50) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_cod_usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

CREATE TABLE `tb_rol_pagos` (
  `cod_rol_pago` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_empleado` int DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_inicio_laboral` date DEFAULT NULL,
  `fecha_fin_laboral` date DEFAULT NULL,
  `sueldo` double DEFAULT NULL,
  `valor_sueldo` double DEFAULT NULL,
  `horas_extra_50` double DEFAULT NULL,
  `horas_extra_100` double DEFAULT NULL,
  `fondo_reserva` double DEFAULT NULL,
  `comision` double DEFAULT NULL,
  `bono` double DEFAULT NULL,
  `aporte_iess` double DEFAULT NULL,
  `prestamo` double DEFAULT NULL,
  `atraso` double DEFAULT NULL,
  `total_ingreso` double DEFAULT NULL,
  `total_egreso` double DEFAULT NULL,
  `total_pagado` double DEFAULT NULL,
  PRIMARY KEY (`cod_rol_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_roles` (
  `cod_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_rol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_roles_empresa` (
  `cod_roles_empresa` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `cod_rol` int DEFAULT NULL,
  PRIMARY KEY (`cod_roles_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_runfood_producto_opcion_detalle` (
  `cod_runfood_producto_opcion_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `cod_runfood` int NOT NULL,
  PRIMARY KEY (`cod_runfood_producto_opcion_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_runfood_productos` (
  `cod_runfood_producto` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `cod_runfood` int NOT NULL,
  PRIMARY KEY (`cod_runfood_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_runfood_sucursal` (
  `cod_runfood_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `dominio` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `facturar` int DEFAULT '0',
  `tipo_documento` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'FAC',
  PRIMARY KEY (`cod_runfood_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_salsas` (
  `cod_salsa` int NOT NULL AUTO_INCREMENT,
  `salsa` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_salsa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_shopping_car` (
  `cod_shopping_car` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `cod_usuario` int NOT NULL,
  `cod_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`cod_shopping_car`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_sistema_facturacion` (
  `cod_sistema_facturacion` int NOT NULL AUTO_INCREMENT,
  `identificador` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_sistema_facturacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_size_crop` (
  `cod_size_crop` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `tipo` enum('productos','productos_gallery','bannner','blog','blog_gallery') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `size_min_width` int DEFAULT NULL,
  `size_min_height` int DEFAULT NULL,
  `size_max_width` int DEFAULT NULL,
  `size_max_height` int DEFAULT NULL,
  `quality` float NOT NULL,
  PRIMARY KEY (`cod_size_crop`)
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

CREATE TABLE `tb_stock` (
  `cod_stock` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_sucursal` int DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_stock`)
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
  `cod_forma_pago` varchar(50) NOT NULL,
  `monto_maximo` decimal(10,2) DEFAULT '0.00',
  `descripcion` text,
  `is_delivery` tinyint(1) DEFAULT '1',
  `is_pickup` tinyint(1) DEFAULT '1',
  `estado` varchar(1) DEFAULT 'A',
  PRIMARY KEY (`cod_sucursal_forma_pago`),
  UNIQUE KEY `uq_suc_fp` (`cod_sucursal`,`cod_forma_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `transferencia_img` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `banner_xl` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `delivery` int NOT NULL DEFAULT '1',
  `insite` int NOT NULL DEFAULT '0',
  `pickup` int NOT NULL DEFAULT '1',
  `show_banner` int NOT NULL DEFAULT '1',
  `tipo_bodega` int NOT NULL DEFAULT '0',
  `programar_pedido` int NOT NULL DEFAULT '0',
  `grava_iva` int NOT NULL DEFAULT '1',
  `envio_grava_iva` int NOT NULL DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_sucursal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_system_notification` (
  `cod_system_notification` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `icono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalle` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` enum('A','I','D','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'A',
  PRIMARY KEY (`cod_system_notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tb_system_notification_tipos` (
  `cod_tipo_system_notification` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icono` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'A',
  PRIMARY KEY (`cod_tipo_system_notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(70) NOT NULL,
  `icono` varchar(20) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `es_predefinido` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_tarifa` (
  `cod_tarifa` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `nombre` varchar(50) NOT NULL DEFAULT 'Estándar',
  `peso_max_kg` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`cod_tarifa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `tb_tarjetas` (
  `cod_tarjeta` int NOT NULL AUTO_INCREMENT,
  `id1` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `id2` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_tarjeta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_telegram` (
  `cod_telegram` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `botname` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`cod_telegram`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tb_telegram_grupos` (
  `cod_telegram_grupo` int NOT NULL AUTO_INCREMENT,
  `cod_chat` varchar(50) DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `estado` enum('A','I','D') DEFAULT NULL,
  PRIMARY KEY (`cod_telegram_grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tb_telegram_sucursal` (
  `cod_telegram_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `code` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('PENDIENTE','ACTIVO','INACTIVO') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `chat_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_telegram_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_telegram_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `chat_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `code` varchar(15) NOT NULL,
  `estado` enum('A','P','I','D') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tb_telegram_usuarios_ubicacion` (
  `cod_ubicacion` int NOT NULL AUTO_INCREMENT,
  `cod_telegram_usuario` int DEFAULT NULL,
  `cod_chat` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `latitud` varchar(25) DEFAULT NULL,
  `longitud` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`cod_ubicacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tb_test` (
  `test` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_timeline_detalles` (
  `cod_timeline_detalle` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `subtitulo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imagen` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` int NOT NULL,
  `cod_timeline` int NOT NULL,
  PRIMARY KEY (`cod_timeline_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_timelines` (
  `cod_timeline` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  `cod_empresa` int NOT NULL,
  `cod_producto` int NOT NULL,
  PRIMARY KEY (`cod_timeline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_tipo_correo` (
  `cod_tipo_correo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_tipo_correo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_transaccion` (
  `cod_transaccion` varchar(25) NOT NULL,
  `cod_usuario` int DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `monto` float NOT NULL,
  `idPayphone` varchar(15) NOT NULL,
  `estado` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`cod_transaccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_unidades_medidas` (
  `cod_unidad_medida` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_unidad_medida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_updates` (
  `cod_update` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `desc_corta` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo_multimedia` int NOT NULL,
  `fecha_create` datetime NOT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_updates_detalle` (
  `cod_update_detalle` int NOT NULL AUTO_INCREMENT,
  `cod_update` int NOT NULL,
  `cod_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_update_detalle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_updates_visualizado` (
  `cod_update_visualizado` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cod_update` int DEFAULT NULL,
  PRIMARY KEY (`cod_update_visualizado`)
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

CREATE TABLE `tb_usuario_client_email` (
  `cod_usuario_client_email` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `correo_user` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo_pass` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imap_server` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `imap_port` int DEFAULT NULL,
  `smtp_server` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `smtp_port` int DEFAULT NULL,
  `folders` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_usuario_client_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_usuario_cliente` (
  `cod_usuario` int DEFAULT NULL,
  `cod_cliente` int DEFAULT NULL
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

CREATE TABLE `tb_usuario_cupon` (
  `cod_usuario_cupon` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int DEFAULT NULL,
  `cupon` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_cupon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_usuario_giftcards_compradas` (
  `cod_usuario_giftcard_comprada` int NOT NULL AUTO_INCREMENT,
  `cod_giftcard` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` enum('A','I','U','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_giftcard_comprada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

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

CREATE TABLE `tb_usuario_mis_giftcards` (
  `cod_usuario_mis_giftcard` int NOT NULL AUTO_INCREMENT,
  `codigo` int DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_mis_giftcard`)
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
  `imagen` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apple_user_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono_verificado` int NOT NULL DEFAULT '0',
  `direccion` varchar(300) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `num_documento` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `cod_idioma` int NOT NULL DEFAULT '1',
  `cod_sucursal` int NOT NULL DEFAULT '0',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recuperacion_pass` int NOT NULL DEFAULT '0',
  `latitud` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `longitud` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `placa` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `is_active` int NOT NULL DEFAULT '1',
  `fecha_ubicacion` datetime DEFAULT NULL,
  `motivo_bloqueo` text COLLATE utf8mb3_unicode_ci,
  `max_carreras` tinyint unsigned DEFAULT NULL COMMENT 'Solo motorizados: carreras a la vez; NULL = usa tb_empresas.max_carreras (0 = sin límite)',
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

CREATE TABLE `tb_visitantes` (
  `cod_visitante` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(60) DEFAULT NULL,
  `apellidos` varchar(60) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `celular` varchar(60) DEFAULT NULL,
  `institucion` varchar(100) DEFAULT NULL,
  `cod_pais` varchar(10) DEFAULT NULL,
  `ciudad` varchar(60) DEFAULT NULL,
  `cod_usuario` int DEFAULT NULL,
  `cod_feria` int DEFAULT NULL,
  `cod_medio` int DEFAULT NULL,
  `qr` varchar(100) DEFAULT NULL,
  `user_create` int DEFAULT NULL,
  `fecha_create` datetime DEFAULT NULL,
  `cod_empresa` int DEFAULT NULL,
  PRIMARY KEY (`cod_visitante`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_web_adicionales` (
  `cod_web_adicionales` int NOT NULL AUTO_INCREMENT,
  `cod_categoria` int DEFAULT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cod_categoria_items` int DEFAULT NULL,
  `posicion` int DEFAULT '99',
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cod_web_adicionales`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tb_web_esquema` (
  `cod_web_esquema` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `forma` enum('lista_4','slide_4','banner','youtube') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` enum('ordenar','anuncios','youtube') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `plataforma` enum('WEB','APP') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'WEB',
  `num_columnas` int NOT NULL,
  `detalle` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_detalle` int NOT NULL,
  `posicion` int NOT NULL,
  `fecha_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_web_esquema`)
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

CREATE TABLE `tb_web_servicios` (
  `cod_web_servicio` int NOT NULL AUTO_INCREMENT,
  `cod_empresa` int NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `descripcion` text,
  `imagen` text,
  `estado` enum('A','I','D') NOT NULL,
  PRIMARY KEY (`cod_web_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tb_zonas_sucursal` (
  `cod_zona_sucursal` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int DEFAULT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `costo` float DEFAULT NULL,
  `tiempo` int DEFAULT NULL,
  `estado` enum('A','I','D') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`cod_zona_sucursal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `timezones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `timezone` varchar(499) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE OR REPLACE VIEW `view_asignacion_motorizado` AS select `oc`.`cod_orden` AS `cod_orden`,`u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`u`.`cedula` AS `num_documento`,`u`.`imagen` AS `foto`,`u`.`telefono` AS `telefono`,`oc`.`cod_courier` AS `is_gacela`,`u`.`cod_usuario` AS `cod_usuario` from ((`tb_orden_cabecera` `oc` join `tb_motorizado_asignacion` `ma` on((`ma`.`cod_orden` = `oc`.`cod_orden`))) join `tb_usuarios` `u` on((`u`.`cod_usuario` = `ma`.`cod_motorizado`))) where ((`u`.`cod_rol` = 17) and (`oc`.`cod_courier` in ('0','1'))) union select `u`.`cod_orden` AS `cod_orden`,`u`.`nombre` AS `nombre`,`u`.`apellido` AS `apellido`,`u`.`num_documento` AS `num_documento`,`u`.`foto` AS `foto`,`u`.`telefono` AS `telefono`,`oc`.`cod_courier` AS `is_gacela`,0 AS `cod_usuario` from (`tb_orden_motorizado` `u` join `tb_orden_cabecera` `oc` on((`u`.`cod_orden` = `oc`.`cod_orden`)));

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
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000006_drop_tablas_obsoletas.sql', 0, '1db4646bbb7928853f02ed2e854119f2');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000007_menu_quitar_paginas_obsoletas.sql', 0, '2db4846661a01fca6cb1e6b54f598955');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000008_limpieza_huerfanos_y_logs.sql', 0, '9591e618ed81a9b914b919821cdf134f');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000009_limpieza_preordenes_y_seguimiento.sql', 0, 'd18b405f945dd9cc17b0fd4a944908d6');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_01_000010_drop_anuncios_y_esquema_web.sql', 0, 'e6f856e790036133a6bfa7720fa95591');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_05_000001_menu_flota_reporte_cobro.sql', 0, '5d0a10818c9583b63a02fc618996e713');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_05_000002_index_ordenes_flota.sql', 0, '8965a078cd1cb1bd1ad45521381b1ade');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_05_000003_index_orden_motorizado_cod_orden.sql', 0, '180bc69dd90f39c4963f9ca01c7776e6');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_06_000001_recrear_tb_telegram_usuarios.sql', 0, 'ecb94965a6bd654dd3a97c360ac3f205');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_07_000001_convert_tb_notificaciones_expo_to_utf8mb4.sql', 0, '3085fb88242f3080f5d3d17c3fb8ab67');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_22_000001_create_tb_empresa_sucursal_medianet.sql', 0, '5026ae3affbd228541c645401ee69702');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_22_000002_seed_proveedor_medianet.sql', 0, '6f6133fa6a08dcd0f4c8058d20c529d8');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000001_create_tb_motorizado_token.sql', 0, 'a78009c881f1955228b4909781b28d9a');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000002_create_tb_motorizado_login_intento.sql', 0, '5cdca39ee27655b39c16c731082403f7');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000003_add_geofence_tb_motorizado_asignacion.sql', 0, 'd59869fc5fe29e3d3227fa14672102d7');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000004_add_max_carreras_tb_empresas.sql', 0, '177b6141d671fea6b0a6e7929a5321e1');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000005_add_max_carreras_tb_usuarios.sql', 0, 'da55a1b4044a3adfb3f05a283a46c6e4');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000006_add_url_tb_empresas_versiones_app.sql', 0, 'b7809bc42c88f21879ce978307d11ea3');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_29_000007_add_otro_motivo_tb_orden_incidencia.sql', 0, 'af3b18233934006fb5cd54b292bc50fd');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_10_30_000001_add_tb_productos_facturacion_componentes.sql', 0, '98af4fa9de353858c1dd1b8461147a78');
INSERT INTO `schema_migrations` (`migration`, `batch`, `checksum`) VALUES ('2026_11_16_000001_add_nuevo_dashboard_tb_empresas.sql', 0, '771388af40919bd0163afed94ac7fce3');
