<?php

class cl_ordenes
{
		var $session;
		var $cod_orden, $cod_empresa, $cod_usuario, $cod_sucursal, $fecha, $pago, $estado;
		//DATOS NUMERICOS
		var $subtotal, $descuento, $envio, $iva, $giftcard, $total, $cod_descuento;
		//DATOS DE ENVIO
		var $is_envio, $tiempo, $cod_sector, $calle, $manzana_villa, $latitud, $longitud, $referencia;
		//DETALLE
		var $productos;
		
		public function __construct($pcod_producto=null)
		{
			if($pcod_producto != null)
				$this->cod_producto = $pcod_producto;
			$this->session = getSession();
		}

		public function lista(){
		    if($this->session['cod_rol']==3)
		    {
		        $where = "AND ca.cod_sucursal =".$this->session['cod_sucursal'];
		    }
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen,s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u,tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND s.cod_sucursal = ca.cod_sucursal
						AND ca.cod_empresa = ".$this->session['cod_empresa']." $where ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaNoPOS(){
		    if($this->session['cod_rol']==3)
		    {
		        $where = "AND ca.cod_sucursal =".$this->session['cod_sucursal'];
		    }
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen,s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u,tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND s.cod_sucursal = ca.cod_sucursal
						AND ca.estado NOT IN('CREADA')
						AND ca.cod_empresa = ".$this->session['cod_empresa']." $where ORDER BY ca.cod_orden DESC LIMIT 0,1500";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaPOS(){
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen,s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u,tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND s.cod_sucursal = ca.cod_sucursal
						AND ca.estado IN('CREADA', 'ANULADA')
						AND ca.cod_empresa = ".$this->session['cod_empresa']." $where ORDER BY ca.cod_orden DESC LIMIT 0,10000";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaByCupon($cupon){
			$query = "SELECT ca.cod_orden, ca.fecha, ca.envio, ca.iva, ca.total, u.nombre, u.apellido, u.correo, u.telefono, u.imagen,s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u,tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND s.cod_sucursal = ca.cod_sucursal
						AND ca.cod_empresa = ".$this->session['cod_empresa']." AND ca.cod_descuento = '$cupon' ORDER BY ca.cod_orden DESC";
            return Conexion::buscarVariosRegistro($query);
		}
		
		public function lista_enviado_asignado(){
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen
						FROM tb_orden_cabecera ca, tb_usuarios u
						WHERE ca.cod_usuario = u.cod_usuario 
						AND ca.estado IN ('ENVIANDO','ASIGNADA')
						AND ca.cod_empresa = ".$this->session['cod_empresa']." ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaLimit(){
		    if($this->session['cod_rol']==3)
		    {
		        $where = "AND ca.cod_sucursal =".$this->session['cod_sucursal'];
		    }
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen,s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u,tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND s.cod_sucursal = ca.cod_sucursal
						AND ca.cod_empresa = ".$this->session['cod_empresa']." $where ORDER BY ca.cod_orden DESC LIMIT 0,10";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaByUsuario($cod_usuario){
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen, s.nombre as sucursal
						FROM tb_orden_cabecera ca, tb_usuarios u, tb_sucursales s
						WHERE ca.cod_usuario = u.cod_usuario 
						AND ca.cod_sucursal = s.cod_sucursal
						AND ca.cod_usuario = $cod_usuario
						AND ca.cod_empresa = ".$this->session['cod_empresa']." ORDER BY ca.cod_orden DESC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lista_gestion($estado, $envio, $cod_sucursal){
			$filter = "";
			if($cod_sucursal>0)
				$filter = " AND ca.cod_sucursal = $cod_sucursal";

			$order = " ORDER BY ca.cod_orden DESC LIMIT 0,150";
			if($envio == 0){
			    /*--NUEVO--*/
				$order = " and (DATE_FORMAT(ca.hora_retiro,'%Y %m %d')=DATE_FORMAT(now(),'%Y %m %d')) ORDER BY TIME_FORMAT(ca.hora_retiro, '%H:%i') ASC";
				/*--NUEVO--*/
			}
			$todas = "AND ca.is_envio = $envio";
			if($envio == 2){
				$todas = "";
				$order = " ORDER BY ca.cod_orden DESC LIMIT 0, 150";
			}
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen
						FROM tb_orden_cabecera ca, tb_usuarios u
						WHERE ca.cod_usuario = u.cod_usuario 
						AND ca.estado = '$estado' 
						$todas
						$filter
						AND ca.cod_empresa = ".$this->session['cod_empresa'].$order;
			//echo $query;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function lista_gestion_new($estado, $envio, $cod_sucursal){
		    $date = $this->fecha;
			$filter = "";
			if($cod_sucursal>0)
				$filter = " AND ca.cod_sucursal = $cod_sucursal";

			$order = " AND ca.fecha BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59'  ORDER BY ca.cod_orden DESC LIMIT 0,150";
			if($envio == 0){
			    /*--NUEVO--*/
				$order = " and (DATE_FORMAT(ca.hora_retiro,'%Y %m %d')=DATE_FORMAT('".$date."','%Y %m %d')) ORDER BY TIME_FORMAT(ca.hora_retiro, '%H:%i') ASC";
				/*--NUEVO--*/
			}
			$query = "SELECT ca.*, u.nombre, u.apellido, u.correo, u.telefono, u.imagen
						FROM tb_orden_cabecera ca, tb_usuarios u
						WHERE ca.cod_usuario = u.cod_usuario 
						AND ca.estado = '$estado' 
						AND ca.is_envio = $envio
						$filter
						AND ca.cod_empresa = ".$this->session['cod_empresa'].$order;
            $resp = Conexion::buscarVariosRegistro($query);
            //echo $query;
            return $resp;
		}
		
		public function listaProgramados($start, $end){
		    $query = "SELECT c.cod_orden, c.total, c.hora_retiro, c.is_envio, c.referencia, u.nombre, u.apellido
                    FROM  tb_orden_cabecera c, tb_usuarios u
                    WHERE c.cod_usuario = u.cod_usuario 
                    AND c.is_programado = 1
                    AND c.hora_retiro >=  '$start'
                    AND c.hora_retiro <=  '$end'
                    AND c.estado NOT IN('ANULADA', 'CANCELADA')
                    AND c.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function get_orden($cod_orden)
		{
			$query = "SELECT * from tb_orden_cabecera where cod_orden = ".$cod_orden;
			$row = Conexion::buscarRegistro($query);
			if(count($row)>0)
			{
				$this->cod_orden = $row['cod_orden'];
				$this->cod_empresa = $row['cod_empresa'];
				$this->cod_usuario = $row['cod_usuario'];
				$this->nombre = $row['nombre'];
				$this->apellido = $row['apellido'];
				$this->imagen = $row['imagen'];
				$this->correo = $row['correo'];
				$this->usuario = $row['usuario'];
				$this->password= $row['password'];
				$this->estado = $row['estado'];
				return true;
			}
			else
			{
				return false;
			}
		}

		public function get_orden_array($cod_orden){
			$query = "SELECT oc.*, u.nombre, u.apellido, u.correo, u.telefono as telefono_user, u.num_documento
						FROM tb_orden_cabecera oc, tb_usuarios u
						WHERE oc.cod_usuario = u.cod_usuario
						AND oc.cod_orden = $cod_orden
						AND oc.cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarRegistro($query);
            if($resp){
                $entrega = "Pickup";
                if($resp['is_envio'] == 1){
                    $entrega = "Delivery";
                }
                $resp['entrega'] = $entrega;
                
            	$query = "SELECT o.*, p.nombre, p.peso, p.cobra_iva
							FROM tb_orden_detalle o, tb_productos p
							WHERE o.cod_producto = p.cod_producto
							AND o.cod_orden = $cod_orden";
				$resp['detalle'] = Conexion::buscarVariosRegistro($query, NULL);

				$query = "SELECT p.forma_pago, p.monto, f.descripcion, p.observacion
							FROM tb_orden_pagos p, tb_formas_pago f
							WHERE p.forma_pago = f.cod_forma_pago
							AND p.cod_orden = $cod_orden";
				$resp['pagos'] = Conexion::buscarVariosRegistro($query, NULL);		
				
				$query = "SELECT *
					FROM tb_orden_datos_facturacion 
					WHERE cod_orden = $cod_orden";
		        $resp['datos_facturacion'] = Conexion::buscarRegistro($query);
				return $resp;
            }else
            	return false;
            return $resp;
		}

		public function get_forma_pago($cod_orden){
			$query = "SELECT p.forma_pago, p.monto, f.descripcion
							FROM tb_orden_pagos p, tb_formas_pago f
							WHERE p.forma_pago = f.cod_forma_pago
							AND p.cod_orden = $cod_orden";
			return Conexion::buscarVariosRegistro($query, NULL);	
		}

		public function get_detalle_orden($cod_orden){
			$query = "SELECT o.*, p.nombre, p.image_min, p.sku 
							FROM tb_orden_detalle o, tb_productos p
							WHERE o.cod_producto = p.cod_producto
							AND o.cod_orden = $cod_orden";
			return Conexion::buscarVariosRegistro($query, NULL);				
		}

		public function aliasDisponible($alias){
			$query = "SELECT * FROM tb_productos WHERE alias = '$alias' AND estado IN ('A','I')";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}

		public function crear(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_productos(cod_producto_padre, cod_empresa, alias, nombre, desc_corta, desc_larga, image_min, image_max, fecha_create, user_create, estado, precio, codigo) ";
        	$query.= "VALUES($this->cod_producto_padre, $empresa, '$this->alias', '$this->nombre', '$this->desc_corta', '$this->desc_larga', '$this->image_min', '$this->image_max', NOW(), $usuario, 'A', $this->precio, '$this->codigo')";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function lista_productos_ingresos(){
			$empresa = $this->session['cod_empresa'];
			$query = "SELECT SUM(d.precio_final) as dinero, SUM(d.cantidad) as producto_cantidad, p.*
						FROM tb_orden_cabecera c, tb_orden_detalle d, tb_productos p
						WHERE c.cod_orden = d.cod_orden 
						AND d.cod_producto = p.cod_producto
						AND c.estado NOT IN ('I')
						AND p.cod_empresa = $empresa
						GROUP BY p.cod_producto
						ORDER BY SUM(d.precio_final) DESC
						LIMIT 0,10";
			$resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function ordenesCercas($cod_orden, $latitud, $longitud, $distancia){
			$cod_empresa = $this->session['cod_empresa'];
			$fecha = fecha();
			$query = "SELECT
						  m.fecha_salida, oc.latitud, oc.longitud, oc.referencia, u.nombre, u.apellido, (
						    3959 * acos (
						      cos ( radians($latitud) )
						      * cos( radians( oc.latitud ) )
						      * cos( radians( oc.longitud ) - radians($longitud) )
						      + sin ( radians($latitud) )
						      * sin( radians( oc.latitud ) )
						    )
						  ) AS distance
						FROM tb_motorizado_asignacion m, tb_orden_cabecera oc, tb_usuarios u
						WHERE m.cod_orden = oc.cod_orden
						AND m.cod_motorizado = u.cod_usuario
						AND m.cod_orden NOT IN($cod_orden)
						AND m.fecha_salida >= '$fecha'
						AND u.cod_empresa = $cod_empresa 
						HAVING distance < $distancia
						ORDER BY distance
						LIMIT 0 , 10";
			$data = Conexion::buscarVariosRegistro($query);
			return $data;
		}

		public function asignarMotorizado($cod_orden, $cod_motorizado, $hora){
			$query = "INSERT INTO tb_motorizado_asignacion (cod_orden, cod_motorizado, fecha_asignacion) 
					VALUES($cod_orden, $cod_motorizado, '$hora')";
			if(Conexion::ejecutar($query,NULL)){
				$query = "UPDATE tb_orden_cabecera SET estado = 'ASIGNADA' WHERE cod_orden = $cod_orden";
				Conexion::ejecutar($query,NULL);
				/*--NUEVO--*/
				$this->orderHistorial($cod_orden, 'ASIGNADA',$hora); 
				/*--NUEVO--*/
        		return true;
        	}else{
        		return false;
        	}
		}
		
		/*--NUEVO--*/
		public function asignarMotorizadoGacela($cod_orden, $nombre,$apellido,$num_documento,$placa,$foto,$telefono){
		    $query = "DELETE FROM tb_orden_motorizado WHERE cod_orden = $cod_orden";
        	if(Conexion::ejecutar($query,NULL)){
        		$query = "INSERT INTO tb_orden_motorizado (cod_orden, nombre,apellido,num_documento,placa,foto,telefono,proceso) 
					VALUES('$cod_orden', '$nombre','$apellido','$num_documento','$placa','$foto','$telefono','En camino al local')";
    			if(Conexion::ejecutar($query,NULL)){
        				$query = "UPDATE tb_orden_cabecera SET estado = 'ASIGNADA' WHERE cod_orden = $cod_orden";
        				Conexion::ejecutar($query,NULL);
                		return true;
            	}else{
            		return false;
            	}
        	}else{
        		return false;
        	}
		}
		
		//Nuevo Solo actualizar info motorizado
		public function reGuardarMotorizadoGacela($cod_orden, $nombre,$apellido,$num_documento,$placa,$foto,$telefono){
		    $query = "SELECT * FROM tb_orden_motorizado WHERE cod_orden = $cod_orden";
        	if(!Conexion::buscarRegistro($query)){
        		$query = "INSERT INTO tb_orden_motorizado (cod_orden, nombre,apellido,num_documento,placa,foto,telefono,proceso) 
					VALUES('$cod_orden', '$nombre','$apellido','$num_documento','$placa','$foto','$telefono','En camino al local')";
    			return Conexion::ejecutar($query,NULL);
        	}else{
        		return false;
        	}
		}
		
		public function updateProcesoMotorizado($cod_orden, $proceso){
		    $query = "UPDATE tb_orden_motorizado SET proceso='$proceso' WHERE cod_orden = $cod_orden";
		    return Conexion::ejecutar($query,NULL);
		}

		public function setCourierCanceled($cod_orden, $cod_courier, $orden_token, $motivo){
			$fecha = fecha();
		    $query = "INSERT INTO tb_orden_courier_canceled (cod_orden,cod_courier,orden_token,motivo,fecha)
		        values ($cod_orden,$cod_courier,'$orden_token','$motivo','$fecha')";
        	return Conexion::ejecutar($query,NULL);
		}
		
		public function orderHistorial($cod_orden, $estado,$fecha){
		    $query = "INSERT INTO tb_orden_historial (cod_orden,estado,fecha)
		        values ('$cod_orden','$estado','$fecha')";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function order_updgacela($cod_orden, $token,$isgacela){
			$query = "UPDATE tb_orden_cabecera SET is_gacela=$isgacela,cod_courier = $isgacela,order_token = '$token' WHERE cod_orden = $cod_orden";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function order_updCourier($cod_orden, $token,$cod_courier){
			$query = "UPDATE tb_orden_cabecera SET cod_courier=$cod_courier,order_token = '$token' WHERE cod_orden = $cod_orden";
        	if(Conexion::ejecutar($query,NULL)){
        	    /*--NUEVO--*/
        	        $query = "UPDATE tb_orden_cabecera SET estado = 'ASIGNADA' WHERE cod_orden = $cod_orden";
				    Conexion::ejecutar($query,NULL);
				    $this->orderHistorial($cod_orden, 'ASIGNADA',fecha()); 
				/*--NUEVO--*/
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function getEmpresaByCodEmpresa($cod_empresa){
		    $query = "SELECT * FROM tb_empresas WHERE cod_empresa = $cod_empresa";
		    return Conexion::buscarRegistro($query);
		}
		
		public function get_orden_by_token($token){
			$query = "SELECT * from tb_orden_cabecera where order_token ='$token'";
			return Conexion::buscarRegistro($query);
		}
        /*--NUEVO--*/
		public function set_estado($cod_orden, $estado){
			$query = "UPDATE tb_orden_cabecera SET estado='$estado' WHERE cod_orden = $cod_orden";
        	if(Conexion::ejecutar($query,NULL)){
        	    /*--NUEVO--*/
        	    $date = date("Y-m-d H:i:s");
        	    $this->orderHistorial($cod_orden, $estado,$date);
        	    /*--NUEVO--*/
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function anularFactura($cod_orden, $comentario = ""){
		    $session = $this->session;
		    $user_create = $session['cod_usuario'];
		    
			$query = "UPDATE tb_cliente_dinero SET estado = 'D' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);

			$query = "UPDATE tb_clientes_puntos SET estado = 'D' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);

			$query = "UPDATE tb_orden_cabecera SET estado = 'ANULADA' WHERE cod_orden = $cod_orden";
			Conexion::ejecutar($query,NULL);
			
			$query = "INSERT INTO tb_orden_cancelacion(cod_orden,motivo,fecha_create,user_create) VALUES($cod_orden,'$comentario',NOW(),$user_create)";
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
			return true;
		}
		
		public function OrdenesMotorizado($codigo){
			$query ="SELECT oc.* , u.nombre, u.apellido, u.correo, u.telefono, u.imagen
                    FROM  tb_motorizado_asignacion ma,  tb_orden_cabecera oc,tb_usuarios u
                    WHERE ma.cod_orden=oc.cod_orden
                    AND oc.cod_usuario = u.cod_usuario 
                    AND oc.estado IN ('ENVIANDO','ASIGNADA')
                    AND ma.cod_motorizado=".$codigo;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function get_destino_orden($cod_orden){
			$query = "SELECT d.*,c.codigo from tb_orden_destino d,tb_ciudades c where c.cod_ciudad = d.cod_ciudad and d.cod_orden = $cod_orden";
			return Conexion::buscarRegistro($query, NULL);				
		}
		
		public function getOrdenesByNumOrden($busqueda, $cod_sucursal, $cod_empresa){
			$filtro = "";
			if($cod_sucursal > 0)
				$filtro = " AND oc.cod_sucurcal = $cod_sucursal";
		
			$query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
						FROM tb_orden_cabecera oc, tb_usuarios u
						WHERE oc.cod_usuario = u.cod_usuario
						AND oc.cod_orden LIKE '%$busqueda%'
						AND oc.cod_empresa = $cod_empresa.$filtro";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getOrdenesByCedula($busqueda, $cod_sucursal, $cod_empresa){
			$filtro = "";
			if($cod_sucursal > 0)
				$filtro = " AND oc.cod_sucurcal = $cod_sucursal";
			$query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
						FROM tb_orden_cabecera oc, tb_usuarios u
						WHERE oc.cod_usuario = u.cod_usuario
						AND u.num_documento LIKE '%$busqueda%'
						AND oc.cod_empresa = $cod_empresa.$filtro";
			return Conexion::buscarVariosRegistro($query);
		}
		
		public function getOrdenesByNombre($busqueda, $cod_sucursal, $cod_empresa){
			$filtro = "";
			if($cod_sucursal > 0)
				$filtro = " AND oc.cod_sucurcal = $cod_sucursal";
			$query = "SELECT  oc.cod_orden, oc.total, oc.fecha, oc.estado, CONCAT(u.nombre, ' ', u.apellido) as nom_cliente
						FROM tb_orden_cabecera oc, tb_usuarios u
						WHERE oc.cod_usuario = u.cod_usuario
						AND CONCAT(u.nombre, u.apellido) LIKE '%$busqueda%'
						AND oc.cod_empresa = $cod_empresa.$filtro";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getDetalleAnulada($id){
			$query = "SELECT * 
						FROM tb_orden_devolucion 
						WHERE id = '$id'";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getFacturaElectronica($cod_orden){
			$query = "SELECT f.*, s.nombre as facturero
						FROM tb_orden_factura_electronica f, tb_sistema_facturacion s
						WHERE f.cod_sistema_facturacion = s.cod_sistema_facturacion
						AND f.cod_orden = $cod_orden";
			return Conexion::buscarRegistro($query);
		}

		public function getMotivoAnulacion($cod_orden){
			$query = "SELECT *
						FROM tb_orden_cancelacion
						WHERE cod_orden = $cod_orden";
			return Conexion::buscarVariosRegistro($query);
		}

		public function GetRetailDestino($cod_orden){
            $query = "SELECT od.*, s.nombre as nom_sucursal, s.nombre as nom_ciudad_origen, c.nombre as nom_ciudad_destino
                        FROM tb_orden_destino od, tb_orden_cabecera oc, tb_sucursales s, tb_ciudades c
                        WHERE od.cod_orden = oc.cod_orden
                        AND oc.cod_sucursal = s.cod_sucursal
                        AND od.cod_ciudad = c.cod_ciudad
                        AND od.cod_orden = $cod_orden";
            return Conexion::buscarRegistro($query);
        }

		public function recordarOrdenPendiente($cod_empresa, $tiempo){
			$fecha_ini = fecha_only();
			$fecha_fin = sumarTiempo("-".$tiempo, "minute");
			$query = "SELECT * FROM tb_orden_cabecera
						WHERE cod_empresa = $cod_empresa
						AND fecha BETWEEN '$fecha_ini 00:00:00' AND '$fecha_fin'
						AND is_envio = 1
						AND estado = 'ENTRANTE'";
			//echo $query;
			return Conexion::buscarVariosRegistro($query);
		}

		public function getOrdenesRunfood($cod_empresa){
			$fecha = fecha_only();
			$query = "SELECT ord.*, oc.total, oc.fecha, oc.estado, u.nombre
						FROM tb_orden_runfood ord, tb_orden_cabecera oc, tb_usuarios u
						WHERE ord.cod_orden = oc.cod_orden
						AND oc.cod_usuario = u.cod_usuario
						AND oc.fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59'
						AND oc.cod_empresa = $cod_empresa
						ORDER BY oc.cod_orden DESC";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getFacturaByOrden($cod_orden) {
			$query = "SELECT * 
						FROM tb_orden_factura_electronica ofe, tb_contifico_empresa ce
						WHERE ofe.cod_contifico_empresa = ce.cod_contifico_empresa
						AND ofe.cod_orden = $cod_orden";
			return Conexion::buscarRegistro($query);
		}

		public function getInventarioByOrden($cod_orden) {
			$query = "SELECT * 
						FROM tb_orden_inventario
						WHERE cod_orden = $cod_orden";
			return Conexion::buscarVariosRegistro($query);
		}
		
		/*--------------FLOTA-----------*/
		
		public function getListOrdersFlota($comercios_, $estados_ ){
		   
		    $cod_empresa = $this->session['cod_empresa'];    
		    
		    $filtroComercios = "";
		    if (is_string($comercios_)) {
                $comercios_ = json_decode($comercios_, true); // convertir a array
                
                    if (!empty($cod_comercio)) {
                        $comercios = array_map('intval', $cod_comercio);
                        
                         $comercios = array_map('intval', $cod_comercio);
                        $listaComercios = implode(",", $comercios);
                        $filtroComercios = "AND e.cod_empresa IN ($listaComercios)";
                    }
            }
            
            $filtroEstados = "";
            if (!empty($estados_)) {
                // Si es string (ej: "\"Entregado\"")
                if (is_string($estados_)) {
                    $estados_ = json_decode($estados_, true); // puede dar string o array
                }
            
                // Si todavía es string, lo convertimos a array
                if (is_string($estados_)) {
                    $estado = [$estados_];
                } else {
                    $estado = $estados_; // ya es array
                }
            
                // escapamos cada valor como string SQL
                $estado = array_map(function($e) {
                    return "'" . addslashes($e) . "'";
                }, $estado);
            
                $filtroEstados = "AND oc.estado IN (" . implode(",", $estado) . ")";
            }


            $query = "SELECT 
                      oc.cod_orden, 
                      oc.total, 
                      oc.fecha, 
                      oc.estado, 
                      CONCAT(u.nombre, ' ', u.apellido) as nom_cliente,
                      s.nombre as sucursal,
                      e.nombre as comercio
                    FROM 
                      tb_orden_cabecera oc 
                      INNER JOIN tb_usuarios u ON oc.cod_usuario = u.cod_usuario 
                      AND oc.cod_courier = 101 
                      INNER JOIN tb_ordenes_flota of on of.cod_orden = oc.cod_orden 
                      INNER JOIN tb_sucursales s on s.cod_sucursal = oc.cod_sucursal
                      INNER JOIN tb_empresas e on e.cod_empresa = oc.cod_empresa $filtroComercios
                    WHERE 
                      of.cod_flota = $cod_empresa 
                      $filtroEstados" ;
			return Conexion::buscarVariosRegistro($query);	
		}
		
		
		public function asignarMotorizadoFlota($cod_orden, $cod_motorizado){
		    $query = "SELECT * FROM tb_orden_motorizado WHERE cod_orden = $cod_orden";
		    
        //	if(!Conexion::buscarRegistro($query)){
        	    
        	    $queryMotorizadoData = "select * from tb_usuarios where cod_usuario = $cod_motorizado";
                $dataMotorizado = Conexion::buscarRegistro($queryMotorizadoData);
                
               // return $queryMotorizadoData;
        	    
        	    $nombre = $dataMotorizado["nombre"];
        	    $apellido = $dataMotorizado["apellido"];
        	    $num_documento = $dataMotorizado["num_documento"];
        	    $placa = $dataMotorizado["placa"];
        	    $foto = $dataMotorizado["imagen"];
        	    $telefono = $dataMotorizado["telefono"];
        		$query = "INSERT INTO tb_orden_motorizado (cod_orden, nombre,apellido,num_documento,placa,foto,telefono) 
					VALUES('$cod_orden', '$nombre','$apellido','$num_documento','$placa','$foto','$telefono')";
    			return Conexion::ejecutar($query,NULL);
       /* 	}else{
        		return false;
        	}*/
		}
}
?>