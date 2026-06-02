<?php

class cl_reporte_tiempos_motorizado
{
    var $session;

    public function __construct()
    {
        $this->session = getSession();
    }

    public function getTiempos($cod_sucursal, $f_inicio, $f_fin)
    {
        $where = "AND o.cod_sucursal = {$cod_sucursal}";
        if ($cod_sucursal == 0) {
            $sucursales = $this->getListaSucursales();
            $where = "AND o.cod_sucursal IN ({$sucursales})";
        }

        // Subqueries para obtener la primera fecha de cada estado en el historial
        $query = "SELECT
                    o.cod_orden,
                    CONCAT(u.nombre, ' ', u.apellido) AS motorizado,
                    h_asig.fecha  AS fecha_asignada,
                    h_env.fecha   AS fecha_enviando,
                    h_ent.fecha   AS fecha_entregada,
                    TIMESTAMPDIFF(MINUTE, h_asig.fecha, h_env.fecha)  AS min_aceptar,
                    TIMESTAMPDIFF(MINUTE, h_env.fecha,  h_ent.fecha)  AS min_camino,
                    TIMESTAMPDIFF(MINUTE, h_asig.fecha, h_ent.fecha)  AS min_total
                  FROM tb_orden_cabecera o
                  INNER JOIN tb_motorizado_asignacion ma ON o.cod_orden = ma.cod_orden
                  INNER JOIN tb_usuarios u ON ma.cod_motorizado = u.cod_usuario
                  LEFT JOIN (
                      SELECT cod_orden, MIN(fecha) AS fecha
                      FROM tb_orden_historial
                      WHERE estado = 'ASIGNADA'
                      GROUP BY cod_orden
                  ) h_asig ON o.cod_orden = h_asig.cod_orden
                  LEFT JOIN (
                      SELECT cod_orden, MIN(fecha) AS fecha
                      FROM tb_orden_historial
                      WHERE estado = 'ENVIANDO'
                      GROUP BY cod_orden
                  ) h_env ON o.cod_orden = h_env.cod_orden
                  LEFT JOIN (
                      SELECT cod_orden, MIN(fecha) AS fecha
                      FROM tb_orden_historial
                      WHERE estado = 'ENTREGADA'
                      GROUP BY cod_orden
                  ) h_ent ON o.cod_orden = h_ent.cod_orden
                  WHERE DATE(o.fecha) >= '$f_inicio'
                  AND DATE(o.fecha) <= '$f_fin'
                  AND o.is_envio = 1
                  AND o.cod_empresa = {$this->session['cod_empresa']}
                  {$where}
                  ORDER BY o.cod_orden DESC";

        return Conexion::buscarVariosRegistro($query);
    }

    private function getListaSucursales()
    {
        require_once "cl_sucursales.php";
        $Clsucursales = new cl_sucursales();
        $sucursales = $Clsucursales->lista();
        $ids = [];
        foreach ($sucursales as $s) {
            $ids[] = $s['cod_sucursal'];
        }
        return implode(',', $ids);
    }
}
?>
