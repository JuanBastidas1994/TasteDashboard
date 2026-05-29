<?php
require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_sucursales.php";

if (!isLogin()) {
    header("Content-Type: application/json");
    echo json_encode(['success' => 0, 'mensaje' => 'No autorizado']);
    exit;
}

controller_create();

function getSucursales()
{
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true);
    $cod_empresa = (int)($input['cod_empresa'] ?? 0);

    if ($cod_empresa <= 0) {
        return ['success' => 0, 'mensaje' => 'Empresa inválida'];
    }

    $sql = "SELECT cod_sucursal, nombre FROM tb_sucursales
            WHERE cod_empresa = :cod_empresa AND estado IN ('A','I')
            ORDER BY nombre";
    $sucursales = Conexion::buscarVariosRegistro($sql, [':cod_empresa' => $cod_empresa]);

    return ['success' => 1, 'data' => $sucursales];
}

function getWidgetsEmpresa()
{
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true);

    if (!$input) {
        return ['success' => 0, 'mensaje' => 'Falta información'];
    }

    $cod_empresa  = (int)($input['cod_empresa']  ?? 0);
    $officeId     = (int)($input['cod_sucursal'] ?? 0);
    $dateStart    = $input['fecha_inicio'] ?? '';
    $dateEnd      = $input['fecha_fin']    ?? '';

    if ($cod_empresa <= 0 || empty($dateStart) || empty($dateEnd)) {
        return ['success' => 0, 'mensaje' => 'Faltan parámetros requeridos'];
    }

    if ($dateStart > $dateEnd) {
        return ['success' => 0, 'mensaje' => 'La fecha inicio no puede ser mayor a la fecha fin'];
    }

    $dateStart .= ' 00:00:00';
    $dateEnd   .= ' 23:59:59';

    return getIndicatorsCustom($officeId, $dateStart, $dateEnd, $cod_empresa);
}

