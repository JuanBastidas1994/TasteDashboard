<?php
require_once "../funciones.php";
require_once "../clases/cl_productos.php";
require_once "../clases/cl_categorias.php";
require_once "../excel_export/SimpleXLSX.php";
require_once "../excel_export/SimpleXLSXGen.php";

use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

$session = getSession();
$filesUP = url_upload.'/assets/empresas/'.$session['alias'].'/';

controller_create();

// ─── IMPORTAR ────────────────────────────────────────────────────────────────

function importar() {
    global $session, $filesUP;

    if (!isset($_FILES["excel"]) || $_FILES["excel"]["error"] !== UPLOAD_ERR_OK) {
        return ['success' => 0, 'mensaje' => "No se recibió ningún archivo válido"];
    }

    $partes = explode(".", $_FILES["excel"]["name"]);
    $ext    = strtolower($partes[count($partes) - 1]);

    if (!in_array($ext, ['xls', 'xlsx'])) {
        return ['success' => 0, 'mensaje' => "Solo se permiten archivos .xls o .xlsx"];
    }

    $nombre  = "importar-v3-".fechaSignos().".".$ext;
    $destino = $filesUP.$nombre;

    if (!move_uploaded_file($_FILES["excel"]["tmp_name"], $destino)) {
        return ['success' => 0, 'mensaje' => "No se pudo guardar el archivo. Ruta: ".$destino];
    }

    $xlsx = SimpleXLSX::parse($destino);
    if (!$xlsx) {
        return ['success' => 0, 'mensaje' => "No se pudo leer el archivo: ".SimpleXLSX::parseError()];
    }

    $data = procesarFilas($xlsx->rows());
    // $ok   = count(array_filter($data, fn($r) => $r['importado'] === true));
    $ok = count(array_filter($data, function($r) {
        return $r['importado'] === true;
    }));
    $err  = count($data) - $ok;

    return [
        'success' => 1,
        'mensaje' => "Proceso finalizado: $ok exitosos, $err con errores",
        'data'    => $data,
    ];
}

