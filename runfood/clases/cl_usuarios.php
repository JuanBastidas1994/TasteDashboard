<?php

class cl_usuarios
{
		public $cod_usuario, $cod_empresa, $cod_rol, $nombre, $apellido, $imagen, $correo, $usuario, $password, $fecha_nacimiento, $estado, $num_documento;
		
		public function __construct($pcod_usuario=null)
		{
			if($pcod_usuario != null)
				$this->cod_usuario = $pcod_usuario;
		}


		public function direcciones($cod_usuario){
			$query = "SELECT * FROM tb_usuario_direcciones WHERE cod_usuario =".$cod_usuario;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function save_direcciones($cod_usuario, $nombre, $direccion, $lat, $lon){
			$query = "INSERT INTO tb_usuario_direcciones(cod_usuario, nombre, direccion, latitud, longitud)";
			$query.= "VALUES($cod_usuario, '$nombre', '$direccion', '$lat', '$lon')";
			return Conexion::ejecutar($query,NULL);
		}

		public function Login($usuario, $password)
		{
			$query = "SELECT *
					FROM tb_usuarios u
					WHERE u.usuario = '$usuario' 
					AND u.password = MD5('$password') 
					AND u.estado = 'A'
					AND u.cod_rol = 4
					AND u.cod_empresa = ".cod_empresa;
			$return = Conexion::buscarRegistro($query);
			return $return;
		}

		public function usuarioDisponible($usuario){
			$query = "SELECT * FROM tb_usuarios WHERE usuario = '$usuario' AND estado IN('A','I') AND cod_empresa = ".cod_empresa;
			$row = Conexion::buscarRegistro($query, NULL);
			if($row)
				return false;
			else
				return true;
		}

		public function registro(){
			$query = "INSERT INTO tb_usuarios(cod_empresa, cod_rol, nombre, apellido, correo, usuario, password, fecha_nacimiento, estado, cedula, num_documento)";
			$query.= "VALUES($this->cod_empresa, $this->cod_rol, '$this->nombre', '$this->apellido', '$this->correo', '$this->correo', MD5('$this->password'), '$this->fecha_nacimiento','A','$this->num_documento','$this->num_documento')";
			return Conexion::ejecutar($query,NULL);
		}

		public function get($cod_usuario){
			$query = "SELECT * FROM tb_usuarios WHERE cod_usuario = $cod_usuario";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getbyNumDocumento($num_documento){
			$query = "SELECT * FROM tb_usuarios WHERE num_documento = '$num_documento'";
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function set_password($cod_usuario, $password){
			$query = "UPDATE tb_usuarios SET password = MD5('$password') WHERE cod_usuario = $cod_usuario";
			return Conexion::ejecutar($query,NULL);
		}
}
?>