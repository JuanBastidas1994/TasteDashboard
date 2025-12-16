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

        /*GET*/
        public function get($cod_empresa_notificacion)
		{
			$query = "SELECT * from tb_empresa_notificaciones where cod_empresa_notificacion=$cod_empresa_notificacion";
			$row = Conexion::buscarRegistro($query);
			return $row;
        }

        public function getByTipo($cod_empresa, $tipo){
        	$query = "SELECT * from tb_empresa_notificaciones WHERE cod_empresa=$cod_empresa AND aplicacion='$tipo'";
        	$row = Conexion::buscarRegistro($query);
			return $row;
        }

		/*LISTA*/
		public function lista($cod_empresa){
			$query = "SELECT * FROM tb_empresa_notificaciones WHERE cod_empresa = $cod_empresa AND estado IN ('A','I')";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaUltimas($cod_empresa){
			$query = "SELECT n.*, e.aplicacion
					FROM tb_notificaciones n, tb_empresa_notificaciones e
					WHERE n.cod_empresa_notificacion = e.cod_empresa_notificacion
					AND e.cod_empresa =  ".$this->session['cod_empresa']."
					AND n.estado IN ('A','I')
					ORDER BY n.fecha DESC LIMIT 0,50";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function listaGeneral($cod_empresa){
			$query = "SELECT n.*, e.aplicacion
					FROM tb_notificaciones n, tb_empresa_notificaciones e
					WHERE n.cod_empresa_notificacion = e.cod_empresa_notificacion
					AND e.cod_empresa =  ".$this->session['cod_empresa']."
					AND n.cod_usuario = 0 
					AND n.estado IN ('A','I')
					ORDER BY n.fecha DESC LIMIT 0,50";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
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