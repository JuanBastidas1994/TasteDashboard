<?php
require_once "../funciones.php";
$session = getSession();

controller_create();

function buildWhere() {
    global $session;
    $cod_empresa  = $session['cod_empresa'];
    $f_ini        = isset($_GET['f_ini'])        ? $_GET['f_ini']                     : date('Y-m-01');
    $f_fin        = isset($_GET['f_fin'])        ? $_GET['f_fin']                     : date('Y-m-d');
    $estado       = isset($_GET['estado'])       ? $_GET['estado']                    : '';
    $origen       = isset($_GET['origen'])       ? $_GET['origen']                    : '';
    $recovery_src = isset($_GET['recovery_src']) ? $_GET['recovery_src']              : '';
    $cod_sucursal = isset($_GET['cod_sucursal']) ? intval($_GET['cod_sucursal'])       : 0;
    $tipo_usuario = isset($_GET['tipo_usuario']) ? intval($_GET['tipo_usuario'])       : 0;

    $where = "AND DATE(cs.created_at) BETWEEN '$f_ini' AND '$f_fin'";

    if ($estado !== '')        $where .= " AND cs.estado = '$estado'";
    if ($origen !== '')        $where .= " AND cs.origen = '$origen'";
    if ($recovery_src !== '')  $where .= " AND cs.recovery_source = '$recovery_src'";
    if ($cod_sucursal > 0)     $where .= " AND cs.cod_sucursal = $cod_sucursal";
    if ($tipo_usuario === 1)   $where .= " AND cs.cod_usuario IS NOT NULL";
    if ($tipo_usuario === 2)   $where .= " AND cs.cod_usuario IS NULL";

    return ['empresa' => $cod_empresa, 'where' => $where, 'f_ini' => $f_ini, 'f_fin' => $f_fin];
}

function getDatos() {
    global $session;
    $ctx         = buildWhere();
    $cod_empresa = $ctx['empresa'];
    $where       = $ctx['where'];

    // Métricas agregadas
    $q_metrics = "SELECT
                      COUNT(*) AS total,
                      COALESCE(SUM(estado = 'ACTIVO'),     0) AS activos,
                      COALESCE(SUM(estado = 'ABANDONADO'), 0) AS abandonados,
                      COALESCE(SUM(estado = 'CONVERTIDO'), 0) AS convertidos,
                      COALESCE(SUM(estado = 'EXPIRADO'),   0) AS expirados,
                      COALESCE(SUM(total_estimado), 0) AS monto_total,
                      COALESCE(SUM(CASE WHEN estado = 'ABANDONADO' THEN total_estimado ELSE 0 END), 0) AS monto_abandonado,
                      COALESCE(SUM(CASE WHEN estado = 'CONVERTIDO' THEN total_estimado ELSE 0 END), 0) AS monto_convertido,
                      ROUND(SUM(estado = 'ABANDONADO') / NULLIF(COUNT(*), 0) * 100, 1)           AS tasa_abandono,
                      ROUND(SUM(estado = 'CONVERTIDO') /
                            NULLIF(SUM(estado IN ('ABANDONADO','CONVERTIDO')), 0) * 100, 1)       AS tasa_conversion
                  FROM carrito_sesion cs
                  WHERE cs.cod_empresa = $cod_empresa
                  $where";
    $metrics = Conexion::buscarRegistro($q_metrics);

    // Recovery por canal
    $q_recovery = "SELECT recovery_source, COUNT(*) AS cnt
                   FROM carrito_sesion cs
                   WHERE cs.cod_empresa = $cod_empresa
                   AND cs.estado = 'CONVERTIDO'
                   AND cs.recovery_source IS NOT NULL
                   $where
                   GROUP BY cs.recovery_source";
    $recovery_rows = Conexion::buscarVariosRegistro($q_recovery);
    $recovery = [];
    if ($recovery_rows) {
        foreach ($recovery_rows as $r) {
            $recovery[$r['recovery_source']] = intval($r['cnt']);
        }
    }

    // Filas de la tabla
    $q_rows = "SELECT
                   cs.id,
                   cs.cart_token,
                   cs.cod_usuario,
                   u.nombre         AS nombre_usuario,
                   cs.email,
                   cs.telefono,
                   cs.origen,
                   cs.estado,
                   cs.total_estimado,
                   cs.cantidad_productos,
                   cs.created_at,
                   cs.updated_at,
                   cs.abandoned_at,
                   cs.recovered_at,
                   cs.converted_at,
                   cs.recovery_source,
                   cs.cod_preorden,
                   cs.cod_orden
               FROM carrito_sesion cs
               LEFT JOIN tb_usuarios u ON cs.cod_usuario = u.cod_usuario
               WHERE cs.cod_empresa = $cod_empresa
               $where
               ORDER BY cs.updated_at DESC";
    $rows = Conexion::buscarVariosRegistro($q_rows);

    return [
        'success'   => 1,
        'metrics'   => $metrics  ?: [],
        'recovery'  => $recovery,
        'rows'      => $rows     ?: [],
    ];
}
?>
