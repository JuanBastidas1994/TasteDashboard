<?php

class cl_reporte_tiempo_aceptacion
{
    var $session;

    public function __construct()
    {
        $this->session = getSession();
    }

    public function getTiempos($cod_sucursal, $f_inicio, $f_fin)
    {
        $where = "AND c.cod_sucursal = {$cod_sucursal}";
        if ($cod_sucursal == 0) {
            $sucursales = $this->getListaSucursales();
            $where = "AND c.cod_sucursal IN ({$sucursales})";
        }

        $query = "SELECT c.cod_orden, c.fecha, c.hora_retiro,
                         s.nombre AS nombre_sucursal,
                         TIMESTAMPDIFF(MINUTE, c.fecha, c.hora_retiro) AS minutos
                  FROM tb_orden_cabecera c
                  INNER JOIN tb_sucursales s ON c.cod_sucursal = s.cod_sucursal
                  WHERE DATE(c.fecha) >= '$f_inicio'
                  AND DATE(c.fecha) <= '$f_fin'
                  AND c.hora_retiro IS NOT NULL
                  AND c.hora_retiro > c.fecha
                  AND c.cod_empresa = {$this->session['cod_empresa']}
                  {$where}
                  ORDER BY c.cod_orden DESC";

        return Conexion::buscarVariosRegistro($query);
    }

    public function getResumenPorSucursal($f_inicio, $f_fin)
    {
        $sucursales = $this->getListaSucursales();
        if (!$sucursales) return [];

        $query = "SELECT s.nombre AS sucursal,
                         COUNT(*) AS total_pedidos,
                         ROUND(AVG(TIMESTAMPDIFF(MINUTE, c.fecha, c.hora_retiro)), 1) AS promedio_minutos,
                         MIN(TIMESTAMPDIFF(MINUTE, c.fecha, c.hora_retiro)) AS min_minutos,
                         MAX(TIMESTAMPDIFF(MINUTE, c.fecha, c.hora_retiro)) AS max_minutos
                  FROM tb_orden_cabecera c
                  INNER JOIN tb_sucursales s ON c.cod_sucursal = s.cod_sucursal
                  WHERE DATE(c.fecha) >= '$f_inicio'
                  AND DATE(c.fecha) <= '$f_fin'
                  AND c.hora_retiro IS NOT NULL
                  AND c.hora_retiro > c.fecha
                  AND c.cod_empresa = {$this->session['cod_empresa']}
                  AND c.cod_sucursal IN ($sucursales)
                  GROUP BY c.cod_sucursal, s.nombre
                  ORDER BY promedio_minutos DESC";

        return Conexion::buscarVariosRegistro($query) ?: [];
    }

    private function getListaSucursales()
    {
        require_once "cl_sucursales.php";
        $Clsucursales = new cl_sucursales();
        $sucursales = $Clsucursales->lista();
        $ids = [];
        foreach ($sucursales as $sucursal) {
            $ids[] = $sucursal['cod_sucursal'];
        }
        return implode(',', $ids);
    }
}
?>
