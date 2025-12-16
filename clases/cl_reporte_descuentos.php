<?php

class cl_reporte_descuentos
{
		var $session;
		var $cod_orden, $cod_empresa, $cod_usuario, $cod_sucursal, $fecha, $pago, $estado;
		//DATOS NUMERICOS
		var $subtotal, $descuento, $envio, $iva, $giftcard, $total, $cod_descuento;
		//DATOS DE ENVIO
		var $is_envio, $tiempo, $cod_sector, $calle, $manzana_villa, $latitud, $longitud, $referencia;
		//DETALLE
		var $productos;
		
		public function __construct($pcod_reporte=null)
		{
			if($pcod_reporte != null)
				$this->cod_reporte= $pcod_reporte;
			$this->session = getSession();
		}

		
		//*********************************

		public function ordenes_fecha($cod_empresa, $sucursal, $f_inicio, $f_fin){
			
			$conca="";
			if($sucursal!=0){$conca=" AND ca.cod_sucursal=$sucursal";}
			
			$query = "SELECT 
			        p.nombre,
                    od.cod_producto, 
                    od.descuento_porcentaje, 
                    od.desc_text,     
                    COUNT(*) AS total_ordenes,
                    SUM(od.precio * od.cantidad) AS total_precio
                FROM 
                    tb_orden_cabecera ca
                INNER JOIN 
                    tb_orden_detalle od 
                    ON od.cod_orden = ca.cod_orden  
                INNER JOIN 
                	tb_productos p
                    ON p.cod_producto = od.cod_producto                    
                WHERE 
                    ca.fecha >= '$f_inicio 00:00:00'
                    AND ca.fecha <= '$f_fin 23:59:00'
                    $conca
                    AND od.descuento_porcentaje > 0
                    AND ca.cod_empresa =".$this->session['cod_empresa']." 
                GROUP BY 
                    od.cod_producto, od.descuento_porcentaje
                ORDER BY 
                    total_precio DESC;    
                    ";
                    

            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		
		public function ordenes_fecha2($cod_empresa, $sucursal, $f_inicio, $f_fin){
			
			$conca="";
			if($sucursal!=0){$conca=" AND ca.cod_sucursal=$sucursal";}
			
			$query = "SELECT 
			        ca.cod_descuento,
                    od.descuento_porcentaje, 
                    od.desc_text,     
                    COUNT(*) AS total_ordenes,
                    SUM(od.precio * od.cantidad) AS total_precio
                FROM 
                    tb_orden_cabecera ca
                INNER JOIN 
                    tb_orden_detalle od 
                    ON od.cod_orden = ca.cod_orden  
                WHERE 
                    ca.fecha >= '$f_inicio 00:00:00'
                    AND ca.fecha <= '$f_fin 23:59:00'
                    $conca
                    AND ca.cod_descuento <> ''
                    AND ca.cod_empresa =".$this->session['cod_empresa']." 
                GROUP BY 
                    ca.cod_descuento
                ORDER BY 
                    total_precio DESC;    
                    ";
                    
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function ordenes_diarias($cod_sucursal, $f_inicio){
		    $conca="";
			if($cod_sucursal!=0){$conca=" AND ca.cod_sucursal=$cod_sucursal";}
			$query = "SELECT ca.cod_orden, ca.fecha, ca.total, u.nombre, u.apellido, u.num_documento
                        FROM tb_orden_cabecera ca, tb_usuarios u
                        WHERE ca.cod_usuario = u.cod_usuario 
                        AND ca.fecha BETWEEN '$f_inicio 00:00:00' AND '$f_inicio 23:59:59'
                        $conca
                        AND ca.estado = 'ENTREGADA'
                        AND ca.cod_empresa =".$this->session['cod_empresa']." 
                        ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function total_ordenes_diarias($cod_sucursal, $f_inicio){
		    $conca="";
			if($cod_sucursal!=0){$conca=" AND ca.cod_sucursal=$cod_sucursal";}
		    $query = "SELECT SUM(ca.subtotal) as subtotal, SUM(ca.iva) as iva, SUM(ca.envio) as envio, SUM(ca.total) as total, COUNT(*) as cant_ordenes, SUM(ca.is_envio) as delivery
                        FROM tb_orden_cabecera ca
                        WHERE ca.fecha BETWEEN '$f_inicio 00:00:00' AND '$f_inicio 23:59:59'
                        AND ca.cod_empresa =".$this->session['cod_empresa']." 
                        $conca
                        AND ca.estado = 'ENTREGADA'
                        ORDER BY ca.cod_orden DESC";
            $row = Conexion::buscarRegistro($query);
            return $row;
		}
        
        public function total_ordenes_diarias_tipopago($cod_sucursal, $f_inicio){
            $conca="";
			if($cod_sucursal!=0){$conca=" AND ca.cod_sucursal=$cod_sucursal";}
		    $query = "SELECT op.forma_pago, SUM(op.monto) as monto
                        FROM tb_orden_cabecera ca, tb_orden_pagos op
                        WHERE ca.cod_orden = op.cod_orden
                        AND ca.fecha BETWEEN '$f_inicio 00:00:00' AND '$f_inicio 23:59:59'
                        AND ca.cod_empresa =".$this->session['cod_empresa']." 
                        $conca
                        AND ca.estado = 'ENTREGADA'
                        GROUP BY op.forma_pago
                        ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
        
		public function datos_grafico($cod_empresa, $x, $sucursal, $anio, $origenes){
			$conca="";
			if($sucursal!=0){
			    $conca=" AND oc.cod_sucursal=$sucursal";
		    }
			$query = "SELECT SUM(oc.total) as monto, DATE_FORMAT(oc.fecha, '%m') as mes, DATE_FORMAT(oc.fecha, '%Y') as year
                    FROM tb_orden_cabecera oc
                    WHERE DATE_FORMAT(oc.fecha, '%m') = ($x)
                    AND DATE_FORMAT(oc.fecha, '%Y') >= '$anio'
                    AND DATE_FORMAT(oc.fecha, '%Y') <='$anio'
                    AND oc.medio_compra IN ($origenes)
                    $conca
                    AND oc.cod_empresa = $cod_empresa
                    AND (oc.estado <> 'ANULADA' OR oc.estado <> 'CANCELADA')";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function total_formasPago($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes){
			
			$conca="";
			if($sucursal!=0){$conca=" AND ca.cod_sucursal=$sucursal";}
			$query = "SELECT op.forma_pago, SUM(op.monto) as total
                    FROM tb_orden_cabecera ca, tb_orden_pagos op
                    WHERE ca.cod_orden = op.cod_orden
                    AND ca.fecha >='$f_inicio 00:00:00'
                    AND ca.fecha <='$f_fin 23:59:00'
                    AND ca.medio_compra IN ($origenes)
                     $conca
                    AND ca.cod_empresa = $cod_empresa
                    AND ca.estado = 'ENTREGADA'
                    GROUP BY op.forma_pago
                    ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function medio_origen($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes){
			$conca="";
			if($sucursal!=0){$conca=" AND ca.cod_sucursal=$sucursal";}
			$query = "SELECT ca.medio_compra,SUM(ca.total) as total
                    FROM tb_orden_cabecera ca
                    WHERE ca.fecha >='$f_inicio 00:00:00'
                    AND ca.fecha <='$f_fin 23:59:00'
                    AND ca.medio_compra IN ($origenes)
                     $conca
                    AND ca.cod_empresa = $cod_empresa
                    AND ca.estado = 'ENTREGADA'
                    GROUP BY ca.medio_compra
                    ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function lista_productos_ingresos($cod_empresa, $sucursal, $f_inicio, $f_fin, $origenes){
			$conca="";
			if($sucursal!=0){$conca=" AND c.cod_sucursal=$sucursal";}
			$query = "SELECT SUM(d.precio_final) as dinero,count(p.cod_producto) as total, p.*
						FROM tb_orden_cabecera c, tb_orden_detalle d, tb_productos p
						WHERE c.fecha >='$f_inicio 00:00:00'
                        AND c.fecha <='$f_fin 23:59:00'
                        $conca
						AND c.cod_orden = d.cod_orden 
						AND d.cod_producto = p.cod_producto
						AND c.estado = 'ENTREGADA'
                        AND c.medio_compra IN ($origenes)
						AND p.cod_empresa = $cod_empresa
						GROUP BY p.cod_producto
						ORDER BY count(p.cod_producto) DESC";
			$resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

        public function getOrigenes($cod_empresa){
            $query = "SELECT DISTINCT medio_compra
                        FROM tb_orden_cabecera WHERE
                        cod_empresa = $cod_empresa";
            return Conexion::buscarVariosRegistro($query);
        }

        public function getTipoPagos(){
            $query = "SELECT * FROM tb_formas_pago";
            return Conexion::buscarVariosRegistro($query);
        }

        public function getOrdenTipoPagos($cod_orden) {
            $query = "SELECT fp.descripcion
                        FROM tb_orden_pagos op, tb_formas_pago fp
                        WHERE op.forma_pago = fp.cod_forma_pago
                        AND op.cod_orden = $cod_orden";
            return Conexion::buscarVariosRegistro($query);
        }
}
?>