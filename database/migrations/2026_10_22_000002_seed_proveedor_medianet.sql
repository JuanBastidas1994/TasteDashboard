-- seed proveedor medianet
-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).

INSERT IGNORE INTO `tb_proveedor_botonpagos` (`cod_proveedor_botonpagos`, `identificador`, `nombre`, `imagen`, `posicion`) VALUES
    (4, 'MEDIANET', 'Medianet', 'medianet.png', 4);
