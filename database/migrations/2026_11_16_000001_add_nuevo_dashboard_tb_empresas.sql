-- add nuevo dashboard tb empresas
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- 1 = la empresa ya migró al nuevo dashboard (admin.tastelatam.com): el legacy la redirige y la API rechaza nuevas órdenes. 0 = sigue en legacy.
ALTER TABLE `tb_empresas`
  ADD COLUMN `nuevo_dashboard` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = usa el nuevo dashboard (legacy redirige, API front no crea ordenes)';