function procesarFilas(array $rows) {
    global $session;

    $resultado   = [];
    $cod_empresa = intval($session['cod_empresa']);

    $empresa      = Conexion::buscarRegistro("SELECT impuesto FROM tb_empresas WHERE cod_empresa = ?", [$cod_empresa]);
    $iva_empresa  = ($empresa && is_numeric($empresa['impuesto'])) ? intval($empresa['impuesto']) : 15;

    // Fila 0 = encabezados, empezamos en 1
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        // Columnas 0-based: A=0, B=1, C=2, D=3, E=4, F=5
        $cod_producto_excel = trim((string)($row[0] ?? ''));
        $nombre_categoria   = ucprimera(trim((string)($row[1] ?? '')));
        $nombre             = ucprimera(trim((string)($row[2] ?? '')));
        $descripcion        = ucprimera(trim((string)($row[3] ?? '')));
        $precio_sin_iva_raw = $row[4] ?? '';
        $precio_con_iva_raw = $row[5] ?? '';

        $tiene_e = $precio_sin_iva_raw !== '' && $precio_sin_iva_raw !== null && is_numeric($precio_sin_iva_raw);
        $tiene_f = $precio_con_iva_raw !== '' && $precio_con_iva_raw !== null && is_numeric($precio_con_iva_raw);

        // Saltar fila si todo está vacío
        if ($nombre === '' && $nombre_categoria === '' && !$tiene_e && !$tiene_f) {
            continue;
        }

        $precio_display = $tiene_f ? $precio_con_iva_raw : $precio_sin_iva_raw;

        $fila = [
            'fila'      => $i + 1,
            'categoria' => $nombre_categoria,
            'nombre'    => $nombre,
            'precio'    => $precio_display,
            'importado' => false,
            'motivo'    => '',
        ];

        if ($nombre === '') {
            $fila['motivo'] = 'FALTA EL NOMBRE DEL PRODUCTO';
            $resultado[]    = $fila;
            continue;
        }

        if (!$tiene_e && !$tiene_f) {
            $fila['motivo'] = 'FALTA EL PRECIO (completa la columna E o F)';
            $resultado[]    = $fila;
            continue;
        }

        // Lógica de IVA:
        // Solo E (F vacío o F==E) → sin IVA
        // F presente y F≠E      → cobra IVA; precio_no_tax se calcula desde F (no se confía en E)
        // Solo F (E vacío)      → cobra IVA; precio_no_tax se calcula desde F
        $precio_e = $tiene_e ? floatval($precio_sin_iva_raw) : null;
        $precio_f = $tiene_f ? floatval($precio_con_iva_raw) : null;

        $iguales = ($tiene_e && $tiene_f && abs($precio_f - $precio_e) < 0.0001);

        if (!$tiene_f || $iguales) {
            // Sin IVA: el precio dado (E) es el precio final
            $cobra_iva      = 0;
            $precio_no_tax  = $precio_e;
            $precio_final   = $precio_e;
            $iva_valor      = 0;
            $iva_porcentaje = 0;
        } else {
            // Con IVA: F es el precio final con impuesto incluido
            // precio_no_tax se recalcula matemáticamente — no se confía en E
            $cobra_iva      = 1;
            $precio_final   = $precio_f;
            $precio_no_tax  = $precio_f / (1 + $iva_empresa / 100);
            $iva_valor      = $precio_final - $precio_no_tax;
            $iva_porcentaje = $iva_empresa;
        }

        // Categoría: case-insensitive, crear si no existe
        $categorias = [];
        if ($nombre_categoria !== '') {
            $cod_categoria = encontrarOCrearCategoria($nombre_categoria, $session);
            if ($cod_categoria) {
                $categorias = [$cod_categoria];
            }
        }

        // Columna A vacía → crear; con ID numérico → actualizar
        if ($cod_producto_excel === '' || !is_numeric($cod_producto_excel)) {

            // Verificar duplicado por nombre (case-insensitive)
            $nombreEnt  = htmlentities($nombre);
            $duplicado  = Conexion::getSingleValue(
                "SELECT cod_producto FROM tb_productos
                 WHERE LOWER(nombre) = LOWER(?) AND cod_empresa = ? AND estado IN ('A','I') LIMIT 1",
                [$nombreEnt, $cod_empresa]
            );
            if ($duplicado) {
                $fila['motivo'] = 'YA EXISTE UN PRODUCTO CON ESTE NOMBRE (ID: '.$duplicado.'). Usa ese ID en columna A para editarlo.';
                $resultado[]    = $fila;
                continue;
            }

            $resultado[] = crearProducto(
                $nombre, $descripcion,
                $precio_no_tax, $precio_final, $iva_valor, $iva_porcentaje, $cobra_iva,
                $categorias, $i, $fila, $session
            );
        } else {
            $resultado[] = actualizarProducto(
                intval($cod_producto_excel),
                $nombre, $descripcion,
                $precio_no_tax, $precio_final, $iva_valor, $iva_porcentaje, $cobra_iva,
                $nombre_categoria, $categorias, $fila, $session
            );
        }
    }

    return $resultado;
}

// ─── EXPORTAR ────────────────────────────────────────────────────────────────

function exportar() {
    global $session;

    $cod_empresa = intval($session['cod_empresa']);

    $productos = Conexion::buscarVariosRegistro(
        "SELECT p.cod_producto,
                MAX(IFNULL(c.categoria, '')) AS categoria,
                p.nombre,
                p.desc_corta,
                p.precio_no_tax,
                p.precio,
                p.cobra_iva
         FROM tb_productos p
         LEFT JOIN tb_productos_categorias pc ON p.cod_producto = pc.cod_producto
         LEFT JOIN tb_categorias c ON pc.cod_categoria = c.cod_categoria
         WHERE p.cod_empresa = ? AND p.estado IN ('A','I') AND p.cod_producto_padre = 0
         GROUP BY p.cod_producto, p.nombre, p.desc_corta, p.precio_no_tax, p.precio, p.cobra_iva
         ORDER BY categoria ASC, p.nombre ASC",
        [$cod_empresa]
    );

    $filas = [
        ['No.', 'Categoria Principal', 'Producto', 'Descripcion Producto', 'Precio sin iva', 'Precio con iva', 'Opciones a anadir al producto o Variantes'],
    ];

    foreach ($productos as $p) {
        $precio_con_iva = $p['cobra_iva'] ? $p['precio'] : '';
        $filas[] = [
            (int)$p['cod_producto'],
            html_entity_decode($p['categoria']),
            html_entity_decode($p['nombre']),
            html_entity_decode($p['desc_corta']),
            (float)$p['precio_no_tax'],
            $precio_con_iva !== '' ? (float)$precio_con_iva : '',
            '',
        ];
    }

    SimpleXLSXGen::fromArray($filas)->downloadAs('plantilla-productos.xlsx');
    exit();
}

