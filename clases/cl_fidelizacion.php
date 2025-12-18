<?php
class cl_fidelizacion
{
        var $session;
        var $cod_noticia,$cod_empresa,$alias,$nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado; 
        /*$cod_categoria_padre, $cod_empresa, $alias, $nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado;*/
        var $cantDiasCaducidadPuntos, $cantDiasCaducidadDinero, $cantDiasCaducidadSaldo;
        
        public function __construct($pfidelizacion=null)
        {
            if($pfidelizacion != null)
                $this->cod_fidelizacion= $pfidelizacion;
                $this->session = getSession();
        }

        public function datos_fidelizacion($cod_empresa){
            $query = "SELECT * FROM tb_empresa_fidelizacion_puntos where cod_empresa=".$cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }

        public function niveles($cod_empresa){
            $query = "SELECT * FROM tb_niveles WHERE estado in ('A','I')AND cod_empresa=$cod_empresa ORDER BY posicion";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }
        
        public function modulosWeb($cod_empresa){
            $query = "SELECT * FROM tb_web_modulos_productos WHERE cod_empresa=".$cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }
        
         public function anuncWeb($cod_empresa){
            $query = "SELECT * FROM tb_anuncio_cabecera WHERE cod_empresa=".$cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
        }
        
        public function set_fidelizacion_puntos($cod_empresa, $divisor, $puntos){
            $query= "UPDATE tb_empresa_fidelizacion_puntos SET divisor_puntos=$divisor, monto_puntos=$puntos WHERE cod_empresa =". $cod_empresa;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }

        public function set_fidelizacion_cumple($monto, $dias, $restriccion, $cod_empresa){
            $query= "UPDATE tb_empresa_fidelizacion_puntos SET valor_regalo_cumple=$monto, dias_regalo_cumple=$dias, compra_minimo_regalo_cumple=$restriccion WHERE cod_empresa = $cod_empresa";
            return Conexion::ejecutar($query,NULL);
        }
        
        

        public function set_costo_envio($base_dinero, $base_km, $adicional_km,$codigo){
            $query= "UPDATE tb_empresa_costo_envio SET base_dinero=$base_dinero, base_km=$base_km , adicional_km=$adicional_km WHERE cod_empresa_costo_envio =".$codigo;
            //echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }

        public function set_niveles($codigo, $nombre, $inicio, $fin, $monto){
            $query= "UPDATE tb_niveles SET nombre='$nombre', punto_inicial=$inicio , punto_final=$fin , dinero_x_punto=$monto WHERE cod_nivel =".$codigo;
           // echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function insert_costo_envio($cod_empresa,$base_dinero, $base_km, $adicional_km,&$id){
			$query = "INSERT INTO tb_empresa_costo_envio(cod_empresa, base_dinero, base_km, adicional_km) ";
        	$query.= "VALUES($cod_empresa, $base_dinero, $base_km, $adicional_km)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        		//return $id;	
        	}else{
        		return false;
        	}
		}
		
