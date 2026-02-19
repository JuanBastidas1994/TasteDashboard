<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
$Clempresas = new cl_empresas();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getWidgets(){
    global $Clpromociones;
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true); // true = array asociativo

    if (!$input) {
        return ['success' => 0, 'mensaje' => 'Falta informacion'];
    }
    $period = $input['period'];

    $validPeriods = ['day', 'week', 'month', 'year'];
    $period = in_array($period, $validPeriods) ? $period : 'month';
    $officeId = $input['office_id'] ?? 0;
    $dates = getDatesByPeriod($period);
	$dateStart = $dates['start'];
	$dateEnd = $dates['end'];

    return getIndicators($officeId, $dateStart, $dateEnd, $period);

    return [
        'office_id' => $officeId,
        'start' => $dateStart,
        'end' => $dateEnd
    ];
}

function getDatesByPeriod($period)
{
    $now = new DateTime();

    switch ($period) {
        case 'day':
            $start = clone $now;
            $end = clone $now;
            break;

        case 'week':
            $start = clone $now;
            $start->modify('monday this week');

            $end = clone $now;
            $end->modify('sunday this week');
            break;

        case 'year':
            $start = clone $now;
            $start->modify('first day of january this year');

            $end = clone $now;
            $end->modify('last day of december this year');
            break;

        case 'month':
        default:
            $start = clone $now;
            $start->modify('first day of this month');

            $end = clone $now;
            $end->modify('last day of this month');
            break;
    }

    // Ajustar a inicio y fin del día
    $start->setTime(0, 0, 0);
    $end->setTime(23, 59, 59);

    return [
        'start' => $start->format('Y-m-d H:i:s'),
        'end'   => $end->format('Y-m-d H:i:s'),
    ];
}

function getIndicators($officeId, $dateStart, $dateEnd, $period)
{
    global $session;
    $empresa = $session['cod_empresa'];

    // 🔥 Ahora usamos alias o
    $baseWhere = "
        o.cod_empresa = :cod_empresa
        AND o.estado = 'ENTREGADA'
        AND o.fecha BETWEEN :start AND :end
    ";

    $params = [
        ':cod_empresa' => $empresa,
        ':start' => $dateStart,
        ':end' => $dateEnd
    ];

    if ($officeId > 0) {
        $baseWhere .= " AND o.cod_sucursal = :officeId ";
        $params[':officeId'] = $officeId;
    }

    // ================================
    // 1️⃣ TOTALES
    // ================================

    $sqlTotals = "
        SELECT 
            COUNT(*) AS total,
            SUM(o.total) AS total_amount,
            AVG(o.total) AS ticket_promedio
        FROM tb_orden_cabecera o
        WHERE $baseWhere
    ";

    $totals = Conexion::buscarRegistro($sqlTotals, $params);

    // ================================
    // 2️⃣ HORAS PICO
    // ================================

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
        $h = str_pad($row['hour_block'], 2, '0', STR_PAD_LEFT);
        $next = str_pad($row['hour_block'] + 1, 2, '0', STR_PAD_LEFT);
        $label = "$h:00-$next:00";
        $topHours[$label] = (int)$row['total_orders'];
    }

    // ================================
    // 3️⃣ TIPO DE ENTREGA
    // ================================

    $sqlType = "
        SELECT 
            o.is_envio,
            SUM(o.total) AS total_amount
        FROM tb_orden_cabecera o
        WHERE $baseWhere
        GROUP BY o.is_envio
    ";

    $typesRaw = Conexion::buscarVariosRegistro($sqlType, $params);

    $mapTypes = [
        0 => 'Pickup',
        1 => 'Delivery',
        2 => 'En mesa'
    ];

    $topDelivery = [];
    foreach ($typesRaw as $row) {
        $name = $mapTypes[$row['is_envio']] ?? 'Otro';
        $topDelivery[$name] = (float)$row['total_amount'];
    }

    // ================================
    // 4️⃣ TENDENCIA MULTI-SUCURSAL
    // ================================
    $trend = getTrendData($baseWhere, $params, $period);

    // ================================
    // 4️⃣ CLIENTES RECURRENTES
    // ================================
    $sqlClientes = "
    SELECT 
        SUM(CASE WHEN first_purchase BETWEEN :start AND :end THEN 1 ELSE 0 END) AS nuevos,
        SUM(CASE WHEN first_purchase < :start THEN 1 ELSE 0 END) AS recurrentes
    FROM (
        SELECT cod_usuario, MIN(fecha) AS first_purchase
        FROM tb_orden_cabecera
        WHERE cod_empresa = :cod_empresa
        GROUP BY cod_usuario
    ) AS t
    WHERE cod_usuario IN (
        SELECT cod_usuario 
        FROM tb_orden_cabecera
        WHERE cod_empresa = :cod_empresa
        AND fecha BETWEEN :start AND :end
    )
    ";
    $clientes = Conexion::buscarRegistro($sqlClientes, $params);
    $clientes_recurrentes = [
        "nuevos" => (int)$clientes['nuevos'],
        "recurrentes" => (int)$clientes['recurrentes']
    ];

    // ================================
    // RETURN FINAL
    // ================================

    return [
        "total" => (int)($totals['total'] ?? 0),
        "totalAmount" => (float)($totals['total_amount'] ?? 0),
        "ticketPromedio" => round((float)($totals['ticket_promedio'] ?? 0), 2),
        "topHours" => $topHours,
        "deliveryTotals" => $topDelivery,
        "trend" => $trend,
        'clientes_recurrentes' => $clientes_recurrentes
    ];
}


