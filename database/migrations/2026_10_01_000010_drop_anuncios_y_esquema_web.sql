-- =====================================================================
-- DROP de las tablas del sistema viejo de anuncios y del constructor de
-- páginas viejo (web_paginas / esquema_web). El HOME ahora se arma con
-- editar_pagina.php ("Mi Página": tb_front_paginas / tb_front_pagina_detalle /
-- tb_front_pagina_detalle_contenido, que se quedan).
--
-- Desplegar ANTES el código que deja de usarlas (taste, api, api_gestion_ordenes).
-- =====================================================================

DROP TABLE IF EXISTS tb_anuncio_detalle, tb_anuncio_cabecera, tb_web_esquema;