// ─── HELPERS ─────────────────────────────────────────────────────────────────

// Primera letra mayúscula, resto minúscula — con soporte UTF-8 para tildes
function ucprimera(string $texto): string {
    if ($texto === '') return '';
    return mb_strtoupper(mb_substr($texto, 0, 1, 'UTF-8'), 'UTF-8')
         . mb_strtolower(mb_substr($texto, 1, null, 'UTF-8'), 'UTF-8');
}

function encontrarOCrearCategoria($nombre_categoria, $session) {
    $cod_empresa = intval($session['cod_empresa']);

    // Búsqueda case-insensitive: "NACHOS" == "nachos" == "Nachos"
    $cod_cat = Conexion::getSingleValue(
        "SELECT cod_categoria FROM tb_categorias
         WHERE LOWER(categoria) = LOWER(?) AND cod_empresa = ? AND estado IN ('A','I') LIMIT 1",
        [$nombre_categoria, $cod_empresa]
    );

    if ($cod_cat) {
        return intval($cod_cat);
    }

    $Clcat = new cl_categorias();
    $aux   = "";
    do {
        $alias = create_slug(sinTildes($nombre_categoria.$aux));
        $aux   = intval(rand(1, 100));
    } while (!$Clcat->aliasDisponible($alias));

    $Clcat->cod_categoria_padre = 0;
    $Clcat->alias               = $alias;
    $Clcat->nombre              = $nombre_categoria;
    $Clcat->desc_corta          = '';
    $Clcat->desc_larga          = '';
    $Clcat->image_min           = '';
    $Clcat->image_max           = '';

    $id_cat = null;
    if ($Clcat->crear($id_cat)) {
        return intval($id_cat);
    }

    return null;
}

function crearProducto($nombre, $descripcion, $precio_no_tax, $precio_final, $iva_valor, $iva_porcentaje, $cobra_iva, $categorias, $i, $fila, $session) {
    $Clproductos = new cl_productos();

    $aux = "";
    do {
        $alias = create_slug(sinTildes($nombre.$aux));
        $aux   = intval(rand(1, 100));
    } while (!$Clproductos->aliasDisponible($alias));


    $Clproductos->cod_producto_padre = 0;
    $Clproductos->alias              = $alias;
    $Clproductos->nombre             = htmlentities($nombre);
    $Clproductos->desc_corta         = htmlentities($descripcion);
    $Clproductos->desc_larga         = '';
    $Clproductos->precio             = number_format($precio_final, 4, '.', '');
    $Clproductos->precio_no_tax      = number_format($precio_no_tax, 4, '.', '');
    $Clproductos->iva_valor          = number_format($iva_valor, 4, '.', '');
    $Clproductos->iva_porcentaje     = $iva_porcentaje;
    $Clproductos->cobra_iva          = $cobra_iva;
    $Clproductos->precio_anterior    = 0;
    $Clproductos->costo              = 0;
    $Clproductos->open_detalle       = 0;
    $Clproductos->peso               = 0;
    $Clproductos->volumen            = 0;
    $Clproductos->sku                = '';
    $Clproductos->is_combo           = 0;
    $Clproductos->facturar_sin_stock = 0;
    $Clproductos->tiempo_preparacion = 0;
    $Clproductos->venta_delivery     = 1;
    $Clproductos->venta_pickup       = 1;
    $Clproductos->venta_mesa         = 1;
    $Clproductos->image_min          = '';
    $Clproductos->image_max          = '';
    $Clproductos->categorias         = $categorias;

    $id = null;
    if ($Clproductos->crear($id)) {
        sincronizarProductoSucursales($id, intval($session['cod_empresa']));
        $fila['importado'] = true;
        $fila['motivo']    = 'CREADO (ID: '.$id.')';
        $fila['id']        = $id;
    } else {
        $fila['motivo'] = 'ERROR AL CREAR EN BASE DE DATOS';
    }

    return $fila;
}

