-- Agrega al menú "Flota" el reporte de cobro de envíos a comercios (flota_reporte_cobro.php)
-- y lo asigna a las flotas existentes: mismas empresas/roles que ya tienen "Flota > Lista" (cod_pagina 77).

INSERT INTO tb_paginas (cod_padre, id, icono, nombre, titulo, data_translate, posicion, estado)
SELECT 71, 'flota-reporte-cobro', '', 'flota_reporte_cobro.php', 'Cobro a comercios', 'menu-flota-reporte-cobro', 3, 'A'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM tb_paginas WHERE nombre = 'flota_reporte_cobro.php');

INSERT INTO tb_pagina_rol (cod_pagina, cod_rol, cod_empresa, posicion)
SELECT p.cod_pagina, pr.cod_rol, pr.cod_empresa, 99
FROM tb_pagina_rol pr
INNER JOIN tb_paginas p ON p.nombre = 'flota_reporte_cobro.php'
WHERE pr.cod_pagina = 77
    AND NOT EXISTS (
        SELECT 1 FROM tb_pagina_rol x
        WHERE x.cod_pagina = p.cod_pagina AND x.cod_rol = pr.cod_rol AND x.cod_empresa = pr.cod_empresa
    );
