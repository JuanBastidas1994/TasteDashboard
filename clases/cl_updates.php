<?php

class cl_updates
{
		var $con;
		var $titulo, $detalle, $desc_corta, $estado, $url, $tipo;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}
		
		public function lista(){
			$query = "SELECT * FROM tb_updates WHERE estado IN('A','I')";
			return Conexion::buscarVariosRegistro($query); 
		}

		public function get($cod_update){
			$query = "SELECT * FROM tb_updates WHERE cod_update = $cod_update";
			return Conexion::buscarRegistro($query); 
		}

		public function getDetalle($cod_update){
			$query = "SELECT * FROM tb_updates_detalle WHERE cod_update = $cod_update";
			return Conexion::buscarVariosRegistro($query);
		}

        public function crear(&$cod_update){
			$fecha = fecha();
            $query = "INSERT INTO tb_updates(titulo, detalle, url, tipo_multimedia, fecha_create, estado) ";
			$query.= "VALUES('$this->titulo', '$this->detalle', '$this->url', '$this->tipo', '$fecha', 'A')";
			$resp = Conexion::ejecutar($query, null);
			if($resp)
				$cod_update = Conexion::lastId();
			return $resp;
        }
		
		public function editar($cod_update){
			$query = "UPDATE tb_updates 
						SET titulo = '$this->titulo', detalle = '$this->detalle', url = '$this->url', tipo_multimedia = '$this->tipo', estado = '$this->estado'
						WHERE cod_update = $cod_update";
			return Conexion::ejecutar($query, null);
		}

		public function crearDetalle($cod_update, $cod_empresa){
			$query = "INSERT INTO tb_updates_detalle(cod_update, cod_empresa)
						VALUES($cod_update, $cod_empresa)";
			return Conexion::ejecutar($query, null);
		}

		public function eliminarDetalle($cod_update){
			$query = "DELETE FROM tb_updates_detalle WHERE cod_update = $cod_update";
			return Conexion::ejecutar($query, null);
		}

		public function getLastUpdate($cod_empresa, &$update){
			$query = "SELECT u.* 
						FROM tb_updates u, tb_updates_detalle ud
						WHERE u.cod_update = ud.cod_update
						AND ud.cod_empresa = $cod_empresa
						AND u.estado = 'A'
						ORDER BY u.fecha_create DESC
						LIMIT 0,1";
			$update = Conexion::buscarRegistro($query);
			if($update)
				return true;
			return false;
		}

		public function mostrarUpdate($cod_usuario){
			$query = "SELECT * 
						FROM tb_updates_visualizado
						WHERE cod_usuario = $cod_usuario";
			return Conexion::buscarVariosRegistro($query);
		}

		public function marcarLeido($cod_update, $cod_usuario){
			$query = "INSERT INTO tb_updates_visualizado(cod_update, cod_usuario)
						VALUES($cod_update, $cod_usuario)";
			return Conexion::ejecutar($query, null);
		}
}
?>