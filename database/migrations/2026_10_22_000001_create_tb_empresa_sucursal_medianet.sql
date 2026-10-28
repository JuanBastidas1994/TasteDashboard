-- create tb empresa sucursal medianet
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Credenciales de MedianetPay (WebCheckout REST) por sucursal.
-- key_webservice es secreta: solo la lee el API, nunca se envía al navegador.
CREATE TABLE IF NOT EXISTS `tb_empresa_sucursal_medianet` (
  `cod_empresa_sucursal_medianet` int NOT NULL AUTO_INCREMENT,
  `cod_sucursal` int NOT NULL,
  `username` varchar(30) COLLATE utf8mb3_unicode_ci NOT NULL,
  `key_webservice` varchar(36) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ambiente` enum('development','production') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'development',
  `estado` enum('A','I','D') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cod_empresa_sucursal_medianet`),
  UNIQUE KEY `uq_medianet_sucursal` (`cod_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
