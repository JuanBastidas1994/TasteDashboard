<?php
class cl_agendamiento
{
    
		public $cod_sucursal, $cod_usuario, $dia, $hora_inicio, $hora_final;	
	
		public function __construct($pcod_noticia=null)
		{
			if($pcod_noticia != null)
				$this->cod_noticia= $pcod_noticia;
			    $this->session = getSession();
		}

		public function crear(&$id){
			$query = "INSERT INTO tb_disponibilidad (cod_sucursal, cod_usuario, dia, hora_inicio, hora_final) VALUES('.$this->cod_sucursal.','.$this->cod_usuario.','.$this->dia.','$this->hora_inicio','$this->hora_final')";
			
			 if(Conexion::ejecutar($query, NULL)){
				$id = Conexion::lastId();
				return true;
			}else{
				return false;
			} 
		}

		public function crearIndisponibilidad($horaInicio, $horaFinal){
			$query = "INSERT INTO tb_indisponibilidad (cod_sucursal, cod_usuario, dia, hora_inicio, hora_final) VALUES('$this->cod_sucursal', '$this->cod_usuario', '$this->dia', '$horaInicio', '$horaFinal')";

			if(Conexion::ejecutar($query, NULL))
				return $query;
			else
				return $query;
		}

		public function listarPorUsuario($cod_usuario){
			$query = "SELECT * FROM tb_disponibilidad WHERE tb_disponibilidad.cod_usuario =".$cod_usuario;
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}

		public function listarPorDia($cod_usuario){
			$query = "SELECT * FROM tb_disponibilidad WHERE tb_disponibilidad.cod_usuario = '$cod_usuario'AND tb_disponibilidad.dia ='$this->dia' ORDER BY tb_disponibilidad.hora_inicio";
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}

		public function obtenerDiaId($cod_agenda){
			$query = "SELECT * FROM tb_disponibilidad WHERE tb_disponibilidad.cod_disponibilidad = '$cod_agenda'";
			$resp = Conexion::buscarRegistro($query);
			return $resp; 
		}

		public function actualizar($cod_disponibilidad){
			$query = "UPDATE tb_disponibilidad SET dia ='$this->dia', hora_inicio = '$this->hora_inicio', hora_final = '$this->hora_final' WHERE cod_disponibilidad = '".$cod_disponibilidad."'";
			if(Conexion::ejecutar($query, NULL))
				return $query;
			else
				return $query;
		}

		public function listarServiciosUsuarios(){
			$query = "SELECT pu.cod_producto FROM tb_productos_usuarios pu WHERE pu.cod_usuario = '$this->cod_usuario'";
			$resp = Conexion::buscarVariosRegistro($query);
			return $resp;
		}

		public function crearServiciosUsuarios($servicio){ //OJO PRACTICA
			$query = "SELECT * FROM  tb_productos_usuarios pu WHERE pu.cod_producto = '$servicio' AND pu.cod_usuario = '$this->cod_usuario'";
			$row = Conexion::buscarRegistro($query);
			if($row['cod_producto'] == 0){
				$queryInsert = "INSERT INTO tb_productos_usuarios(cod_producto, cod_usuario) VALUES('$servicio', '$this->cod_usuario')";
				if(Conexion::ejecutar($queryInsert, NULL)){
					return true;
				}else{
					return false;
				}
			}else{
				$queryDelete = "DELETE FROM tb_productos_usuarios WHERE tb_productos_usuarios.cod_producto = '$servicio' AND tb_productos_usuarios.cod_usuario = '$this->cod_usuario'";
				if(Conexion::ejecutar($queryDelete, NULL)){
					return true;
				}else{
					return false;
				}

			}
		}

		public function eliminarDisponibilidad($cod){
			$query = "DELETE FROM tb_disponibilidad WHERE tb_disponibilidad.cod_disponibilidad = '$cod'";
			if(Conexion::ejecutar($query, NULL))
				return true;
			else
				return false;
		}

		public function eliminarDiaIndisponibilidad(){
			$query = "DELETE FROM tb_indisponibilidad WHERE tb_indisponibilidad.cod_usuario = '$this->cod_usuario' AND tb_indisponibilidad.dia = '$this->dia' AND tb_indisponibilidad.cod_sucursal = '$this->cod_sucursal'";
			if(Conexion::ejecutar($query, NULL))
				return true;
			else
				return false;
		}


}
?>