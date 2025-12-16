<?php

class cl_clientes
{
		var $cod_cliente, $qr, $secuencia, $cod_nivel, $nombre, $cedula, $cod_para_referir, $fecha_nac, $cod_orden;
		
		public function __construct($pcedula=null)
		{
			if($pcedula != null){
				$this->cedula = $pcedula;
				$this->get();
			}
		}

	/*GET*/
		public function create($cedula){
			$cod_empresa = cod_empresa;
			$query = "INSERT INTO tb_clientes(cod_empresa,cod_nivel,tipo_documento,num_documento,estado) 
					VALUES ($cod_empresa, 1, 1, '$cedula', 'A')"; 
		}

		public function get(){
			$query = "SELECT * FROM tb_clientes c WHERE c.num_documento = '$this->cedula' AND c.cod_empresa = ".cod_empresa;
			$row = Conexion::buscarRegistro($query);
			if($row){
				$this->cod_cliente = $row['cod_cliente'];
				$this->nombre = $row['nombre'];
				$this->cedula = $row['num_documento'];
				$this->cod_nivel = $row['cod_nivel'];
				$this->fecha_nac = $row['fecha_nac'];
				//$this->cod_para_referir = $row['cod_para_referir'];
				$this->cod_para_referir = "UA12345";
			}
            return $row;
		}

		public function getDocumento($cedula){
			$query = "SELECT * FROM tb_clientes c WHERE c.num_documento = '$cedula' AND c.cod_empresa = ".cod_empresa;
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
		    $query = "SELECT * FROM tb_niveles n WHERE n.cod_nivel = $this->cod_nivel AND n.cod_empresa = ".cod_empresa;
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
		    $query = "SELECT * FROM tb_niveles n WHERE $puntaje BETWEEN punto_inicial AND punto_final AND n.cod_empresa = ".cod_empresa;
		    return Conexion::buscarRegistro($query);
		}


	/*ACTUALIZACIONES*/
		public function ActualizarSaldo($saldoAumentar){
		    $saldoActual = $this->GetSaldo();
		    $nuevoSaldo = $saldoAumentar + $saldoActual;
		    $query = "DELETE FROM tb_clientes_saldos WHERE cod_cliente = $this->cod_cliente AND estado IN ('A','I')";
		    if(Conexion::ejecutar($query,NULL)){
		        $query = "INSERT INTO tb_clientes_saldos(cod_cliente,dinero,fecha_create,fecha_caducidad,estado,cod_orden) ";
		        $query.= "VALUES($this->cod_cliente,$nuevoSaldo,NOW(),DATE_ADD(NOW(), INTERVAL 2 WEEK),'A',$this->cod_orden)";
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

		    $query = "INSERT INTO tb_clientes_puntos(cod_cliente,cod_nivel,puntos,dinero,fecha_create,fecha_caducidad,estado,cod_orden)";
		    $query.= "VALUES($this->cod_cliente,".$nivelNuevo['cod_nivel'].",$puntosAumentar,$dinero,NOW(),DATE_ADD(NOW(), INTERVAL 3 MONTH),'A',$this->cod_orden)";
		    $resp = Conexion::ejecutar($query,NULL);
		    if($resp){
		        $query = "INSERT INTO tb_cliente_dinero(cod_cliente,cod_tipo_pago,dinero,saldo,fecha,fecha_caducidad,estado,cod_orden) ";
    		    $query.= "VALUES($this->cod_cliente,3,$dinero,$dinero,NOW(),DATE_ADD(NOW(), INTERVAL 3 MONTH),'A',$this->cod_orden)";
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
	public function AumentarSaldos($divisor, $cedula, $monto, $cod_orden){
	    $IsPuntos = true;
	    $IsSaldo = true;
	    $this->cod_orden = $cod_orden;
	    
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
}
?>