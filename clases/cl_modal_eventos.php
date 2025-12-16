<?php
class cl_modal_eventos
{
		public $session;
		public $cod_empresa, $cod_modal_evento, $titulo, $descripcion, $imagen, $accion_id, $accion_desc, $fecha_ini, $fecha_fin, $estado;
		
		public function __construct($pcod_sucursal=null)
		{
			if($pcod_sucursal != null)
				$this->pcod_sucursal = $pcod_sucursal;
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}

		public function lista(){
			$query = "SELECT * 
                        FROM tb_modal_eventos 
                        WHERE estado IN('A','I') 
                        AND cod_empresa = $this->cod_empresa";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

        public function get($cod_modal_evento){
			$query = "SELECT * 
                        FROM tb_modal_eventos 
                        WHERE cod_modal_evento = $cod_modal_evento";
            return Conexion::buscarRegistro($query);
		}

        public function getArray($cod_modal_evento, &$array){
			$query = "SELECT * 
                        FROM tb_modal_eventos 
                        WHERE cod_modal_evento = $cod_modal_evento";
			$array = Conexion::buscarRegistro($query);
			return $array;
		}

        public function crear(){
            $query = "INSERT INTO tb_modal_eventos
                        SET cod_empresa = '$this->cod_empresa', 
                            titulo = '$this->titulo', 
                            descripcion = '$this->descripcion', 
                            imagen = '$this->imagen', 
                            accion_id = '$this->accion_id', 
                            accion_desc = '$this->accion_desc', 
                            fecha_inicio = '$this->fecha_ini', 
                            fecha_fin = '$this->fecha_fin', 
                            estado = '$this->estado'";
            return Conexion::ejecutar($query, null);
        }

        public function editar(){
            $query = "UPDATE tb_modal_eventos
                        SET titulo = '$this->titulo', 
                            descripcion = '$this->descripcion', 
                            accion_id = '$this->accion_id',
                            accion_desc = '$this->accion_desc', 
                            fecha_inicio = '$this->fecha_ini', 
                            fecha_fin = '$this->fecha_fin', 
                            estado = '$this->estado'
                        WHERE cod_modal_evento = $this->cod_modal_evento";
            return Conexion::ejecutar($query, null);
        }

        public function setImage($name, $scale, $cod_modal_evento){
		    $option = "imagen='$name'";
		    $query = "UPDATE tb_modal_eventos SET $option WHERE cod_modal_evento = $cod_modal_evento";
		    return Conexion::ejecutar($query,NULL);
		}

        public function set_estado($cod_modal_evento, $estado){
			$query = "UPDATE tb_modal_eventos SET estado='$estado' WHERE cod_modal_evento = $cod_modal_evento";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
}
?>