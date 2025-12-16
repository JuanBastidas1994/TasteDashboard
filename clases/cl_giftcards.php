<?php
class cl_giftcards
{
		var $session;
		var $cod_giftcard, $cod_empresa, $alias, $nombre, $monto, $image_min, $image_max,$imagen, $estado;
		
		public function __construct($pcod_giftcard=null)
		{
			if($pcod_giftcard != null)
			$this->cod_giftcard = $pcod_giftcard;
			$this->session = getSession();
		}

		public function lista(){
			$query = "SELECT * FROM tb_giftcards WHERE estado IN('A','I') AND cod_empresa = ".$this->session['cod_empresa']." order by posicion ASC";
            $resp = Conexion::buscarVariosRegistro($query);
            return $resp;
		}
		
	
		public function crear(&$id){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
			$query = "INSERT INTO tb_giftcards(nombre, cod_empresa, montos,imagen, estado) ";
        	$query.= "VALUES('$this->nombre', $empresa, '$this->monto','$this->imagen','A')";
        	if(Conexion::ejecutar($query,NULL)){
        		$id = Conexion::lastId();
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function getArray($cod_giftcard, &$array)
		{
			$query = "SELECT * from tb_giftcards where cod_giftcard = ".$cod_giftcard;
			$array = Conexion::buscarRegistro($query);
			return $array;
		}
		
		public function editar(){
			$usuario = $this->session['cod_usuario'];
			$empresa = $this->session['cod_empresa'];
        	$query= "UPDATE tb_giftcards SET nombre='$this->nombre', montos='$this->monto', estado='$this->estado' WHERE cod_giftcard = $this->cod_giftcard";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function set_estado($cod_giftcard, $estado){
			$query = "UPDATE tb_giftcards SET estado='$estado' WHERE cod_giftcard = $cod_giftcard";
        	if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
		}
		
		public function actPosicionOpciones($cod_giftcard, $posicion){
    	    $query = "UPDATE tb_giftcards SET posicion = $posicion WHERE cod_giftcard = $cod_giftcard";
    	    if(Conexion::ejecutar($query,NULL)){
        		return true;
        	}else{
        		return false;
        	}
	    }
        
        public function getGiftByCode($codigo, &$array){
            $query = "SELECT * FROM tb_usuario_giftcards_compradas WHERE codigo = '$codigo'";
			$array = Conexion::buscarRegistro($query);
			return $array;
        }
}
?>