-- Columnas de "precio especial por tiempo" (commit 5b9257ea) que se usan en cl_productos::crear/editar
-- pero nunca tuvieron migración. Sin ellas falla el INSERT de productos y variantes.
ALTER TABLE tb_productos
    ADD COLUMN precio_especial DECIMAL(10,2) NULL DEFAULT NULL,
    ADD COLUMN precio_especial_inicio DATE NULL DEFAULT NULL,
    ADD COLUMN precio_especial_fin DATE NULL DEFAULT NULL;
