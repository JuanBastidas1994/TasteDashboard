<?php
require_once "../funciones.php";
//Clases
require_once "../clases/cl_empresas.php";
$Clempresas = new cl_empresas();
$session = getSession();
error_reporting(E_ALL);

controller_create();

function getInfoReport(){
    $rawData = file_get_contents("php://input");
    $input = json_decode($rawData, true);

    $officeId = $input['office_id'] ?? 0;
    $dateStart = $input['dateStart'] ?? 0;
    $dateEnd = $input['dateEnd'] ?? 0;

    global $session;
    $empresa = $session['cod_empresa'];

    $params = [
        ':cod_empresa' => $empresa,
        ':start' => $dateStart,
        ':end' => $dateEnd
    ];

    $officeFilter = "";
    if ($officeId > 0) {
        $officeFilter = " AND cod_sucursal = :officeId ";
        $params[':officeId'] = $officeId;
    }

    $sql = "
        SELECT
            SUM(CASE WHEN u.first_purchase BETWEEN :start AND :end THEN 1 ELSE 0 END) AS nuevos,
            SUM(CASE WHEN u.first_purchase < :start THEN 1 ELSE 0 END) AS recurrentes
        FROM (
            SELECT 
                o.cod_usuario,
                MIN(o.fecha) AS first_purchase
            FROM tb_orden_cabecera o
            WHERE o.cod_empresa = :cod_empresa
            GROUP BY o.cod_usuario
        ) u
        INNER JOIN (
            SELECT DISTINCT cod_usuario
            FROM tb_orden_cabecera
            WHERE cod_empresa = :cod_empresa
            AND fecha BETWEEN :start AND :end
            $officeFilter
        ) r ON r.cod_usuario = u.cod_usuario
    ";

    $info = Conexion::buscarRegistro($sql, $params);

    $grafico = getNewVsReturningTrend($officeId, $dateStart, $dateEnd);

    return [
        'clients_news' => (int)$info['nuevos'],
        'clients_recurrents' => (int)$info['recurrentes'],
        'chart_news_recurrents' => $grafico
    ];
}



function getNewVsReturningTrend($officeId, $dateStart, $dateEnd)
{
    global $session;

    $empresa = $session['cod_empresa'];

    $params = [
        ':cod_empresa' => $empresa,
        ':start' => $dateStart,
        ':end' => $dateEnd
    ];

    $officeFilter = "";
    if ($officeId > 0) {
        $officeFilter = " AND o.cod_sucursal = :officeId ";
        $params[':officeId'] = $officeId;
    }

    $daysDiff = (strtotime($dateEnd) - strtotime($dateStart)) / 86400;

    // Decidir agrupación
    if ($daysDiff <= 30) {
        $periodField = "DATE(o.fecha)";
        $labelField = "DATE_FORMAT(o.fecha, '%d %b %Y')";
        $intervalUnit = "DAY";
    } else {
        $periodField = "DATE_FORMAT(o.fecha, '%Y-%m-01')";
        $labelField = "DATE_FORMAT(o.fecha, '%b %Y')";
        $intervalUnit = "MONTH";
    }

    $sql = "
    SELECT 
        periodo,
        label,
        COUNT(DISTINCT CASE 
            WHEN first_purchase >= periodo 
            AND first_purchase < DATE_ADD(periodo, INTERVAL 1 $intervalUnit)
            THEN cod_usuario 
        END) AS nuevos,

        COUNT(DISTINCT CASE 
            WHEN first_purchase < periodo 
            THEN cod_usuario 
        END) AS recurrentes

    FROM (
        SELECT 
            o.cod_usuario,
            fp.first_purchase,
            $periodField AS periodo,
            $labelField AS label
        FROM tb_orden_cabecera o
        INNER JOIN (
            SELECT 
                cod_usuario,
                MIN(fecha) AS first_purchase
            FROM tb_orden_cabecera
            WHERE cod_empresa = :cod_empresa
            GROUP BY cod_usuario
        ) fp ON fp.cod_usuario = o.cod_usuario
        WHERE o.cod_empresa = :cod_empresa
        AND o.fecha BETWEEN :start AND :end
        $officeFilter
    ) x
    GROUP BY periodo, label
    ORDER BY periodo
    ";

    $rows = Conexion::buscarVariosRegistro($sql, $params);

    $categories = [];
    $dataNew = [];
    $dataReturning = [];

    foreach ($rows as $row) {
        $categories[] = $row['label'];
        $dataNew[] = (int)$row['nuevos'];
        $dataReturning[] = (int)$row['recurrentes'];
    }

    return [
        "categories" => $categories,
        "series" => [
            [
                "name" => "Nuevos",
                "data" => $dataNew
            ],
            [
                "name" => "Recurrentes",
                "data" => $dataReturning
            ]
        ]
    ];
}






?>