function actualizarProducto($cod_producto, $nombre, $descripcion, $precio_no_tax, $precio_final, $iva_valor, $iva_porcentaje, $cobra_iva, $nombre_categoria, $categorias, $fila, $session) {
    $cod_empresa = intval($session['cod_empresa']);

    $existe = Conexion::getSingleValue(
        "SELECT cod_producto FROM tb_productos
         WHERE cod_producto = ? AND cod_empresa = ? AND estado IN ('A','I') LIMIT 1",
        [$cod_producto, $cod_empresa]
    );

    if (!$existe) {
        $fila['motivo'] = 'ID '.$cod_producto.' NO ENCONTRADO EN ESTA EMPRESA';
        return $fila;
    }

    $ok = Conexion::ejecutar(
        "UPDATE tb_productos SET
            nombre             = :nombre,
            desc_corta         = :desc_corta,
            precio             = :precio,
            precio_no_tax      = :precio_no_tax,
            iva_valor          = :iva_valor,
            iva_porcentaje     = :iva_porcentaje,
            cobra_iva          = :cobra_iva,
            fecha_modificacion = NOW()
         WHERE cod_producto = :cod_producto AND cod_empresa = :cod_empresa",
        [
            ':nombre'         => htmlentities($nombre),
            ':desc_corta'     => htmlentities($descripcion),
            ':precio'         => number_format($precio_final, 4, '.', ''),
            ':precio_no_tax'  => number_format($precio_no_tax, 4, '.', ''),
            ':iva_valor'      => number_format($iva_valor, 4, '.', ''),
            ':iva_porcentaje' => $iva_porcentaje,
            ':cobra_iva'      => $cobra_iva,
            ':cod_producto'   => $cod_producto,
            ':cod_empresa'    => $cod_empresa,
        ]
    );

    if (!$ok) {
        $fila['motivo'] = 'ERROR AL ACTUALIZAR EN BASE DE DATOS';
        return $fila;
    }

    if ($nombre_categoria !== '' && !empty($categorias)) {
        Conexion::ejecutar("DELETE FROM tb_productos_categorias WHERE cod_producto = ?", [$cod_producto]);
        foreach ($categorias as $cat_id) {
            Conexion::ejecutar(
                "INSERT INTO tb_productos_categorias(cod_producto, cod_categoria) VALUES(?, ?)",
                [$cod_producto, $cat_id]
            );
        }
    }

    sincronizarProductoSucursales($cod_producto, $cod_empresa);
    $fila['importado'] = true;
    $fila['motivo']    = 'ACTUALIZADO CORRECTAMENTE';
    return $fila;
}

function sincronizarProductoSucursales($cod_producto, $cod_empresa) {
    static $cache = [];
    if (!isset($cache[$cod_empresa])) {
        $cache[$cod_empresa] = Conexion::buscarVariosRegistro(
            "SELECT cod_sucursal FROM tb_sucursales WHERE estado IN ('A','I') AND cod_empresa = ?",
            [$cod_empresa]
        ) ?: [];
    }
    $sucursales = $cache[$cod_empresa];
    if (!$sucursales) return;

    foreach ($sucursales as $suc) {
        $existe = Conexion::getSingleValue(
            "SELECT cod_producto_sucursal FROM tb_productos_sucursal WHERE cod_producto = ? AND cod_sucursal = ? LIMIT 1",
            [$cod_producto, $suc['cod_sucursal']]
        );
        if (!$existe) {
            Conexion::ejecutar(
                "INSERT INTO tb_productos_sucursal (cod_producto, cod_sucursal, replacePrice, precio, precio_anterior, precio_no_tax, iva_valor, agotado_inicio, agotado_fin, estado)
                 VALUES (?, ?, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'A')",
                [$cod_producto, $suc['cod_sucursal']]
            );
        }
    }
}
