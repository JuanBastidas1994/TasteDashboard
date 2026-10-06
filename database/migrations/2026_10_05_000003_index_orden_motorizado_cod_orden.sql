-- Índice para obtener el motorizado de una orden (flota_reporte_cobro.php y similares).

ALTER TABLE tb_orden_motorizado
    ADD INDEX idx_orden_motorizado_orden (cod_orden);
