<?php
require_once "funciones.php";

if (!isLogin()) {
    header("location:login.php");
}

$session = getSession();

/* ---------------------------------------------------------------
   Tutoriales disponibles.
   Para agregar más: añadir entradas al array $tutoriales.
   Campos: id, titulo, descripcion, youtube_id, duracion, fecha, categoria
--------------------------------------------------------------- */
$tutoriales = [
    [
        'id'          => 1,
        'titulo'      => 'Promoción: Monto mínimo de compra',
        'descripcion' => 'Aprende a configurar una promoción que se activa cuando el cliente supera un monto mínimo.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:10',
        'fecha'       => '2026-06-18',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 2,
        'titulo'      => 'Promoción: Compra X y lleva Y',
        'descripcion' => 'Configura una promoción del tipo "compra X productos y lleva Y gratis".',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:04',
        'fecha'       => '2026-06-18',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 3,
        'titulo'      => 'Cómo funciona el Delivery',
        'descripcion' => 'Entiende el flujo completo de un pedido delivery: desde que entra hasta que es entregado.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:36',
        'fecha'       => '2026-03-03',
        'categoria'   => 'Órdenes',
    ],
    [
        'id'          => 4,
        'titulo'      => 'Cómo funciona el Pedido Pickup',
        'descripcion' => 'Gestiona pedidos para retirar en local: flujo, estados y notificaciones al cliente.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:56',
        'fecha'       => '2026-03-03',
        'categoria'   => 'Órdenes',
    ],
    [
        'id'          => 5,
        'titulo'      => 'Configuración de módulos',
        'descripcion' => 'Activa y configura los módulos disponibles en el sistema según las necesidades de tu negocio.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:25',
        'fecha'       => '2026-03-03',
        'categoria'   => 'Configuración',
    ],
    [
        'id'          => 6,
        'titulo'      => 'Tipos de promociones',
        'descripcion' => 'Conoce los diferentes tipos de promociones disponibles y cuándo usar cada uno.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:03',
        'fecha'       => '2026-02-23',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 7,
        'titulo'      => 'Promociones avanzadas',
        'descripcion' => 'Configuración avanzada de promociones: combinaciones, restricciones y reglas especiales.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '8:46',
        'fecha'       => '2026-02-20',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 8,
        'titulo'      => 'Cómo crear una promoción',
        'descripcion' => 'Crea tu primera promoción paso a paso: tipo, condiciones, fechas de vigencia.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:14',
        'fecha'       => '2026-02-18',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 9,
        'titulo'      => 'Crear cupones de descuento',
        'descripcion' => 'Genera cupones de descuento y asígnalos a tus clientes para fidelizarlos.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:47',
        'fecha'       => '2026-02-15',
        'categoria'   => 'Promociones',
    ],
    [
        'id'          => 10,
        'titulo'      => 'Cómo agregar productos',
        'descripcion' => 'Agrega productos a tu menú con opciones, precios, imágenes y disponibilidad.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:13',
        'fecha'       => '2026-02-10',
        'categoria'   => 'Productos',
    ],
    [
        'id'          => 11,
        'titulo'      => 'Cómo crear categorías',
        'descripcion' => 'Organiza tu menú creando categorías y subcategorías para tus productos.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:01',
        'fecha'       => '2026-02-08',
        'categoria'   => 'Productos',
    ],
    [
        'id'          => 12,
        'titulo'      => 'Configurar zona de cobertura',
        'descripcion' => 'Define el área de cobertura de tu delivery dibujando polígonos en el mapa.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:08',
        'fecha'       => '2026-02-05',
        'categoria'   => 'Sucursales',
    ],
    [
        'id'          => 13,
        'titulo'      => 'Sucursales - Configuración avanzada',
        'descripcion' => 'Horarios, zonas de entrega, impresoras y más opciones avanzadas de tu sucursal.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:32',
        'fecha'       => '2026-02-03',
        'categoria'   => 'Sucursales',
    ],
    [
        'id'          => 14,
        'titulo'      => 'Cómo crear una sucursal',
        'descripcion' => 'Aprende a crear y configurar una nueva sucursal para tu restaurante.',
        'youtube_id'  => 'dX6FNIqg3YQ',
        'duracion'    => '1:56',
        'fecha'       => '2026-02-01',
        'categoria'   => 'Sucursales',
    ],
];

