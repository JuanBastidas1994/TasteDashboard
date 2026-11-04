-- add tb productos facturacion componentes
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

-- Artículos de Runfood en los que se factura un producto de Taste (ej. un combo que se factura como
-- 2 promociones). El precio neto del producto se reparte en partes iguales entre sus componentes.
-- Un producto con componentes no usa tb_productos_facturacion.
CREATE TABLE `tb_productos_facturacion_componentes` (
  `cod_componente` int NOT NULL AUTO_INCREMENT,
  `cod_producto` int NOT NULL,
  `cod_contifico_empresa` int NOT NULL COMMENT 'Sucursal Runfood (ruc)',
  `id_runfood` varchar(50) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `nombre_runfood` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cod_componente`),
  KEY `idx_producto_sucursal` (`cod_producto`,`cod_contifico_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
