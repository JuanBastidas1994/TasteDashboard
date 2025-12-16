<?php
class cl_personal
{
		public $session;
		public $cod_empresa, $cod_personal, $nombre, $apellido, $correo, $telefono, $imagen, $fecha_nac, $estado;
		
		public function __construct($pcod_personal=null)
		{
			if($pcod_personal != null)
				$this->pcod_personal = $pcod_personal;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function lista(){
			$query = "SELECT * FROM tb_personal WHERE estado IN('A','I') AND cod_empresa = ".$this->cod_empresa;
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function crear($cod_usuario){
            $query = "INSERT INTO tb_personal(cod_empresa, cod_usuario, nombre, apellido, correo, telefono, imagen, fecha_nacimiento, estado) 
                        VALUES('$this->cod_empresa', $cod_usuario, '$this->nombre', '$this->apellido', '$this->correo', '$this->telefono', '$this->imagen', '$this->fecha_nac', '$this->estado')";
            return Conexion::ejecutar($query, null);
        }
        public function editar($cod_usuario){
            $query = "UPDATE tb_personal 
                        SET nombre = '$this->nombre', apellido = '$this->apellido', correo = '$this->correo', telefono = '$this->telefono', fecha_nacimiento = '$this->fecha_nac', estado = '$this->estado' 
                        WHERE cod_usuario = $cod_usuario";
            return Conexion::ejecutar($query, null);
        }
        
        public function getArray($cod_usuario){
            $query = "SELECT * FROM tb_personal WHERE cod_usuario = $cod_usuario";
            $row = Conexion::buscarRegistro($query);
            return $row;
        }

        public function set_estado($cod_usuario, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_personal SET estado='$estado' WHERE cod_usuario = $cod_usuario";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
}
?>