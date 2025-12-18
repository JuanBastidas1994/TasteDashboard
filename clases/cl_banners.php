<?php

class cl_banners
{
		var $con, $session;
		var $cod_empresa, $cod_banner, $titulo, $subtitulo, $descuento, $image_min, $text_boton, $url_boton, $estado;
		
		public function __construct()
		{
			$this->session = getSession();
			$this->cod_empresa = $this->session['cod_empresa'];
		}
		

		public function lista(){
			$query = "SELECT * FROM tb_banner WHERE estado IN ('A','I') AND cod_empresa =$this->cod_empresa ORDER BY posicion";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}

		public function crear(&$id){
			$query = "INSERT INTO tb_banner(cod_empresa, titulo, subtitulo, descuento, image_min, text_boton, url_boton, tipo, posicion, estado) ";
        	$query.= "VALUES($this->cod_empresa, '$this->titulo', '$this->subtitulo', '$this->descuento', '$this->image_min', '$this->text_boton', '$this->url_boton', 'WEB', 999, '$this->estado')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}

		public function editar(){
			$query = "UPDATE tb_banner 
							SET titulo='$this->titulo', subtitulo='$this->subtitulo', descuento='$this->descuento', text_boton='$this->text_boton', url_boton='$this->url_boton', estado = '$this->estado' 
							WHERE cod_banner = $this->cod_banner";
			return Conexion::ejecutar($query,NULL);
		}

		public function set_estado($cod_banner, $estado){
			$query = "UPDATE tb_banner 
						SET estado='$estado' 
						WHERE cod_banner = $cod_banner";
			return Conexion::ejecutar($query,NULL);
		}

		public function getArray($cod_banner, &$array)
		{
			$query = "select * from tb_banner where cod_banner = ".$cod_banner;
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
		
		public function moverBanners( $cod_banner, $posicion){
		   
		 	$query = "UPDATE  tb_banner SET posicion=$posicion WHERE cod_banner =".$cod_banner;
		 	//echo $query;
	    	if(Conexion::ejecutar($query,NULL)){
    		return true;
        	}else{
        		return false;
        	}
	    
		}
}
?>