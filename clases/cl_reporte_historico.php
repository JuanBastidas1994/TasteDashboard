<?php

class cl_reporte_historico {

    var $session;

    public function __construct() {
        $this->session = getSession();
    }

    public function getAnios($cod_empresa) {
        $query = "SELECT DISTINCT anio
                  FROM resumen_ventas_mensual
                  WHERE cod_empresa = $cod_empresa
                  ORDER BY anio DESC";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getResumenGeneral($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";

        $sqlTotales = "SELECT
                        SUM(total_ventas)  AS total_ventas,
                        SUM(total_ordenes) AS total_ordenes,
                        SUM(total_ventas) / NULLIF(SUM(total_ordenes), 0) AS ticket_promedio
                       FROM resumen_ventas_mensual
                       WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro";
        $totales = Conexion::buscarRegistro($sqlTotales);

        $sqlTendencia = "SELECT mes, cod_sucursal, nombre_sucursal,
                                SUM(total_ventas) AS total_ventas
                         FROM resumen_ventas_mensual
                         WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                         GROUP BY mes, cod_sucursal, nombre_sucursal
                         ORDER BY mes, cod_sucursal";
        $tendencia = Conexion::buscarVariosRegistro($sqlTendencia);

        return ['totales' => $totales, 'tendencia' => $tendencia];
    }

    public function getTiposEntrega($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";
        $query  = "SELECT mes,
                          SUM(monto_pickup)   AS pickup,
                          SUM(monto_delivery) AS delivery,
                          SUM(monto_mesa)     AS mesa
                   FROM resumen_ventas_mensual
                   WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                   GROUP BY mes ORDER BY mes";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getClientes($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";
        $query  = "SELECT mes,
                          SUM(clientes_nuevos)      AS nuevos,
                          SUM(clientes_recurrentes) AS recurrentes
                   FROM resumen_ventas_mensual
                   WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                   GROUP BY mes ORDER BY mes";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getProductos($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";
        $query  = "SELECT cod_producto, nombre_producto,
                          SUM(cantidad)     AS cantidad,
                          SUM(total_ventas) AS total_ventas
                   FROM resumen_productos_mensual
                   WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                   GROUP BY cod_producto, nombre_producto
                   ORDER BY cantidad DESC
                   LIMIT 10";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getMedios($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";
        $query  = "SELECT medio_compra,
                          SUM(total_ordenes) AS total_ordenes,
                          SUM(total_ventas)  AS total_ventas
                   FROM resumen_medios_mensual
                   WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                   GROUP BY medio_compra
                   ORDER BY total_ordenes DESC";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getHoras($cod_empresa, $anio, $cod_sucursal) {
        $filtro = $cod_sucursal > 0 ? "AND cod_sucursal = $cod_sucursal" : "";
        $query  = "SELECT hora,
                          SUM(total_ordenes) AS total_ordenes,
                          SUM(total_ventas)  AS total_ventas
                   FROM resumen_horas_mensual
                   WHERE cod_empresa = $cod_empresa AND anio = $anio $filtro
                   GROUP BY hora ORDER BY hora";
        return Conexion::buscarVariosRegistro($query);
    }
}
?>
