<?php
require_once "../funciones.php";
$session = getSession();

controller_create();

function getDatos() {
    global $session;
    $cod_empresa  = $session['cod_empresa'];
    $cod_sucursal = isset($_GET['cod_sucursal']) ? intval($_GET['cod_sucursal']) : 0;
    $periodo      = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes_actual';

    $today        = date('Y-m-d');
    $this_m_start = date('Y-m-01');
    $last_m_start = date('Y-m-01', strtotime('-1 month'));
    $last_m_end   = date('Y-m-t',  strtotime('-1 month'));

    switch ($periodo) {
        case 'mes_anterior':
            $f_ini      = $last_m_start;
            $f_fin      = $last_m_end;
            $f_prev_ini = date('Y-m-01', strtotime('-2 months'));
            $f_prev_fin = date('Y-m-t',  strtotime('-2 months'));
            $n_meses    = 5;
            break;
        case 'ultimos_3_meses':
            $f_ini      = date('Y-m-01', strtotime('-2 months'));
            $f_fin      = $today;
            $f_prev_ini = date('Y-m-01', strtotime('-5 months'));
            $f_prev_fin = date('Y-m-t',  strtotime('-3 months'));
            $n_meses    = 6;
            break;
        case 'ultimos_6_meses':
            $f_ini      = date('Y-m-01', strtotime('-5 months'));
            $f_fin      = $today;
            $f_prev_ini = date('Y-m-01', strtotime('-11 months'));
            $f_prev_fin = date('Y-m-t',  strtotime('-6 months'));
            $n_meses    = 6;
            break;
        default: // mes_actual
            $f_ini      = $this_m_start;
            $f_fin      = $today;
            $f_prev_ini = $last_m_start;
            $f_prev_fin = $last_m_end;
            $n_meses    = 5;
            break;
    }

    $where_suc = $cod_sucursal > 0
        ? "AND JSON_UNQUOTE(JSON_EXTRACT(pj.json, '$.cod_sucursal')) = '$cod_sucursal'"
        : "";

    // Abandonadas - período actual
    $q_aban = "SELECT COUNT(*) AS cnt, COALESCE(SUM(pj.amount), 0) AS monto
               FROM tb_preorden_json pj
               INNER JOIN tb_usuarios u ON pj.cod_usuario = u.cod_usuario
               WHERE u.cod_empresa = $cod_empresa
               $where_suc
               AND pj.estado = 'ABANDONADA'
               AND DATE(pj.fecha_create) BETWEEN '$f_ini' AND '$f_fin'";
    $aban = Conexion::buscarRegistro($q_aban);

    // Abandonadas - período anterior (para calcular delta)
    $q_prev = "SELECT COUNT(*) AS cnt, COALESCE(SUM(pj.amount), 0) AS monto
               FROM tb_preorden_json pj
               INNER JOIN tb_usuarios u ON pj.cod_usuario = u.cod_usuario
               WHERE u.cod_empresa = $cod_empresa
               $where_suc
               AND pj.estado = 'ABANDONADA'
               AND DATE(pj.fecha_create) BETWEEN '$f_prev_ini' AND '$f_prev_fin'";
    $aban_prev = Conexion::buscarRegistro($q_prev);

    // Pagadas período actual (para donut)
    $q_total = "SELECT COUNT(*) AS cnt
                FROM tb_preorden_json pj
                INNER JOIN tb_usuarios u ON pj.cod_usuario = u.cod_usuario
                WHERE u.cod_empresa = $cod_empresa
                $where_suc
                AND pj.estado = 'PAGADA'
                AND DATE(pj.fecha_create) BETWEEN '$f_ini' AND '$f_fin'";
    $total = Conexion::buscarRegistro($q_total);

    // Tendencia mensual (últimos N meses)
    $trend_start = date('Y-m-01', strtotime("-" . ($n_meses - 1) . " months"));
    $q_trend = "SELECT DATE_FORMAT(pj.fecha_create, '%Y-%m') AS mes,
                       DATE_FORMAT(pj.fecha_create, '%b')    AS mes_label,
                       COALESCE(SUM(pj.amount), 0)           AS monto
                FROM tb_preorden_json pj
                INNER JOIN tb_usuarios u ON pj.cod_usuario = u.cod_usuario
                WHERE u.cod_empresa = $cod_empresa
                $where_suc
                AND pj.estado = 'ABANDONADA'
                AND DATE(pj.fecha_create) >= '$trend_start'
                GROUP BY DATE_FORMAT(pj.fecha_create, '%Y-%m')
                ORDER BY mes ASC";
    $trend = Conexion::buscarVariosRegistro($q_trend);

    $cnt      = intval($aban['cnt']);
    $monto    = floatval($aban['monto']);
    $cnt_prev = intval($aban_prev['cnt']);
    $mon_prev = floatval($aban_prev['monto']);
    $cnt_tot  = intval($total['cnt']);

    $delta_cnt = $cnt_prev  > 0 ? round((($cnt   - $cnt_prev)  / $cnt_prev)  * 100, 1) : null;
    $delta_mon = $mon_prev  > 0 ? round((($monto - $mon_prev)  / $mon_prev)  * 100, 1) : null;

    $t_labels = [];
    $t_values = [];
    if ($trend) {
        foreach ($trend as $t) {
            $t_labels[] = $t['mes_label'];
            $t_values[] = floatval($t['monto']);
        }
    }

    return [
        'success'               => 1,
        'count_aban'            => $cnt,
        'amount_aban'           => $monto,
        'delta_count'           => $delta_cnt,
        'delta_amount'          => $delta_mon,
        'count_aban_donut'      => $cnt,
        'count_completed_donut' => max(0, $cnt_tot - $cnt),
        'trend_labels'          => $t_labels,
        'trend_values'          => $t_values,
    ];
}
?>
