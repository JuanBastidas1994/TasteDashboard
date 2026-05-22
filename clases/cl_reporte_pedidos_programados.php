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
                         d.cantidad, d.precio_final, d.descripcion
                  FROM tb_orden_cabecera c
                  INNER JOIN tb_orden_detalle d ON c.cod_orden = d.cod_orden
                  INNER JOIN tb_productos p ON d.cod_producto = p.cod_producto
                  WHERE c.is_programado = 1
                  AND DATE(c.hora_retiro) >= '$f_inicio'
                  AND DATE(c.hora_retiro) <= '$f_fin'
                  AND c.cod_empresa = {$this->session['cod_empresa']}
                  {$where}
                  ORDER BY p.nombre";

        return Conexion::buscarVariosRegistro($query);
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
