<?php

class cl_reporte_productos {
    var $cod_courier, $cod_sucursal, $fechaInicio, $fechaFin;
    var $cod_empresa, $session;

    public function __construct() {
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        public function getReporteProductosMasVendido($cod_empresa, $cod_sucursal, $fechaInicio, $fechaFin) {
            $fechaInicio = $fechaInicio . " 00:00:00";
            $fechaFin = $fechaFin . " 23:59:59";

            $sucursalFilter = " AND oc.cod_sucursal = $cod_sucursal ";
            if($cod_sucursal == 0) {
                $sucursalFilter = "";
            }

            $query = "SELECT SUM(od.cantidad) as cantidad, p.nombre
                        FROM tb_orden_cabecera oc
                        INNER JOIN tb_orden_detalle od 
                            ON oc.cod_orden = od.cod_orden
                        INNER JOIN tb_productos p
                            ON od.cod_producto = p.cod_producto
                        WHERE oc.cod_empresa = $cod_empresa
                        AND oc.estado = 'ENTREGADA'
                        $sucursalFilter
                        AND oc.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                        GROUP BY od.cod_producto
                        ORDER BY cantidad DESC;";
            return Conexion::buscarVariosRegistro($query);
        } 
}
?>