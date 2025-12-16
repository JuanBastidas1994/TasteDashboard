<?php
class cl_reporte_ordenes {
    public $session;
    public $cod_empresa;
    public $fechaInicio, $fechaFin, $tipoPago, $estado = "";
    
    public function __construct() {
        $this->session = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    public function getReporteTotales() {
        $filtro = "";
        if($this->estado != "") {
            $filtro = " AND oc.estado = 'ENTREGADA' ";
        }
        $query = "  SELECT 
                        e.nombre, COUNT(oc.cod_orden) as cant_ordenes
                    FROM 
                        tb_empresas e
                    LEFT JOIN tb_orden_cabecera oc
                        ON oc.cod_empresa = e.cod_empresa
                        AND oc.fecha BETWEEN '$this->fechaInicio' AND '$this->fechaFin'
                        $filtro
                    WHERE e.estado = 'A'
                    AND e.cod_tipo_empresa = 1
                    GROUP BY e.cod_empresa
                    ORDER BY e.nombre";
        return Conexion::buscarVariosRegistro($query);
    }
    
    public function getReportePorTipoPago() {
        $query = "  SELECT 
                        e.nombre, COUNT(op.cod_orden) as cant_ordenes
                    FROM 
                        tb_empresas e
                    LEFT JOIN tb_orden_cabecera oc
                        ON oc.cod_empresa = e.cod_empresa
                        AND oc.fecha BETWEEN '$this->fechaInicio' AND '$this->fechaFin'
                        AND oc.estado = 'ENTREGADA'
                    LEFT JOIN tb_orden_pagos op
                        ON op.cod_orden = oc.cod_orden
                        AND op.forma_pago = '$this->tipoPago'
                    WHERE e.estado = 'A'
                    AND e.cod_tipo_empresa = 1
                    GROUP BY e.cod_empresa
                    ORDER BY e.nombre";
        return Conexion::buscarVariosRegistro($query);
    }
}
?>