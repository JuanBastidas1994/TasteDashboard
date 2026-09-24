-- Agrega el permiso GO_NOTIFICACIONES (grupo GESTION DE ORDENES)
-- Muestra el bloque "Notificaciones" (Pedido listo / Más) en el modal de detalle de la orden
INSERT INTO tb_permisos (identificador, nombre, grupo, descripcion, estado)
VALUES ('GO_NOTIFICACIONES', 'Enviar notificaciones al cliente desde el detalle de la orden', 'GESTION DE ORDENES', '', 'A');
