-- add otro motivo tb orden incidencia
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Motivo "OTRO" de la API de motorizados (/api/riders/v1), igual que en el dashboard nuevo.
ALTER TABLE `tb_orden_incidencia`
  MODIFY COLUMN `motivo` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR','OTRO') NOT NULL,
  MODIFY COLUMN `motivo_resolucion` enum('NO_CONTESTA','DIRECCION_INCORRECTA','CLIENTE_RECHAZO','NO_QUISO_PAGAR','OTRO') DEFAULT NULL;
