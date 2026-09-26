<?php
class cl_notificaciones
{
		public $session;
		public $cod_empresa, $nombre, $alias, $telefono, $logo, $api, $estado;
		public $contacto, $correo, $password;
		public $cod_usuario, $icono, $titulo, $detalle, $url, $fecha;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

        public function getByTipo($cod_empresa, $tipo){
        	$query = "SELECT * from tb_empresa_notificaciones WHERE cod_empresa=$cod_empresa AND aplicacion='$tipo'";
        	$row = Conexion::buscarRegistro($query);
			return $row;
        }

		/*FUNCIONES*/
		public function crear($cod_empresa_notificacion, $cod_usuario, $tipo, $titulo, $detalle){
		    Conexion::ejecutar("SET NAMES 'utf8mb4'", NULL);
			$query = "INSERT INTO tb_notificaciones(cod_empresa_notificacion, cod_usuario, tipo, titulo, detalle, fecha, estado) ";
        	$query.= "VALUES($cod_empresa_notificacion, $cod_usuario, '$tipo', '$titulo', '$detalle', NOW(), 'A')";
        	return Conexion::ejecutar($query,NULL);
		}
		
		public function getTipoNotificacion(){
		    $query = "SELECT * FROM tb_system_notification_tipos";
		    $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function insertarNotiDash(){
		    Conexion::ejecutar("SET NAMES 'utf8mb4'", NULL);
		    $query = "INSERT INTO tb_system_notification(cod_usuario, icono, titulo, detalle, url, fecha) ";
		    $query.= "VALUES($this->cod_usuario, '$this->icono', '$this->titulo', '$this->detalle', '$this->url', '$this->fecha')";
		    return Conexion::ejecutar($query,NULL);
		}

		public function getTipoNotificacionUsuario(){
			$query = "SELECT * 
						FROM tb_notificaciones_tipo 
						WHERE estado = 'A' 
						ORDER BY posicion ASC";
			return Conexion::buscarVariosRegistro($query);
		}
}
?>