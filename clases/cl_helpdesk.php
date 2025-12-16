<?php

class cl_helpdesk
{       
        var $cod_empresa, $session;
		var $cod_helpdesk, $titulo, $tags, $desc_corta, $desc_larga, $user_create, $posicion, $estado, $alias, $url;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}
		

		public function lista(){
			$query = "SELECT * FROM tb_dashboard_helpdesk ORDER BY posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
		public function filterByTags($filter){
			$query = "SELECT * FROM tb_dashboard_helpdesk WHERE tags LIKE '%$filter%' ORDER BY posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear(&$id){
			$empresa = $this->cod_empresa;
			$query = "INSERT INTO tb_dashboard_helpdesk(titulo, alias, tags, video, desc_corta, desc_larga, user_create, fecha_create, estado) ";
        	$query.= "VALUES('$this->titulo', '$this->alias', '$this->tags', '$this->url', '$this->desc_corta', '$this->desc_larga', '$this->user_create', '$this->fecha_create', '$this->estado')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar($cod_helpdesk){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_dashboard_helpdesk SET titulo='$this->titulo', tags = '$this->tags', video = '$this->url', desc_corta = '$this->desc_corta', desc_larga = '$this->desc_larga', estado = '$this->estado' WHERE cod_helpdesk = $cod_helpdesk";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function aliasDisponible($alias){
			$query = "SELECT * FROM tb_dashboard_helpdesk WHERE alias = '$alias' AND estado IN ('A','I')";
			$row = Conexion::buscarVariosRegistro($query, NULL);
			if(count($row)==0)
				return true;
			else
				return false;
		}

		public function set_estado($cod_helpdesk, $estado){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->cod_empresa;
			$query = "UPDATE tb_dashboard_helpdesk SET estado='$estado' WHERE cod_helpdesk = $cod_helpdesk";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getArray($cod_helpdesk, &$array)
		{
			$query = "SELECT * FROM tb_dashboard_helpdesk WHERE cod_helpdesk = $cod_helpdesk";
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


		public function get($cod_banner)
		{
			$query = "select * from tb_banner where cod_banner = ".$cod_banner;
			$row = Conexion::buscarRegistro($query);
			return $row;
		}
		
		public function moverHelpdesk( $cod_helpdesk, $posicion){
		   
		 	$query = "UPDATE  tb_dashboard_helpdesk SET posicion=$posicion WHERE cod_helpdesk =".$cod_helpdesk;
		 	//echo $query;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}
}
?>