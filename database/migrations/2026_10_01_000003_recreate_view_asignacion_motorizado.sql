-- Recrea la vista view_asignacion_motorizado (quedó inválida en producción:
-- phpMyAdmin no puede leer sus columnas y rompe el export completo de la BD).
-- Causa: tb_usuarios.cedula fue renombrada a tb_usuarios.num_documento.
-- Usada por design.php (detalle de asignación de motorizado en la orden).

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `view_asignacion_motorizado` AS
SELECT `oc`.`cod_orden` AS `cod_orden`,
       `u`.`nombre` AS `nombre`,
       `u`.`apellido` AS `apellido`,
       `u`.`num_documento` AS `num_documento`,
       `u`.`imagen` AS `foto`,
       `u`.`telefono` AS `telefono`,
       `oc`.`cod_courier` AS `is_gacela`,
       `u`.`cod_usuario` AS `cod_usuario`
FROM `tb_orden_cabecera` `oc`
JOIN `tb_motorizado_asignacion` `ma` ON `ma`.`cod_orden` = `oc`.`cod_orden`
JOIN `tb_usuarios` `u` ON `u`.`cod_usuario` = `ma`.`cod_motorizado`
WHERE `u`.`cod_rol` = 17
  AND `oc`.`cod_courier` IN ('0','1')
UNION
SELECT `u`.`cod_orden` AS `cod_orden`,
       `u`.`nombre` AS `nombre`,
       `u`.`apellido` AS `apellido`,
       `u`.`num_documento` AS `num_documento`,
       `u`.`foto` AS `foto`,
       `u`.`telefono` AS `telefono`,
       `oc`.`cod_courier` AS `is_gacela`,
       0 AS `cod_usuario`
FROM `tb_orden_motorizado` `u`
JOIN `tb_orden_cabecera` `oc` ON `u`.`cod_orden` = `oc`.`cod_orden`;
