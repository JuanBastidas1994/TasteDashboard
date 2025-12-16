<?php
class cl_demo
{
		public $session;
		public $cod_empresa, $nombre, $alias, $telefono, $logo, $api, $estado,$tipoem,$urlWeb,$color,$txt_description,$txt_keywords,$direccion;
		public $contacto, $correo, $password;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        /*GET*/
        public function get($cod_empresa)
		{
			$query = "SELECT * from tb_demos where cod_demo = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getInfoContact($cod_empresa)
		{
			$query = "SELECT * from tb_demos where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getRedesSociales($cod_empresa)
		{
			$query = "SELECT * from tb_empresa_red_social where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getByAlias($alias)
		{
			$query = "SELECT * from tb_demos where alias = '$alias'";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		
		public function get_tipoem()
		{
		    $query = "SELECT * from tb_tipo_empresas";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function lista(){
			$query = "SELECT * FROM tb_demos WHERE estado IN ('A','I')";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function aliasDisponible($alias){
			$empresa = $this->session['cod_empresa'];
			$query = "SELECT * FROM tb_empresas WHERE alias = '$alias' AND estado IN ('A','I')";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}
		
		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$query = "INSERT INTO tb_demos(cod_empresa, nombre, direccion, correo, telefono, alias, logo, estado, color) ";
        	$query.= "VALUES('$this->cod_empresa','$this->nombre', '$this->direccion', '$this->correo', '$this->telefono', '$this->alias',  '$this->logo', '$this->estado', '$this->color')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
        
		public function editarDemo($cod_empresa){
            $queryUpdate= "UPDATE tb_demos SET cod_empresa = '$this->cod_empresa', nombre='$this->nombre', direccion = '$this->direccion', telefono='$this->telefono' , correo='$this->correo', logo='$this->logo' , estado='$this->estado', color='$this->color' WHERE cod_demo =".$cod_empresa;
           // echo $query;
            if(Conexion::ejecutar($queryUpdate,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function set_update_contact($direccion, $telefono, $correo,$cod_empresa){
            $queryUpdate= "UPDATE tb_demos SET direccion='$direccion', telefono='$telefono' ,correo='$correo' WHERE cod_empresa =".$cod_empresa;
            if(Conexion::ejecutar($queryUpdate,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
         public function set_update_redes($id,$text,$cod_empresa){
             $query = "DELETE FROM tb_empresa_red_social WHERE cod_empresa = $cod_empresa and cod_red_social =$id ";
             if(Conexion::ejecutar($query,NULL)){
                 
                    $query = "INSERT INTO tb_empresa_red_social(cod_empresa, cod_red_social, descripcion) ";
                	$query.= "VALUES($cod_empresa, $id,'".$text."')";
                	if(Conexion::ejecutar($query,NULL)){
                		return true;
                	}else{
                		return false;
                	}
        	}else{
        		return false;
        	}
            
        }

		public function set_estado($cod_demo, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_demos SET estado='$estado' WHERE cod_demo = $cod_demo";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function set_costo_envio($cod_empresa, $tarifa_base, $km_base, $tarifa_adicional){
			$usuario = $this->session['cod_usuario'];
			$query = "UPDATE tb_empresa_costo_envio SET base_dinero='$tarifa_base', base_km='$km_base', adicional_km='$tarifa_adicional' WHERE cod_empresa = $cod_empresa";
        	return Conexion::ejecutar($query,NULL);
		}

		public function getArray($cod_sucursal, &$array)
		{
			$query = "SELECT * from tb_sucursales where cod_sucursal = ".$cod_sucursal;
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

		/*DISPONIBILIDAD*/
		public function lista_disponibilidad(){
			$fecha = fecha_only()." 00:00:00";
			$query = "SELECT f.*, s.nombre 
					FROM tb_sucursal_festivos f, tb_sucursales s 
					WHERE f.cod_sucursal = s.cod_sucursal
					AND f.fecha_inicio >= '$fecha' 
					AND s.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear_disponibilidad($cod_sucursal, $fecha, $hora_ini, $hora_fin){
			$fecha_inicio = $fecha." ".$hora_ini;
			$fecha_fin    = $fecha." ".$hora_fin;
			$query = "INSERT INTO tb_sucursal_festivos(cod_sucursal, fecha, hora_inicio, hora_fin, fecha_inicio, fecha_fin) ";
        	$query.= "VALUES($cod_sucursal, '$fecha', '$hora_ini', '$hora_fin', '$fecha_inicio', '$fecha_fin')";
        	if(Conexion::ejecutar($query,NULL)){
        		//$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function eliminar_disponibilidad($cod_sucursal_festivos){
			$query = "DELETE FROM tb_sucursal_festivos WHERE cod_sucursal_festivos = $cod_sucursal_festivos";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function get_roles(){
		    $cod_empresa = $this->cod_empresa;
		    $id = 1;
		    $rol = $this->session['cod_rol'];
		    if($cod_empresa == 1 && $rol == 1) // Solo si es superadministrador de digital mind
		    {
		        $id = 0;
		    }
			$query = "SELECT * FROM tb_roles WHERE estado = 'A' AND cod_rol > $id";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function addPagina($cod_empresa, $cod_rol, $cod_pagina){
			$query = "INSERT INTO tb_pagina_rol(cod_empresa, cod_rol, cod_pagina, posicion) 
					VALUES($cod_empresa, $cod_rol, $cod_pagina, 999)";
			return Conexion::ejecutar($query,NULL);
		}
		public function deletePagina($cod_empresa, $cod_rol, $cod_pagina){
			$query = "DELETE FROM tb_pagina_rol WHERE cod_empresa=$cod_empresa AND cod_rol=$cod_rol AND cod_pagina=$cod_pagina ";
			return Conexion::ejecutar($query,NULL);
		}
		public function updatePaginaPosicion($cod_empresa, $cod_rol, $cod_pagina, $posicion){
			$query = "UPDATE tb_pagina_rol SET posicion=$posicion 
					WHERE cod_empresa=$cod_empresa AND cod_rol=$cod_rol AND cod_pagina=$cod_pagina ";
			return Conexion::ejecutar($query,NULL);
		}
		/*NUEVO BRI*/
	
	    public function getAllRedesSociales($cod_empresa)
		{
			$query = "SELECT r.codigo, r.icono, er.descripcion
                        FROM tb_empresa_red_social er, tb_red_social r
                        WHERE er.cod_red_social = r.cod_red
                        AND er.cod_empresa = $cod_empresa";
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
        }
        
        public function getFormasPago($cod_empresa){
            $query = "SELECT *
                        FROM tb_formas_pago";
            $resp = Conexion::buscarVariosRegistro($query);
			return $resp;
        }
        
         public function getFormasPagoEmpresa($cod_empresa){
            $query = "SELECT efp.*, fp.descripcion as fp_desc
                        FROM tb_empresa_forma_pago efp, tb_formas_pago fp
                        WHERE efp.cod_forma_pago = fp.cod_forma_pago
                        AND efp.cod_empresa = $cod_empresa";
            $resp = Conexion::buscarVariosRegistro($query);
            if($resp)
                return true;
            else
			    return false;
        }
        
        function getFormasPagoEmp($cod_empresa){
            $query = "SELECT fp.descripcion as fp_desc, fp.cod_forma_pago as id_forma_pago, efp.*
                        FROM tb_formas_pago fp
                        LEFT JOIN tb_empresa_forma_pago efp
                        ON fp.cod_forma_pago = efp.cod_forma_pago
                        AND efp.cod_empresa = $cod_empresa";
            $resp = Conexion::buscarVariosRegistro($query);
			return $resp;
        }
        
        public function insertFormasPagoEmpresa($cod_empresa, $cod_forma_pago, $estado){
            $query = "INSERT INTO tb_empresa_forma_pago(cod_empresa, cod_forma_pago, estado) ";
            $query.= "VALUES($cod_empresa, '$cod_forma_pago', '$estado')";
            $resp = Conexion::ejecutar($query,NULL);
        }
        
        public function desactivarFormasPagoEmpresa($cod_empresa){
            $query = "UPDATE tb_empresa_forma_pago 
                        SET estado = 'I' 
                        WHERE cod_empresa = $cod_empresa ";
            //echo $query;
            $resp = Conexion::ejecutar($query,NULL);
        }
        
        public function set_update_formas_pago($estado,$codigo){
            $query = "UPDATE tb_empresa_forma_pago 
                        SET estado = '$estado' 
                        WHERE cod_empresa_forma_pago = '$codigo' ";
            //echo $query;
            $resp = Conexion::ejecutar($query,NULL);
            return $resp;
        }
        
        
        public function updateFormasPagoEmpresa($cod_empresa, $cod_forma_pago, $estado){
            $query = "UPDATE tb_empresa_forma_pago 
                        SET estado = '$estado' 
                        WHERE cod_empresa = $cod_empresa 
                        AND cod_forma_pago = '$cod_forma_pago'";
            //echo $query;
            $resp = Conexion::ejecutar($query,NULL);
        } 
        
        public function updateDescripcionFormaPago($cod,$descripcion){
            $query = "UPDATE tb_empresa_forma_pago 
                        SET descripcion = '$descripcion' 
                        WHERE cod_empresa_forma_pago = '$cod'";
            //echo $query;
            $resp = Conexion::ejecutar($query,NULL);
            if($resp)
			    return true;
			else
			    return false;
        }
        
        public function getUnaFormaPagoEmpresa($cod_empresa, $cod_forma_pago){
            $query = "SELECT * 
                        FROM tb_empresa_forma_pago
                        WHERE cod_empresa = $cod_empresa
                        AND cod_forma_pago = '$cod_forma_pago'";
            $resp = Conexion::buscarVariosRegistro($query);
			if($resp)
			    return true;
			else
			    return false;
        }
}
?>