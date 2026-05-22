<?php
require_once "../funciones.php";
require_once "../clases/cl_reporte_tiempo_aceptacion.php";

$clreporte = new cl_reporte_tiempo_aceptacion();
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
            'mensaje' => 'No hay pedidos aceptados en este lapso de tiempo',
            'tabla'   => ''
        ];
    }

    $tabla          = '';
    $totalMinutos   = 0;
    $cantidadFilas  = count($rows);

    foreach ($rows as $row) {
        $minutos        = (int)$row['minutos'];
        $totalMinutos  += $minutos;
        $tiempoFormato  = formatearTiempo($minutos);

        // Color según velocidad de aceptación
        if ($minutos <= 5) {
            $badge = 'badge-success';
        } elseif ($minutos <= 15) {
            $badge = 'badge-warning';
        } else {
            $badge = 'badge-danger';
        }

        $tabla .= '<tr>
                    <td class="text-center">' . $row['cod_orden'] . '</td>
                    <td class="text-center">' . $row['fecha'] . '</td>
                    <td class="text-center">' . $row['hora_retiro'] . '</td>
                    <td class="text-center">
                        <span class="badge ' . $badge . '" style="font-size:0.9em; padding:5px 10px;">' . $tiempoFormato . '</span>
                    </td>
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

    $promedioMinutos = $cantidadFilas > 0 ? round($totalMinutos / $cantidadFilas) : 0;

    return [
        'success'  => 1,
        'mensaje'  => 'Información encontrada',
        'tabla'    => $tabla,
        'promedio' => formatearTiempo($promedioMinutos)
    ];
}

function formatearTiempo($minutos)
{
    if ($minutos < 60) {
        return $minutos . ' min';
    }
    $horas   = floor($minutos / 60);
    $minRest = $minutos % 60;
    return $minRest > 0 ? $horas . 'h ' . $minRest . 'min' : $horas . 'h';
}
?>
