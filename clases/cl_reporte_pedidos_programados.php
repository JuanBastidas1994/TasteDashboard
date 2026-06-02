<?php

class cl_reporte_pedidos_programados
{
    var $session;

    public function __construct()
    {
        $this->session = getSession();
    }

    public function getProductosProgramados($cod_sucursal, $f_inicio, $f_fin)
    {
        $where = "AND c.cod_sucursal = {$cod_sucursal}";
        if ($cod_sucursal == 0) {
            $sucursales = $this->getListaSucursales();
            $where = "AND c.cod_sucursal IN ({$sucursales})";
        }

        $query = "SELECT p.cod_producto, p.nombre AS producto,
                         d.cantidad, d.precio_final, d.descripcion,
                         c.cod_orden, c.hora_retiro,
                         s.nombre AS nombre_sucursal
                  FROM tb_orden_cabecera c
                  INNER JOIN tb_orden_detalle d  ON c.cod_orden   = d.cod_orden
                  INNER JOIN tb_productos p       ON d.cod_producto = p.cod_producto
                  INNER JOIN tb_sucursales s       ON c.cod_sucursal = s.cod_sucursal
                  WHERE c.is_programado = 1
                  AND DATE(c.hora_retiro) >= '$f_inicio'
                  AND DATE(c.hora_retiro) <= '$f_fin'
                  AND c.estado NOT IN ('ANULADA', 'CANCELADA')
                  AND c.cod_empresa = {$this->session['cod_empresa']}
                  {$where}
                  ORDER BY c.hora_retiro ASC, p.nombre ASC";

        return Conexion::buscarVariosRegistro($query);
    }

    public function getOrdenes($cod_sucursal, $f_inicio, $f_fin)
    {
        $where = "AND c.cod_sucursal = {$cod_sucursal}";
        if ($cod_sucursal == 0) {
            $sucursales = $this->getListaSucursales();
            $where = "AND c.cod_sucursal IN ({$sucursales})";
        }

        $query = "SELECT c.cod_orden,
                         CONCAT(u.nombre, ' ', COALESCE(u.apellido, '')) AS cliente,
                         c.hora_retiro,
                         s.nombre AS sucursal,
                         c.estado,
                         c.total
                  FROM tb_orden_cabecera c
                  INNER JOIN tb_usuarios u   ON c.cod_usuario  = u.cod_usuario
                  INNER JOIN tb_sucursales s  ON c.cod_sucursal = s.cod_sucursal
                  WHERE c.is_programado = 1
                  AND DATE(c.hora_retiro) >= '$f_inicio'
                  AND DATE(c.hora_retiro) <= '$f_fin'
                  AND c.cod_empresa = {$this->session['cod_empresa']}
                  {$where}
                  ORDER BY c.hora_retiro ASC";

        return Conexion::buscarVariosRegistro($query) ?: [];
    }

    private function getListaSucursales()
    {
        require_once "cl_sucursales.php";
        $Clsucursales = new cl_sucursales();
        $sucursales   = $Clsucursales->lista();
        $ids = [];
        foreach ($sucursales as $sucursal) {
            $ids[] = $sucursal['cod_sucursal'];
        }
        return implode(',', $ids);
    }
}
?>
