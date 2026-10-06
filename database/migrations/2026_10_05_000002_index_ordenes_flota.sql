-- Índices para los reportes de flota: filtran por cod_flota y cruzan por cod_orden.
-- Sin ellos, flota_reporte_cobro.php tardaba ~19s en un rango anual.

ALTER TABLE tb_ordenes_flota
    ADD INDEX idx_ordenes_flota_flota_orden (cod_flota, cod_orden),
    ADD INDEX idx_ordenes_flota_orden (cod_orden);
