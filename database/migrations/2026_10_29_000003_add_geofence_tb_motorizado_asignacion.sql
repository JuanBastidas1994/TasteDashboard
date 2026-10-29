-- add geofence tb motorizado asignacion
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- fecha_cerca: cuándo quedó cerca del cliente. Las distancias guardan qué tan lejos estaba al marcar a mano
-- "llegué al local" / "entregado" (una marca lejos queda señalada para que la flota la revise).
ALTER TABLE `tb_motorizado_asignacion`
  ADD COLUMN `fecha_cerca` datetime DEFAULT NULL COMMENT 'Llegó cerca del cliente (geofence)' AFTER `push_cercania_enviado`,
  ADD COLUMN `llegada_local_distancia_m` int unsigned DEFAULT NULL COMMENT 'Metros al local al marcar la llegada; NULL = sin ubicación o marcada por GPS' AFTER `fecha_cerca`,
  ADD COLUMN `llegada_local_lejos` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Marcó la llegada estando lejos del local (y confirmó)' AFTER `llegada_local_distancia_m`,
  ADD COLUMN `entrega_distancia_m` int unsigned DEFAULT NULL COMMENT 'Metros al cliente al marcar entregado; NULL = sin ubicación' AFTER `llegada_local_lejos`,
  ADD COLUMN `entrega_lejos` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Marcó entregado estando lejos del cliente (y confirmó)' AFTER `entrega_distancia_m`;
