<?php

class cl_clientes
{
		var $cod_cliente, $qr, $secuencia, $cod_nivel, $nombre, $cedula, $cod_para_referir;
		var $session;
		var $cod_usuario;
		public function __construct($pcedula=null)
		{
			$this->session = getSession();
			if($pcedula != null){
				$this->cedula = $pcedula;
				$this->get();
			}
		}

		public function lista(){
			$query = "SELECT * FROM tb_clientes WHERE estado IN('A','I') AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

	/*GET*/
		public function get(){
			$cod_empresa = $this->session['cod_empresa'];
			$query = "SELECT c.* 
						FROM tb_clientes c 
						WHERE c.num_documento = '$this->cedula'
						AND c.cod_empresa = $cod_empresa";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$this->cod_cliente = $row['cod_cliente'];
				$this->nombre = $row['nombre'];
				$this->cedula = $row['num_documento'];
				$this->cod_nivel = $row['cod_nivel'];
				//$this->cod_para_referir = $row['cod_para_referir'];
				$this->cod_para_referir = "UA12345";
			}
            return $row;
		}
		
		public function getById($id){
			$query = "SELECT * FROM tb_clientes c WHERE c.cod_cliente = $id";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$this->cod_cliente = $row['cod_cliente'];
				$this->nombre = $row['nombre'];
				$this->cedula = $row['num_documento'];
				$this->cod_nivel = $row['cod_nivel'];
				$this->cod_usuario = $row['cod_usuario'];
				//$this->cod_para_referir = $row['cod_para_referir'];
				$this->cod_para_referir = "UA12345";
			}
            return $row;
		}

		public function getDocumento($cedula){
			$query = "SELECT * FROM tb_clientes c WHERE c.num_documento = '$cedula'";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$this->cod_cliente = $row['cod_cliente'];
				$this->nombre = $row['nombre'];
				$this->cedula = $row['num_documento'];
				$this->cod_nivel = $row['cod_nivel'];
				//$this->cod_para_referir = $row['cod_para_referir'];
				$this->cod_para_referir = "UA12345";
			}
            return $row;
		}

		public function GetSaldo(){
			$query = "SELECT * FROM tb_clientes_saldos WHERE cod_cliente = $this->cod_cliente AND estado = 'A' AND fecha_caducidad >= NOW() LIMIT 0,1";
			$resp = Conexion::buscarRegistro($query);
			if($resp){
				return $resp['dinero'];
			}else{
				return 0;
			}
		}

		public function GetPuntos(){
			$query = "SELECT SUM(cp.puntos) as puntos 
                    FROM tb_clientes_puntos cp
                    WHERE cp.cod_cliente = $this->cod_cliente
                    AND cp.estado = 'A'
                    AND cp.fecha_caducidad > NOW()
                    GROUP BY cp.cod_cliente";
			$resp = Conexion::buscarRegistro($query);
			if($resp){
				return $resp['puntos'];
			}
			else{
				return 0;
			}
		}

		public function GetDinero(){
			$query = "SELECT SUM(cp.saldo) as saldo 
                    FROM tb_cliente_dinero cp
                    WHERE cp.cod_cliente = $this->cod_cliente
                    AND cp.estado = 'A'
                    AND cp.fecha_caducidad > NOW()
                    GROUP BY cp.cod_cliente";
			$resp = Conexion::buscarRegistro($query);
			if($resp){
				return $resp['saldo'];
			}
			else
			{
				return 0;
			}
		}
		
		public function getDataNivel(){
		    $query = "SELECT * FROM tb_niveles n WHERE n.cod_nivel = $this->cod_nivel";
		   	$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getDataNivelByPosicion(){
		    $query = "SELECT * FROM tb_niveles n WHERE n.posicion = ".$this->cod_nivel." AND n.cod_empresa = ".$this->session['cod_empresa'];
		   	$resp = Conexion::buscarRegistro($query);
			return $resp;
		}
		
		public function getNumReferidos(){
		    $query = "SELECT COUNT(cod_referido_por) as cant_ya_referidos
    					FROM tb_usuarios_cdv 
    					WHERE cod_referido_por = '$this->cod_para_referir'
    					AND estado_referido = 'A'";
		    $resp = Conexion::buscarRegistro($query);
			if($resp){
		        return $resp['cant_ya_referidos'];
		    }
		    return 0;
		}

		function getNivel($puntaje){
		    $query = "SELECT * FROM tb_niveles n WHERE $puntaje BETWEEN punto_inicial AND punto_final AND n.cod_empresa =".$this->session['cod_empresa'];
		    return Conexion::buscarRegistro($query);
		}

	/*ADICIONES*/
	
		public function AddDinero($monto, $cod_cliente, $tipo, $duracionMeses = 3, $cod_usuario = 0){
			$query = "INSERT INTO tb_cliente_dinero(cod_cliente, cod_tipo_pago, dinero, saldo, fecha, fecha_caducidad, estado, user_create)
					VALUES($cod_cliente, $tipo, $monto, $monto, NOW(), DATE_ADD(NOW(), INTERVAL $duracionMeses MONTH), 'A', $cod_usuario)";
			return Conexion::ejecutar($query,NULL);		
		}	


	/*ACTUALIZACIONES*/
		public function ActualizarSaldo($saldoAumentar){
		    $saldoActual = $this->GetSaldo();
		    $nuevoSaldo = $saldoAumentar + $saldoActual;
		    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $this->cod_cliente AND estado IN ('A','I')";
		    if(Conexion::ejecutar($query,NULL)){
		        $query = "INSERT INTO tb_clientes_saldos(cod_cliente,dinero,fecha_create,fecha_caducidad,estado) ";
		        $query.= "VALUES($this->cod_cliente,$nuevoSaldo,NOW(),DATE_ADD(NOW(), INTERVAL 2 WEEK),'A')";
		        return Conexion::ejecutar($query,NULL);
		    }else{
		        return false;
		    }
		}

		public function LimpiarSaldo(){
		    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $this->cod_cliente AND estado IN ('A','I')";
		    return Conexion::ejecutar($query,NULL);
		}


		public function ActualizarPuntos($puntosAumentar){
		    $puntoActual = $this->GetPuntos();
		    $nuevoPuntaje = $puntosAumentar + $puntoActual;
		    
		    $nivelActual = $this->getNivel($puntoActual);
		    $nivelNuevo = $this->getNivel($nuevoPuntaje);
		    
		    if($nivelActual['cod_nivel'] != $nivelNuevo['cod_nivel']){
		        $puntos_nuevo_nivel = $nuevoPuntaje - $nivelActual['punto_final'];
		        $puntos_actual_nivel = $puntosAumentar - $puntos_nuevo_nivel;
		        
		        $query = "UPDATE tb_clientes SET cod_nivel = ".$nivelNuevo['cod_nivel']." WHERE cod_cliente = $this->cod_cliente";
                $resp = Conexion::ejecutar($query,NULL);
                
		    }else{
		        $puntos_nuevo_nivel = 0;
		        $puntos_actual_nivel = $puntosAumentar;
		    }
		    
		    $dinero = 0;
		    if($puntos_actual_nivel > 0){
		        $dinero += $puntos_actual_nivel * $nivelActual['dinero_x_punto'];
		    }
		    if($puntos_nuevo_nivel > 0){
		        $dinero += $puntos_nuevo_nivel * $nivelNuevo['dinero_x_punto'];
		    }

		    $query = "INSERT INTO tb_clientes_puntos(cod_cliente,cod_nivel,puntos,dinero,fecha_create,fecha_caducidad,estado)";
		    $query.= "VALUES($this->cod_cliente,".$nivelNuevo['cod_nivel'].",$puntosAumentar,$dinero,NOW(),DATE_ADD(NOW(), INTERVAL 3 MONTH),'A')";
		    $resp = Conexion::ejecutar($query,NULL);
		    if($resp){
		        $query = "INSERT INTO tb_cliente_dinero(cod_cliente,cod_tipo_pago,dinero,saldo,fecha,fecha_caducidad,estado) ";
    		    $query.= "VALUES($this->cod_cliente,3,$dinero,$dinero,NOW(),DATE_ADD(NOW(), INTERVAL 3 MONTH),'A')";
    		    $resp = Conexion::ejecutar($query,NULL);
    		    if($resp){
    		        /*
    		        $cod_dinero = Conexion::lastId();
    		        $query = "INSERT INTO tb_sincronizacion_pos(secuencia,qr,cod_dinero,tipo,fecha) VALUES($this->secuencia,'$this->qr',$cod_dinero,'+',NOW())";
    		        $resp = Conexion::ejecutar($query,NULL);*/
    		        return true;
    		    }
    		    else
    		        return false;
		    }
		    else
		        return false;
		}

		public function DecrementarDinero($cedula, $monto){
			if($this->getDocumento($cedula)){
			    $dineroActual = $this->GetDinero();
			    if($dineroActual < $monto){
			        return false;
			    }
			    
			    //TRAE TODO SU DINERO DESGLOSADO
			    $query = "SELECT * FROM tb_cliente_dinero cd 
			    		WHERE cd.cod_cliente = $this->cod_cliente AND cd.estado = 'A' AND cd.saldo > 0 AND cd.fecha_caducidad > NOW() 
			    		ORDER BY cd.fecha_caducidad ASC";
			    $resp = Conexion::buscarVariosRegistro($query);
			    foreach ($resp as $row){
			        $saldo = $row['saldo'];
					if($monto > $saldo){
					    $nuevoSaldo = 0;
					    $estadoSaldo = "I";
			            $monto = $monto - $saldo;
			        }else{
			            $nuevoSaldo = $saldo - $monto;
			            $estadoSaldo = "A";
			            if($nuevoSaldo == 0)
			                $estadoSaldo = "I";
			            $monto = 0;
			        }
			        
			        $query = "UPDATE tb_cliente_dinero SET saldo=$nuevoSaldo, estado='$estadoSaldo' WHERE cod_cliente_dinero = ".$row['cod_cliente_dinero'];
			        $respSaldo = Conexion::ejecutar($query,NULL);
			        if(!$respSaldo)
			            return false;
			        
			        if($monto == 0)
			            return true;
			    }
			    return true;
			}
			else
				return false;    
		}


	//FUNCIONES MAS IMPORTANTES
	public function AumentarSaldos($divisor, $cedula, $monto){
	    $IsPuntos = true;
	    $IsSaldo = true;
	    
	    $puntos = intval($monto/$divisor);
	    $saldo = $monto-($divisor*$puntos);
	    
	    if($this->getDocumento($cedula)){
	        
	        /*ACTUALIZAR PUNTOS*/
	        $saldoActual = $this->GetSaldo();
	        $nuevoSaldo = $saldoActual + $saldo;
	        if($nuevoSaldo >= $divisor){
	            $puntosAdicional = intval($nuevoSaldo/$divisor);
	            $saldo = $nuevoSaldo-($divisor*$puntosAdicional);
	            $puntos += $puntosAdicional;
	            
	            $this->LimpiarSaldo();
	        }
	        
	        if($puntos>0){
	            if($this->ActualizarPuntos($puntos)){
	                $IsPuntos = true;
	            }else{
	                $IsPuntos = false;
	            }
	        }
	        /*ACTUALIZAR PUNTOS*/
	        
	        /*ACTUALIZAR SALDO*/
	        if($this->ActualizarSaldo($saldo)){
	            $nuevoSaldo = $this->GetSaldo();
	            $IsSaldo = true;
	        }else{
	            $nuevoSaldo = "Error";
	            $IsSaldo = false;
	        }
	        /*ACTUALIZAR SALDO*/
	        
	        $nuevoPuntos = $this->GetPuntos();
	        
	        if($IsPuntos && $IsSaldo){
	            $nuevoDinero = $this->GetDinero();
	            //$this->RollBack();
	            return true;
	        }else{
	            //$this->RollBack();
	            return false;
	        }
	    }
	    else
	    	return false;
	}

	/*ORDENES DEL CLIENTE*/
	public function ordenes_faltantes($cedula){
		$query = "SELECT op.*
					FROM tb_orden_puntos op, tb_orden_cabecera c, tb_usuarios u
					WHERE c.cod_orden = op.cod_orden
					AND c.cod_usuario = u.cod_usuario
					AND u.num_documento = '$cedula'
					AND op.estado = 0";
		$resp = Conexion::buscarVariosRegistro($query);
        return $resp;			
	}

	public function ordenes_forma_pago($cod_orden){
		$query = "SELECT * FROM tb_orden_pagos WHERE cod_orden = $cod_orden";
		$resp = Conexion::buscarVariosRegistro($query);
        return $resp;
	}

	public function orden_complete($cod_orden){
		$query = "UPDATE tb_orden_puntos SET estado = 1 WHERE cod_orden = $cod_orden";
		return Conexion::ejecutar($query,NULL);
	}

	public function getByCodUsuario($cod_usuario){
		$query = "SELECT *
					FROM tb_clientes
					WHERE cod_usuario = $cod_usuario";
		return Conexion::buscarRegistro($query);
	}

	public function creditoActivo($cod_usuario) {
		$estadoOrden = "'ENTREGADA', 'CREADA'";
		if($this->session["cod_empresa"] == 78) // 78=MAGA-STUDIO
			$estadoOrden = "'CREADA'";
		$fecha = fecha_only();

		$query = "SELECT oc.cod_orden, oc.fecha, s.nombre as sucursal, IF(oc.is_envio=1, 'DELIVERY', 'PICKUP') as tipo, oc.medio_compra, oc.estado, oc.total, IFNULL(n.nombre, '--') as nivel, IFNULL(cp.puntos, '--') as puntos, IFNULL(cp.dinero, '--') as dinero_ganado, IFNULL(cp.fecha_caducidad, '--') as fecha_caducidad, IFNULL(op.monto, '--') as dinero_utilizado, IF(cp.fecha_caducidad <= CURDATE(), 'EXPIRADO', 'VIGENTE') as dinero_status
				FROM tb_orden_cabecera oc
				LEFT JOIN tb_orden_pagos op
					ON oc.cod_orden = op.cod_orden 
					AND op.forma_pago = 'P'
				INNER JOIN tb_usuarios u
					ON oc.cod_usuario = u.cod_usuario
				INNER JOIN tb_clientes c
					ON u.cod_usuario = c.cod_usuario
				INNER JOIN tb_sucursales s
					ON oc.cod_sucursal = s.cod_sucursal
				LEFT JOIN tb_clientes_puntos cp
					ON oc.cod_orden = cp.cod_orden
					-- AND cp.fecha_caducidad > '$fecha'
					-- AND cp.estado = 'A'
				-- LEFT JOIN tb_cliente_dinero cd
					-- ON oc.cod_orden = cd.cod_orden
				LEFT JOIN tb_niveles n
					ON cp.cod_nivel = n.posicion
					AND oc.cod_empresa = n.cod_empresa
				WHERE oc.cod_usuario = $cod_usuario
				AND oc.estado IN($estadoOrden)
				-- AND oc.fecha > DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
				ORDER BY oc.cod_orden DESC";
		return Conexion::buscarVariosRegistro($query);
	}

	public function creditoCaducado($cod_usuario) {
		$fecha = fecha_only();

		$query = "SELECT cp.puntos, cp.dinero, cp.fecha_create, cp.fecha_caducidad, DATEDIFF(cp.fecha_caducidad, cp.fecha_create) as diferencia
				FROM tb_clientes c
				INNER JOIN tb_usuarios u
					ON c.cod_usuario = u.cod_usuario
				INNER JOIN tb_clientes_puntos cp
					ON c.cod_cliente = cp.cod_cliente
					AND cp.fecha_caducidad <= '$fecha'
				WHERE c.cod_usuario = $cod_usuario";
		return Conexion::buscarVariosRegistro($query);
	}
	
	public function dineroCaducado($cod_cliente) {
		$fecha = fecha_only();

		$query = "SELECT cp.saldo, cp.fecha, cp.fecha_caducidad, DATEDIFF(cp.fecha_caducidad, cp.fecha) as diferencia
				FROM tb_clientes c
				INNER JOIN tb_usuarios u
					ON c.cod_usuario = u.cod_usuario
				INNER JOIN tb_cliente_dinero cp
					ON c.cod_cliente = cp.cod_cliente
					AND cp.fecha_caducidad <= '$fecha'
				WHERE c.cod_usuario = $cod_cliente
				AND cp.cod_tipo_pago = 3
				ORDER BY fecha DESC";
		return Conexion::buscarVariosRegistro($query);
	}

	public function historicoPuntos($cod_cliente) {
		$query = "SELECT SUM(puntos) as puntos
					FROM tb_clientes_puntos
					WHERE cod_cliente = $cod_cliente";
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return $resp["puntos"];
	}
	
	public function historicoPuntosCaducados($cod_cliente) {
		$fecha = fecha_only();
		$query = "SELECT IFNULL(SUM(puntos), 0) as puntos
					FROM tb_clientes_puntos
					WHERE cod_cliente = $cod_cliente
					AND fecha_caducidad <= '$fecha'";
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return $resp["puntos"];
	}
	
	public function historicoDinero($cod_cliente) {
		$query = "SELECT SUM(dinero) as dinero
					FROM tb_cliente_dinero
					WHERE cod_cliente = $cod_cliente";
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return number_format($resp["dinero"], 2, ".", "");
	}
	
	public function historicoDineroCaducado($cod_cliente) {
		$fecha = fecha_only();
		$query = "SELECT SUM(saldo) as saldo
					FROM tb_cliente_dinero
					WHERE cod_cliente = $cod_cliente
					AND fecha_caducidad <= '$fecha'
					AND cod_tipo_pago = 3
					ORDER BY fecha DESC"; // DINERO POR PUNTOS CON ÓRDENES
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return number_format($resp["saldo"], 2, ".", "");
	}
	
	public function historicoOrdenesDinero($cod_usuario) {
		$estadoOrden = "'ENTREGADA', 'CREADA'";
		if($this->session["cod_empresa"] == 78) // 78=MAGA-STUDIO
			$estadoOrden = "'CREADA'";

		$query = "SELECT SUM(total) as total
					FROM tb_orden_cabecera
					WHERE cod_usuario = $cod_usuario
					AND estado IN($estadoOrden)";
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return number_format($resp["total"], 2, ".", "");
	}
	
	public function historicoCreditoUtilizado($cod_usuario) {
		$estadoOrden = "'ENTREGADA', 'CREADA'";
		if($this->session["cod_empresa"] == 78) // 78=MAGA-STUDIO
			$estadoOrden = "'CREADA'";

		$query = "SELECT SUM(op.monto) as monto
					FROM tb_orden_pagos op, tb_orden_cabecera oc
					WHERE op.cod_orden = oc.cod_orden
					AND op.forma_pago = 'P'
					AND oc.cod_usuario = $cod_usuario
					AND oc.estado IN($estadoOrden)";
		$resp = Conexion::buscarRegistro($query);
		if(!$resp)
			return 0;
		return number_format($resp["monto"], 2, ".", "");
	}

	public function creditoActivoMaga($cod_usuario) {
		$estadoOrden = "'ENTREGADA', 'CREADA'";
		if($this->session["cod_empresa"] == 78) // 78=MAGA-STUDIO
			$estadoOrden = "'CREADA'";
		$fecha = fecha_only();

		$query = "SELECT oc.cod_orden, oc.fecha, s.nombre as sucursal, oc.medio_compra, oc.estado, oc.total, 
						IF(oc.is_envio=1, 'DELIVERY', 'PICKUP') as tipo, 
						IFNULL(n.nombre, '--') as nivel, 
						--IFNULL(SUM(cp.puntos), '--') as puntos, 
						-- IFNULL(cp.dinero, '--') as dinero_ganado, 
						IFNULL(cp.fecha_caducidad, '--') as fecha_caducidad, 
						IF(cp.fecha_caducidad <= CURDATE(), 'EXPIRADO', 'VIGENTE') as dinero_status,
						SUM(cp.puntos) as puntos
				FROM tb_orden_cabecera oc
				INNER JOIN tb_usuarios u
					ON oc.cod_usuario = u.cod_usuario
				INNER JOIN tb_clientes c
					ON u.cod_usuario = c.cod_usuario
				INNER JOIN tb_sucursales s
					ON oc.cod_sucursal = s.cod_sucursal
				LEFT JOIN tb_clientes_puntos cp
					ON oc.cod_orden = cp.cod_orden
					-- AND cp.fecha_caducidad > '$fecha'
					-- AND cp.estado = 'A'
				-- LEFT JOIN tb_cliente_dinero cd
					-- ON oc.cod_orden = cd.cod_orden
				LEFT JOIN tb_niveles n
					ON cp.cod_nivel = n.posicion
					AND oc.cod_empresa = n.cod_empresa
				WHERE oc.cod_usuario = $cod_usuario
				AND oc.estado IN($estadoOrden)
				-- AND oc.fecha > DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
				GROUP BY oc.cod_orden
				ORDER BY oc.cod_orden DESC";
		return Conexion::buscarVariosRegistro($query);
	}

	public function getCreditoUsadoEnOrden($cod_orden) {
		$monto = "--";
		$query = "SELECT monto 
					FROM tb_orden_pagos
					WHERE cod_orden = $cod_orden
					AND forma_pago = 'P'";
		$resp = Conexion::buscarRegistro($query);
		if($resp)
			$monto = number_format($resp["monto"], 2);
		return $monto;
	}
	
	public function getDineroRegistrado($cod_orden) {
		$saldo = "--";
		$ganado = "--";
		$query = "SELECT SUM(saldo) as saldo, SUM(dinero) as dinero
					FROM tb_cliente_dinero
					WHERE cod_orden = $cod_orden
					GROUP BY cod_orden";
		$resp = Conexion::buscarRegistro($query);
		if($resp) {
			$ganado = $resp["dinero"];
			$saldo = $resp["saldo"];
		}
		$response["saldo"] = number_format($saldo, 2);
		$response["ganado"] = number_format($ganado, 2);
		return $response;
	}

	public function getOtherCredits($cod_usuario) {
		$query = "SELECT cd.*, 
						td.nombre AS tipo_pago,
						CONCAT(u.nombre, ' ', u.apellido) AS created_by,
					CASE 
						WHEN cd.estado = 'I' THEN 'UTILIZADO'
						WHEN cd.fecha_caducidad <= CURDATE() THEN 'EXPIRADO'
						ELSE 'VIGENTE'
						END as dinero_status
					FROM tb_clientes c
					INNER JOIN tb_cliente_dinero cd ON c.cod_cliente = cd.cod_cliente
					INNER JOIN tb_tipo_dinero td ON cd.cod_tipo_pago = td.cod_tipo_pago
					LEFT JOIN tb_usuarios u ON cd.user_create = u.cod_usuario AND cd.user_create > 0
					WHERE c.cod_usuario = $cod_usuario
					AND cd.cod_tipo_pago NOT IN(3)
					ORDER BY cd.fecha DESC";
		return Conexion::buscarVariosRegistro($query);
	}
}
?>