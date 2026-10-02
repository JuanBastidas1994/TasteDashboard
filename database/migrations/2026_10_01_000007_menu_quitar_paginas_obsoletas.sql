-- Quita del menú del dashboard las páginas eliminadas en la limpieza de módulos
-- obsoletos (agenda, programas, stock, helpdesk, novedades del dashboard).
-- Correr junto con el deploy del código que borra esas páginas.

DELETE pr
FROM tb_pagina_rol pr
INNER JOIN tb_paginas p ON p.cod_pagina = pr.cod_pagina
WHERE p.nombre IN (
    'eventos.php',
    'programas.php',
    'representantes.php',
    'stock.php',
    'helpdesk.php',
    'crear_helpdesk.php',
    'dashboard_updates.php',
    'crear_dashboard_updates.php'
);

DELETE FROM tb_paginas
WHERE nombre IN (
    'eventos.php',
    'programas.php',
    'representantes.php',
    'stock.php',
    'helpdesk.php',
    'crear_helpdesk.php',
    'dashboard_updates.php',
    'crear_dashboard_updates.php'
);
