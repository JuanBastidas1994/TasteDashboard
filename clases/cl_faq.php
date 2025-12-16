<?php

class cl_faq
{       
        var $cod_empresa, $session;
		var $cod_faq, $cod_tipo_empresa, $titulo, $desc_corta, $desc_larga, $posicion, $estado;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}
		

		public function lista($cod_tipo_empresa=0){
			$query = "SELECT * FROM tb_faqs WHERE estado IN('A','I') ORDER BY posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function listaMostrar($cod_tipo_empresa){
			$query = "SELECT * FROM tb_faqs WHERE estado = 'A' AND cod_tipo_empresa IN(0, $cod_tipo_empresa) ORDER BY posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function filterByTitulo($cod_tipo_empresa, $busqueda){
            $busqueda = str_replace(" ", "%", $busqueda);
			$query = "SELECT * FROM tb_faqs WHERE estado = 'A' AND cod_tipo_empresa IN(0, $cod_tipo_empresa) AND titulo LIKE '%$busqueda%' ORDER BY posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear(&$id){
			$query = "INSERT INTO tb_faqs(cod_tipo_empresa, titulo, desc_corta, desc_larga, estado) ";
        	$query.= "VALUES('$this->cod_tipo_empresa', '$this->titulo', '$this->desc_corta', '$this->desc_larga', '$this->estado')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar($cod_faq){
			$query = "UPDATE tb_faqs 
                        SET cod_tipo_empresa = '$this->cod_tipo_empresa', titulo = '$this->titulo', desc_corta = '$this->desc_corta', desc_larga = '$this->desc_larga', estado = '$this->estado' 
                        WHERE cod_faq = $cod_faq";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function set_estado($cod_faq, $estado){
			$query = "UPDATE tb_faqs SET estado='$estado' WHERE cod_faq = $cod_faq";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}

		public function getArray($cod_faq, &$array){
			$query = "SELECT * FROM tb_faqs WHERE cod_faq = $cod_faq";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$array = $row;
				return true;
			}
			else{
				return false;
			}
		}

		public function getArrayDetalle($cod_faq, $cod_tipo_empresa, &$array){
			$query = "SELECT * FROM tb_faqs WHERE cod_faq = $cod_faq AND cod_tipo_empresa IN(0, $cod_tipo_empresa)";
			$row = Conexion::buscarRegistro($query);
			if($row){
				$array = $row;
				return true;
			}
			else{
				return false;
			}
		}

		public function moverHelpdesk( $cod_faq, $posicion){
		   
		 	$query = "UPDATE  tb_faqs SET posicion=$posicion WHERE cod_faq =".$cod_faq;
		 	//echo $query;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}
}
?>