-- ============================================================
-- MIGRACIÓN: tb_notificaciones_expo
-- Histórico de notificaciones push (Expo) enviadas a clientes
-- desde el dashboard (promos, productos nuevos, eventos).
-- No reemplaza tb_notificaciones (esa es del flujo FCM/órdenes).
-- ============================================================

CREATE TABLE IF NOT EXISTS tb_notificaciones_expo (
    id                INT NOT NULL AUTO_INCREMENT,
    cod_empresa       INT NOT NULL,
    tipo              VARCHAR(20) NOT NULL,   -- promo | producto_nuevo | evento
    titulo            VARCHAR(150) NOT NULL,
    mensaje           TEXT NOT NULL,
    data              TEXT NULL,              -- json extra (cod_producto, etc)
    cod_usuario_admin INT NOT NULL,           -- quien la disparo desde el dashboard
    total_enviados    INT NOT NULL DEFAULT 0,
    fecha             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cod_empresa (cod_empresa)
);
