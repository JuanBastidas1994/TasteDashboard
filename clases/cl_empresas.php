<?php
class cl_empresas
{
		public $session;
		public $cod_empresa, $nombre, $alias, $telefono, $logo, $api, $estado,$tipoem,$urlWeb,$color,$txt_description,$txt_keywords, $pixel, $pixel_verify, $cod_plan, $precio_mensual, $fecha_caducidad, $tipoRecorte;
		public $contacto, $correo, $password;
		public $ckStartOnMenu;
		
		public function __construct(){
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        /*GET*/
        public function get($cod_empresa){
			$query = "SELECT * from tb_empresas where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getInfoContact($cod_empresa){
			$query = "SELECT * from tb_empresas where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }
        
        public function getRedesSociales($cod_empresa){
			$query = "SELECT * from tb_empresa_red_social where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }

		public function getImgCumple($cod_empresa){
			$query = "SELECT * from tb_empresa_modal_cumple where cod_empresa = ".$cod_empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
        }

		public function createImgCumple($img, $cod_empresa){
			$query = "INSERT INTO tb_empresa_modal_cumple
			SET cod_empresa = $cod_empresa, imagen = '$img', estado = 'A';";
			return Conexion::ejecutar($query,NULL);
		}

		public function updateImgCumple($img, $cod_empresa){
			$query = "UPDATE tb_empresa_modal_cumple
			SET imagen = '$img' WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query,NULL);
		}

		public function setEstadoImgCumple($estado, $cod_empresa){
			$query = "UPDATE tb_empresa_modal_cumple
			SET estado = '$estado' WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query,NULL);
		}
        
        public function getByAlias($alias){
			$query = "SELECT * from tb_empresas where alias = '$alias'";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		
		public function get_tipoem(){
		    $query = "SELECT * FROM tb_tipo_empresas";
			$row = Conexion::buscarVariosRegistro($query);
			return $row;
		}
		
		public function lista(){
			$query = "SELECT * FROM tb_empresas WHERE estado IN ('A','I')";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaWithProgress(){
		    $query = "SELECT e.cod_empresa, e.nombre, e.alias, e.logo, e.impuesto, e.folder,  e.representante_nombre, 
		                        SUM(ep.porcentaje) as porcentaje, u.imagen as foto, CONCAT(u.nombre, ' ', u.apellido) as usuario
                        FROM tb_empresas e
                        INNER JOIN tb_empresa_progresos ep ON ep.cod_empresa = e.cod_empresa
                        LEFT JOIN tb_usuarios u ON u.cod_usuario = e.user_create
                        WHERE e.estado IN ('A', 'I')
                        GROUP BY e.cod_empresa
                        ORDER BY e.cod_empresa ASC;";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function getEmpresasPorTipo($cod_tipo_empresa){
		    $filtro = '';
		    if($cod_tipo_empresa > 0)
		        $filtro = "AND cod_tipo_empresa = $cod_tipo_empresa";
		    $query = "SELECT * FROM tb_empresas WHERE estado = 'A' 
				$filtro
				ORDER BY ambiente DESC, nombre ASC
			";
		    return Conexion::buscarVariosRegistro($query);
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
			$query = "INSERT INTO tb_empresas(cod_tipo_empresa, cod_plan, fecha_caducidad, mensualidad, nombre, alias, telefono, representante_nombre, representante_correo, logo, api_key, estado,url_web,color, description, keywords,fecha_registro,user_create, facebook_pixel, facebook_pixel_verify, tipo_recorte, iniciar_en_menu) ";
        	$query.= "VALUES('$this->tipoem', '$this->cod_plan', '$this->fecha_caducidad', '$this->precio_mensual', '$this->nombre', '$this->alias', '$this->telefono', '$this->contacto', '$this->correo', '$this->logo', '$this->api', '$this->estado','$this->urlWeb','$this->color', '$this->description', '$this->keywords', NOW(),$usuario, '$this->pixel', '$this->pixel_verify', '$this->cmbTipoRecorte', '$this->ckStartOnMenu')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
        
		public function editarEmpresa($cod_empresa){
            $queryUpdate= "UPDATE tb_empresas SET cod_plan = '$this->cod_plan', fecha_caducidad = '$this->fecha_caducidad', mensualidad = '$this->precio_mensual', nombre='$this->nombre', telefono='$this->telefono' ,representante_nombre='$this->contacto' , representante_correo='$this->correo', logo='$this->logo' , estado='$this->estado', url_web='$this->urlWeb',color='$this->color', description='$this->description', keywords='$this->keywords', facebook_pixel = '$this->pixel', facebook_pixel_verify = '$this->pixel_verify', tipo_recorte = '$this->cmbTipoRecorte', cod_tipo_empresa = '$this->tipoem', iniciar_en_menu = '$this->ckStartOnMenu' WHERE cod_empresa =".$cod_empresa;
           // echo $query;
            if(Conexion::ejecutar($queryUpdate,NULL)){
                return true;
            }else{
                return false;
            }
        }
        
        public function set_update_contact($direccion, $telefono, $correo,$cod_empresa){
            $queryUpdate= "UPDATE tb_empresas SET direccion='$direccion', telefono='$telefono' ,correo='$correo' WHERE cod_empresa =".$cod_empresa;
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

		public function set_estado($cod_sucursal, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_sucursales SET estado='$estado' WHERE cod_sucursal = $cod_sucursal";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function setPermisoFidelizacion($estado, $cod_empresa){
			$query = "UPDATE tb_empresas
			SET fidelizacion = $estado WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query,NULL);
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

		public function addPagina($cod_empresa, $cod_rol, $cod_pagina, $posicion = 99){
			$query = "INSERT INTO tb_pagina_rol(cod_empresa, cod_rol, cod_pagina, posicion) 
					VALUES($cod_empresa, $cod_rol, $cod_pagina, $posicion)";
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
		
		public function getPagesCopy(){
		    $query = "SELECT * FROM tb_pagina_rol WHERE cod_empresa = 1 AND cod_rol IN(2,3,6) ORDER BY posicion ASC";
		    return Conexion::buscarVariosRegistro($query);
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
                        AND efp.cod_empresa = $cod_empresa order by efp.posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            if($resp)
                return true;
            else
			    return false;
        }
        
        function getFormasPagoEmp($cod_empresa){
            $query = "SELECT fp.descripcion as fp_desc, fp.cod_forma_pago as id_forma_pago, efp.nombre as nomFP, efp.*
                        FROM tb_formas_pago fp
                        LEFT JOIN tb_empresa_forma_pago efp
                        ON fp.cod_forma_pago = efp.cod_forma_pago
                        AND efp.cod_empresa = $cod_empresa";
            $resp = Conexion::buscarVariosRegistro($query);
			return $resp;
        }
        
        public function insertFormasPagoEmpresa($cod_empresa, $cod_forma_pago, $estado){
			$query = "SELECT descripcion
						FROM tb_formas_pago
						WHERE cod_forma_pago = '$cod_forma_pago'";
			$fp = Conexion::buscarRegistro($query);
			$nomFP = $fp["descripcion"];

            $query = "INSERT INTO tb_empresa_forma_pago(cod_empresa, cod_forma_pago, estado, nombre) ";
            $query.= "VALUES($cod_empresa, '$cod_forma_pago', '$estado', '$nomFP')";
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
        
          public function actPosicionOpciones($cod, $posicion){
    	    $query = "UPDATE tb_empresa_forma_pago SET posicion = $posicion WHERE cod_empresa_forma_pago = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
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
        
		public function actualizarPermisoTienda($cod_empresa, $encender){
			if($encender == 1){
				$query = "INSERT INTO tb_empresa_configuraciones(cod_empresa, encender_tienda)
							VALUES($cod_empresa, 1)";
				return Conexion::ejecutar($query, null);
			}
			else{
				$query = "DELETE FROM tb_empresa_configuraciones 
							WHERE cod_empresa = $cod_empresa 
							AND encender_tienda = 1";
				return Conexion::ejecutar($query, null);
			}
		}

		public function getPermisoTienda($cod_empresa){
			$query = "SELECT * 
						FROM tb_empresa_configuraciones
						WHERE cod_empresa = $cod_empresa";
			return Conexion::buscarVariosRegistro($query);
		}
        
        /*------------- COURIERS ---------------*/
        public function getCourer(){
            $query = "SELECT * from tb_courier where estado = 'A' ";
            $resp = Conexion::buscarVariosRegistro($query);
			return $resp;
        }
        
         public function editarLaar($cod, $token){
    	    $query = "UPDATE tb_laar_sucursal SET token = '$token' WHERE cod_laar_sucursal = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function ingresarLaar($cod_empresa,$cod_sucursal,$token,&$id){
			$query = "INSERT INTO tb_laar_sucursal(cod_empresa,cod_sucursal,token,estado) ";
        	$query.= "VALUES($cod_empresa,$cod_sucursal, '$token', 'A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function editarLaar2($cod, $user, $pass){
    	    $query = "UPDATE tb_laar_sucursal SET username = '$user', password = '$pass' WHERE cod_laar_sucursal = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function ingresarLaar2($cod_empresa, $cod_sucursal, $user, $pass, &$id){
			$query = "INSERT INTO tb_laar_sucursal(cod_empresa, cod_sucursal, username, password, estado) ";
        	$query.= "VALUES($cod_empresa, $cod_sucursal, '$user', '$pass', 'A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
        
        public function editarGacela($cod, $token,$api,$ambiente){
    	    $query = "UPDATE tb_gacela_sucursal SET token = '$token',api='$api' WHERE cod_gacela_sucursal = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function ingresarGacela($cod_empresa,$cod_sucursal,$token,$api,$ambiente,&$id){
			$query = "INSERT INTO tb_gacela_sucursal(cod_empresa,cod_sucursal,token,api,ambiente,estado) ";
        	$query.= "VALUES($cod_empresa,$cod_sucursal, '$token','$api','$ambiente', 'A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

        public function editarPicker($cod, $api, $ambiente){
    	    $query = "UPDATE tb_picker_sucursal SET api='$api' WHERE cod_picker_sucursal = $cod";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
	    
	    public function ingresarPicker($cod_empresa, $cod_sucursal, $api, $ambiente, &$id){
			$query = "INSERT INTO tb_picker_sucursal(cod_empresa, cod_sucursal, api, ambiente, estado) ";
        	$query.= "VALUES($cod_empresa,$cod_sucursal, '$api','$ambiente', 'A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function getgacelaEmpresa($cod_empresa,$ambiente)
        {
            $query = "select * from tb_gacela_sucursal gs where gs.cod_empresa = ".$cod_empresa." and gs.ambiente = '$ambiente' limit 0,1";
			$row = Conexion::buscarRegistro($query);
			if($row)
			{
			    return true;
			}
			else
			{
			    return false;
			}
			
        }
        
        public function getAmbientegacelaEmpresa($cod_empresa,$ambiente)
        {
            $query = "select * from tb_gacela_sucursal gs where gs.cod_empresa = ".$cod_empresa." and gs.ambiente = '$ambiente' and gs.estado='A' limit 0,1";
			$row = Conexion::buscarRegistro($query);
			if($row)
			{
			    return true;
			}
			else
			{
			    return false;
			}
			
        }
        
        public function AmbienteGacelaEmpresa($cod_empresa,$ambiente){
            $ambientes = array('development','production');
            $aux=0;
            for($i=0;$i<count($ambientes);$i++)
            {
                $estado = 'I';
                if($ambientes[$i]==$ambiente){$estado = 'A';}
                
        	    $query = "UPDATE tb_gacela_sucursal SET estado='$estado' WHERE cod_empresa = $cod_empresa and ambiente ='$ambientes[$i]' ";
        	    if(Conexion::ejecutar($query,NULL)){
            		$aux++;
            	    }
            }
            
            if($aux==count($ambientes))
			{
			    return true;
			}
			else
			{
			    return false;
			}
	    }
        
        public function getEmpresaCourier($cod_empresa){
            $query = "SELECT cod_courier from tb_empresa_courier where cod_empresa=$cod_empresa ";
            $resp = Conexion::buscarVariosRegistro($query);
			foreach ($resp as $data) {
            	$return[] = $data['cod_courier'];
            }
            return $return;
        }
        
        public function getEmpresaCourierId($cod_empresa, $cod_courier){
            $query = "SELECT * 
						FROM tb_empresa_courier 
						WHERE cod_empresa=$cod_empresa 
						AND cod_courier = $cod_courier";
            return Conexion::buscarRegistro($query);
        }
        
        public function setEmpresaCourierId($cod_empresa, $cod_courier){
            $query = "INSERT INTO tb_empresa_courier(cod_empresa, cod_courier) ";
        	$query.= "VALUES($cod_empresa, $cod_courier)";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        }

        public function getSucursalCourierId($cod_sucursal, $cod_courier){
            $query = "SELECT * FROM tb_sucursal_courier where cod_sucursal = $cod_sucursal AND cod_courier = $cod_courier";
            $resp = Conexion::buscarRegistro($query);
            return $resp;
        }
        
        public function setSucursalCourierId($cod_sucursal, $cod_courier){
            $query = "INSERT INTO tb_sucursal_courier(cod_sucursal, cod_courier) ";
        	$query.= "VALUES($cod_sucursal, $cod_courier)";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        }
        
        /*------------- RECURSIVIDAD PAGOS ---------------*/
        public function setCard($cod_empresa,$token,$type,$status,$number,$reference,$expiry_month,$expiry_year){
            $query = "UPDATE tb_empresa_tarjeta SET estado = 'I' WHERE cod_empresa = $cod_empresa";
            Conexion::ejecutar($query,NULL);
            
            $query = "INSERT INTO tb_empresa_tarjeta(cod_empresa,token,type,status,number,reference,expiry_month,expiry_year,estado) 
                    VALUES($cod_empresa,'$token','$type','$status','$number','$reference','$expiry_month','$expiry_year','A')";
            return Conexion::ejecutar($query,NULL);
        }
        
        public function getCardActive($cod_empresa){
            $query = "SELECT * FROM tb_empresa_tarjeta WHERE cod_empresa = ".$cod_empresa." AND estado = 'A'";
            return Conexion::buscarRegistro($query);
        }
        
        public function getPlanes(){
            $query = "SELECT * FROM tb_planes ORDER BY posicion ASC";
            return Conexion::buscarVariosRegistro($query);
        }
        
        public function getLogsPagos($cod_empresa){
            $query = "SELECT * FROM mie_log_pago WHERE cod_empresa = $cod_empresa";
            return Conexion::buscarVariosRegistro($query);
        }
        
        public function getLogsSuccess($cod_log, &$row){
            $query = "SELECT *
                        FROM mie_log_pago_success
                        WHERE cod_mie_log_pago = $cod_log";
            $row = Conexion::buscarRegistro($query);
            if($row)
                return true;
            return false;
        }
        
        public function getLogsError($cod_log, &$row){
            $query = "SELECT *
                        FROM mie_log_pago_error
                        WHERE cod_mie_log_pago = $cod_log";
            $row = Conexion::buscarRegistro($query);
            if($row)
                return true;
            return false;
        }
        
        public function getTarjetaActiva($cod_empresa){
            $query = "SELECT * FROM tb_empresa_tarjeta WHERE estado = 'A' AND cod_empresa = $cod_empresa";
            $row = Conexion::buscarRegistro($query);
            if($row)
                return $row['token'];
            else{
                return 0;
            }
        }
        
        public function actulizarTarjeta($token, $cod_empresa){
            $query = "  UPDATE tb_empresa_tarjeta SET estado = 'I' WHERE cod_empresa = $cod_empresa AND estado = 'A';
                        UPDATE tb_empresa_tarjeta SET estado = 'A' WHERE token = '$token';";
            return Conexion::ejecutar($query,NULL);
        }
        
        
        /*------------- FACTURACION ELECTRONICA ---------------*/
        public function getProveedorFact($cod_empresa){
            $query = "SELECT f.*
                    FROM tb_empresa_facturacion ef, tb_sistema_facturacion f
                    WHERE ef.cod_sistema_facturacion = f.cod_sistema_facturacion
                    AND ef.cod_empresa = $cod_empresa
                    AND f.estado = 'A'";
            return Conexion::buscarRegistro($query,NULL);
        }

		public function setProgramarPedido($cod_empresa, $programa){
			$query = "UPDATE tb_empresas
						SET programar_pedido = $programa
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}

		public function setGravaIva($cod_empresa, $grava){
			$query = "UPDATE tb_empresas
						SET envio_grava_iva = $grava
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}

		public function updateFolder($cod_empresa, $folder){
			$query = "UPDATE tb_empresas
						SET folder = '$folder'
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}
		
		public function updateHosting($cod_empresa, $hosting){
			$query = "UPDATE tb_empresas
						SET hosting = '$hosting'
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}
		

		public function cantProductosByEmpresa($cod_empresa){
			$query = "SELECT COUNT(*) as cant_productos
						FROM tb_productos 
						WHERE cod_empresa = $cod_empresa
						AND estado = 'A'";
			$row = Conexion::buscarRegistro($query);		
			return $row['cant_productos'];
		}

		public function cantPermisosAdminEmpresa($cod_empresa){
			$query = "SELECT COUNT(*) as cant_permisos
						FROM tb_pagina_rol
						WHERE cod_empresa = $cod_empresa
						AND cod_rol = 2";
			$row = Conexion::buscarRegistro($query);		
			return $row['cant_permisos'];
		}

		public function cantPermisosAdminSucursal($cod_empresa){
			$query = "SELECT COUNT(*) as cant_permisos
						FROM tb_pagina_rol
						WHERE cod_empresa = $cod_empresa
						AND cod_rol = 3";
			$row = Conexion::buscarRegistro($query);		
			return $row['cant_permisos'];
		}

		public function ventasSemanales($fecha_inicio, $fecha_fin){
			$query = "SELECT e.nombre, e.logo, e.alias, e.ambiente, COALESCE(SUM(oc.total), 0) as total
						FROM tb_empresas e
						LEFT JOIN tb_orden_cabecera oc
						ON e.cod_empresa = oc.cod_empresa
						AND e.estado = 'A'
						AND oc.estado = 'ENTREGADA'
						AND oc.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
						GROUP BY e.cod_empresa
						HAVING e.ambiente = 'production'
						ORDER BY total ASC";
			return Conexion::buscarVariosRegistro($query);
		}

		public function updateProgresoEmpresa($cod_empresa, $titulo, $porcentaje){
			$fecha = fecha();
			$query = "SELECT * 
						FROM tb_empresa_progresos 
						WHERE cod_empresa = $cod_empresa 
						AND titulo = '$titulo'";
			$resp = Conexion::buscarVariosRegistro($query);
			if($resp)
				return false;
			$query = "INSERT INTO tb_empresa_progresos(cod_empresa, titulo, porcentaje, fecha_create)
						VALUES($cod_empresa, '$titulo', $porcentaje, '$fecha')";
			return Conexion::ejecutar($query, null);
		}

		public function setAmbienteEmpresa($cod_empresa, $ambiente){
			$query = "UPDATE tb_empresas
						SET ambiente = '$ambiente'
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}

		public function setPermisoRecordatorio($cod_empresa, $recordar_ordenes){
			$query = "UPDATE tb_empresas
						SET recordar_ordenes = $recordar_ordenes
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}

		public function setEmprendedorEmpresa($cod_empresa, $isEmprendedor){
			$query = "UPDATE tb_empresas
						SET is_emprendedor = $isEmprendedor
						WHERE cod_empresa = $cod_empresa";
			return Conexion::ejecutar($query, null);
		}

		public function getSizeCrop($cod_empresa){
			$query = "SELECT * 
						FROM tb_size_crop 
						WHERE cod_empresa = $cod_empresa";
			return Conexion::buscarRegistro($query);
		}

		public function setMontoMaximoFormaPago($cod_forma_pago, $monto){
			$cod_empresa = $this->session['cod_empresa'];
			$query = "UPDATE tb_empresa_forma_pago
						SET monto_maximo = $monto
						WHERE cod_empresa = $cod_empresa
						AND cod_forma_pago = '$cod_forma_pago'";
			return Conexion::ejecutar($query, null);
		}

		public function setNombreFormaPago($nombre, $cod_empresa_forma_pago){
			$query = "UPDATE tb_empresa_forma_pago
						SET nombre = '$nombre'
						WHERE cod_empresa_forma_pago = $cod_empresa_forma_pago";
			return Conexion::ejecutar($query, null);
		}

		public function setPermisoTipoEnvio($tipo_envio, $encendido, $cod_empresa_forma_pago){
			$field = "is_delivery";
			if($tipo_envio == "P")
				$field = "is_pickup";

			$query = "UPDATE tb_empresa_forma_pago
						SET $field = '$encendido'
						WHERE cod_empresa_forma_pago = $cod_empresa_forma_pago";
			return Conexion::ejecutar($query, null);
		}
		
		//Permisos
		public function getPermisionsByGroup($grupo){
		    $query = "SELECT * FROM tb_permisos WHERE grupo = '$grupo' ORDER BY nombre ASC";
		    return Conexion::buscarVariosRegistro($query);
		}
		
		//Permisos - Todos los grupos
		public function getAllPermisionsGroup(){
		    $query = "SELECT DISTINCT grupo FROM tb_permisos GROUP BY grupo ORDER BY grupo ASC";
		    $groups = Conexion::buscarVariosRegistro($query);
		    if($groups){
		        foreach ($groups as $key => $grupo) {
            	    $groups[$key]['permisos'] = $this->getPermisionsByGroup($grupo['grupo']);
                }
                return $groups;
		    }
		    return false;
		}
		
		//Permisos - Get Permisos por empresa
		public function getIdPermisionByBusiness($cod_empresa){
		    $identificadores = [];
		    $x=0;
		    $query = "SELECT identificador FROM tb_permisos_empresas WHERE cod_empresa = $cod_empresa";
		    $permisos = Conexion::buscarVariosRegistro($query);
		    foreach ($permisos as $key => $permiso) {
		        $identificadores[$x]=$permiso['identificador'];
		        $x++;
		    }
		    return $identificadores;
		}
		
		//Permisos add/delete
		public function setPermisionToBusiness($cod_empresa, $permiso, $status){
		    if($status == 0){
		        //DELETE
		        $query = "DELETE FROM tb_permisos_empresas WHERE cod_empresa = $cod_empresa AND identificador = '$permiso'";
			    return Conexion::ejecutar($query, null);
		    }else{
		        $query = "INSERT INTO tb_permisos_empresas (cod_empresa, identificador, habilitado, estado)
						VALUES($cod_empresa, '$permiso', 1, 'A')";
			    return Conexion::ejecutar($query, null);
		    }
		}
		
		public function tienePermiso($cod_empresa, $permiso){
		    $query = "SELECT * FROM tb_permisos_empresas 
                        WHERE habilitado = 1 AND estado = 'A'
                        AND cod_empresa = $cod_empresa 
                        AND identificador = '$permiso'";
            return Conexion::buscarRegistro($query);
		}
		
		/*CONTIFICO EMPRESA - RUCS*/
		public function getRucs($cod_empresa){
            $query = "SELECT * FROM tb_contifico_empresa WHERE cod_empresa = $cod_empresa AND estado = 'A'";
            return Conexion::buscarVariosRegistro($query);
        }
        
        
        /*IMPUESTOS*/
        public function updateImpuesto($cod_empresa, $impuesto, $tipo){
            try {
				$con = Conexion::obtenerConexion();
				$con->beginTransaction();
				
				
				$query = "SELECT * FROM tb_empresas WHERE cod_empresa = $cod_empresa";
				$empresa = Conexion::buscarRegistro($query);
				if(!$empresa){
				    return false;
				}
				
				$impuesto_anterior = $empresa['impuesto'];
				
				
    			$query = "UPDATE tb_empresas
    						SET impuesto = $impuesto
    						WHERE cod_empresa = $cod_empresa";
    			Conexion::ejecutar($query, null);
    			
    			
    			$query = "SELECT cod_producto, precio, iva_porcentaje, precio_no_tax FROM tb_productos WHERE cod_empresa = $cod_empresa AND estado IN ('A','I')";
    			$productos = Conexion::buscarVariosRegistro($query);
    			foreach($productos as $key => $producto){
    			    if($tipo == "mantener_precioNoTax"){
    			        $this->mantenerNoTax($producto, $impuesto);
    			    }else
    			        $this->mantenerPVP($producto, $impuesto);
    			}
    		
    			
    			if($tipo == "mantener_precioNoTax"){
    			    if($impuesto_anterior !== $impuesto)
    			        $this->actualizarPreciodeOpciones($cod_empresa, $impuesto, $impuesto_anterior);
    			}
    			
    			
    			$con->commit();
				return true;
			} catch (\Throwable $th) {
				$con->rollBack();
				return false;
			}
		}
		
		//public function 
		
		public function mantenerPVP($producto, $impuesto){ //Actualizar el precio no tax
		    $imp = $impuesto / 100;
		    $id = $producto['cod_producto'];
		    $no_tax = number_format($producto['precio'] / $imp, 4);
		    $iva = number_format($producto['precio'] - $no_tax, 4);
		    
		    $query = "UPDATE tb_productos SET precio_no_tax = $no_tax, iva_valor = $iva, iva_porcentaje = $impuesto WHERE cod_producto = $id";
		    Conexion::ejecutar($query, null);
		    file_put_contents("log_impuestos/ImpuestoMantenerPVP$cod_empresa.log", PHP_EOL . $query, FILE_APPEND);
		}
		
		public function mantenerNoTax($producto, $impuesto){    //Actualizar el PVP
		    $id = $producto['cod_producto'];
		    $imp = (intval($producto['iva_porcentaje'])/100)+1;
		    
		    $no_tax = number_format($producto['precio_no_tax'], 2);
		    $iva = number_format($no_tax * ($impuesto/100), 2);
		    $pvp = number_format($no_tax + $iva,2);
		    
		    $query = "UPDATE tb_productos SET precio=$pvp, iva_valor = $iva, iva_porcentaje = $impuesto WHERE cod_producto = $id";
		    Conexion::ejecutar($query, null);
		    file_put_contents("log_impuestos/ImpuestoMantenerTax$cod_empresa.log", PHP_EOL . $query, FILE_APPEND);
		}
		
		public function actualizarPreciodeOpciones($cod_empresa, $impuesto, $impuesto_anterior){
		    $query = "SELECT pod.cod_producto_opciones_detalle, p.nombre, po.titulo, pod.item, pod.precio, pod.grava_iva, pod.aumentar_precio 
                    FROM tb_productos_opciones po
                    INNER JOIN tb_productos_opciones_detalle pod ON po.cod_producto_opcion = pod.cod_producto_opcion
                    INNER JOIN tb_productos p ON p.cod_producto = po.cod_producto
                    AND p.cod_empresa = $cod_empresa
                    AND pod.aumentar_precio = 1";
            $opciones = Conexion::buscarVariosRegistro($query);
            foreach($opciones as $key => $opcion){
                $id = $opcion['cod_producto_opciones_detalle'];
                $imp_ant = ($impuesto_anterior/100)+1;
			    $no_tax = number_format($opcion['precio'] / $imp_ant, 2);
		        $iva = number_format($no_tax * ($impuesto/100), 2);
		        $pvp = number_format($no_tax + $iva,2);
		        
		        $query = "UPDATE tb_productos_opciones_detalle SET precio=$pvp WHERE cod_producto_opciones_detalle = $id";
		        Conexion::ejecutar($query, null);
		        file_put_contents("log_impuestos/ImpuestoMantenerTaxOpciones$cod_empresa.log", PHP_EOL . $query, FILE_APPEND);
			}
            
		}
		
		public function getVersionesWeb(){
		    $query = "SELECT * FROM tb_version_web ORDER BY fecha_creacion DESC";
		    return Conexion::buscarVariosRegistro($query);
		}

		public function getMessagePayment($id) {
			$query = "SELECT * FROM tb_empresa_pagos WHERE cod_empresa = {$id}";
			return Conexion::buscarRegistro($query);
		}

		public function setMessagePayment($id, $title, $message) {
			$date = fecha();

			$this->removeMessagePayment($this->cod_empresa);
			
			$query = "INSERT INTO tb_empresa_pagos SET cod_empresa = {$id}, titulo = '{$title}', mensaje = '{$message}', fecha_create = '{$date}'";
			return Conexion::ejecutar($query, null);
		}

		public function removeMessagePayment($id) {
			$query = "DELETE FROM tb_empresa_pagos WHERE cod_empresa = {$id}";
			return Conexion::ejecutar($query, null);
		}
		
		/*-------------FLOTA-------------*/
		public function getComercios(){
		    $query = "SELECT 
                e.cod_empresa, e.nombre, e.logo, e.alias
                FROM 
                  tb_sucursal_flota sf
                  INNER JOIN tb_sucursales s ON s.cod_sucursal = sf.cod_sucursal  
                  INNER JOIN tb_empresas e ON e.cod_empresa = s.cod_empresa AND e.estado IN ('A')
                GROUP BY e.nombre
                ORDER BY 
                  e.nombre ASC";
		    return Conexion::buscarVariosRegistro($query);
		}
		
		public function getFlotas(){
		    $cod_empresa = $this->cod_empresa;
		    $query = "SELECT DISTINCT sf.cod_flota, e.nombre 
                FROM tb_sucursal_flota sf
                INNER JOIN tb_sucursales s ON sf.cod_sucursal = s.cod_sucursal AND s.cod_empresa = $cod_empresa
                INNER JOIN tb_empresas e ON sf.cod_flota = e.cod_empresa";
            return Conexion::buscarVariosRegistro($query);
		}
}
?>