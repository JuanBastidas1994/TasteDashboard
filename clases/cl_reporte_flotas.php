<?php

class cl_reporte_flotas {
    var $cod_courier, $cod_sucursal, $fechaInicio, $fechaFin;
    var $cod_empresa, $session;

    public function __construct() {
		$this->session = getSession();
		$this->cod_empresa = $this->session['cod_empresa'];
	}

	public function orderFlotas($cod_sucursal, $cod_flota, $fechaInicio, $fechaFin){
            
        $cod_empresa = $this->cod_empresa;
        $officeAnd = "";
        if($cod_sucursal > 0)
            $officeAnd = " AND s.cod_sucursal = $cod_sucursal ";

        $query = "SELECT 
                o.cod_orden,
                o.fecha,
                o.envio,
                o.total,
                o.estado,
                s.nombre AS sucursal,
                o.distancia,
            
                -- Pagos concatenados desde tabla tb_formas_pago
                GROUP_CONCAT(
                    DISTINCT fp.descripcion
                    ORDER BY fp.descripcion
                    SEPARATOR ', '
                ) AS pagos
            
            FROM tb_orden_cabecera o
            
            INNER JOIN tb_ordenes_flota ofl 
                ON ofl.cod_orden = o.cod_orden 
                AND ofl.cod_flota = $cod_flota 
            
            INNER JOIN tb_sucursales s 
                ON s.cod_sucursal = o.cod_sucursal 
                AND s.cod_empresa = $cod_empresa 
                $officeAnd
            
            LEFT JOIN tb_orden_pagos op 
                ON op.cod_orden = o.cod_orden
            
            LEFT JOIN tb_formas_pago fp
                ON fp.cod_forma_pago = op.forma_pago
            
            WHERE 
                o.is_envio = 1
                AND o.estado IN ('ENTREGADA', 'ENVIANDO', 'ASIGNADA')
                AND o.fecha >= '$fechaInicio 00:00:00'
                AND o.fecha <= '$fechaFin 23:59:00'
            
            GROUP BY  
                o.cod_orden, 
                o.fecha, 
                o.envio, 
                o.total, 
                o.estado, 
                s.nombre, 
                o.distancia;";
       $row = Conexion::buscarVariosRegistro($query);
        return $row;
	}	
	
	public function resumenPagosFlota($cod_sucursal, $cod_flota, $fechaInicio, $fechaFin)
    {
        $cod_empresa = $this->cod_empresa;
        $officeAnd = "";
    
        if ($cod_sucursal > 0) {
            $officeAnd = " AND s.cod_sucursal = $cod_sucursal ";
        }
    
        $query = "
    
            SELECT 
                final.metodo_pago,
                SUM(final.envio) AS total
            FROM (
    
                SELECT 
                    o.cod_orden,
                    o.envio,
    
                    -- Método final, eliminando P si hay otro método
                    CASE 
                        WHEN SUM(op.forma_pago = 'P') > 0 
                             AND COUNT(op.forma_pago) > SUM(op.forma_pago = 'P')
                        THEN 
                            -- Tiene P + otro → devolver el otro método
                            MAX(CASE WHEN op.forma_pago <> 'P' THEN fp.descripcion END)
    
                        WHEN SUM(op.forma_pago = 'P') > 0 
                             AND COUNT(op.forma_pago) = SUM(op.forma_pago = 'P')
                        THEN 
                            -- Solo P → NO CUENTA PARA FLOTAS
                            NULL
    
                        ELSE 
                            -- No hay P → método normal
                            MAX(fp.descripcion)
                    END AS metodo_pago
    
                FROM tb_orden_cabecera o
    
                INNER JOIN tb_ordenes_flota ofl 
                    ON ofl.cod_orden = o.cod_orden
                    AND ofl.cod_flota = $cod_flota
    
                INNER JOIN tb_sucursales s 
                    ON s.cod_sucursal = o.cod_sucursal
                    AND s.cod_empresa = $cod_empresa
                    $officeAnd
    
                LEFT JOIN tb_orden_pagos op 
                    ON op.cod_orden = o.cod_orden
    
                LEFT JOIN tb_formas_pago fp
                    ON fp.cod_forma_pago = op.forma_pago
    
                WHERE 
                    o.is_envio = 1
                    AND o.estado IN ('ENTREGADA', 'ENVIANDO', 'ASIGNADA')
                    AND o.fecha >= '$fechaInicio 00:00:00'
                    AND o.fecha <= '$fechaFin 23:59:00'
    
                GROUP BY o.cod_orden
    
            ) AS final
    
            WHERE final.metodo_pago IS NOT NULL   -- Descarta órdenes solo P
    
            GROUP BY final.metodo_pago
    
            ORDER BY final.metodo_pago ASC;
        ";
    
        return Conexion::buscarVariosRegistro($query);
    }
    	
        
}
?>