-- Notificaciones push (Expo) v2: programadas, audiencia, métricas de entrega/apertura y automáticas
-- (calificación, cumpleaños, recompra) que corren en taste/cron/notificaciones_push.php.

-- Historial/campañas: ahora también guarda las programadas y el resultado real del envío
ALTER TABLE tb_notificaciones_expo
    ADD COLUMN estado VARCHAR(15) NOT NULL DEFAULT 'ENVIADA' AFTER total_enviados,
    ADD COLUMN audiencia VARCHAR(255) NULL AFTER estado,
    ADD COLUMN fecha_programada DATETIME NULL AFTER audiencia,
    ADD COLUMN fecha_envio DATETIME NULL AFTER fecha_programada,
    ADD COLUMN total_ok INT NOT NULL DEFAULT 0 AFTER fecha_envio,
    ADD COLUMN total_error INT NOT NULL DEFAULT 0 AFTER total_ok,
    ADD INDEX idx_programadas (estado, fecha_programada);

UPDATE tb_notificaciones_expo SET fecha_envio = fecha, total_ok = total_enviados WHERE fecha_envio IS NULL;

-- Aperturas: la app reporta el toque (api: POST notificaciones/abierta). Una por usuario y notificación.
CREATE TABLE IF NOT EXISTS tb_notificaciones_expo_aperturas (
    id INT NOT NULL AUTO_INCREMENT,
    notificacion_id INT NOT NULL,
    cod_usuario INT NOT NULL,
    fecha DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_notificacion_usuario (notificacion_id, cod_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Configuración de automáticas por empresa (calificación y cumpleaños van siempre, solo recompra se configura)
CREATE TABLE IF NOT EXISTS tb_notificaciones_auto_config (
    cod_empresa INT NOT NULL,
    recompra_activo TINYINT(1) NOT NULL DEFAULT 0,
    recompra_dias INT NOT NULL DEFAULT 15,
    recompra_titulo VARCHAR(65) NOT NULL DEFAULT '',
    recompra_mensaje VARCHAR(200) NOT NULL DEFAULT '',
    fecha_update DATETIME NULL,
    PRIMARY KEY (cod_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Evita repetir automáticas: cumpleaños 1 vez por año, recompra 1 vez por ciclo (clave = última orden)
CREATE TABLE IF NOT EXISTS tb_notificaciones_auto_log (
    id INT NOT NULL AUTO_INCREMENT,
    cod_empresa INT NOT NULL,
    cod_usuario INT NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    clave VARCHAR(30) NOT NULL,
    fecha DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuario_tipo_clave (cod_usuario, tipo, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
