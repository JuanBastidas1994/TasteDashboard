-- Agrega el permiso GO_SCAN_BARCODE (grupo GESTION DE ORDENES)
-- Permite activar por empresa el escaneo de código de billetera del cliente (estrella de Fidelización)
INSERT INTO tb_permisos (identificador, nombre, grupo, descripcion, estado)
VALUES ('GO_SCAN_BARCODE', 'Poder scanear codigo de billetera del cliente', 'GESTION DE ORDENES', '', 'A');
