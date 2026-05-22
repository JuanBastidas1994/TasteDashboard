<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_pedidos_programados.php";

$clreporte = new cl_reporte_pedidos_programados();
$session = getSession();

controller_create();

function getProductos()
{
    global $clreporte;

    if (count($_POST) == 0) {
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }

    extract($_POST);

    $rows = $clreporte->getProductosProgramados($sucursal, $f_inicio, $f_fin);

    if (!$rows) {
        return [
            'success' => 0,
            'mensaje' => 'No hay pedidos programados en este lapso de tiempo',
            'tabla'   => ''
        ];
    }

    // Agregar por producto y acumular opciones
    $productos = [];
    foreach ($rows as $row) {
        $key      = $row['cod_producto'];
        $cantidad = (int)$row['cantidad'];

        if (!isset($productos[$key])) {
            $productos[$key] = [
                'nombre'   => $row['producto'],
                'cantidad' => 0,
                'precio'   => 0,
                'opciones' => []
            ];
        }

        $productos[$key]['cantidad'] += $cantidad;
        $productos[$key]['precio']   += (float)$row['precio_final'];

        // Parsear JSON de opciones si existe
        if (!empty($row['descripcion'])) {
            $grupos = json_decode($row['descripcion'], true);
            if ($grupos) {
                foreach ($grupos as $grupo) {
                    if (!empty($grupo['detalles'])) {
                        foreach ($grupo['detalles'] as $detalle) {
                            $nombreOpcion = $detalle['nombre'];
                            // cantidad de la opcion * cantidad del producto en la orden
                            $cantOpcion = (int)$detalle['cantidad'] * $cantidad;
                            if (!isset($productos[$key]['opciones'][$nombreOpcion])) {
                                $productos[$key]['opciones'][$nombreOpcion] = 0;
                            }
                            $productos[$key]['opciones'][$nombreOpcion] += $cantOpcion;
                        }
                    }
                }
            }
        }
    }

    // Ordenar por cantidad descendente
    uasort($productos, fn($a, $b) => $b['cantidad'] - $a['cantidad']);

    // Construir HTML
    $tabla        = '';
    $totalGeneral = 0;

    foreach ($productos as $prod) {
        $precio = number_format($prod['precio'], 2);
        $totalGeneral += $prod['precio'];

        $opcionesHtml = '';
        if (!empty($prod['opciones'])) {
            $opcionesHtml = '<ul style="margin:6px 0 0 0; padding-left:0; font-size:0.85em; color:#666; list-style:none;">';
            foreach ($prod['opciones'] as $nombre => $cant) {
                $opcionesHtml .= '<li style="padding:1px 0;">&#8627; ' . htmlspecialchars($nombre) . ': <strong>' . $cant . '</strong></li>';
            }
            $opcionesHtml .= '</ul>';
        }

        $tabla .= '<tr>
                    <td>' . htmlspecialchars($prod['nombre']) . $opcionesHtml . '</td>
                    <td class="text-center"><strong>' . $prod['cantidad'] . '</strong></td>
                    <td class="text-right">$ ' . $precio . '</td>
                   </tr>';
    }

    return [
        'success' => 1,
        'mensaje' => 'Información encontrada',
        'tabla'   => $tabla,
        'total'   => number_format($totalGeneral, 2)
    ];
}
?>