function getIndicatorsCustom($officeId, $dateStart, $dateEnd, $empresa)
{
    $baseWhere = "
        o.cod_empresa = :cod_empresa
        AND o.estado = 'ENTREGADA'
        AND o.fecha BETWEEN :start AND :end
    ";

    $params = [
        ':cod_empresa' => $empresa,
        ':start'       => $dateStart,
        ':end'         => $dateEnd
    ];

    if ($officeId > 0) {
        $baseWhere .= " AND o.cod_sucursal = :officeId ";
        $params[':officeId'] = $officeId;
    }

    // Detectar agrupación automática según el rango
    $diffDays = (strtotime($dateEnd) - strtotime($dateStart)) / 86400;
    if ($diffDays <= 1) {
        $period = 'day';
    } elseif ($diffDays <= 90) {
        $period = 'range_day';
    } else {
        $period = 'range_month';
    }

    // 1. TOTALES
    $sqlTotals = "
        SELECT
            COUNT(*) AS total,
            SUM(o.total) AS total_amount,
            AVG(o.total) AS ticket_promedio
        FROM tb_orden_cabecera o
        WHERE $baseWhere
    ";
    $totals = Conexion::buscarRegistro($sqlTotals, $params);

    // 2. HORAS PICO
    $sqlHours = "
        SELECT
            HOUR(o.fecha) AS hour_block,
            COUNT(*) AS total_orders
        FROM tb_orden_cabecera o
        WHERE $baseWhere
        GROUP BY HOUR(o.fecha)
        ORDER BY hour_block
    ";
    $hours = Conexion::buscarVariosRegistro($sqlHours, $params);
    $topHours = [];
    foreach ($hours as $row) {
        $h    = str_pad($row['hour_block'],     2, '0', STR_PAD_LEFT);
        $next = str_pad($row['hour_block'] + 1, 2, '0', STR_PAD_LEFT);
        $topHours["$h:00-$next:00"] = (int)$row['total_orders'];
    }

    // 3. TIPO DE ENTREGA
    $sqlType = "
        SELECT o.is_envio, SUM(o.total) AS total_amount
        FROM tb_orden_cabecera o
        WHERE $baseWhere
        GROUP BY o.is_envio
    ";
    $typesRaw  = Conexion::buscarVariosRegistro($sqlType, $params);
    $mapTypes  = [0 => 'Pickup', 1 => 'Delivery', 2 => 'En mesa'];
    $topDelivery = [];
    foreach ($typesRaw as $row) {
        $topDelivery[$mapTypes[$row['is_envio']] ?? 'Otro'] = (float)$row['total_amount'];
    }

    // 4. TENDENCIA MULTI-SUCURSAL
    $trend = getTrendDataCustom($baseWhere, $params, $period);

    // 5. CLIENTES RECURRENTES
    $officeFilter = $officeId > 0 ? " AND cod_sucursal = :officeId " : "";
    $sqlClientes  = "
        SELECT
            SUM(CASE WHEN first_purchase BETWEEN :start AND :end THEN 1 ELSE 0 END) AS nuevos,
            SUM(CASE WHEN first_purchase < :start THEN 1 ELSE 0 END) AS recurrentes
        FROM (
            SELECT cod_usuario, MIN(fecha) AS first_purchase
            FROM tb_orden_cabecera
            WHERE cod_empresa = :cod_empresa $officeFilter
            GROUP BY cod_usuario
        ) AS t
        WHERE cod_usuario IN (
            SELECT cod_usuario
            FROM tb_orden_cabecera
            WHERE cod_empresa = :cod_empresa $officeFilter
            AND fecha BETWEEN :start AND :end
        )
    ";
    $clientes = Conexion::buscarRegistro($sqlClientes, $params);
    $clientes_recurrentes = [
        "nuevos"      => (int)($clientes['nuevos']      ?? 0),
        "recurrentes" => (int)($clientes['recurrentes'] ?? 0)
    ];

    // 6. VENTAS POR DÍA DE SEMANA
    $sqlDays = "
        SELECT
            WEEKDAY(o.fecha) AS day_block,
            COUNT(*) AS total_orders,
            SUM(o.total) AS total_amount
        FROM tb_orden_cabecera o
        WHERE $baseWhere
        GROUP BY WEEKDAY(o.fecha)
        ORDER BY day_block
    ";
    $days     = Conexion::buscarVariosRegistro($sqlDays, $params);
    $weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    $topDays      = [];
    $topDaysSales = [];
    for ($i = 0; $i < 7; $i++) {
        $topDays[$weekDays[$i]]      = 0;
        $topDaysSales[$weekDays[$i]] = 0;
    }
    foreach ($days as $row) {
        $label = $weekDays[(int)$row['day_block']];
        $topDays[$label]      = (int)$row['total_orders'];
        $topDaysSales[$label] = (float)$row['total_amount'];
    }

    // 7. PLATAFORMA
    $sqlPlatform = "
        SELECT o.medio_compra, COUNT(*) AS total_orders
        FROM tb_orden_cabecera o
        WHERE $baseWhere
        GROUP BY o.medio_compra
        ORDER BY total_orders DESC
    ";
    $platforms    = Conexion::buscarVariosRegistro($sqlPlatform, $params);
    $topPlatforms = [];
    foreach ($platforms as $row) {
        $topPlatforms[$row['medio_compra'] ?: 'OTRO'] = (int)$row['total_orders'];
    }

    // 8. ÓRDENES POR ESTADO
    $officeJoinFilter = $officeId > 0 ? "AND o.cod_sucursal = :officeId" : "";
    $sqlByState = "
        SELECT
            e.nombre AS estado_nombre,
            e.posicion,
            COUNT(o.cod_orden) AS total
        FROM tb_estado_ordenes e
        LEFT JOIN tb_orden_cabecera o
            ON o.estado = e.cod_estado
            AND o.cod_empresa = :cod_empresa
            AND o.fecha BETWEEN :start AND :end
            $officeJoinFilter
        GROUP BY e.cod_estado, e.nombre, e.posicion
        ORDER BY e.posicion
    ";
    $statesRaw       = Conexion::buscarVariosRegistro($sqlByState, $params);
    $ordenesPorEstado = [];
    foreach ($statesRaw as $row) {
        $ordenesPorEstado[$row['estado_nombre']] = (int)$row['total'];
    }

    // 9. TOP 5 PRODUCTOS
    $sqlTopProds = "
        SELECT
            p.nombre,
            SUM(od.cantidad)              AS cantidad,
            SUM(od.precio * od.cantidad)  AS total_ventas
        FROM tb_orden_cabecera o
        INNER JOIN tb_orden_detalle od ON o.cod_orden  = od.cod_orden
        INNER JOIN tb_productos     p  ON p.cod_producto = od.cod_producto
        WHERE $baseWhere
        GROUP BY od.cod_producto, p.nombre
        ORDER BY total_ventas DESC
        LIMIT 5
    ";
    $topProdsRaw  = Conexion::buscarVariosRegistro($sqlTopProds, $params);
    $topProductos = [];
    foreach ($topProdsRaw as $row) {
        $topProductos[] = [
            'nombre'       => html_entity_decode($row['nombre'], ENT_QUOTES, 'UTF-8'),
            'cantidad'     => (int)$row['cantidad'],
            'total_ventas' => round((float)$row['total_ventas'], 2),
        ];
    }

    // 10. RENDIMIENTO POR SUCURSAL (siempre a nivel empresa, sin filtro de sucursal)
    $paramsNoSuc = [
        ':cod_empresa' => $empresa,
        ':start'       => $dateStart,
        ':end'         => $dateEnd,
    ];
    $sqlSucursales = "
        SELECT
            s.nombre          AS sucursal,
            SUM(o.total)      AS ventas,
            COUNT(o.cod_orden) AS ordenes,
            AVG(o.total)      AS ticket_promedio
        FROM tb_orden_cabecera o
        INNER JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
        WHERE o.cod_empresa = :cod_empresa
          AND o.estado      = 'ENTREGADA'
          AND o.fecha       BETWEEN :start AND :end
        GROUP BY o.cod_sucursal, s.nombre
        ORDER BY ventas DESC
        LIMIT 5
    ";
    $sucursalesRaw    = Conexion::buscarVariosRegistro($sqlSucursales, $paramsNoSuc);
    $totalVentasSuc   = array_sum(array_column($sucursalesRaw, 'ventas'));
    $rendimientoSucursal = [];
    foreach ($sucursalesRaw as $row) {
        $rendimientoSucursal[] = [
            'sucursal'        => html_entity_decode($row['sucursal'], ENT_QUOTES, 'UTF-8'),
            'ventas'          => round((float)$row['ventas'], 2),
            'porcentaje'      => $totalVentasSuc > 0
                                    ? round(($row['ventas'] / $totalVentasSuc) * 100)
                                    : 0,
            'ordenes'         => (int)$row['ordenes'],
            'ticket_promedio' => round((float)$row['ticket_promedio'], 2),
        ];
    }

    // 11. CALIFICACIÓN PROMEDIO
    $calSucFilter = $officeId > 0 ? " AND c.cod_sucursal = :officeId " : "";
    $sqlCal = "
        SELECT
            ROUND(AVG(oc.calificacion), 1) AS promedio,
            COUNT(*)                        AS total_resenas
        FROM tb_orden_calificacion oc
        INNER JOIN tb_orden_cabecera c ON c.cod_orden = oc.cod_orden
        WHERE c.cod_empresa = :cod_empresa
          AND c.fecha        BETWEEN :start AND :end
          $calSucFilter
    ";
    $calRaw      = Conexion::buscarRegistro($sqlCal, $params);
    $calificacion = [
        'promedio'      => (float)($calRaw['promedio']     ?? 0),
        'total_resenas' => (int)($calRaw['total_resenas']  ?? 0),
    ];

    // INSIGHTS AUTOMÁTICOS
    $insights = [];

    // Horario más fuerte
    if (!empty($topHours)) {
        $hoursSorted = $topHours;
        arsort($hoursSorted);
        $peakLabel = array_key_first($hoursSorted);
        $insights[] = "El horario más fuerte fue de $peakLabel.";
    }

    // Canal de entrega dominante
    if (!empty($topDelivery)) {
        $totalVentasCanal = array_sum($topDelivery);
        if ($totalVentasCanal > 0) {
            $topDeliverySorted = $topDelivery;
            arsort($topDeliverySorted);
            $topCanal   = array_key_first($topDeliverySorted);
            $pctCanal   = round(($topDeliverySorted[$topCanal] / $totalVentasCanal) * 100);
            $insights[] = "$topCanal representa el {$pctCanal}% de tus ventas.";
        }
    }

    // Mejor día de ventas
    if (!empty($topDaysSales)) {
        $daysSorted = $topDaysSales;
        arsort($daysSorted);
        $bestDay = array_key_first($daysSorted);
        $dayMap  = ['Lun' => 'Lunes', 'Mar' => 'Martes', 'Mié' => 'Miércoles',
                    'Jue' => 'Jueves', 'Vie' => 'Viernes', 'Sáb' => 'Sábados', 'Dom' => 'Domingos'];
        $insights[] = "Los " . ($dayMap[$bestDay] ?? $bestDay) . " fueron tu mejor día de ventas.";
    }

    // Clientes recurrentes
    $totalCli = ($clientes_recurrentes['nuevos'] + $clientes_recurrentes['recurrentes']);
    if ($totalCli > 0) {
        $pctRec     = round(($clientes_recurrentes['recurrentes'] / $totalCli) * 100);
        $insights[] = "Tienes el {$pctRec}% de clientes recurrentes.";
    }

    // Sucursal top
    if (!empty($rendimientoSucursal)) {
        $topSuc     = $rendimientoSucursal[0];
        $insights[] = "La sucursal {$topSuc['sucursal']} generó el {$topSuc['porcentaje']}% de tus ventas.";
    }

    // Producto estrella
    if (!empty($topProductos)) {
        $insights[] = "Tu producto estrella es {$topProductos[0]['nombre']}.";
    }

    return [
        'success'              => 1,
        'total'                => (int)($totals['total']          ?? 0),
        'totalAmount'          => (float)($totals['total_amount'] ?? 0),
        'ticketPromedio'       => round((float)($totals['ticket_promedio'] ?? 0), 2),
        'topHours'             => $topHours,
        'topDays'              => $topDays,
        'topDaysSales'         => $topDaysSales,
        'topPlatforms'         => $topPlatforms,
        'deliveryTotals'       => $topDelivery,
        'trend'                => $trend,
        'clientes_recurrentes' => $clientes_recurrentes,
        'ordenesPorEstado'     => $ordenesPorEstado,
        'topProductos'         => $topProductos,
        'rendimientoSucursal'  => $rendimientoSucursal,
        'calificacion'         => $calificacion,
        'insights'             => $insights,
    ];
}

