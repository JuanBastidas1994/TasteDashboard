-- Umbral (en minutos) a partir del cual la web/app muestra el chip "Xh de preparación"
-- en tarjetas de producto, detalle y carrito. Se administra en configuraciones.php > Productos.
ALTER TABLE tb_empresas
    ADD COLUMN prep_time_badge_minutes INT NOT NULL DEFAULT 120;
