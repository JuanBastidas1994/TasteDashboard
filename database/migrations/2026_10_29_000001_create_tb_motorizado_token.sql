-- create tb motorizado token
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Sesión de la app de motorizados (api/riders/v1): reemplaza al User-Id crudo de api_flotas.
-- Solo se guarda el sha256 del token; el token en claro se entrega una vez al iniciar sesión.
CREATE TABLE IF NOT EXISTS `tb_motorizado_token` (
  `cod_token` int NOT NULL AUTO_INCREMENT,
  `cod_usuario` int NOT NULL COMMENT 'Motorizado dueño de la sesión (tb_usuarios, rol 17)',
  `token_hash` char(64) COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'sha256 del token; el token en claro nunca se guarda',
  `dispositivo` varchar(60) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'ANDROID | IOS, del header Device-Type',
  `ultimo_uso` datetime DEFAULT NULL,
  `fecha_revocado` datetime DEFAULT NULL COMMENT 'Cierre de sesión o baja del motorizado; NULL = sesión vigente',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_token`),
  UNIQUE KEY `uq_motorizado_token_hash` (`token_hash`),
  KEY `idx_motorizado_token_usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
