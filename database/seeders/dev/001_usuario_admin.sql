-- SOLO DESARROLLO (php bin/migrate.php seed --dev; bloqueado con APP_ENV=production).
-- Super administrador para entrar al dashboard en una BD nueva:
--   usuario: admin   contraseña: admin123
-- El login actual compara MD5 (cl_usuarios::LoginV2).
INSERT IGNORE INTO `tb_usuarios` (`cod_usuario`, `cod_empresa`, `cod_rol`, `nombre`, `apellido`, `imagen`, `correo`, `telefono`, `usuario`, `password`, `estado`, `cod_sucursal`)
VALUES (1, 1, 1, 'Admin', 'Local', '', 'admin@taste.local', '', 'admin', MD5('admin123'), 'A', 0);