$categorias_menu = [
    'Todos los tutoriales',
    'Primeros pasos',
    'Sucursales',
    'Productos',
    'Órdenes',
    'Promociones',
    'Clientes',
    'Usuarios',
    'Mis motorizados',
    'Reportes',
    'Configuración',
];

$iconos_categoria = [
    'Todos los tutoriales' => 'grid',
    'Primeros pasos'       => 'flag',
    'Sucursales'           => 'map-pin',
    'Productos'            => 'shopping-bag',
    'Órdenes'              => 'shopping-cart',
    'Promociones'          => 'tag',
    'Clientes'             => 'users',
    'Usuarios'             => 'user',
    'Mis motorizados'      => 'truck',
    'Reportes'             => 'bar-chart-2',
    'Configuración'        => 'settings',
];

$cat_activa = isset($_GET['cat']) ? $_GET['cat'] : 'Todos los tutoriales';

// Filtrar tutoriales por categoría
$lista = ($cat_activa === 'Todos los tutoriales')
    ? $tutoriales
    : array_values(array_filter($tutoriales, fn($t) => $t['categoria'] === $cat_activa));

// Contar por categoría
$conteo = [];
foreach ($categorias_menu as $cat) {
    $conteo[$cat] = ($cat === 'Todos los tutoriales')
        ? count($tutoriales)
        : count(array_filter($tutoriales, fn($t) => $t['categoria'] === $cat));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php css_mandatory(); ?>
    <style>
        .academia-wrap { display: flex; gap: 24px; align-items: flex-start; }

        /* ---- Sidebar categorías ---- */
        .academia-cats {
            width: 220px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e9e9e9;
            overflow: hidden;
        }
        .academia-cats .cat-title {
            font-size: 12px;
            font-weight: 700;
            color: #888ea8;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 16px 16px 8px;
        }
        .academia-cats a.cat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 16px;
            font-size: 13px;
            color: #3b3f5c;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: background .15s, color .15s;
        }
        .academia-cats a.cat-item:hover { background: #FFF5F4; color: #FD6550; }
        .academia-cats a.cat-item.active {
            background: #FFF5F4;
            color: #FD6550;
            border-left-color: #FD6550;
            font-weight: 600;
        }
        .academia-cats a.cat-item .cat-left { display: flex; align-items: center; gap: 8px; }
        .academia-cats a.cat-item .cat-count {
            font-size: 11px;
            background: #E4E3EA;
            color: #3b3f5c;
            border-radius: 20px;
            padding: 1px 7px;
            font-weight: 600;
        }
        .academia-cats a.cat-item.active .cat-count { background: #FFDAD4; color: #FD6550; }

        /* ---- Bloque guía primeros pasos ---- */
        .card-guia {
            margin: 12px;
            background: linear-gradient(135deg, #FFF5F4 0%, #FFDAD4 100%);
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            color: #3b3f5c;
        }
        .card-guia strong { display: block; margin-bottom: 4px; color: #202020; }
        .card-guia a { color: #FD6550; font-weight: 600; font-size: 12px; }

        /* ---- Panel principal ---- */
        .academia-main { flex: 1; min-width: 0; }

        /* Top bar */
        .academia-topbar {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
        }
        .academia-search {
            flex: 1;
            position: relative;
        }
        .academia-search input {
            width: 100%;
            border: 1px solid #e9e9e9;
            border-radius: 8px;
            padding: 9px 14px 9px 38px;
            font-size: 13px;
            color: #3b3f5c;
            outline: none;
            background: #fff;
        }
        .academia-search input:focus { border-color: #FD6550; }
        .academia-search svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #888ea8;
            width: 16px; height: 16px;
        }
        .card-ayuda {
            width: 220px;
            flex-shrink: 0;
            background: #F4F3FF;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12px;
            color: #3b3f5c;
        }
        .card-ayuda strong { display: block; margin-bottom: 4px; font-size: 13px; color: #202020; }
        .card-ayuda p { color: #888ea8; margin-bottom: 10px; line-height: 1.4; }
        .btn-soporte {
            display: inline-block;
            background: #FD6550;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-soporte:hover { background: #e5523d; color: #fff; text-decoration: none; }

        /* Lista tutoriales */
        .academia-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .academia-header h5 { font-size: 15px; font-weight: 700; color: #202020; margin: 0; }
        .academia-header .sort-select {
            font-size: 12px;
            border: 1px solid #e9e9e9;
            border-radius: 6px;
            padding: 5px 10px;
            color: #3b3f5c;
            background: #fff;
            cursor: pointer;
        }

        .tutorial-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e9e9e9;
            padding: 14px 16px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: box-shadow .15s, border-color .15s;
        }
        .tutorial-card:hover { border-color: #FD6550; box-shadow: 0 2px 10px rgba(253,101,80,.12); }

        .tutorial-thumb {
            position: relative;
            width: 120px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            background: #E4E3EA;
        }
        .tutorial-thumb img { width: 100%; height: 68px; object-fit: cover; display: block; }
        .tutorial-thumb .duration {
            position: absolute;
            bottom: 4px;
            right: 5px;
            background: rgba(0,0,0,.72);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            border-radius: 4px;
            padding: 1px 5px;
        }
        .tutorial-thumb .play-btn {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,.18);
            transition: background .15s;
        }
        .tutorial-card:hover .play-btn { background: rgba(253,101,80,.35); }
        .play-btn svg { color: #fff; width: 28px; height: 28px; filter: drop-shadow(0 1px 3px rgba(0,0,0,.5)); }

        .tutorial-info { flex: 1; min-width: 0; }
        .tutorial-info h6 {
            font-size: 14px;
            font-weight: 700;
            color: #202020;
            margin: 0 0 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tutorial-info p {
            font-size: 12px;
            color: #888ea8;
            margin: 0 0 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .tutorial-info .tutorial-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            color: #adb5bd;
        }
        .tutorial-info .tutorial-meta .cat-badge {
            background: #E4E3EA;
            color: #3b3f5c;
            border-radius: 20px;
            padding: 2px 8px;
            font-weight: 600;
        }

        .tutorial-date { font-size: 12px; color: #adb5bd; white-space: nowrap; flex-shrink: 0; }

        /* ---- Modal video ---- */
        #modalTutorial .modal-dialog { max-width: 800px; }
        #modalTutorial .modal-content { border-radius: 12px; overflow: hidden; }
        #modalTutorial .modal-header { border-bottom: 1px solid #E4E3EA; padding: 14px 20px; }
        #modalTutorial .modal-body { padding: 0; background: #000; }
        #modalTutorial iframe { width: 100%; height: 450px; display: block; border: none; }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #888ea8;
        }
        .empty-state svg { width: 48px; height: 48px; color: #E4E3EA; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }
    </style>
</head>
<body>
    <?php top(); ?>
    <?php navbar(); ?>

    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <?php sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-12 mb-3">
                        <h3 style="font-size:22px; font-weight:700; color:#202020;">Centro de tutoriales</h3>
                        <p class="text-muted" style="font-size:13px; margin:0;">Aprende a usar todas las herramientas de Taste para gestionar y hacer crecer tu restaurante.</p>
                    </div>

                    <div class="col-12">
                        <div class="academia-wrap">

                            <!-- Sidebar categorías -->
                            <div class="academia-cats">
                                <div class="cat-title">Categorías</div>
                                <?php foreach ($categorias_menu as $cat):
                                    $url_cat = 'academia.php?cat=' . urlencode($cat);
                                    $activa  = ($cat === $cat_activa) ? 'active' : '';
                                    $icono   = $iconos_categoria[$cat] ?? 'book';
                                    $count   = $conteo[$cat];
                                ?>
                                <a href="<?= $url_cat ?>" class="cat-item <?= $activa ?>">
                                    <span class="cat-left">
                                        <i data-feather="<?= $icono ?>" style="width:14px;height:14px;"></i>
                                        <?= htmlspecialchars($cat) ?>
                                    </span>
                                    <?php if ($count > 0): ?>
                                        <span class="cat-count"><?= $count ?></span>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>

                                <div class="card-guia">
                                    <strong>¿Eres nuevo en Taste?</strong>
                                    Sigue nuestra guía de primeros pasos.
                                    <br><a href="academia.php?cat=Primeros+pasos">Ver guía →</a>
                                </div>
                            </div>

                            <!-- Panel principal -->
                            <div class="academia-main">

                                <!-- Top bar -->
                                <div class="academia-topbar">
                                    <div class="academia-search">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        <input type="text" id="searchTutorial" placeholder="Buscar tutoriales...">
                                    </div>
                                    <div class="card-ayuda">
                                        <strong>¿No encuentras lo que buscas?</strong>
                                        <p>Escríbenos y te ayudamos.</p>
                                        <a href="mailto:soporte@taste.com.ec" class="btn-soporte">Contactar soporte</a>
                                    </div>
                                </div>

                                <!-- Header lista -->
                                <div class="academia-header">
                                    <h5><?= htmlspecialchars($cat_activa) ?> (<?= count($lista) ?>)</h5>
                                    <select class="sort-select">
                                        <option>Más recientes</option>
                                        <option>Más antiguos</option>
                                    </select>
                                </div>

                                <!-- Lista tutoriales -->
                                <div id="listaTutoriales">
                                <?php if (empty($lista)): ?>
                                    <div class="empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        <p>No hay tutoriales en esta categoría aún.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($lista as $t):
                                        $thumb  = "https://img.youtube.com/vi/{$t['youtube_id']}/mqdefault.jpg";
                                        $fecha  = date('d M Y', strtotime($t['fecha']));
                                    ?>
                                    <div class="tutorial-card" data-youtube="<?= $t['youtube_id'] ?>" data-titulo="<?= htmlspecialchars($t['titulo']) ?>" data-search="<?= strtolower($t['titulo'] . ' ' . $t['descripcion'] . ' ' . $t['categoria']) ?>">
                                        <div class="tutorial-thumb">
                                            <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($t['titulo']) ?>">
                                            <div class="play-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            </div>
                                            <span class="duration"><?= $t['duracion'] ?></span>
                                        </div>
                                        <div class="tutorial-info">
                                            <h6><?= htmlspecialchars($t['titulo']) ?></h6>
                                            <p><?= htmlspecialchars($t['descripcion']) ?></p>
                                            <div class="tutorial-meta">
                                                <span class="cat-badge"><?= htmlspecialchars($t['categoria']) ?></span>
                                            </div>
                                        </div>
                                        <div class="tutorial-date"><?= $fecha ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </div>

                                <p class="text-center text-muted mt-4" style="font-size:12px;">
                                    ¿No encuentras el tutorial que buscas? <a href="mailto:soporte@taste.com.ec" style="color:#FD6550;">Contáctanos</a> y con gusto te ayudamos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php footer(); ?>
        </div>
    </div>

    <!-- Modal video -->
    <div class="modal fade" id="modalTutorial" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTutorialTitulo"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="modalTutorialFrame" src="" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script>
    $(function () {
        feather.replace();

        // Abrir modal con video
        $(document).on('click', '.tutorial-card', function () {
            var yt    = $(this).data('youtube');
            var title = $(this).data('titulo');
            $('#modalTutorialTitulo').text(title);
            $('#modalTutorialFrame').attr('src', 'https://www.youtube.com/embed/' + yt + '?autoplay=1');
            $('#modalTutorial').modal('show');
        });

        // Limpiar iframe al cerrar (detiene el video)
        $('#modalTutorial').on('hide.bs.modal', function () {
            $('#modalTutorialFrame').attr('src', '');
        });

        // Búsqueda en tiempo real
        $('#searchTutorial').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            $('.tutorial-card').each(function () {
                var texto = $(this).data('search');
                $(this).toggle(!q || texto.indexOf(q) !== -1);
            });
        });
    });
    </script>
</body>
</html>
