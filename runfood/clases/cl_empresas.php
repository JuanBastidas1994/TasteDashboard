<?php

class cl_empresas
{
		public $cod_usuario, $cod_empresa, $cod_rol, $nombre, $apellido, $imagen, $correo, $usuario, $password, $fecha_nacimiento, $estado;
		
		public function __construct($pcod_usuario=null)
		{
			if($pcod_usuario != null)
				$this->cod_usuario = $pcod_usuario;
		}

		public function get(){
			$query = "SELECT * FROM tb_empresas WHERE cod_empresa = ".cod_empresa;
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getFidelizacion(){
			$query = "SELECT * FROM  tb_empresa_fidelizacion_puntos WHERE cod_empresa = ".cod_empresa;
			$resp = Conexion::buscarRegistro($query);
			return $resp;
		}

		public function getNiveles(){
			$cod_empresa = cod_empresa;
			$query = "SELECT nombre, punto_inicial, punto_final, dinero_x_punto, posicion FROM tb_niveles WHERE cod_empresa = $cod_empresa ORDER BY posicion";
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}
}
?>