function getTrendData($baseWhere, $params, $period)
{
    switch ($period) {

        case 'day':
            $groupBy = "HOUR(o.fecha)";
            $labelField = "HOUR(o.fecha)";
            $range = range(0, 23);
            break;

        case 'week':
            $groupBy = "WEEKDAY(o.fecha)";
            $labelField = "WEEKDAY(o.fecha)";
            $range = range(0, 6);
            break;

        case 'month':
            $daysInMonth = date('t', strtotime($params[':start']));
            $groupBy = "DAY(o.fecha)";
            $labelField = "DAY(o.fecha)";
            $range = range(1, $daysInMonth);
            break;

        case 'year':
        default:
            $groupBy = "MONTH(o.fecha)";
            $labelField = "MONTH(o.fecha)";
            $range = range(1, 12);
            break;
    }

    // 🔥 JOIN con sucursales
    $sqlTrend = "
        SELECT 
            $labelField AS label,
            o.cod_sucursal,
            s.nombre AS sucursal_nombre,
            SUM(o.total) AS total_amount
        FROM tb_orden_cabecera o
        INNER JOIN tb_sucursales s 
            ON s.cod_sucursal = o.cod_sucursal
        WHERE $baseWhere
        GROUP BY o.cod_sucursal, $groupBy
        ORDER BY o.cod_sucursal, label
    ";

    $trendRaw = Conexion::buscarVariosRegistro($sqlTrend, $params);

    // =============================
    // 1️⃣ Detectar sucursales
    // =============================

    $branches = [];

    foreach ($trendRaw as $row) {
        $branches[$row['cod_sucursal']] = $row['sucursal_nombre'];
    }

    // =============================
    // 2️⃣ Inicializar estructura en 0
    // =============================

    $dataByBranch = [];

    foreach ($branches as $branchId => $branchName) {
        foreach ($range as $r) {
            $dataByBranch[$branchId]['name'] = $branchName;
            $dataByBranch[$branchId]['data'][$r] = 0;
        }
    }

    // =============================
    // 3️⃣ Sobrescribir con datos reales
    // =============================

    foreach ($trendRaw as $row) {
        $branchId = $row['cod_sucursal'];
        $label = (int)$row['label'];
        $dataByBranch[$branchId]['data'][$label] = (float)$row['total_amount'];
    }

    // =============================
    // 4️⃣ Construir categorías
    // =============================

    $categories = [];

    $weekDays = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    $months = [
        1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',
        7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'
    ];

    foreach ($range as $key) {

        switch ($period) {
            case 'day':
                $label = str_pad($key, 2, '0', STR_PAD_LEFT) . ":00";
                break;
            case 'week':
                $label = $weekDays[$key];
                break;
            case 'month':
                $label = $key;
                break;
            case 'year':
            default:
                $label = $months[$key];
                break;
        }

        $categories[] = $label;
    }

    // =============================
    // 5️⃣ Formatear series para Apex
    // =============================

    $series = [];

    foreach ($dataByBranch as $branch) {
        $series[] = [
            "name" => $branch['name'],
            "data" => array_values($branch['data'])
        ];
    }

    return [
        "categories" => $categories,
        "series" => $series
    ];
}




?>