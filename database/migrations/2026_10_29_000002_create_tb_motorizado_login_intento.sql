-- create tb motorizado login intento
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Límite de intentos del login de la app de motorizados (8 cada 5 minutos por usuario + IP).
CREATE TABLE IF NOT EXISTS `tb_motorizado_login_intento` (
  `cod_intento` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(190) COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'rider-login:usuario|ip, rider-google:ip o rider-code:ip',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`cod_intento`),
  KEY `idx_motorizado_intento_clave` (`clave`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
