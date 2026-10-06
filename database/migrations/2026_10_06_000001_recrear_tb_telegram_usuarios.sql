-- Recrea tb_telegram_usuarios, que la limpieza 2026_10_01_000006 borró por error.
-- La usan las notificaciones por Telegram a administradores (permiso NOTIFY_TELEGRAM):
--   * api: aviso de nueva orden a admins de empresa y de la sucursal.
--   * api_gestion_ordenes: aviso al administrador de la flota cuando se le asigna una orden.
-- IF NOT EXISTS: si el entorno nunca ejecutó el DROP (000006 ya corregido), no hace nada.
-- Estructura original de producción (id, cod_usuario, chat_id, user_id, code, estado); estado: A activo, P pendiente.
-- Los datos (30 vínculos al 2026-10-01) se recuperan del dump original, no de esta migración.

CREATE TABLE IF NOT EXISTS tb_telegram_usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    cod_usuario INT DEFAULT NULL,
    chat_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    code VARCHAR(15) NOT NULL,
    estado ENUM('A','P','I','D') NOT NULL,
    PRIMARY KEY (id),
    KEY idx_telegram_usuarios_usuario (cod_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
