<?php

class cl_reporte_couriers {
    var $cod_courier, $cod_sucursal, $fechaInicio, $fechaFin;
    var $cod_empresa, $session;

    public function __construct() {
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

    public function getDetalle() {
        $query = "SELECT oc.cod_orden, oc.envio, oc.pago
                    FROM tb_orden_cabecera oc
                    WHERE oc.cod_sucursal = $this->cod_sucursal
                    AND oc.cod_courier = $this->cod_courier 
                    AND oc.estado = 'ENTREGADA'
                    ORDER BY oc.cod_orden
                    LIMIT 300";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getFormasPago($cod_orden) {
        $query = "SELECT *
                    FROM tb_orden_pagos
                    WHERE cod_orden = $cod_orden";
        return Conexion::buscarVariosRegistro($query);
    }

    public function getReport($cod_sucursal, $fechaInicio, $fechaFin) {
        $cod_empresa = $this->cod_empresa;
        $filtro = " AND oc.cod_empresa = $cod_empresa ";
        if($cod_sucursal <> 0)
            $filtro = " AND oc.cod_sucursal = $cod_sucursal ";
        $query = "SELECT c.nombre, c.imagen, oc.cod_courier, IF(oc.pago = 'E', 'EFECTIVO', 'TARJETA') as pago, SUM(oc.total) as total, SUM(oc.envio) as envio 
                    FROM tb_orden_cabecera oc 
                    INNER JOIN tb_courier c
                        ON c.cod_courier = oc.cod_courier
                    WHERE oc.estado = 'ENTREGADA'
                    AND oc.is_envio = 1
                    AND oc.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                    $filtro
                    GROUP BY oc.cod_courier, oc.pago
                    ORDER BY oc.cod_courier";
        return Conexion::buscarVariosRegistro($query);
    }
    
    
    public function totalCouriers($cod_sucursal, $cod_courier, $fechaInicio, $fechaFin){
            $whereSucursal = "WHERE o.cod_sucursal = {$cod_sucursal}";
            if($cod_sucursal == 0) {
                $sucursales = $this->getListaSucursales();
                $whereSucursal = "WHERE o.cod_sucursal IN ({$sucursales})";
            }
            
            $whereCourier = "AND o.cod_courier = {$cod_courier} AND o.cod_courier != 99 ";
            
//SELECT * FROM tb_orden_cabecera WHERE cod_courier = 100 AND estado IN ('ENTREGADA', 'ENVIANDO')
		    $query = "SELECT u.cod_usuario, u.nombre, u.apellido, u.imagen, u.correo, u.telefono, SUM(o.envio) as total, COUNT(o.cod_orden) as num_items
                        FROM tb_orden_cabecera o
                        INNER JOIN tb_motorizado_asignacion m ON o.cod_orden = m.cod_orden
                        INNER JOIN tb_usuarios u ON m.cod_motorizado = u.cod_usuario
                        {$whereSucursal}
                        {$whereCourier}
                        AND o.is_envio = 1
                         AND o.estado in ('ENTREGADA', 'ENVIANDO')                        
                        AND o.fecha >='$fechaInicio 00:00:00'
                        AND o.fecha <='$fechaFin 23:59:00'
                        GROUP BY m.cod_motorizado";
        //    $row = Conexion::buscarVariosRegistro($query);
           // return $row;
           return $query;
		}
	
	public function orderCouriers($cod_sucursal, $cod_courier, $fechaInicio, $fechaFin){
            $whereSucursal = "WHERE o.cod_sucursal = {$cod_sucursal}";
            $sucSelect = $cod_sucursal;
            if($cod_sucursal == 0) {
                $sucursales = $this->getListaSucursales();
                $sucSelect = $sucursales;
                $whereSucursal = "WHERE o.cod_sucursal IN ({$sucursales})";
            }
            
             $whereCourier = "AND o.cod_courier = {$cod_courier}";
            if($cod_courier == 0) {
                 $couriers = $this->getListaCourier($sucSelect);
                 
//                 return $couriers;
                $whereCourier = "AND o.cod_courier IN ({$couriers})";
            }
		    $query = "SELECT o.cod_orden, o.fecha, o.envio, o.total, o.estado, c.nombre as courier
                        FROM tb_orden_cabecera o
                         INNER JOIN tb_courier c on c.cod_courier = o.cod_courier
                        {$whereSucursal}
                        {$whereCourier}
                        AND o.is_envio = 1
                         AND o.estado in ('ENTREGADA', 'ENVIANDO')                        
                        AND o.fecha >='$fechaInicio 00:00:00'
                        AND o.fecha <='$fechaFin 23:59:00'
                        ";
           $row = Conexion::buscarVariosRegistro($query);
            return $row;
		}	
	
	/**
     * 
     */	
	function getListaSucursales() {
            require_once "cl_sucursales.php";
            $Clsucursales = new cl_sucursales();
            $sucursales = $Clsucursales->lista();
            $_sucursales = [];
            foreach ($sucursales as $key => $sucursal) {
                $_sucursales[] = $sucursal["cod_sucursal"]; 
            }
            return implode(',', $_sucursales);
        }
        
    /**
     * 
     */
    function getListaCourier($sucursal) {
            require_once "cl_sucursales.php";
            $Clsucursales = new cl_sucursales();
            $dataCouriers = $Clsucursales->getCouriersIn($sucursal);
            $_codCouriers = [];
            $idsNotCouriers = [99];
            foreach ($dataCouriers as $key => $courier) {
                if(in_array($courier['cod_courier'], $idsNotCouriers))
                 continue;
                $_codCouriers[] = $courier["cod_courier"]; 
            }
           return implode(',', $_codCouriers);
           
        }    
}
?>