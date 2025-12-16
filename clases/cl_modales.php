<?php
class cl_modales
{
		var $session;
		var $cod_modal_evento, $cod_empresa, $titulo, $descripcion, $imagen, $accion_id, $accion_desc, $fecha_inicio, $fecha_fin,$estado; 
		
		public function __construct($pcod_noticia=null)
		{
			if($pcod_noticia != null)
				$this->cod_noticia= $pcod_noticia;
			    $this->session = getSession();
		}

		public function lista(){
			$query = "SELECT * FROM tb_modal_eventos WHERE estado IN('A','I') AND cod_empresa = ".$this->session['cod_empresa'];
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function get($id){
			$query = "SELECT * FROM tb_modal_eventos WHERE cod_modal_evento = $id";
			return Conexion::buscarRegistro($query);
		}


		public function crear(&$id){
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_modal_eventos(cod_empresa,titulo, descripcion, imagen, fecha_inicio, fecha_fin, accion_id, accion_desc, estado) ";
        	$query.= "VALUES($empresa, '$this->titulo', '$this->descripcion', '$this->imagen', '$this->fecha_inicio', '$this->fecha_fin', '$this->accion_id', '$this->accion_desc', 'A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar($id){
			$empresa = $this->session['cod_empresa'];
        	$query= "UPDATE tb_modal_eventos SET 
				titulo='$this->titulo',  
				accion_id='$this->accion_id', 
				accion_desc='$this->accion_desc', 
				fecha_inicio='$this->fecha_inicio', 
				fecha_fin='$this->fecha_fin' WHERE cod_modal_evento = $id";
        	return Conexion::ejecutar($query,NULL);
		}
}
?>