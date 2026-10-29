-- add url tb empresas versiones app
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Dónde descarga la app el APK (o la tienda) cuando hay una versión nueva.
ALTER TABLE `tb_empresas_versiones_app`
  ADD COLUMN `url` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'APK o tienda de la versión';
