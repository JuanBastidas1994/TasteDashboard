-- convert tb notificaciones expo to utf8mb4
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- En producción la tabla quedó en utf8mb3 y no guarda emojis (4 bytes).
ALTER TABLE tb_notificaciones_expo
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
