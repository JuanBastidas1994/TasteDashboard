<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_tiempos_motorizado.php";

$clreporte = new cl_reporte_tiempos_motorizado();
$session   = getSession();

controller_create();

function getTiempos()
{
    global $clreporte;

    if (count($_POST) == 0) {
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }

    extract($_POST);

    $rows = $clreporte->getTiempos($sucursal, $f_inicio, $f_fin);

    if (!$rows) {
        return [
            'success' => 0,
            'mensaje' => 'No hay pedidos con motorizado asignado en este lapso de tiempo',
            'tabla'   => ''
        ];
    }

    $tabla         = '';
    $sumaAceptar   = 0;
    $sumaCamino    = 0;
    $sumaTotal     = 0;
    $cntAceptar    = 0;
    $cntCamino     = 0;
    $cntTotal      = 0;

    foreach ($rows as $row) {
        $minAceptar = $row['min_aceptar'];
        $minCamino  = $row['min_camino'];
        $minTotal   = $row['min_total'];

        // Acumular promedios solo con valores válidos
        if ($minAceptar !== null && $minAceptar >= 0) { $sumaAceptar += $minAceptar; $cntAceptar++; }
        if ($minCamino  !== null && $minCamino  >= 0) { $sumaCamino  += $minCamino;  $cntCamino++;  }
        if ($minTotal   !== null && $minTotal   >= 0) { $sumaTotal   += $minTotal;   $cntTotal++;   }

        $tabla .= '<tr>
                    <td class="text-center">' . $row['cod_orden'] . '</td>
                    <td>' . htmlspecialchars($row['motorizado']) . '</td>
                    <td class="text-center">' . formatFecha($row['fecha_asignada']) . '</td>
                    <td class="text-center">' . formatFecha($row['fecha_enviando']) . '</td>
                    <td class="text-center">' . formatFecha($row['fecha_entregada']) . '</td>
                    <td class="text-center">' . badgeTiempo($minAceptar, 5, 15)  . '</td>
                    <td class="text-center">' . badgeTiempo($minCamino,  20, 40) . '</td>
                    <td class="text-center">' . badgeTiempo($minTotal,   25, 60) . '</td>
                    <td class="text-center">
                        <ul class="table-controls">
                            <li>
                                <a href="../orden_detalle.php?id=' . $row['cod_orden'] . '" target="_blank" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="Ver Detalles">
                                    <i data-feather="eye"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                   </tr>';
    }

    return [
        'success'         => 1,
        'mensaje'         => 'Información encontrada',
        'tabla'           => $tabla,
        'promedio_aceptar'=> $cntAceptar > 0 ? formatearTiempo(round($sumaAceptar / $cntAceptar)) : '--',
        'promedio_camino' => $cntCamino  > 0 ? formatearTiempo(round($sumaCamino  / $cntCamino))  : '--',
        'promedio_total'  => $cntTotal   > 0 ? formatearTiempo(round($sumaTotal   / $cntTotal))   : '--',
    ];
}

function badgeTiempo($minutos, $limiteVerde, $limiteAmarillo)
{
    if ($minutos === null || $minutos < 0) {
        return '<span style="color:#aaa;">--</span>';
    }
    if ($minutos <= $limiteVerde) {
        $clase = 'badge-success';
    } elseif ($minutos <= $limiteAmarillo) {
        $clase = 'badge-warning';
    } else {
        $clase = 'badge-danger';
    }
    return '<span class="badge ' . $clase . '" style="font-size:0.85em; padding:4px 8px;">' . formatearTiempo($minutos) . '</span>';
}

function formatFecha($fecha)
{
    if (!$fecha) return '<span style="color:#aaa;">--</span>';
    return $fecha;
}

function formatearTiempo($minutos)
{
    if ($minutos < 60) return $minutos . ' min';
    $h = floor($minutos / 60);
    $m = $minutos % 60;
    return $m > 0 ? $h . 'h ' . $m . 'min' : $h . 'h';
}
?>
