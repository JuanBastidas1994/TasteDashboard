-- add max carreras tb usuarios
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Tope propio del motorizado (rol 17); NULL = usa el de su empresa (0 = sin límite).
ALTER TABLE `tb_usuarios`
  ADD COLUMN `max_carreras` tinyint unsigned DEFAULT NULL COMMENT 'Solo motorizados: carreras a la vez; NULL = usa tb_empresas.max_carreras (0 = sin límite)';
