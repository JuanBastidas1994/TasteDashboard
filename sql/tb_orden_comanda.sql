-- ============================================================
-- MIGRACIÓN: tb_orden_comanda
-- Pedido abierto (comanda) enviado a un sistema externo que también es POS/cocina
-- (hoy solo Runfood). Se crea cuando la orden sale de ENTRANTE: el pedido se abre en
-- Runfood (imprime la comanda en cocina) y se factura al ENTREGAR la orden.
-- Es independiente de tb_orden_factura_electronica, que solo registra la factura.
-- ============================================================

CREATE TABLE IF NOT EXISTS tb_orden_comanda (
    cod_orden_comanda       INT AUTO_INCREMENT PRIMARY KEY,
    cod_orden               INT          NOT NULL,
    cod_sistema_facturacion INT          NOT NULL,
    cod_proveedor           INT          NOT NULL COMMENT 'cod_sucursal para Runfood',
    external_order_id       VARCHAR(50)  NOT NULL COMMENT 'id del pedido en el sistema externo',
    external_tab_id         VARCHAR(50)  NULL     COMMENT 'id de la cuenta (tab) a facturar; puede ser 0',
    order_number            VARCHAR(20)  NULL,
    estado                  ENUM('ABIERTA','FACTURADA','ANULADA') NOT NULL DEFAULT 'ABIERTA',
    fecha                   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion     DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_orden_comanda (cod_orden)
);
