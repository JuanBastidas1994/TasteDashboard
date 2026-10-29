-- add max carreras tb empresas
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Carreras que un motorizado de la flota/comercio puede tener aceptadas y sin entregar a la vez (0 = sin límite).
ALTER TABLE `tb_empresas`
  ADD COLUMN `max_carreras` tinyint unsigned NOT NULL DEFAULT 1 COMMENT 'Carreras a la vez por motorizado (0 = sin límite)';