		public function insert_fidelizacion($cod_empresa, $divisor_puntos, $monto_puntos,&$id){
			$query = "INSERT INTO tb_empresa_fidelizacion_puntos(cod_empresa, divisor_puntos, monto_puntos, valor_regalo_cumple) ";
        	$query.= "VALUES($cod_empresa, $divisor_puntos, $monto_puntos, 0)";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        		//return $id;	
        	}else{
        		return false;
        	}
		}
		
		public function insert_niveles($cod_empresa, $nombre, $inicio, $fin, $monto,$x){
		 
    			$query = "INSERT INTO tb_niveles(cod_empresa, nombre, punto_inicial,punto_final,dinero_x_punto,estado,posicion) ";
            	$query.= "VALUES($cod_empresa, '$nombre', $inicio,$fin,$monto,'A', $x)";
            	if(Conexion::ejecutar($query,NULL)){
            		$id = Conexion::lastId();
            		return true;
            		//return $id;	
            	}else{
            		return false;
            	}
        
		}
		
		public function delete_niveles($cod_empresa){
			$query = "DELETE FROM tb_niveles WHERE cod_empresa =".$cod_empresa;
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function notificaciones($cod_empresa,$tipo){
            $query = "SELECT * FROM  tb_empresa_notificaciones where aplicacion='$tipo' AND cod_empresa=".$cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }
        
        public function insert_notificacion($cod_empresa,$token, $topic,$tipo,&$id){
    			$query = "INSERT INTO  tb_empresa_notificaciones(cod_empresa, aplicacion,token,topic,estado) ";
            	$query.= "VALUES($cod_empresa, '$tipo', '$token','$topic','A')";
            	//echo $query;
            	if(Conexion::ejecutar($query,NULL)){
            		$id = Conexion::lastId();
            		return true;
            		//return $id;	
            	}else{
            		return false;
            	}
		}
		
		public function update_notificacion($token, $topic,$codigo){
            $query= "UPDATE tb_empresa_notificaciones SET token='$token', topic='$topic' WHERE cod_empresa_notificacion =".$codigo;
           // echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
         public function insert_modulo($nombre_modulo, $cod_empresa, $descripcion, &$id){
    			$query = "INSERT INTO  tb_web_modulos_productos(nombre, cod_empresa, descripcion) ";
            	$query.= "VALUES('$nombre_modulo', $cod_empresa, '$descripcion')";
            	//echo $query;
            	if(Conexion::ejecutar($query,NULL)){
            		$id = Conexion::lastId();
            		return true;
            	}else{
            		return false;
            	}
		}
		
		public function update_modulo($nombre_modulo, $descripcion, $codigo){
            $query= "UPDATE tb_web_modulos_productos SET nombre = '$nombre_modulo', descripcion = '$descripcion' WHERE cod_web_modulos_producto = $codigo";
           // echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function delete_modulo($codigo){
			$query = "DELETE FROM tb_web_modulos_productos WHERE cod_web_modulos_producto =".$codigo;
        	if(Conexion::ejecutar($query,NULL)){
        	    $queryDos = "DELETE FROM  tb_web_modulos_productos_detalle WHERE cod_web_modulos_producto = $codigo";
        	    if(Conexion::ejecutar($queryDos,NULL)){
                return true;
                }else{
                    return false;
                }
        	
        	}else{
        		return false;
        	}
		}
		
		public function insert_anuncio($nombre_anuncio, $cod_empresa, $descripcion,$width,$height, &$id){
    			$query = "INSERT INTO  tb_anuncio_cabecera(nombre, cod_empresa, descripcion,width,height) ";
            	$query.= "VALUES('$nombre_anuncio', $cod_empresa, '$descripcion',$width,$height)";
            	//echo $query;
            	if(Conexion::ejecutar($query,NULL)){
            		$id = Conexion::lastId();
            		return true;
            	}else{
            		return false;
            	}
		}
        
        public function update_anuncio($nombre_anuncio, $descripcion,$width,$height, $codigo){
            $query= "UPDATE tb_anuncio_cabecera SET nombre = '$nombre_anuncio', descripcion = '$descripcion', width=$width,height=$height WHERE cod_anuncio_cabecera = $codigo";
           // echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function delete_anuncio($codigo){
			$query = "DELETE FROM tb_anuncio_cabecera WHERE cod_anuncio_cabecera =".$codigo;
        	if(Conexion::ejecutar($query,NULL)){
        	    $queryDos = "DELETE FROM  tb_anuncio_cabecera WHERE cod_anuncio_cabecera = $codigo";
        	    if(Conexion::ejecutar($queryDos,NULL)){
                return true;
                }else{
                    return false;
                }
        	
        	}else{
        		return false;
        	}
		}

        public function actualizarFechasCaducidad($cod_empresa){
            $query = "UPDATE tb_empresa_fidelizacion_puntos
                        SET cant_dias_caducidad_puntos = $this->cantDiasCaducidadPuntos,
                            cant_dias_caducidad_dinero = $this->cantDiasCaducidadDinero, 
                            cant_dias_caducidad_saldo = $this->cantDiasCaducidadSaldo
                        WHERE cod_empresa = $cod_empresa";
            return Conexion::ejecutar($query, null);
        }
}
?>