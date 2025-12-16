<?php
class cl_botonpagos
{
        var $session;
        var $cod_empresa,$alias,$nombre, $desc_corta, $desc_larga, $image_min, $image_max, $estado; 
        
        public function __construct($pbotonpagos=null)
        {
            if($pbotonpagos != null)
                $this->cod= $pbotonpagos;
                $this->session = getSession();
        }

        public function datos_paymentez($cod_empresa){
            $query = "SELECT * FROM tb_empresa_paymentez where cod_empresa=".$cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }

		public function insert_paymentez($cod_empresa, $servercode, $serverkey,$clientcode,$clientkey,$tipo,&$id){
		    $usuario = $this->session['cod_usuario'];
			$query = "INSERT INTO tb_empresa_paymentez(cod_empresa, server_code, server_key,client_code,client_key,ambiente,user_creacion) ";
        	$query.= "VALUES($cod_empresa, '$servercode', '$serverkey','$clientcode','$clientkey','$tipo','$usuario')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
	//	update_paymentez($servercode, $serverkey,$clientcode,$clientkey,$tipo,$id)
		public function update_paymentez($servercode, $serverkey,$clientcode,$clientkey,$tipo,$id){
		    $usuario = $this->session['cod_usuario'];
            $query= "UPDATE tb_empresa_paymentez SET server_code='$servercode', server_key='$serverkey',client_code='$clientcode', client_key='$clientkey',ambiente='$tipo',user_creacion='$usuario' WHERE cod_empresa_paymentez =".$id;
           // echo $query;
            if(Conexion::ejecutar($query,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function actPosicionOpciones($cod, $posicion){
    	    $query = "UPDATE tb_empresa_paymentez SET posicion = $posicion WHERE cod_empresa_paymentez = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }

        public function lista(){
            $query = "SELECT * 
                        FROM tb_proveedor_botonpagos";
            return Conexion::buscarVariosRegistro($query);
        }

        public function listaByEmpresas($cod_empresa){
            $query = "SELECT pb.cod_proveedor_botonpagos, pb.nombre
                        FROM tb_proveedor_botonpagos pb, tb_empresa_botonpagos eb
                        WHERE pb.cod_proveedor_botonpagos = eb.cod_proveedor_botonpagos
                        AND eb.cod_empresa = $cod_empresa";
            return Conexion::buscarVariosRegistro($query);
        }
        
        public function datos_datafast($cod_empresa){
            $query = "SELECT * 
                        FROM tb_empresa_datafast 
                        WHERE cod_empresa = $cod_empresa
                        AND estado = 'A'";
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }

        public function insert_datafast($cod_empresa, $api, $entity, $mid, $tid, $ambiente, $fase, &$id){
            $fecha = fecha();
		    $query = "DELETE FROM tb_empresa_datafast WHERE cod_empresa = $cod_empresa";
            $resp = Conexion::ejecutar($query, null);
            if($resp){
                $query = "INSERT INTO tb_empresa_datafast(cod_empresa, api, entityId, mid, tid, ambiente, fase, fecha_creacion, estado) ";
                $query.= "VALUES($cod_empresa, '$api', '$entity', '$mid', '$tid', '$ambiente', '$fase', '$fecha', 'A')";
                if(Conexion::ejecutar($query,NULL)){
                    $id = Conexion::lastId();
                    return true;
                }else{
                    return false;
                }
            }
            return false;
		}

        public function botonActualEmpresa($cod_empresa){
            $query = "SELECT eb.*, pb.nombre
                        FROM tb_empresa_botonpagos eb, tb_proveedor_botonpagos pb
                        WHERE eb.cod_proveedor_botonpagos = pb.cod_proveedor_botonpagos
                        AND eb.cod_empresa = $cod_empresa
                        AND eb.estado = 'A'";
            return Conexion::buscarRegistro($query);
        }

        public function establecerBoton($cod_empresa, $cod_proveedor_botonpagos){
            $fecha = fecha();
            $query = "UPDATE tb_empresa_botonpagos
                        SET estado = 'I'
                        WHERE cod_empresa = $cod_empresa";
            $resp = Conexion::ejecutar($query, null);
            if($resp){
                $query = "SELECT * 
                            FROM tb_empresa_botonpagos
                            WHERE cod_empresa = $cod_empresa
                            AND cod_proveedor_botonpagos = $cod_proveedor_botonpagos";
                $row = Conexion::buscarRegistro($query);
                if($row){
                    $query = "UPDATE tb_empresa_botonpagos
                                SET estado = 'A'
                                WHERE cod_empresa = $cod_empresa
                                AND cod_proveedor_botonpagos = $cod_proveedor_botonpagos";
                    return Conexion::ejecutar($query, null);
                }
                else{
                    $query = "INSERT INTO tb_empresa_botonpagos(cod_empresa, cod_proveedor_botonpagos, fecha_create, estado)
                            VALUES($cod_empresa, $cod_proveedor_botonpagos, '$fecha', 'A')";
                    return Conexion::ejecutar($query, null);
                }
            }
            return false;
        }

        public function agregarComoConfigurado($cod_proveedor_botonpagos, $cod_empresa, $estado){
            $fecha = fecha();
            
            $query = "INSERT INTO tb_empresa_botonpagos(cod_empresa, cod_proveedor_botonpagos, fecha_create, estado)
                        VALUES($cod_empresa, $cod_proveedor_botonpagos, '$fecha', '$estado')";
            return Conexion::ejecutar($query, null);
        }

        public function existeBotonConfigurado($cod_empresa, $cod_proveedor_botonpagos){
            $query = "SELECT *
                        FROM tb_empresa_botonpagos
                        WHERE cod_empresa = $cod_empresa
                        AND cod_proveedor_botonpagos = $cod_proveedor_botonpagos";
            return Conexion::buscarVariosRegistro($query);
        }

        public function sucursalPaymentez($cod_sucursal){
            $query = "SELECT s.nombre, sp.* 
                        FROM tb_empresa_sucursal_paymentez sp, tb_sucursales s
                        WHERE sp.cod_sucursal = s.cod_sucursal
                        AND sp.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }

        public function sucursalDatafast($cod_sucursal){
            $query = "SELECT s.nombre, sd.* 
                        FROM tb_empresa_sucursal_datafast sd, tb_sucursales s
                        WHERE sd.cod_sucursal = s.cod_sucursal
                        AND sd.cod_sucursal = $cod_sucursal";
            return Conexion::buscarRegistro($query);
        }
}
?>