function getTrendDataCustom($baseWhere, $params, $period)
{
    switch ($period) {
        case 'day':
            $groupBy    = "HOUR(o.fecha)";
            $labelField = "HOUR(o.fecha)";
            break;
        case 'range_day':
            $groupBy    = "DATE(o.fecha)";
            $labelField = "DATE(o.fecha)";
            break;
        case 'range_month':
        default:
            $groupBy    = "DATE_FORMAT(o.fecha, '%Y-%m')";
            $labelField = "DATE_FORMAT(o.fecha, '%Y-%m')";
            break;
    }

    $sqlTrend = "
        SELECT
            $labelField AS label,
            o.cod_sucursal,
            s.nombre AS sucursal_nombre,
            SUM(o.total) AS total_amount
        FROM tb_orden_cabecera o
        INNER JOIN tb_sucursales s ON s.cod_sucursal = o.cod_sucursal
        WHERE $baseWhere
        GROUP BY o.cod_sucursal, $groupBy
        ORDER BY o.cod_sucursal, label
    ";

    $trendRaw = Conexion::buscarVariosRegistro($sqlTrend, $params);

    $branches  = [];
    $allLabels = [];
    foreach ($trendRaw as $row) {
        $branches[$row['cod_sucursal']] = $row['sucursal_nombre'];
        $allLabels[$row['label']]       = true;
    }
    ksort($allLabels);
    $allLabels = array_keys($allLabels);

    $dataByBranch = [];
    foreach ($branches as $branchId => $branchName) {
        $dataByBranch[$branchId]['name'] = $branchName;
        foreach ($allLabels as $lbl) {
            $dataByBranch[$branchId]['data'][$lbl] = 0;
        }
    }

    foreach ($trendRaw as $row) {
        $dataByBranch[$row['cod_sucursal']]['data'][$row['label']] = (float)$row['total_amount'];
    }

    $categories = [];
    foreach ($allLabels as $lbl) {
        $categories[] = ($period === 'day')
            ? str_pad($lbl, 2, '0', STR_PAD_LEFT) . ":00"
            : (string)$lbl;
    }

    $series = [];
    foreach ($dataByBranch as $branch) {
        $series[] = [
            'name' => $branch['name'],
            'data' => array_values($branch['data'])
        ];
    }

    return [
        'categories' => $categories,
        'series'     => $series
    ];
}
