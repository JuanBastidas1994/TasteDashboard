<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_pedidos_programados.php";

$clreporte = new cl_reporte_pedidos_programados();
$session   = getSession();

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
        return ['success' => 0, 'mensaje' => 'No hay pedidos programados en este lapso de tiempo'];
    }

    // ── Agrupar por hora y acumular globalmente ────────────────────────────
    $porHora      = [];   // ['14:00' => ['ordenes' => [], 'productos' => [key => [...]]]]
    $totalGeneral = [];
    $totalMonto   = 0;

    foreach ($rows as $row) {
        $hora     = date('H:00', strtotime($row['hora_retiro']));
        $key      = $row['cod_producto'];
        $cantidad = (int)$row['cantidad'];

        // Inicializar bloque horario
        if (!isset($porHora[$hora])) {
            $porHora[$hora] = ['ordenes' => [], 'productos' => []];
        }
        $porHora[$hora]['ordenes'][$row['cod_orden']] = true;

        // Producto en el bloque horario
        if (!isset($porHora[$hora]['productos'][$key])) {
            $porHora[$hora]['productos'][$key] = [
                'nombre'   => html_entity_decode($row['producto'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'cantidad' => 0,
                'opciones' => []
            ];
        }
        $porHora[$hora]['productos'][$key]['cantidad'] += $cantidad;
        agregarOpciones($porHora[$hora]['productos'][$key]['opciones'], $row['descripcion'], $cantidad);

        // Total general
        if (!isset($totalGeneral[$key])) {
            $totalGeneral[$key] = [
                'nombre'   => html_entity_decode($row['producto'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'cantidad' => 0,
                'opciones' => []
            ];
        }
        $totalGeneral[$key]['cantidad'] += $cantidad;
        agregarOpciones($totalGeneral[$key]['opciones'], $row['descripcion'], $cantidad);
        $totalMonto += (float)$row['precio_final'];
    }

    ksort($porHora);
    uasort($totalGeneral, fn($a, $b) => $b['cantidad'] - $a['cantidad']);

    // Métricas
    $totalPedidos  = count(array_unique(array_column($rows, 'cod_orden')));
    $totalUnidades = array_sum(array_column($rows, 'cantidad'));

    // ── Construir HTML ─────────────────────────────────────────────────────
    $html = '';

    foreach ($porHora as $hora => $bloque) {
        $numOrdenes = count($bloque['ordenes']);
        uasort($bloque['productos'], fn($a, $b) => $b['cantidad'] - $a['cantidad']);

        $html .= '<div class="hora-bloque mb-3">';
        $html .= '<div class="hora-bloque-header d-flex align-items-center px-3 py-2" style="background:#f1f2f3;border-radius:6px 6px 0 0;">';
        $html .= '<i data-feather="clock" style="width:16px;height:16px;margin-right:6px;"></i>';
        $html .= '<strong style="font-size:15px;">' . $hora . '</strong>';
        $html .= '<span class="badge badge-info ml-2">' . $numOrdenes . ' ' . ($numOrdenes == 1 ? 'pedido' : 'pedidos') . '</span>';
        $html .= '</div>';
        $html .= '<table class="table table-sm mb-0" style="border:1px solid #e0e6ed;border-top:none;border-radius:0 0 6px 6px;">';
        $html .= '<thead><tr><th>Producto</th><th class="text-center" style="width:80px;">Cant.</th></tr></thead><tbody>';

        foreach ($bloque['productos'] as $prod) {
            $opcionesHtml = buildOpcionesHtml($prod['opciones']);
            $html .= '<tr>';
            $html .= '<td>' . $prod['nombre'] . $opcionesHtml . '</td>';
            $html .= '<td class="text-center"><strong>' . $prod['cantidad'] . '</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
    }

    // Bloque total consolidado
    $html .= '<div class="hora-bloque mt-4">';
    $html .= '<div class="hora-bloque-header d-flex align-items-center px-3 py-2" style="background:#1abc9c;border-radius:6px 6px 0 0;">';
    $html .= '<i data-feather="package" style="width:16px;height:16px;margin-right:6px;color:#fff;"></i>';
    $html .= '<strong style="font-size:15px;color:#fff;">TOTAL CONSOLIDADO</strong>';
    $html .= '</div>';
    $html .= '<table class="table table-sm mb-0" style="border:1px solid #1abc9c;border-top:none;border-radius:0 0 6px 6px;">';
    $html .= '<thead><tr><th>Producto</th><th class="text-center" style="width:80px;">Total</th></tr></thead><tbody>';

    foreach ($totalGeneral as $prod) {
        $opcionesHtml = buildOpcionesHtml($prod['opciones']);
        $html .= '<tr>';
        $html .= '<td>' . $prod['nombre'] . $opcionesHtml . '</td>';
        $html .= '<td class="text-center"><strong>' . $prod['cantidad'] . '</strong></td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table></div>';

    return [
        'success'        => 1,
        'mensaje'        => 'OK',
        'html'           => $html,
        'total_pedidos'  => $totalPedidos,
        'total_unidades' => $totalUnidades,
        'total_monto'    => number_format($totalMonto, 2)
    ];
}

function getOrdenes()
{
    global $clreporte;

    if (count($_POST) == 0) {
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }

    extract($_POST);

    $rows = $clreporte->getOrdenes($sucursal, $f_inicio, $f_fin);

    if (!$rows) {
        return ['success' => 0, 'mensaje' => 'No hay órdenes programadas en este período'];
    }

    $estadoBadge = [
        'CREADA'    => 'badge-secondary',
        'PENDIENTE' => 'badge-warning',
        'ACEPTADA'  => 'badge-info',
        'ENVIANDO'  => 'badge-primary',
        'ASIGNADA'  => 'badge-primary',
        'ENTREGADA' => 'badge-success',
        'ANULADA'   => 'badge-dark',
        'CANCELADA' => 'badge-danger',
    ];

    $tabla = '';
    foreach ($rows as $row) {
        $badge  = $estadoBadge[$row['estado']] ?? 'badge-secondary';
        $hora   = date('d/m H:i', strtotime($row['hora_retiro']));
        $tabla .= '<tr>
            <td class="text-center">' . $row['cod_orden'] . '</td>
            <td>' . htmlspecialchars(trim($row['cliente'])) . '</td>
            <td class="text-center">' . $hora . '</td>
            <td>' . htmlspecialchars($row['sucursal']) . '</td>
            <td class="text-center"><span class="badge ' . $badge . '">' . $row['estado'] . '</span></td>
            <td class="text-right">$ ' . number_format((float)$row['total'], 2) . '</td>
            <td class="text-center">
                <a href="orden_detalle.php?id=' . $row['cod_orden'] . '" target="_blank"
                   class="bs-tooltip" data-toggle="tooltip" title="Ver detalle">
                    <i data-feather="eye"></i>
                </a>
            </td>
        </tr>';
    }

    return ['success' => 1, 'tabla' => $tabla];
}

// ── Helpers ────────────────────────────────────────────────────────────────────
function agregarOpciones(&$opciones, $descripcion, $cantidad)
{
    if (empty($descripcion)) return;
    $grupos = json_decode($descripcion, true);
    if (!$grupos) return;
    foreach ($grupos as $grupo) {
        if (!empty($grupo['detalles'])) {
            foreach ($grupo['detalles'] as $detalle) {
                $nombre = html_entity_decode($detalle['nombre'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $cant   = (int)$detalle['cantidad'] * $cantidad;
                $opciones[$nombre] = ($opciones[$nombre] ?? 0) + $cant;
            }
        }
    }
}

function buildOpcionesHtml($opciones)
{
    if (empty($opciones)) return '';
    $html = '<ul style="margin:4px 0 0 0;padding-left:0;font-size:0.83em;color:#666;list-style:none;">';
    foreach ($opciones as $nombre => $cant) {
        $html .= '<li style="padding:1px 0;">&#8627; ' . $nombre . ': <strong>' . $cant . '</strong></li>';
    }
    return $html . '</ul>';
}
?>
