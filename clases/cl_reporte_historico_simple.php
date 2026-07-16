<?php

class cl_reporte_historico_simple {

    var $session;

    public function __construct() {
        $this->session = getSession();
    }

    public function getAnios($cod_empresa) {
        $query = "SELECT anio FROM resumen_ventas_mensual WHERE cod_empresa = $cod_empresa
                  UNION
                  SELECT anio FROM resumen_ventas_anual WHERE cod_empresa = $cod_empresa
                  ORDER BY anio DESC";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getTabla($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";

        $sqlMensual = "SELECT mes, cod_sucursal, nombre_sucursal, total_ventas, total_ordenes,
                              total_pickup, total_delivery, total_mesa, clientes_nuevos, clientes_recurrentes
                       FROM resumen_ventas_mensual
                       WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                       ORDER BY mes, cod_sucursal";
        $rows = Conexion::buscarVariosRegistro($sqlMensual);
        if (!empty($rows)) return $rows;

        $sqlAnual = "SELECT 0 AS mes, cod_sucursal, nombre_sucursal, total_ventas, total_ordenes,
                            total_pickup, total_delivery, total_mesa, clientes_nuevos, clientes_recurrentes
                     FROM resumen_ventas_anual
                     WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                     ORDER BY cod_sucursal";
        return Conexion::buscarVariosRegistro($sqlAnual);
    }
}
?>
