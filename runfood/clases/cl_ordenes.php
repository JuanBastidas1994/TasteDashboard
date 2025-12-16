<?php

class cl_ordenes
{
		var $session;
		var $cod_producto, $cod_producto_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $fecha_create, $user_create, $estado, $precio, $codigo;
		
		public function __construct($pcod_producto=null)
		{
			if($pcod_producto != null)
				$this->cod_producto = $pcod_producto;
			$this->session = getSession();
		}

		public function lista(){
			$query = "SELECT ca.*, u.nombre, u.apellido
						FROM tb_orden_cabecera ca, tb_usuarios u
						WHERE ca.cod_usuario = u.cod_usuario 
						AND ca.cod_usuario = ".$this->session['cod_usuario']."
						AND ca.cod_empresa = ".cod_empresa." ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function get($cod_orden){
			$query = "SELECT * FROM tb_orden_cabecera WHERE cod_orden = $cod_orden";
			return Conexion::buscarRegistro($query);
		}

		public function getRunfood($id){
			$query = "SELECT c.*, r.id FROM tb_orden_cabecera c, tb_orden_runfood r WHERE c.cod_orden = r.cod_orden AND r.id = '$id'";
			return Conexion::buscarRegistro($query);
		}

		public function get_orden_array($cod_orden){
			$query = "SELECT oc.cod_orden, oc.fecha, oc.subtotal, oc.descuento, oc.envio, oc.iva, oc.total, oc.estado, u.nombre, u.apellido, u.correo, u.telefono
						FROM tb_orden_cabecera oc, tb_usuarios u
						WHERE oc.cod_usuario = u.cod_usuario
						AND oc.cod_orden = $cod_orden";
            $resp = Conexion::buscarRegistro($query);
            if($resp){
            	$query = "SELECT o.*, p.nombre
							FROM tb_orden_detalle o, tb_productos p
							WHERE o.cod_producto = p.cod_producto
							AND o.cod_orden = $cod_orden";
				$resp['detalle'] = Conexion::buscarVariosRegistro($query, NULL);

				$query = "SELECT p.forma_pago, p.monto, f.descripcion
							FROM tb_orden_pagos p, tb_formas_pago f
							WHERE p.forma_pago = f.cod_forma_pago
							AND p.cod_orden = $cod_orden";
				$resp['pagos'] = Conexion::buscarVariosRegistro($query, NULL);	

				$resp['fidelizacion']['dinero'] = Conexion::buscarVariosRegistro("SELECT cd.dinero FROM tb_cliente_dinero cd WHERE cd.cod_orden = $cod_orden", NULL); 	

				$resp['fidelizacion']['puntos'] = Conexion::buscarVariosRegistro("SELECT puntos, cod_nivel FROM tb_clientes_puntos WHERE cod_orden = $cod_orden", NULL); 		
				return $resp;
            }else
            	return false;
            return $resp;
		}

		public function anularFactura($cod_orden){
			$query = "UPDATE tb_cliente_dinero SET estado = 'D' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);

			$query = "UPDATE tb_clientes_puntos SET estado = 'D' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);

			$query = "UPDATE tb_orden_cabecera SET estado = 'ANULADA' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);
			
			$query = "SELECT c.cod_cliente, p.monto
                    FROM tb_orden_cabecera o, tb_usuarios u, tb_clientes c, tb_orden_pagos p
                    WHERE o.cod_usuario = u.cod_usuario
                    AND c.cod_usuario = u.cod_usuario
                    AND o.cod_orden = p.cod_orden
                    AND p.forma_pago = 'P'
                    AND o.cod_orden = $cod_orden";
		    $dineroUsado = Conexion::buscarRegistro($query, NULL);
		    if($dineroUsado){
		        $cod_cliente = $dineroUsado['cod_cliente'];
		        $monto = $dineroUsado['monto'];
		        $query = "INSERT INTO tb_cliente_dinero(cod_cliente, cod_tipo_pago, dinero, saldo, fecha, fecha_caducidad, estado) 
					VALUES($cod_cliente, 3, $monto, $monto, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 'A')";
			    Conexion::ejecutar($query,NULL);	
		    }
		}

		public function crear($checkout, $cod_usuario, &$id){
			$cod_empresa = cod_empresa;
			$cod_sucursal = 0;
			$is_envio = 0;
			$subtotal = number_format($checkout['subtotal'],2);
			$descuento = number_format($checkout['descuento'],2);
			$iva = number_format($checkout['iva'],2);
			$envio = number_format($checkout['envio'],2);
			$total = number_format($checkout['total'],2);
			//$notas = $checkout['orderNotes'];
			$notas = '';
			$id_unico = $checkout['id'];

			//FORMA DE PAGO
			$MetodoPago = $checkout['metodoPago'][0];
			$is_suelto = 0;
			$monto_suelto = 0;
			$forma_pago = $MetodoPago['tipo'];
			if($forma_pago == "E"){
				$is_suelto = 0;
				$monto_suelto = 0;
			}

			$datos_facturacion = "";

			$query = "INSERT INTO tb_orden_cabecera(cod_empresa, cod_sucursal, cod_usuario, fecha, subtotal, descuento, envio, iva, total, is_envio, pago, referencia, estado, latitud, longitud, is_suelto, monto_suelto, datos_facturacion, medio_compra) ";
        	$query.= "VALUES($cod_empresa, $cod_sucursal, $cod_usuario, NOW(), $subtotal, $descuento, $envio, $iva, $total, $is_envio, '$forma_pago', '$notas', 'CREADA','', '',$is_suelto, $monto_suelto, '$datos_facturacion', 'RUNFOOD')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		
        		$queryPunto = "INSERT INTO tb_orden_puntos(cod_orden) VALUES($id)";
        		Conexion::ejecutar($queryPunto,NULL);

        		$queryPunto = "INSERT INTO tb_orden_runfood(cod_orden,id) VALUES($id, '$id_unico')";
        		Conexion::ejecutar($queryPunto,NULL);

        		/*GUARDAR FORMAS DE PAGO*/
        		$MetodoPago = $checkout['metodoPago'];
        		foreach ($MetodoPago as $pago) {
        			$tipo = $pago['tipo'];
        			$monto = $pago['monto'];
        			$queryPago = "INSERT INTO tb_orden_pagos(cod_orden, forma_pago, monto)
        						VALUES($id, '$tipo', $monto)";
        			Conexion::ejecutar($queryPago,NULL);	
        		}

        		return true;
        	}else{
        		return false;
        	}
		}

}
?>