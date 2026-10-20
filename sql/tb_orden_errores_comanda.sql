-- ============================================================
-- MIGRACIÓN: tb_orden_errores admite errores de COMANDA
-- Cuando no se puede abrir el pedido en Runfood al aceptar/asignar, el motivo se guarda aquí
-- para mostrarlo en la tarjeta y el detalle de la orden (Gestión de Órdenes).
-- ============================================================

ALTER TABLE tb_orden_errores
    MODIFY COLUMN tipo ENUM('FACTURA','COURIER','COMANDA') NULL;
