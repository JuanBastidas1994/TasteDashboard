<?php

class cl_usuarios
{
		var $con;
		var $cod_usuario, $cod_empresa, $cod_rol, $nombre, $apellido, $telefono, $imagen, $correo, $usuario, $password, $estado, $fecha_nacimiento, $rol, $cod_sucursal=0, $direccion, $motivoBloqueo, $temporal, $placa="";
		var $session = null;
		
		public function __construct($pcod_usuario=null)
		{
			if($pcod_usuario != null){
				$this->cod_usuario = $pcod_usuario;
				$this->GetDatos();
			}
			if(infoLogin()){
				$this->session = getSession();
				$this->cod_empresa = $this->session['cod_empresa'];	
			}
		}
		

		public function lista(){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol NOT IN(4,17) AND u.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lista_publicador(){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol NOT IN(4,17,3,2,1) AND u.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;

			$query = "INSERT INTO tb_usuarios(cod_empresa, cod_rol, nombre, apellido, telefono, imagen, correo, usuario, password, fecha_nacimiento, estado, cod_sucursal, placa) ";
        	$query.= "VALUES($this->cod_empresa, '$this->cod_rol', '$this->nombre', '$this->apellido', '$this->telefono', '$this->imagen', '$this->correo', '$this->usuario', MD5('$this->password'), '$this->fecha_nacimiento', '$this->estado', $this->cod_sucursal, '$this->placa')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function crearFlota(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;

			$query = "INSERT INTO tb_usuarios(cod_empresa, cod_rol, nombre, apellido, telefono, imagen, correo, usuario, password, placa, estado) ";
        	$query.= "VALUES($this->cod_empresa, '$this->cod_rol', '$this->nombre', '$this->apellido', '$this->telefono', '$this->imagen', '$this->correo', '$this->usuario', MD5('$this->password'), '$this->placa', '$this->estado')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$password = "";
			if($this->password!="")
				$password = ", password = MD5('$this->password')";

			$sucursal = "";
			if($this->cod_sucursal >= 0)
				$sucursal = ", cod_sucursal = $this->cod_sucursal";
			
			$query = "UPDATE tb_usuarios SET cod_rol= '$this->cod_rol', nombre= '$this->nombre', apellido= '$this->apellido', telefono='$this->telefono', correo='$this->correo', usuario='$this->usuario', fecha_nacimiento='$this->fecha_nacimiento', estado='$this->estado', placa='$this->placa' $password $sucursal WHERE cod_usuario = $this->cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function editarFlota(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$password = "";
			if($this->password!="")
				$password = ", password = MD5('$this->password')";

			$sucursal = "";
			if($this->cod_sucursal >= 0)
				$sucursal = ", cod_sucursal = $this->cod_sucursal";
			
			$query = "UPDATE tb_usuarios SET cod_rol= '$this->cod_rol', nombre= '$this->nombre', apellido= '$this->apellido', telefono='$this->telefono', correo='$this->correo', usuario='$this->usuario', placa='$this->placa', estado='$this->estado' $password $sucursal WHERE cod_usuario = $this->cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function setUserLocation($cod_usuario, $lat, $lng){
		    $query = "UPDATE tb_usuarios SET latitud='$lat', longitud='$lng' WHERE cod_usuario = $cod_usuario";
		    return Conexion::ejecutar($query,NULL);
		}

		
		public function user_administrador($cod_empresa){
			$query = "SELECT u.* FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol = 2 AND u.cod_empresa = ".$cod_empresa." limit 0,1";
            $resp = Conexion::buscarRegistro($query);
            return $resp;
		}
		
		public function lista_registrados(){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol = 4 AND u.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaRegistradosByEmpresa($cod_empresa){
			$query = "SELECT u.*, r.nombre as rol 
			FROM tb_usuarios u, tb_roles r 
			WHERE u.cod_rol = r.cod_rol 
			AND u.estado ='A' 
			AND u.cod_rol = 4 
			AND u.cod_empresa = $cod_empresa
			ORDER BY nombre ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lista_motorizados(){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.estado ='A' AND u.cod_rol = 17 AND u.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function lista_motorizadosByEmpresa($cod_empresa){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r 
				WHERE u.cod_rol = r.cod_rol 
					AND u.estado ='A' 
					AND u.cod_rol = 17 
					AND u.cod_empresa = ".$cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		


		public function lst_roles(){
			$query = "SELECT * FROM tb_roles WHERE estado = 'A' AND cod_rol > 2";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function lst_roles_publicador(){
			$query = "SELECT * FROM tb_roles WHERE estado = 'A' AND cod_rol > 3";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
	/*FUNCIONES DE LOGIN*/
		public function Login($usuario, $password){
			$query = "SELECT u.*, e.nombre as empresa, e.fecha_caducidad, e.logo, e.alias FROM tb_usuarios u, tb_empresas e WHERE u.cod_empresa = e.cod_empresa AND u.usuario = '$usuario' and u.password = MD5('$password') AND u.estado = 'A' AND u.cod_rol NOT IN(4)";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function LoginV2($usuario, $password){
			$query = "SELECT u.*, e.nombre as empresa, e.fecha_caducidad, e.logo, e.alias 
				FROM tb_usuarios u, tb_empresas e 
				WHERE u.cod_empresa = e.cod_empresa 
				AND u.usuario = ? 
				AND u.password = MD5(?) 
				AND u.estado = 'A' 
				AND u.cod_rol NOT IN(4)";
			$resp = Conexion::buscarRegistro($query, array($usuario, $password));
			return $resp;
		}

		public function LoginAutomatico($usuario, $password)
		{
			$query = "SELECT u.*, e.nombre as empresa, e.fecha_caducidad, e.logo, e.alias FROM tb_usuarios u, tb_empresas e WHERE u.cod_empresa = e.cod_empresa AND u.usuario = '$usuario' and u.password = '$password' AND u.estado = 'A' AND u.cod_rol NOT IN(4)";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function setRememberLogin($cod_usuario, $token, $expira, $navigator="", $operative_system="", $is_mobile=0){
			$fecha = fecha();
			$fecha_fin = date("Y-m-d",$expira);
			$query = "INSERT INTO auth_tokens(cod_usuario, token, fecha_creacion, fecha_expiracion, estado, navigator, operative_system, is_mobile) 
					VALUES($cod_usuario, '$token', '$fecha','$fecha_fin','A','$navigator','$operative_system', $is_mobile)";
			return Conexion::ejecutar($query,NULL);
		}

		public function getRememberLogin($token){
			$fecha = fecha();
			$query = "SELECT u.*, e.nombre as empresa, e.fecha_caducidad, e.logo, e.alias 
					FROM auth_tokens a, tb_usuarios u, tb_empresas e
					WHERE a.cod_usuario = u.cod_usuario
					AND u.cod_empresa = e.cod_empresa 
					AND u.estado = 'A' AND a.estado = 'A'
					AND a.token = '$token'
					AND a.fecha_expiracion > '$fecha'";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getAllAuthTokens($cod_usuario){
			$query = "SELECT * FROM auth_tokens WHERE estado='A' AND cod_usuario=$cod_usuario";
			return Conexion::buscarVariosRegistro($query);
		}
		
		public function DisabledAllAuthTokens($cod_usuario){
		    $query = "UPDATE auth_tokens SET estado = 'I' WHERE cod_usuario = $cod_usuario";
			return Conexion::ejecutar($query,NULL);
		}
		
		public function DisabledOneTokenAuthTokens($cod_usuario, $token){
		    $query = "UPDATE auth_tokens SET estado = 'I' WHERE cod_usuario = $cod_usuario AND token = '$token'";
			return Conexion::ejecutar($query,NULL);
		}

		public function setIntentLogin($usuario, $password, $token, $ip, $success, $cod_usuario=0){
			$fecha =fecha();
			$query = "INSERT INTO mie_auth_intent_login(usuario, password, token, fecha, ip, success, cod_usuario, estado)
					VALUES('$usuario','$password','$token','$fecha','$ip','$success',$cod_usuario,'A')";
			return Conexion::ejecutar($query,NULL);
		}

		public function getIntentLoginFailure($usuario){
			$query = "SELECT count(usuario) as intentos_fallidos FROM mie_auth_intent_login
					WHERE usuario = '$usuario'
					AND success = 0
					AND estado = 'A'";
		}
	/*FUNCIONES DE LOGIN*/
	
		public function GetDatos(){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r WHERE u.cod_rol = r.cod_rol AND u.cod_usuario = ".$this->cod_usuario;
			$row = Conexion::buscarRegistro($query);
			if(count($row)>0)
			{
				$this->cod_empresa = $row['cod_empresa'];
				$this->cod_rol = $row['cod_rol'];
				$this->rol = $row['rol'];
				$this->nombre = $row['nombre'];
				$this->apellido = $row['apellido'];
				$this->imagen = $row['imagen'];
				$this->correo = $row['correo'];
				$this->usuario = $row['usuario'];
				$this->password= $row['password'];
				$this->telefono= $row['telefono'];
				$this->fecha_nacimiento= $row['fecha_nacimiento'];
				$this->estado = $row['estado'];
				return true;
			}
			else
			{
				return false;
			}
		}

		public function getArray($cod_usuario, &$array){
			$query = "select * from tb_usuarios where cod_usuario = ".$cod_usuario;
			$row = Conexion::buscarRegistro($query);
			if($row)
			{
				$array = $row;
				return true;
			}
			else
			{
				return false;
			}
		}

		public function get($cod_usuario){
			$query = "SELECT * from tb_usuarios where cod_usuario = ".$cod_usuario;
			$row = Conexion::buscarRegistro($query);
			return $row;
		}

		public function get2($cod_usuario){
			$empresa = $this->cod_empresa;
			$query = "SELECT * from tb_usuarios where cod_usuario = $cod_usuario AND estado IN('A','I') AND cod_empresa = ".$empresa;
			$row = Conexion::buscarRegistro($query);
			return $row;
		}

		public function getByUsuario($usuario){
			$query = "SELECT * from tb_usuarios where usuario = '$usuario' AND estado IN('A','I')";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}

		public function getExistenteByUsuario($usuario, $cod_usuario){
			$query = "SELECT * from tb_usuarios where usuario = '$usuario' AND estado IN('A','I') AND cod_usuario <> $cod_usuario";
			$row = Conexion::buscarRegistro($query);
			return $row;
		}

		public function set_estado($cod_usuario, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_usuarios SET estado='$estado', motivo_bloqueo = '$this->motivoBloqueo' WHERE cod_usuario = $cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function set_estado_cliente($cod_usuario, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_clientes SET estado='$estado' WHERE cod_usuario = $cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function set_password($cod_usuario, $password){
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_usuarios SET password=MD5('$password') WHERE cod_usuario = $cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function set_userAdmin($cod_usuario,$usuario, $password){
			if($password != null)
			{
			    $where =" password=MD5('$password'),";
			}
			$query = "UPDATE tb_usuarios SET $where usuario='$usuario' WHERE cod_rol=2 and cod_usuario = $cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
        	return $query;
		}
		
		/*NUEVO*/
		public function info_motorizado($codigo){
			$query = "SELECT u.*, r.nombre as rol FROM tb_usuarios u, tb_roles r
			          WHERE u.cod_rol = r.cod_rol AND u.estado ='A' 
			          AND u.cod_rol = 17 
			          AND u.cod_usuario=$codigo
			          AND u.cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarRegistro($query);
            return $resp;
		}
		
		public function getAdmins($cod_empresa){
		    $query = "SELECT u.*, e.nombre as nom_empresa, r.nombre as rol
		                FROM tb_usuarios u, tb_empresas e, tb_roles r
		                WHERE u.cod_empresa = e.cod_empresa
		                AND u.cod_rol = r.cod_rol
		                AND u.cod_rol IN(2,3,5) 
		                AND u.cod_empresa = $cod_empresa 
		                AND u.estado = 'A'";
		    $resp = Conexion::buscarVariosRegistro($query);
		    return $resp;
		}
		
		public function getAdminsByRol($cod_empresa, $cod_rol){
		    $query = "SELECT *
		                FROM tb_usuarios 
		                WHERE cod_empresa = $cod_empresa 
		                AND cod_rol = $cod_rol
		                AND estado = 'A'";
		    $resp = Conexion::buscarVariosRegistro($query);
		    //echo $query;
		    return $resp;
		}
        
        public function restablecerUsuario($cod_usuario){
            $query = "UPDATE tb_usuarios SET usuario = '$this->usuario' WHERE cod_usuario = $cod_usuario";
            return Conexion::ejecutar($query,NULL);
        }
        
        public function getLang($cod_idioma){
            $query = "SELECT * FROM tb_idiomas WHERE cod_idioma = $cod_idioma";
            $resp = Conexion::buscarRegistro($query);
            if($resp)
                return $resp['prefijo'];
            else
                return "";
        }

		public function editarCedula($cod_usuario, $num_documento){
			$query = "UPDATE tb_usuarios 
						SET num_documento = '$num_documento' 
						WHERE cod_usuario = $cod_usuario";
			Conexion::ejecutar($query, null);

			$query = "UPDATE tb_clientes 
						SET num_documento = '$num_documento' 
						WHERE cod_usuario = $cod_usuario";
			return Conexion::ejecutar($query, null);
		}

		public function editarCliente($cod_usuario){
			$nombreApe = $this->nombre ." ". $this->apellido;
			$query = "UPDATE tb_clientes 
						SET nombre = '$nombreApe', fecha_nac = '$this->fecha_nacimiento', 
							num_documento = '$this->num_documento', telefono = '$this->telefono', 
							direccion = '$this->direccion'
						WHERE cod_usuario = $cod_usuario";
			Conexion::ejecutar($query, null);

			$query = "UPDATE tb_usuarios
						SET nombre = '$this->nombre', apellido = '$this->apellido', fecha_nacimiento = '$this->fecha_nacimiento', 
							num_documento = '$this->num_documento', telefono = '$this->telefono', 
							direccion = '$this->direccion'
						WHERE cod_usuario = $cod_usuario";
			return Conexion::ejecutar($query, null);
		}

		public function cedulaRepetida($cedula, $cod_usuario, $cod_empresa){
			$query = "SELECT *
						FROM tb_usuarios 
						WHERE num_documento = '$cedula'
						AND cod_usuario <> $cod_usuario
						AND cod_empresa = $cod_empresa";
			return Conexion::buscarVariosRegistro($query);
		}

		public function getUserAdmin($cod_empresa){
			$query = "SELECT * 
						FROM tb_usuarios
						WHERE cod_empresa = $cod_empresa
						AND cod_rol = 2
						AND estado = 'A'
						LIMIT 0, 1";
			return Conexion::buscarRegistro($query);
		}
		
		public function getUserByEmpresa($cod_empresa, $cod_usuario){
			$query = "SELECT * 
						FROM tb_usuarios
						WHERE cod_empresa = $cod_empresa
						AND cod_usuario = $cod_usuario
						AND estado = 'A'
						LIMIT 0, 1";
			return Conexion::buscarRegistro($query);
		}

		public function crearKeystore($cod_usuario, $clave, $temporal){
			$fecha = fecha();

			$query = "UPDATE tb_usuario_keystore
						SET estado = 'I'
						WHERE cod_usuario = '$cod_usuario'";
			Conexion::ejecutar($query, null);
			
			$query = "INSERT INTO tb_usuario_keystore
						SET cod_usuario = '$cod_usuario',
						clave = MD5('$clave'),
						temporal = '$temporal',
						fecha_create = '$fecha',
						estado = 'A'";
			return Conexion::ejecutar($query, null);  
		}
	
	/*EMAIL CLIENT*/
	    public function getEmailConfig($cod_usuario){
	        $query = "SELECT * 
                        FROM tb_usuario_client_email 
                        WHERE cod_usuario = $cod_usuario";
			return Conexion::buscarRegistro($query);
	    }


		public function listaDeMotorizados(){
		    $cod_empresa = $this->cod_empresa;
			$query = "SELECT u.*, s.nombre as sucursal
                    FROM tb_usuarios u
                    LEFT JOIN tb_sucursales s ON s.cod_sucursal = u.cod_sucursal 
                    WHERE u.cod_rol = 17 AND u.cod_empresa = $cod_empresa AND u.estado = 'A'";
			return Conexion::buscarVariosRegistro($query);	
		}

		public function getIntentosPagos($fechaInicio, $fechaFin) {
			$fechaInicio = $fechaInicio . " 00:00:00";
			$fechaFin = $fechaFin . " 23:59:59";

			$query = "SELECT CONCAT(u.nombre, ' ', u.apellido) as nombre, uip.fecha, uip.monto, uip.tipo, uip.json, uip.cod_proveedor_botonpagos, uip.fraude
						FROM tb_usuarios u, tb_usuario_intento_pago uip
						WHERE u.cod_usuario = uip.cod_usuario
						AND u.cod_empresa = $this->cod_empresa
						AND uip.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
						ORDER BY uip.fecha DESC";
			return Conexion::buscarVariosRegistro($query);
		}
		
		
		public function listaCourierMotorizados(){
		    $cod_empresa = $this->cod_empresa;
			$query = "SELECT u.*, s.nombre as sucursal
                    FROM tb_usuarios u
                    LEFT JOIN tb_sucursales s ON s.cod_sucursal = u.cod_sucursal 
                    WHERE u.cod_rol = 21 AND u.cod_empresa = $cod_empresa AND u.estado = 'A'";
			return Conexion::buscarVariosRegistro($query);	
		}
}
